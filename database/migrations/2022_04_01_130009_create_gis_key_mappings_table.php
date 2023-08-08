<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGisKeyMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gis_key_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gis_boundary_sub_type_id');
            $table->string('gis_key');
            $table->string('gis_key_slug')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gis_key_mappings');
    }
}
