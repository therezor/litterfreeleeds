<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Reference data imported from the ONS Postcode Directory — see the
     * postcodes:import command. Keyed by the normalised postcode (uppercase,
     * no space) so lookups never depend on how a visitor typed it.
     *
     * No index beyond the primary key: this table is only ever read by a single
     * key lookup for the visitor's own postcode. The distance sort runs over
     * community_picks, which carries its own copy of the coordinates.
     */
    public function up(): void
    {
        Schema::create('postcodes', function (Blueprint $table) {
            // Natural key: the normalised postcode is immutable and unique, so a
            // surrogate id would only add 8 bytes and a second B-tree across 1.8m
            // rows. Longest normalised UK postcode is 7 characters (EC1A1BB).
            $table->string('postcode', 7)->primary();
            $table->string('outward_code', 4);

            // ONS ward code (ONSPD's `osward`), e.g. E05011389. Nullable because
            // it is only populated by postcodes:import — seeded demo postcodes
            // have none. Indexed: resolving a pick's area starts here.
            $table->string('ward_code', 9)->nullable()->index();

            // ONS local authority district code (ONSPD's `oslaua`). Lets a pick
            // outside Leeds say which town or city it is in.
            $table->string('district_code', 9)->nullable()->index();

            // double, not decimal: radians() takes double precision, and a decimal
            // cast hands Eloquent strings.
            $table->double('latitude');
            $table->double('longitude');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postcodes');
    }
};
