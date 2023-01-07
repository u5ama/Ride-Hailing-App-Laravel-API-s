<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_models', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('brand_id');
            $table->string('image');
            $table->unsignedBigInteger('model_order');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('brand_id')
                ->references('id')
                ->on('brands')
                ->onDelete('cascade');
        });

        Schema::create('brand_model_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('brand_model_id');
            $table->string('name');
            $table->string('locale', 5)->index();
            $table->unique(['brand_model_id', 'locale']);
            $table->timestamps();

            $table->foreign('brand_model_id')
                ->references('id')
                ->on('brand_models')
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
        Schema::dropIfExists('brand_models');
        Schema::dropIfExists('brand_model_translations');
    }
}
