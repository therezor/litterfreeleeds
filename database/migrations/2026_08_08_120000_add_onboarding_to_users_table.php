<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Nullable throughout: admins and every user who predates onboarding
            // have no postcode, and only volunteers who register through /join
            // ever tick the terms box.
            $table->string('postcode', 7)->nullable()->index();
            $table->string('outward_code', 4)->nullable()->index();

            // Copied from the postcodes table by the UserObserver, mirroring
            // community_picks. double, not decimal: radians() takes double
            // precision, and a decimal cast hands Eloquent strings.
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();

            // Proof of consent to data processing, captured at registration.
            $table->timestamp('terms_accepted_at')->nullable();

            // Set by the assigned Purple Bag Holder once they have made contact.
            $table->timestamp('onboarded_at')->nullable();

            // Deliberately nullOnDelete, unlike community_picks.responsible_user_id:
            // a bag holder standing down should not be undeletable because pickers
            // point at them, and an unassigned picker is a state we already render.
            // Nullable also covers day one, when no Purple Bag Holder exists yet.
            $table->foreignId('assigned_bag_holder_id')
                ->nullable()
                ->index()
                ->constrained('users')
                ->nullOnDelete();
        });

        // User now implements MustVerifyEmail, which switches on Filament's
        // `verified` middleware across the whole panel. Without this backfill
        // every account created before today would be locked out of /app.
        DB::table('users')->whereNull('email_verified_at')->update([
            'email_verified_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_bag_holder_id');
            $table->dropColumn([
                'postcode',
                'outward_code',
                'latitude',
                'longitude',
                'terms_accepted_at',
                'onboarded_at',
            ]);
        });
    }
};
