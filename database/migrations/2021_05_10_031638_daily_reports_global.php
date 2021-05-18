<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DailyReportsGlobal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_reports_global', function(Blueprint $table){
            $table->id();
            $table->integer('person_id');
            $table->dateTime('date')->nullable();
            $table->string('status')->nullable();
            $table->enum('media',['Email','Whatsapp','Discord','Facebook'])->default('Email');
            $table->string('author_contact');
            $table->enum('inquiries',['Royalty','Non-exclusive','Non English','Fan Fiction','Introduction','Non Fiction','Underage','Reject','MIA','Assisted by Order'])->default('Royalty');
            $table->string('platform')->nullable();
            $table->string('username')->nullable();
            $table->string('title')->nullable();
            $table->string('webnovel_username')->nullable();
            $table->string('cbid_book_id')->nullable();
            $table->string('book_title')->nullable();
            $table->string('book_genre')->nullable();
            $table->text('book_plot')->nullable();
            $table->enum('4k+?',['Yes','No'])->default('No');
            $table->enum('maintain_account',['Yes','No'])->default('No');
            $table->date('follow_up1')->nullable();
            $table->date('follow_up2')->nullable();
            $table->date('follow_up3')->nullable();
            $table->date('follow_up4')->nullable();
            $table->date('follow_up5')->nullable();
            $table->date('sent_non_exclusive')->nullable();
            $table->integer('marker')->nullable();
            $table->enum('status_book',['Old','New'])->default('New');
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
