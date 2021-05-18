<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GlobalTeamMonitoringDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_team_monitoring_details', function(Blueprint $table){
            $table->id();
            $table->integer('person_id');
            $table->integer('total_answer_n_auth');
            $table->integer('avg_answer_n_auth');
            $table->integer('total_n_auth_non_ex');
            $table->integer('avg_n_auth_non_ex');
            $table->integer('total_follow_up');
            $table->integer('avg_follow_up');
            $table->integer('total_follow_up_non_ex');
            $table->integer('avg_follow_up_non_ex');
            $table->integer('total_sent_e_contract');
            $table->integer('avg_sent_e_contract');
            $table->integer('total_rec_e_contract');
            $table->integer('avg_rec_e_contract');
            $table->integer('total_done_non_ex');
            $table->integer('avg_done_non_ex');
            $table->integer('total_royalty');
            $table->integer('avg_royalty');
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