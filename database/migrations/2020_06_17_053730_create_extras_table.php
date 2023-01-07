<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExtrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extras', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('extra_order');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('extra_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('extra_id');
            $table->string('name');
            $table->string('locale', 5)->index();
            $table->unique(['extra_id', 'locale']);
            $table->timestamps();

            $table->foreign('extra_id')
                ->references('id')
                ->on('extras')
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
        Schema::dropIfExists('extras');
    }
}
