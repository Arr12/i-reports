<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReportSpamNovelListFromRanking extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_spam_novel_list_from_ranking', function (Blueprint $table) {
            $table->id();
            $table->text('cbid')->nullable();
            $table->text('book_title')->nullable();
            $table->text('author_name')->nullable();
            $table->text('author_contact')->nullable();
            $table->string('genre',80)->nullable();
            $table->string('total_chapter',50)->nullable();
            $table->string('chapter_within_7_days',50)->nullable();
            $table->text('collection')->nullable();
            $table->string('status_ongoing',80)->nullable();
            $table->string('FL_ML',50)->nullable();
            $table->text('editor')->nullable();
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
        Schema::dropIfExists('report_spam_novel_list_from_ranking');
    }
}
