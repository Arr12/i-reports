<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReportSpamMangatoonNovelList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_spam_mangatoon_novel_list', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->text('reasons')->nullable();
            $table->text('book_name')->nullable();
            $table->text('author_name')->nullable();
            $table->text('views')->nullable();
            $table->text('likes')->nullable();
            $table->text('ratings')->nullable();
            $table->text('update_status')->nullable();
            $table->text('tags')->nullable();
            $table->text('episodes')->nullable();
            $table->text('link')->nullable();
            $table->text('screenshot_from_wave')->nullable();
            $table->date('date_feedback_received')->nullable();
            $table->text('author_feedback')->nullable();
            $table->text('comment_from_wave')->nullable();
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
        Schema::dropIfExists('report_spam_mangatoon_novel_list');
    }
}
