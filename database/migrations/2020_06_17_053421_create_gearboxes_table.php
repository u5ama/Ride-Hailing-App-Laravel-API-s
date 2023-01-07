<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGearboxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gearboxes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('gearbox_order');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('gearbox_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('gearbox_id');
            $table->string('name');
            $table->string('locale', 5)->index();
            $table->unique(['gearbox_id', 'locale']);
            $table->timestamps();

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
        Schema::dropIfExists('gearboxes');
        Schema::dropIfExists('gearbox_translations');
    }
}
