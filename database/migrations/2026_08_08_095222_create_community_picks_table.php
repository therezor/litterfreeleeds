<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_picks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->date('date');
            $table->time('time_from');
            $table->time('time_to');
            $table->string('excerpt', 255);
            $table->text('description')->nullable();
            $table->string('location');
            $table->string('postcode', 7);
            $table->string('outward_code', 4)->index();

            // Copied from the postcodes table on save. Denormalised so the public
            // page keeps working while postcodes:import --fresh truncates the
            // lookup table, and so a revised ONS centroid never silently moves a
            // pick that has already been advertised.
            $table->double('latitude');
            $table->double('longitude');

            // Postgres does not index foreign keys automatically.
            // Required, so cascading would silently delete picks and nulling is
            // impossible — deleting a coordinator who still owns picks should fail.
            $table->foreignId('responsible_user_id')->index()->constrained('users')->restrictOnDelete();

            $table->timestamps();

            // Matches the orderBy('date')->orderBy('time_from') in both scopes.
            $table->index(['date', 'time_from']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_picks');
    }
};
