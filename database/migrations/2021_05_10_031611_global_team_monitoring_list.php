<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GlobalTeamMonitoringList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_team_monitoring_list', function(Blueprint $table){
            $table->id();
            $table->integer('details_id');
            $table->integer('answer_n_auth')->nullable();
            $table->integer('answer_n_auth_non_ex')->nullable();
            $table->integer('follow_up')->nullable();
            $table->integer('follow_up_non_ex')->nullable();
            $table->integer('help')->nullable();
            $table->integer('solved_problem')->nullable();
            $table->integer('sent_e_contract')->nullable();
            $table->integer('rec_e_contract')->nullable();
            $table->integer('done_non_ex')->nullable();
            $table->integer('royalty')->nullable();
            $table->integer('conversion_royalty')->nullable();
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

    }
}