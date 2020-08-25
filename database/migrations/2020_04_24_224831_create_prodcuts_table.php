<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdcutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('deleted')->default(0);
            $table->enum('status',['active','suspended','blocked']);
            $table->enum('type',['static','variable']);
            $table->bigInteger('seller_id')->unsigned();
            $table->foreign('seller_id')->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('ar_name');
            $table->string('en_name');
            $table->longText('ar_desc');
            $table->longText('en_desc');
            $table->text('ar_special');
            $table->text('en_special');
            $table->string('image');
            $table->string('thumb_image');
            $table->integer('rate')->default(0);
            $table->integer('views')->default(0);
            $table->integer('sold')->default(0);
            $table->tinyInteger('discount')->default(0);
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
        Schema::dropIfExists('products');
    }
}
