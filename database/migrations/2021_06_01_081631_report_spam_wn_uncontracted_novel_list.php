<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReportSpamWnUncontractedNovelList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_spam_wn_uncontracted_novel_list', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->text('reasons')->nullable();
            $table->text('editor')->nullable();
            $table->text('cbid')->nullable();
            $table->text('book_title')->nullable();
            $table->text('author_name')->nullable();
            $table->text('discord_contact')->nullable();
            $table->text('other_contact_way')->nullable();
            $table->text('genre')->nullable();
            $table->text('total_chapter')->nullable();
            $table->text('chapter_within_7_days')->nullable();
            $table->text('collection')->nullable();
            $table->text('status_ongoing')->nullable();
            $table->text('FL_ML')->nullable();
            $table->date('date_feedback_received')->nullable();
            $table->text('feedback_from_author')->nullable();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('report_spam_wn_uncontracted_novel_list');
    }
}