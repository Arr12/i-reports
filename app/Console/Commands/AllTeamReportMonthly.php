<?php

namespace App\Console\Commands;

use App\Http\Controllers\SheetController;
use Illuminate\Console\Command;

class AllTeamReportMonthly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:all-team-report-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $result = new SheetController();
        $type = 'month';
        $date = 'month';
        $result->setAllTeamReportMonthly($type, $date);

        $this->info('Insert to first line of Lv 2 All Team Report Monthly successfully');
    }
}
