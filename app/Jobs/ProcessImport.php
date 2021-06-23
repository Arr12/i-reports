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

class ProcessImport implements ShouldQueue
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
        if(isset($this->data)){
            switch($this->data){
                case "ame" :
                    Artisan::call('daily:ame');
                    return ResponseFormatter::success(null, "Success", 200);
                case "anna" :
                    Artisan::call('daily:anna');
                    return ResponseFormatter::success(null, "Success", 200);
                case "carol" :
                    Artisan::call('daily:carol');
                    return ResponseFormatter::success(null, "Success", 200);
                case "eric" :
                    Artisan::call('daily:eric');
                    return ResponseFormatter::success(null, "Success", 200);
                case "icha" :
                    Artisan::call('daily:icha');
                    return ResponseFormatter::success(null, "Success", 200);
                case "lily" :
                    Artisan::call('daily:lily');
                    return ResponseFormatter::success(null, "Success", 200);
                case "maydewi" :
                    Artisan::call('daily:maydewi');
                    return ResponseFormatter::success(null, "Success", 200);
                case "rani" :
                    Artisan::call('daily:rani');
                    return ResponseFormatter::success(null, "Success", 200);
                case "indo-ichanur" :
                    Artisan::call('daily:icha-nur');
                    return ResponseFormatter::success(null, "Success", 200);
                case "indo-irel" :
                    Artisan::call('daily:irel');
                    return ResponseFormatter::success(null, "Success", 200);
                case "mangatoon" :
                    Artisan::call('daily:mangatoon');
                    return ResponseFormatter::success(null, "Success", 200);
                case "wn-uncontracted" :
                    Artisan::call('daily:wn-uncontracted');
                    return ResponseFormatter::success(null, "Success", 200);
                case "novel-list-ranking" :
                    Artisan::call('daily:novel-list-ranking');
                    return ResponseFormatter::success(null, "Success", 200);
                case "non-exclusive" :
                    Artisan::call('daily:non-exclusive');
                    return ResponseFormatter::success(null, "Success", 200);
                default :
                    return ResponseFormatter::error(null, "Data Not Found", 404);
            }
        } else {
            return ResponseFormatter::error(null, "Data Not Found", 404);
        }
    }
}
