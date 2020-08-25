<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('deleted')->default(0);
            $table->enum('status',['active','suspended']);
            $table->bigInteger('country_id')->nullable();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('image')->default('default.png');
            $table->string('password');
            $table->enum('lang',['ar','en']);
            $table->longText('permissions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
