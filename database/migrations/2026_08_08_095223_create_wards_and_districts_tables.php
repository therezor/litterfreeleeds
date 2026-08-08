<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Electoral wards and local authority districts, imported from the lookup
     * CSVs that ship inside an ONSPD release. Reference data — see
     * postcodes:import.
     */
    public function up(): void
    {
        foreach (['wards', 'districts'] as $table) {
            Schema::create($table, function (Blueprint $table) {
                $table->string('code', 9)->primary();
                $table->string('name')->index();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
        Schema::dropIfExists('wards');
    }
};
