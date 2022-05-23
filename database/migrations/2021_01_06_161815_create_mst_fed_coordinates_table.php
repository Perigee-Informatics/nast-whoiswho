<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstFedCoordinatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_fed_coordinates', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('code',20);
            $table->smallInteger('level');
            $table->jsonb('coordinates'); 
            $table->timestamps();

            $table->unique(['code'],'uq_mst_fed_coordinates_type_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_fed_coordinates');

    }
}
