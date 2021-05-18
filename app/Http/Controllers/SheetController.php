<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SheetController extends Controller
{
    private static $limit = 1000;
    private static $time_reset_cache = 60 * 60 * 24;
    private static $spreadsheetIdLv2 = "16xGw6KdeUzxASEnsXuIKIPawD5Dx6lSi52NohPp5u5s";
    public function index(){
        return view('sheet');
    }
    public static function getApiSpreadsheet($spreadsheetId, $get_range){
        $client = new \Google_Client();
        $client->setApplicationName('Google Sheets and PHP');
        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');
        $client->setAuthConfig(public_path('credentials/credentials.json'));
        $service = new \Google_Service_Sheets($client);

        //Request to get data from spreadsheet.
        $response = $service->spreadsheets_values->get($spreadsheetId, $get_range);
        $values = $response->getValues();
        return $values;
    }
    public function getPerson(){
        $cached['person']['global'] = self::personGlobal();
        $cached['person']['indo'] = self::personIndo();
        return $cached;
    }

    public static function personGlobal()
    {
        $get_rangeGlobal = "Editors Data to Sunny!A2:G10";
        $sId = self::$spreadsheetIdLv2;
        Cache::forget(date('Y-m-d')."PersonGlobal");
        return Cache::remember(date('Y-m-d')."PersonGlobal", self::$time_reset_cache, function () use($sId, $get_rangeGlobal) {
            return self::getApiSpreadsheet($sId, $get_rangeGlobal);
        });
    }

    public static function personIndo()
    {
        $get_rangeIndo = "Editors Data to Sunny!A14:G16";
        $sId = self::$spreadsheetIdLv2;
        Cache::forget(date('Y-m-d')."PersonIndo");
        return Cache::remember(date('Y-m-d')."PersonIndo", self::$time_reset_cache, function () use($sId, $get_rangeIndo) {
            return self::getApiSpreadsheet($sId, $get_rangeIndo);
        });
    }

    public function DailyReport(){
        $spreadsheetId = "1zaxZhyxO0CCJUE-vYV5olkgHPKx43AIY0NunYgIDKno";
        $get_range = "Daily Report!A2:X".self::$limit;
        $data = self::getApiSpreadsheet($spreadsheetId, $get_range);
        return $data;
    }
    public function ReportsAllTeam(){

    }
}
