<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRydeInstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ryde_instances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ryde_id');
            $table->unsignedBigInteger('body_id');
            $table->unsignedBigInteger('engine_id');
            $table->unsignedBigInteger('door_id');
            $table->unsignedBigInteger('fuel_id');
            $table->unsignedBigInteger('gearbox_id');
            $table->unsignedBigInteger('seats');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('ryde_id')
                ->references('id')
                ->on('rydes')
                ->onDelete('cascade');

            $table->foreign('body_id')
                ->references('id')
                ->on('bodies')
                ->onDelete('cascade');

            $table->foreign('engine_id')
                ->references('id')
                ->on('engines')
                ->onDelete('cascade');

            $table->foreign('door_id')
                ->references('id')
                ->on('doors')
                ->onDelete('cascade');

            $table->foreign('fuel_id')
                ->references('id')
                ->on('fuels')
                ->onDelete('cascade');

            $table->foreign('gearbox_id')
                ->references('id')
                ->on('gearboxes')
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
        Schema::dropIfExists('ryde_instances');
    }
}
