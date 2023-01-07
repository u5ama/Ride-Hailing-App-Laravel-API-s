<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnginesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('engines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('vehicle_type_id');
            $table->unsignedBigInteger('engine_order');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('vehicle_type_id')
                ->references('id')
                ->on('vehicle_types')
                ->onDelete('cascade');
        });

        Schema::create('engine_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('engine_id');
            $table->string('name');
            $table->string('locale', 5)->index();
            $table->unique(['engine_id', 'locale']);
            $table->timestamps();

            $table->foreign('engine_id')
                ->references('id')
                ->on('engines')
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
        Schema::dropIfExists('engines');
        Schema::dropIfExists('engine_translations');
    }
}
