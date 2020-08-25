<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('deleted')->default(0);
            $table->enum('status',['active','suspended']);
            $table->bigInteger('parent_id')->nullable()->unsigned();
            $table->foreign('parent_id')->references('id')->on('countries')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('ar_name');
            $table->string('en_name');
            $table->string('ar_currency');
            $table->string('en_currency');
            $table->double('tax_percentage');
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
        Schema::dropIfExists('countries');
    }
}
