<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyReportEricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_report_erics', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('status',50)->nullable();
            $table->string('media',50)->nullable();
            $table->text('author_contact')->nullable();
            $table->text('inquiries')->nullable();
            $table->text('platform')->nullable();
            $table->text('platform_user')->nullable();
            $table->text('platform_title')->nullable();
            $table->text('username')->nullable();
            $table->text('cbid')->nullable();
            $table->text('title')->nullable();
            $table->text('genre')->nullable();
            $table->text('plot')->nullable();
            $table->text('k4')->nullable();
            $table->text('maintain_account')->nullable();
            $table->date('fu_1')->nullable();
            $table->date('fu_2')->nullable();
            $table->date('fu_3')->nullable();
            $table->date('fu_4')->nullable();
            $table->date('fu_5')->nullable();
            $table->date('sent_royalty')->nullable();
            $table->date('sent_non_exclusive')->nullable();
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
        Schema::dropIfExists('daily_report_erics');
    }
}
