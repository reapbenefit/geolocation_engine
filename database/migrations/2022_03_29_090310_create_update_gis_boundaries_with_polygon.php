<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpdateGisBoundariesWithPolygon extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gis_coordinates', function (Blueprint $table) {
            $table->polygon('polygon')->nullable();
            $table->json('properties')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gis_coordinates', function (Blueprint $table) {
            $table->dropColumn('polygon')->nullable();
            $table->dropColumn('json')->nullable();
        });
    }
}
