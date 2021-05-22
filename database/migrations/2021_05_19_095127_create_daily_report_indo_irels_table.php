<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyReportIndoIrelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_report_indo_irels', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('status',50)->nullable();
            $table->date('date_solved')->nullable();
            $table->text('author_contact')->nullable();
            $table->text('inquiries')->nullable();
            $table->text('cbid')->nullable();
            $table->text('title')->nullable();
            $table->text('author')->nullable();
            $table->text('zoom_tutorial')->nullable();
            $table->date('fu_1')->nullable();
            $table->date('fu_2')->nullable();
            $table->date('fu_3')->nullable();
            $table->date('fu_4')->nullable();
            $table->date('fu_5')->nullable();
            $table->date('fu_6')->nullable();
            $table->date('fu_7')->nullable();
            $table->date('fu_8')->nullable();
            $table->date('fu_9')->nullable();
            $table->date('fu_10')->nullable();
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
        Schema::dropIfExists('daily_report_indo_irels');
    }
}