<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyReportIndoIchaNursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_report_indo_icha_nurs', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('contact_way',50)->nullable();
            $table->text('author_contact')->nullable();
            $table->text('platform')->nullable();
            $table->string('status',50)->nullable();
            $table->text('inquiries')->nullable();
            $table->text('new_cbid')->nullable();
            $table->text('old_cbid')->nullable();
            $table->text('author')->nullable();
            $table->text('title')->nullable();
            $table->text('genre')->nullable();
            $table->text('k4')->nullable();
            $table->text('plot')->nullable();
            $table->text('maintain_account')->nullable();
            $table->date('fu_1')->nullable();
            $table->date('fu_2')->nullable();
            $table->date('fu_3')->nullable();
            $table->date('fu_4')->nullable();
            $table->date('fu_5')->nullable();
            $table->date('data_sent')->nullable();
            $table->text('marker')->nullable();
            $table->text('old_new_book')->nullable();
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
        Schema::dropIfExists('daily_report_indo_icha_nurs');
    }
}