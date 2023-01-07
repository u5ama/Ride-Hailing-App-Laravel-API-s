<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLanguageStringsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('language_strings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('app_or_panel');
            $table->string('screen_name');
            $table->string('name_key');
            $table->timestamps();
        });

        Schema::create('language_string_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('language_string_id')->unsigned();
            $table->string('name');
            $table->string('locale')->index();
            $table->unique(['language_string_id', 'locale']);
            $table->foreign('language_string_id')
                ->references('id')
                ->on('language_strings')
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
        Schema::dropIfExists('language_strings');
        Schema::dropIfExists('language_string_translations');
    }
}
