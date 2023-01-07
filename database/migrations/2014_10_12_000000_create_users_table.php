<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('mobile_no')->nullable();
            $table->string('locale')->default('en');
            $table->tinyInteger('panel_mode');
            $table->enum('user_type', ['company', 'admin']);
            $table->string('image')->default('assets/default/user.png');
            $table->enum('status', ['Active', 'InActive']);
            $table->unsignedBigInteger('parent_id');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
