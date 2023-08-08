<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateGisBoundaryTypeIdNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gis_boundary_sub_types', function (Blueprint $table) {
            $table->unsignedBigInteger('gis_boundary_type_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gis_boundary_sub_types', function (Blueprint $table) {
            $table->unsignedBigInteger('gis_boundary_type_id')->nullable(false);
        });
    }
}
