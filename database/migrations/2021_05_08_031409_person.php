<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Person extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('person', function(Blueprint $table){
            $table->id();
            $table->string('address')->nullable();
            $table->string('name')->nullable();
            $table->string('nik')->nullable();
            $table->string('qq_email')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('discord')->nullable();
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
            $table->string('status')->nullable();
            $table->enum('level',['0','1','2'])->default('0');
            $table->enum('team',['Global','Indo','Spam'])->default('Global');
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
        //
    }
}
