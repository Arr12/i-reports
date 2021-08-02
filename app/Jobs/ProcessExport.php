<?php

namespace App\Jobs;

use App\Http\Controllers\ResponseFormatter;
use App\Http\Controllers\SheetController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class ProcessExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $data;
    public function __construct($request)
    {
        $this->data = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $z = explode("?",$this->data);
        $this->data = $z[0];
        if(isset($this->data)){
            switch($this->data){
                case "team-monitoring-global" :
                    Artisan::call('set:team-monitoring-global');
                    return ResponseFormatter::success(null, "Success", 200);
                case "team-monitoring-indo" :
                    Artisan::call('set:team-monitoring-indo');
                    return ResponseFormatter::success(null, "Success", 200);
                case "all-team-report-weekly" :
                    Artisan::call('set:all-team-report-weekly');
                    return ResponseFormatter::success(null, "Success", 200);
                case "all-team-report-weekly-periode" :
                    $q = new SheetController;
                    $type = $z[1];
                    $date = $z[2];
                    $q->setAllTeamReportWeekly($type, $date);
                    return ResponseFormatter::success(null, "Success", 200);
                case "all-team-report-monthly" :
                    Artisan::call('set:all-team-report-monthly');
                    return ResponseFormatter::success(null, "Success", 200);
                case "all-team-report-monthly-periode" :
                    $q = new SheetController;
                    $type = $z[1];
                    $date = $z[2];
                    $q->setAllTeamReportMonthly($type, $date);
                    return ResponseFormatter::success(null, "Success", 200);
                default :
                    return ResponseFormatter::error(null, "Data Not Found", 404);
            }
        }else{
            return ResponseFormatter::error(null, "Data Not Found", 404);
        }
    }
}
