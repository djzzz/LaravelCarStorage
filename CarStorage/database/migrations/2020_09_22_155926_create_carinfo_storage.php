<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarinfoStorage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carinfo_storage', function (Blueprint $table) {
            $table->id();
            $table->string('Brand');
            $table->tinyInteger('Year');
            $table->string('LicensePlate');
            $table->boolean('company');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carinfo_storage');
    }
}
