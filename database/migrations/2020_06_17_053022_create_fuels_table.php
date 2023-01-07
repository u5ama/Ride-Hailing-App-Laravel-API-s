<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFuelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fuels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('fuel_order');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('fuel_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('fuel_id');
            $table->string('name');
            $table->string('locale', 5)->index();
            $table->unique(['fuel_id', 'locale']);
            $table->timestamps();

            $table->foreign('fuel_id')
                ->references('id')
                ->on('fuels')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fuels');
        Schema::dropIfExists('fuel_translations');
    }
}
