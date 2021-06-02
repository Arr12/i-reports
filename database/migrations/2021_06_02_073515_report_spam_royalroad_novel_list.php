<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReportSpamRoyalroadNovelList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_spam_royalroad_novel_list', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->text('editor')->nullable();
            $table->text('title')->nullable();
            $table->text('author')->nullable();
            $table->text('url')->nullable();
            $table->text('type')->nullable();
            $table->text('followers')->nullable();
            $table->text('pages')->nullable();
            $table->text('chapters')->nullable();
            $table->text('views')->nullable();
            $table->text('latest_update')->nullable();
            $table->text('tags6')->nullable();
            $table->text('tag1')->nullable();
            $table->text('tag2')->nullable();
            $table->text('tag3')->nullable();
            $table->text('tag4')->nullable();
            $table->text('tag5')->nullable();
            $table->text('tag6')->nullable();
            $table->text('tags7')->nullable();
            $table->text('tags8')->nullable();
            $table->text('tags9')->nullable();
            $table->text('tags10')->nullable();
            $table->text('tags11')->nullable();
            $table->text('date_feedback_received')->nullable();
            $table->text('feedback_from_author')->nullable();
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
        Schema::dropIfExists('report_spam_royalroad_novel_list');
    }
}
