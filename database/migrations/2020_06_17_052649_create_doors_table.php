<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDoorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('vehicle_type_id');
            $table->unsignedBigInteger('door_order');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('vehicle_type_id')
                ->references('id')
                ->on('vehicle_types')
                ->onDelete('cascade');
        });

        Schema::create('door_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('door_id');
            $table->string('name');
            $table->string('locale', 5)->index();
            $table->unique(['door_id', 'locale']);
            $table->timestamps();

            $table->foreign('door_id')
                ->references('id')
                ->on('doors')
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
        Schema::dropIfExists('doors');
        Schema::dropIfExists('door_translations');
    }
}
