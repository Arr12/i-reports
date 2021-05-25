<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNonExclusiveReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('non_exclusive_reports', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('global_editor',50)->nullable();
            $table->text('author_contact')->nullable();
            $table->string('platform', 50)->nullable();
            $table->text('username')->nullable();
            $table->text('title')->nullable();
            $table->string('book_status',50)->nullable();
            $table->string('latest_update',50)->nullable();
            $table->date('first_touch')->nullable();
            $table->string('book_id',50)->nullable();
            $table->date('sent_e_contract')->nullable();
            $table->string('officer', 50)->nullable();
            $table->date('date_sent')->nullable();
            $table->text('and_notes')->nullable();
            $table->text('global_editor_notes')->nullable();
            $table->date('solved_date')->nullable();
            $table->text('pdf_evidence')->nullable();
            $table->date('rec_e_contract')->nullable();
            $table->date('fu_1')->nullable();
            $table->date('fu_2')->nullable();
            $table->date('fu_3')->nullable();
            $table->date('fu_4')->nullable();
            $table->date('fu_5')->nullable();
            $table->string('marker_for_global',50)->nullable();
            $table->string('marker_for_and',50)->nullable();
            $table->date('email_sent')->nullable();
            $table->date('batch_date')->nullable();
            $table->text('and_evidence')->nullable();
            $table->text('global_evidence')->nullable();
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
        Schema::dropIfExists('non_exclusive_reports');
    }
}