<?php

namespace App\Http\Controllers;

use App\Models\DailyReportAme;
use App\Models\DailyReportAnna;
use App\Models\DailyReportCarol;
use App\Models\DailyReportEric;
use App\Models\DailyReportIcha;
use App\Models\DailyReportIndoIchaNur;
use App\Models\DailyReportIndoIrel;
use App\Models\DailyReportLily;
use App\Models\DailyReportMaydewi;
use App\Models\DailyReportRani;
use App\Models\NonExclusiveReport;
use Google_Service_Sheets_BatchUpdateSpreadsheetRequest;
use Google_Service_Sheets_Request;
use Google_Service_Sheets_Spreadsheet;
use Google_Service_Sheets_UpdateSheetPropertiesRequest;
use Google_Service_Sheets_ValueRange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SheetController extends Controller
{
    private static $limit = 1000;
    private static $time_reset_cache = false;
    public function FormatDateTime($str){
        if($str){
            $format = substr($str,6,4)."-".substr($str,3,2)."-".substr($str,0,2);
            $date = $format ? date('Y-m-d', strtotime($format)) : null;
        }else{
            $date = null;
        }
        return $date;
    }
    public static function ApiSpreadsheet(){
        $client = new \Google_Client();
        $client->setApplicationName('Google Sheets and PHP');
        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');
        $client->setAuthConfig(public_path('credentials/credentials.json'));
        $service = new \Google_Service_Sheets($client);
        return $service;
    }
    /*--------------------------
    | CREATE NEW SPREADSHEET
    ----------------------------*/
    public function CreateNewSpreadsheet($title){
        $service = self::ApiSpreadsheet();
        $spreadsheet = new Google_Service_Sheets_Spreadsheet([
            'properties' => [
                'title' => $title
            ]
        ]);
        $spreadsheet = $service->spreadsheets->create($spreadsheet, [
            'fields' => 'spreadsheetId'
        ]);
        return $spreadsheet->spreadsheetId;
    }
    public function CreateNewWorksheet($spreadsheetId,$title){
        $service = self::ApiSpreadsheet();
        $body = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
            'requests' => [
                'addSheet' => [
                    'properties' => [
                        'title' => $title
                    ]
                ]
            ]
        ]);

        $result = $service->spreadsheets->batchUpdate($spreadsheetId,$body);
        return true;
    }
    public function UpdateSheetProperties($spreadsheetId, $update_sheet){
        $service = self::ApiSpreadsheet();

        // Get our spreadsheet
        $spreadsheets = $service->spreadsheets->get($spreadsheetId);

        // We get the current properties of the previously created sheet, indicating its identifier - 1
        $SheetProperties = $spreadsheets->getSheets()[0]->getProperties();

        // Set new name
        $SheetProperties->setTitle($update_sheet);

        // Object - request to update sheet properties
        $UpdateSheetRequests = new Google_Service_Sheets_UpdateSheetPropertiesRequest();
        $UpdateSheetRequests->setProperties($SheetProperties);

        // We indicate which property we want to update
        $UpdateSheetRequests->setFields('title');

        // Object - sheet request
        $SheetRequests = new Google_Service_Sheets_Request();
        $SheetRequests->setUpdateSheetProperties($UpdateSheetRequests);

        $requests = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest();
        $requests->setRequests($SheetRequests);

        // Execute the request
        $response = $service->spreadsheets->BatchUpdate($spreadsheetId, $requests);
        return true;
    }

    /*----------------------------
    | GET DATA SPREADSHEET
    -------------------------------*/
    public static function getApiSpreadsheet($spreadsheetId, $get_range){
        $service = self::ApiSpreadsheet();
        //Request to get data from spreadsheet.
        $response = $service->spreadsheets_values->get($spreadsheetId, $get_range);
        $values = $response->getValues();
        return $values;
    }
    public function GetDailyReport($request = false){
        /*----------
        / GLOBAL
        --------------*/
        $daily = $request ?: request()->input('d');
        if($daily == 'ame'){
            $this->getDailyReportAme();
        }
        else if($daily == 'anna'){
            $this->getDailyReportAnna();
        }
        else if($daily == 'carol'){
            $this->getDailyReportCarol();
        }
        else if($daily == 'eric'){
            $this->getDailyReportEric();
        }
        else if($daily == 'icha'){
            $this->getDailyReportIcha();
        }
        else if($daily == 'lily'){
            $this->getDailyReportLily();
        }
        else if($daily == 'maydewi'){
            $this->getDailyReportMayDewi();
        }
        else if($daily == 'rani'){
            $this->getDailyReportRani();
        }
        /*-----------
        / INDO
        --------------*/
        else if($daily == 'indo-ichanur'){
            $this->getDailyReportIndoIchaNur();
        }
        else if($daily == 'indo-irel'){
            $this->getDailyReportIndoIrel();
        }
        /*-----------
        / EXCLUSIVE
        --------------*/
        else if($daily == 'non-exclusive'){
            $this->getNonExReport();
        }
        else if($daily == 'all'){
            $this->getDailyReportAme();
            $this->getDailyReportAnna();
            $this->getDailyReportCarol();
            $this->getDailyReportEric();
            $this->getDailyReportIcha();
            $this->getDailyReportLily();
            $this->getDailyReportMayDewi();
            $this->getDailyReportRani();
            $this->getDailyReportIndoIchaNur();
            $this->getDailyReportIndoIrel();
            $this->getNonExReport();
        }
        else{
            $p = 400;
        }
        return isset($p) ? $p : 200;
    }
    public function getDailyReportAme(){
        $sId = "1Aoc2wVeZoP1sc7eSJybIHzxY2IPVDWhLMlLQzUwUVAo";
        $keyMaster = date('Y-m-d')."_daily_report_ame";
        $sheets = "Daily Report";
        $alphaX = "A";
        $alphaY = "X";
        $this->getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
        DailyReportAme::truncate();
        foreach($cached as $key => $keyDaily){
            $cachedDaily = Cache::get($keyDaily, []);
            $savedData = [];
            foreach ($cachedDaily as $key => $data) {
                // dump($data);
                if(!$data[0]){continue;}
                $date = $this->FormatDateTime($data[0]);
                $status = isset($data[1]) ? $data[1] : null;
                $media = isset($data[2]) ? $data[2] : null;
                $author = isset($data[3]) ? $data[3] : null;
                $inquiries = isset($data[4]) ? $data[4] : null;
                $platform = isset($data[5]) ? $data[5] : null;
                $platform_user = isset($data[6]) ? $data[6] : null;
                $platform_title = isset($data[7]) ? $data[7] : null;
                $username = isset($data[8]) ? $data[8] : null;
                $cbid = isset($data[9]) ? $data[9] : null;
                $title = isset($data[10]) ? $data[10] : null;
                $genre = isset($data[11]) ? $data[11] : null;
                $plot = isset($data[12]) ? $data[12] : null;
                $k4 = isset($data[13]) ? $data[13] : null;
                $maintain = isset($data[14]) ? $data[14] : null;
                $fu_1 = isset($data[15]) ? $this->FormatDateTime($data[15]) : null;
                $fu_2 = isset($data[16]) ? $this->FormatDateTime($data[16]) : null;
                $fu_3 = isset($data[17]) ? $this->FormatDateTime($data[17]) : null;
                $fu_4 = isset($data[18]) ? $this->FormatDateTime($data[18]) : null;
                $fu_5 = isset($data[19]) ? $this->FormatDateTime($data[19]) : null;
                $sent_royalty = isset($data[20]) ? $this->FormatDateTime($data[20]) : null;
                $sent_non_exclusive = isset($data[21]) ? $this->FormatDateTime($data[21]) : null;
                $marker = isset($data[22]) ? $data[22] : null;
                $old_new_book = isset($data[23]) ? $data[23] : null;
                array_push($savedData, [
                    'date' => $date,
                    'status' => $status,
                    'media' => $media,
                    'author_contact' => $author,
                    'inquiries' => $inquiries,
                    'platform' => $platform,
                    'platform_user' => $platform_user,
                    'platform_title' => $platform_title,
                    'username' => $username,
                    'cbid' => $cbid,
                    'title' => $title,
                    'genre' => $genre,
                    'plot' => $plot,
                    'k4' => $k4,
                    'maintain_account' => $maintain,
                    'fu_1' => $fu_1,
                    'fu_2' => $fu_2,
                    'fu_3' => $fu_3,
                    'fu_4' => $fu_4,
                    'fu_5' => $fu_5,
                    'sent_royalty' => $sent_royalty,
                    'sent_non_exclusive' => $sent_non_exclusive,
                    'marker' => $marker,
                    'old_new_book' => $old_new_book
                ]);
            }

            DailyReportAme::insert($savedData);
        }
        return true;
    }
    public function getDailyReportAnna(){
        $sId = "1tCKH2zgvAv313WaJeEnxYKdt5BHOo7jGg3QI6WM2qhM";
        $keyMaster = date('Y-m-d')."_daily_report_anna";
        $sheets = "Daily Report";
        $alphaX = "A";
        $alphaY = "X";
        $this->getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
        DailyReportAnna::truncate();
        foreach($cached as $key => $keyDaily){
            $cachedDaily = Cache::get($keyDaily, []);
            $savedData = [];
            foreach ($cachedDaily as $key => $data) {
                // dump($data);
                if(!$data[0]){continue;}
                $date = $this->FormatDateTime($data[0]);
                $status = isset($data[1]) ? $data[1] : null;
                $media = isset($data[2]) ? $data[2] : null;
                $author = isset($data[3]) ? $data[3] : null;
                $inquiries = isset($data[4]) ? $data[4] : null;
                $platform = isset($data[5]) ? $data[5] : null;
                $platform_user = isset($data[6]) ? $data[6] : null;
                $platform_title = isset($data[7]) ? $data[7] : null;
                $username = isset($data[8]) ? $data[8] : null;
                $cbid = isset($data[9]) ? $data[9] : null;
                $title = isset($data[10]) ? $data[10] : null;
                $genre = isset($data[11]) ? $data[11] : null;
                $plot = isset($data[12]) ? $data[12] : null;
                $k4 = isset($data[13]) ? $data[13] : null;
                $maintain = isset($data[14]) ? $data[14] : null;
                $fu_1 = isset($data[15]) ? $this->FormatDateTime($data[15]) : null;
                $fu_2 = isset($data[16]) ? $this->FormatDateTime($data[16]) : null;
                $fu_3 = isset($data[17]) ? $this->FormatDateTime($data[17]) : null;
                $fu_4 = isset($data[18]) ? $this->FormatDateTime($data[18]) : null;
                $fu_5 = isset($data[19]) ? $this->FormatDateTime($data[19]) : null;
                $sent_royalty = isset($data[20]) ? $this->FormatDateTime($data[20]) : null;
                $sent_non_exclusive = isset($data[21]) ? $this->FormatDateTime($data[21]) : null;
                $marker = isset($data[22]) ? $data[22] : null;
                $old_new_book = isset($data[23]) ? $data[23] : null;
                array_push($savedData, [
                    'date' => $date,
                    'status' => $status,
                    'media' => $media,
                    'author_contact' => $author,
                    'inquiries' => $inquiries,
                    'platform' => $platform,
                    'platform_user' => $platform_user,
                    'platform_title' => $platform_title,
                    'username' => $username,
                    'cbid' => $cbid,
                    'title' => $title,
                    'genre' => $genre,
                    'plot' => $plot,
                    'k4' => $k4,
                    'maintain_account' => $maintain,
                    'fu_1' => $fu_1,
                    'fu_2' => $fu_2,
                    'fu_3' => $fu_3,
                    'fu_4' => $fu_4,
                    'fu_5' => $fu_5,
                    'sent_royalty' => $sent_royalty,
                    'sent_non_exclusive' => $sent_non_exclusive,
                    'marker' => $marker,
                    'old_new_book' => $old_new_book
                ]);
            }

            DailyReportAnna::insert($savedData);
        }
        return true;
    }
    public function getDailyReportCarol(){
        $sId = "1zaxZhyxO0CCJUE-vYV5olkgHPKx43AIY0NunYgIDKno";
        $keyMaster = date('Y-m-d')."_daily_report_carol";
        $sheets = "Daily Report";
        $alphaX = "A";
        $alphaY = "X";
        $this->getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
        DailyReportCarol::truncate();
        foreach($cached as $key => $keyDaily){
            $cachedDaily = Cache::get($keyDaily, []);
            $savedData = [];
            foreach ($cachedDaily as $key => $data) {
                // dump($data);
                if(!$data[0]){continue;}
                $date = $this->FormatDateTime($data[0]);
                $status = isset($data[1]) ? $data[1] : null;
                $media = isset($data[2]) ? $data[2] : null;
                $author = isset($data[3]) ? $data[3] : null;
                $inquiries = isset($data[4]) ? $data[4] : null;
                $platform = isset($data[5]) ? $data[5] : null;
                $platform_user = isset($data[6]) ? $data[6] : null;
                $platform_title = isset($data[7]) ? $data[7] : null;
                $username = isset($data[8]) ? $data[8] : null;
                $cbid = isset($data[9]) ? $data[9] : null;
                $title = isset($data[10]) ? $data[10] : null;
                $genre = isset($data[11]) ? $data[11] : null;
                $plot = isset($data[12]) ? $data[12] : null;
                $k4 = isset($data[13]) ? $data[13] : null;
                $maintain = isset($data[14]) ? $data[14] : null;
                $fu_1 = isset($data[15]) ? $this->FormatDateTime($data[15]) : null;
                $fu_2 = isset($data[16]) ? $this->FormatDateTime($data[16]) : null;
                $fu_3 = isset($data[17]) ? $this->FormatDateTime($data[17]) : null;
                $fu_4 = isset($data[18]) ? $this->FormatDateTime($data[18]) : null;
                $fu_5 = isset($data[19]) ? $this->FormatDateTime($data[19]) : null;
                $sent_royalty = isset($data[20]) ? $this->FormatDateTime($data[20]) : null;
                $sent_non_exclusive = isset($data[21]) ? $this->FormatDateTime($data[21]) : null;
                $marker = isset($data[22]) ? $data[22] : null;
                $old_new_book = isset($data[23]) ? $data[23] : null;
                array_push($savedData, [
                    'date' => $date,
                    'status' => $status,
                    'media' => $media,
                    'author_contact' => $author,
                    'inquiries' => $inquiries,
                    'platform' => $platform,
                    'platform_user' => $platform_user,
                    'platform_title' => $platform_title,
                    'username' => $username,
                    'cbid' => $cbid,
                    'title' => $title,
                    'genre' => $genre,
                    'plot' => $plot,
                    'k4' => $k4,
                    'maintain_account' => $maintain,
                    'fu_1' => $fu_1,
                    'fu_2' => $fu_2,
                    'fu_3' => $fu_3,
                    'fu_4' => $fu_4,
                    'fu_5' => $fu_5,
                    'sent_royalty' => $sent_royalty,
                    'sent_non_exclusive' => $sent_non_exclusive,
                    'marker' => $marker,
                    'old_new_book' => $old_new_book
                ]);
            }
            DailyReportCarol::insert($savedData);
        }
        return true;
    }
    public function getDailyReportEric(){
        $sId = "1Fu3-AE0Wr9RlCxr2qcpaU-x6V5BpMSAbb71sbhPscTM";
        $keyMaster = date('Y-m-d')."_daily_report_eric";
        $sheets = "Daily Report";
        $alphaX = "A";
        $alphaY = "X";
        $this->getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
        DailyReportEric::truncate();
        foreach($cached as $key => $keyDaily){
            $cachedDaily = Cache::get($keyDaily, []);
            $savedData = [];
            foreach ($cachedDaily as $key => $data) {
                // dump($data);
                if(!$data[0]){continue;}
                $date = $this->FormatDateTime($data[0]);
                $status = isset($data[1]) ? $data[1] : null;
                $media = isset($data[2]) ? $data[2] : null;
                $author = isset($data[3]) ? $data[3] : null;
                $inquiries = isset($data[4]) ? $data[4] : null;
                $platform = isset($data[5]) ? $data[5] : null;
                $platform_user = isset($data[6]) ? $data[6] : null;
                $platform_title = isset($data[7]) ? $data[7] : null;
                $username = isset($data[8]) ? $data[8] : null;
                $cbid = isset($data[9]) ? $data[9] : null;
                $title = isset($data[10]) ? $data[10] : null;
                $genre = isset($data[11]) ? $data[11] : null;
                $plot = isset($data[12]) ? $data[12] : null;
                $k4 = isset($data[13]) ? $data[13] : null;
                $maintain = isset($data[14]) ? $data[14] : null;
                $fu_1 = isset($data[15]) ? $this->FormatDateTime($data[15]) : null;
                $fu_2 = isset($data[16]) ? $this->FormatDateTime($data[16]) : null;
                $fu_3 = isset($data[17]) ? $this->FormatDateTime($data[17]) : null;
                $fu_4 = isset($data[18]) ? $this->FormatDateTime($data[18]) : null;
                $fu_5 = isset($data[19]) ? $this->FormatDateTime($data[19]) : null;
                $sent_royalty = isset($data[20]) ? $this->FormatDateTime($data[20]) : null;
                $sent_non_exclusive = isset($data[21]) ? $this->FormatDateTime($data[21]) : null;
                $marker = isset($data[22]) ? $data[22] : null;
                $old_new_book = isset($data[23]) ? $data[23] : null;
                array_push($savedData, [
                    'date' => $date,
                    'status' => $status,
                    'media' => $media,
                    'author_contact' => $author,
                    'inquiries' => $inquiries,
                    'platform' => $platform,
                    'platform_user' => $platform_user,
                    'platform_title' => $platform_title,
                    'username' => $username,
                    'cbid' => $cbid,
                    'title' => $title,
                    'genre' => $genre,
                    'plot' => $plot,
                    'k4' => $k4,
                    'maintain_account' => $maintain,
                    'fu_1' => $fu_1,
                    'fu_2' => $fu_2,
                    'fu_3' => $fu_3,
                    'fu_4' => $fu_4,
                    'fu_5' => $fu_5,
                    'sent_royalty' => $sent_royalty,
                    'sent_non_exclusive' => $sent_non_exclusive,
                    'marker' => $marker,
                    'old_new_book' => $old_new_book
                ]);
            }
            DailyReportEric::insert($savedData);
        }
        return true;
    }
    public function getDailyReportIcha(){
        $sId = "12B_FXZkuDush0Nmv1lY1iBxE1GCOk2TplP4gdkX9OvE";
        $keyMaster = date('Y-m-d')."_daily_report_icha";
        $sheets = "Daily Report";
        $alphaX = "A";
        $alphaY = "X";
        $this->getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
        DailyReportIcha::truncate();
        foreach($cached as $key => $keyDaily){
            $cachedDaily = Cache::get($keyDaily, []);
            $savedData = [];
            $now = date('Y-m-d H:i:s');
            foreach ($cachedDaily as $key => $data) {
                // dump($data);
                if(!$data[0]){continue;}
                $date = $this->FormatDateTime($data[0]);
                $status = isset($data[1]) ? $data[1] : null;
                $media = isset($data[2]) ? $data[2] : null;
                $author = isset($data[3]) ? $data[3] : null;
                $inquiries = isset($data[4]) ? $data[4] : null;
                $platform = isset($data[5]) ? $data[5] : null;
                $platform_user = isset($data[6]) ? $data[6] : null;
                $platform_title = isset($data[7]) ? $data[7] : null;
                $username = isset($data[8]) ? $data[8] : null;
                $cbid = isset($data[9]) ? $data[9] : null;
                $title = isset($data[10]) ? $data[10] : null;
                $genre = isset($data[11]) ? $data[11] : null;
                $plot = isset($data[12]) ? $data[12] : null;
                $k4 = isset($data[13]) ? $data[13] : null;
                $maintain = isset($data[14]) ? $data[14] : null;
                $fu_1 = isset($data[15]) ? $this->FormatDateTime($data[15]) : null;
                $fu_2 = isset($data[16]) ? $this->FormatDateTime($data[16]) : null;
                $fu_3 = isset($data[17]) ? $this->FormatDateTime($data[17]) : null;
                $fu_4 = isset($data[18]) ? $this->FormatDateTime($data[18]) : null;
                $fu_5 = isset($data[19]) ? $this->FormatDateTime($data[19]) : null;
                $sent_royalty = isset($data[20]) ? $this->FormatDateTime($data[20]) : null;
                $sent_non_exclusive = isset($data[21]) ? $this->FormatDateTime($data[21]) : null;
                $marker = isset($data[22]) ? $data[22] : null;
                $old_new_book = isset($data[23]) ? $data[23] : null;
                array_push($savedData, [
                    'date' => $date,
                    'status' => $status,
                    'media' => $media,
                    'author_contact' => $author,
                    'inquiries' => $inquiries,
                    'platform' => $platform,
                    'platform_user' => $platform_user,
                    'platform_title' => $platform_title,
                    'username' => $username,
                    'cbid' => $cbid,
                    'title' => $title,
                    'genre' => $genre,
                    'plot' => $plot,
                    'k4' => $k4,
                    'maintain_account' => $maintain,
                    'fu_1' => $fu_1,
                    'fu_2' => $fu_2,
                    'fu_3' => $fu_3,
                    'fu_4' => $fu_4,
                    'fu_5' => $fu_5,
                    'sent_royalty' => $sent_royalty,
                    'sent_non_exclusive' => $sent_non_exclusive,
                    'marker' => $marker,
                    'old_new_book' => $old_new_book,
                    'created_at' => $now
                ]);
            }
            DailyReportIcha::insert($savedData);
        }
        return true;
    }
    public function getDailyReportLily(){
        $sId = "1hgkVhoRsILCJQeO4i5_EMXvlNbsfmB2_9k8Fg-Cx8eg";
        $keyMaster = date('Y-m-d')."_daily_report_lily";
        $sheets = "Daily Report";
        $alphaX = "A";
        $alphaY = "X";
        $this->getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
        DailyReportLily::truncate();
        foreach($cached as $key => $keyDaily){
            $cachedDaily = Cache::get($keyDaily, []);
            $savedData = [];
            foreach ($cachedDaily as $key => $data) {
                // dump($data);
                if(!$data[0]){continue;}
                $date = $this->FormatDateTime($data[0]);
                $status = isset($data[1]) ? $data[1] : null;
                $media = isset($data[2]) ? $data[2] : null;
                $author = isset($data[3]) ? $data[3] : null;
                $inquiries = isset($data[4]) ? $data[4] : null;
                $platform = isset($data[5]) ? $data[5] : null;
                $platform_user = isset($data[6]) ? $data[6] : null;
                $platform_title = isset($data[7]) ? $data[7] : null;
                $username = isset($data[8]) ? $data[8] : null;
                $cbid = isset($data[9]) ? $data[9] : null;
                $title = isset($data[10]) ? $data[10] : null;
                $genre = isset($data[11]) ? $data[11] : null;
                $plot = isset($data[12]) ? $data[12] : null;
                $k4 = isset($data[13]) ? $data[13] : null;
                $maintain = isset($data[14]) ? $data[14] : null;
                $fu_1 = isset($data[15]) ? $this->FormatDateTime($data[15]) : null;
                $fu_2 = isset($data[16]) ? $this->FormatDateTime($data[16]) : null;
                $fu_3 = isset($data[17]) ? $this->FormatDateTime($data[17]) : null;
                $fu_4 = isset($data[18]) ? $this->FormatDateTime($data[18]) : null;
                $fu_5 = isset($data[19]) ? $this->FormatDateTime($data[19]) : null;
                $sent_royalty = isset($data[20]) ? $this->FormatDateTime($data[20]) : null;
                $sent_non_exclusive = isset($data[21]) ? $this->FormatDateTime($data[21]) : null;
                $marker = isset($data[22]) ? $data[22] : null;
                $old_new_book = isset($data[23]) ? $data[23] : null;
                array_push($savedData, [
                    'date' => $date,
                    'status' => $status,
                    'media' => $media,
                    'author_contact' => $author,
                    'inquiries' => $inquiries,
                    'platform' => $platform,
                    'platform_user' => $platform_user,
                    'platform_title' => $platform_title,
                    'username' => $username,
                    'cbid' => $cbid,
                    'title' => $title,
                    'genre' => $genre,
                    'plot' => $plot,
                    'k4' => $k4,
                    'maintain_account' => $maintain,
                    'fu_1' => $fu_1,
                    'fu_2' => $fu_2,
                    'fu_3' => $fu_3,
                    'fu_4' => $fu_4,
                    'fu_5' => $fu_5,
                    'sent_royalty' => $sent_royalty,
                    'sent_non_exclusive' => $sent_non_exclusive,
                    'marker' => $marker,
                    'old_new_book' => $old_new_book
                ]);
            }
            DailyReportLily::insert($savedData);
        }
        return true;
    }
    public function getDailyReportMayDewi(){
        $sId = "1zFrtw-fGgSBIImwk7t2LRPnxKMq3_hsxhgI3mLa0ksw";
        $keyMaster = date('Y-m-d')."_daily_report_maydewi";
        $sheets = "Daily Report";
        $alphaX = "A";
        $alphaY = "X";
        $this->getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
        DailyReportMayDewi::truncate();
        foreach($cached as $key => $keyDaily){
            $cachedDaily = Cache::get($keyDaily, []);
            $savedData = [];
            foreach ($cachedDaily as $key => $data) {
                // dump($data);
                if(!$data[0]){continue;}
                $date = $this->FormatDateTime($data[0]);
                $status = isset($data[1]) ? $data[1] : null;
                $media = isset($data[2]) ? $data[2] : null;
                $author = isset($data[3]) ? $data[3] : null;
                $inquiries = isset($data[4]) ? $data[4] : null;
                $platform = isset($data[5]) ? $data[5] : null;
                $platform_user = isset($data[6]) ? $data[6] : null;
                $platform_title = isset($data[7]) ? $data[7] : null;
                $username = isset($data[8]) ? $data[8] : null;
                $cbid = isset($data[9]) ? $data[9] : null;
                $title = isset($data[10]) ? $data[10] : null;
                $genre = isset($data[11]) ? $data[11] : null;
                $plot = isset($data[12]) ? $data[12] : null;
                $k4 = isset($data[13]) ? $data[13] : null;
                $maintain = isset($data[14]) ? $data[14] : null;
                $fu_1 = isset($data[15]) ? $this->FormatDateTime($data[15]) : null;
                $fu_2 = isset($data[16]) ? $this->FormatDateTime($data[16]) : null;
                $fu_3 = isset($data[17]) ? $this->FormatDateTime($data[17]) : null;
                $fu_4 = isset($data[18]) ? $this->FormatDateTime($data[18]) : null;
                $fu_5 = isset($data[19]) ? $this->FormatDateTime($data[19]) : null;
                $sent_royalty = isset($data[20]) ? $this->FormatDateTime($data[20]) : null;
                $sent_non_exclusive = isset($data[21]) ? $this->FormatDateTime($data[21]) : null;
                $marker = isset($data[22]) ? $data[22] : null;
                $old_new_book = isset($data[23]) ? $data[23] : null;
                array_push($savedData, [
                    'date' => $date,
                    'status' => $status,
                    'media' => $media,
                    'author_contact' => $author,
                    'inquiries' => $inquiries,
                    'platform' => $platform,
                    'platform_user' => $platform_user,
                    'platform_title' => $platform_title,
                    'username' => $username,
                    'cbid' => $cbid,
                    'title' => $title,
                    'genre' => $genre,
                    'plot' => $plot,
                    'k4' => $k4,
                    'maintain_account' => $maintain,
                    'fu_1' => $fu_1,
                    'fu_2' => $fu_2,
                    'fu_3' => $fu_3,
                    'fu_4' => $fu_4,
                    'fu_5' => $fu_5,
                    'sent_royalty' => $sent_royalty,
                    'sent_non_exclusive' => $sent_non_exclusive,
                    'marker' => $marker,
                    'old_new_book' => $old_new_book
                ]);
            }
            DailyReportMaydewi::insert($savedData);
        }
        return true;
    }
    public function getDailyReportRani(){
        $sId = "12pPkC3NIAdc8JQvaz5su4uVxCjURI1YzviwStk6m5QY";
        $keyMaster = date('Y-m-d')."_daily_report_Rani";
        $sheets = "Daily Report";
        $alphaX = "A";
        $alphaY = "X";
        $this->getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
        DailyReportRani::truncate();
        foreach($cached as $key => $keyDaily){
            $cachedDaily = Cache::get($keyDaily, []);
            $savedData = [];
            foreach ($cachedDaily as $key => $data) {
                // dump($data);
                if(!$data[0]){continue;}
                $date = $this->FormatDateTime($data[0]);
                $status = isset($data[1]) ? $data[1] : null;
                $media = isset($data[2]) ? $data[2] : null;
                $author = isset($data[3]) ? $data[3] : null;
                $inquiries = isset($data[4]) ? $data[4] : null;
                $platform = isset($data[5]) ? $data[5] : null;
                $platform_user = isset($data[6]) ? $data[6] : null;
                $platform_title = isset($data[7]) ? $data[7] : null;
                $username = isset($data[8]) ? $data[8] : null;
                $cbid = isset($data[9]) ? $data[9] : null;
                $title = isset($data[10]) ? $data[10] : null;
                $genre = isset($data[11]) ? $data[11] : null;
                $plot = isset($data[12]) ? $data[12] : null;
                $k4 = isset($data[13]) ? $data[13] : null;
                $maintain = isset($data[14]) ? $data[14] : null;
                $fu_1 = isset($data[15]) ? $this->FormatDateTime($data[15]) : null;
                $fu_2 = isset($data[16]) ? $this->FormatDateTime($data[16]) : null;
                $fu_3 = isset($data[17]) ? $this->FormatDateTime($data[17]) : null;
                $fu_4 = isset($data[18]) ? $this->FormatDateTime($data[18]) : null;
                $fu_5 = isset($data[19]) ? $this->FormatDateTime($data[19]) : null;
                $sent_royalty = isset($data[20]) ? $this->FormatDateTime($data[20]) : null;
                $sent_non_exclusive = isset($data[21]) ? $this->FormatDateTime($data[21]) : null;
                $marker = isset($data[22]) ? $data[22] : null;
                $old_new_book = isset($data[23]) ? $data[23] : null;
                array_push($savedData, [
                    'date' => $date,
                    'status' => $status,
                    'media' => $media,
                    'author_contact' => $author,
                    'inquiries' => $inquiries,
                    'platform' => $platform,
                    'platform_user' => $platform_user,
                    'platform_title' => $platform_title,
                    'username' => $username,
                    'cbid' => $cbid,
                    'title' => $title,
                    'genre' => $genre,
                    'plot' => $plot,
                    'k4' => $k4,
                    'maintain_account' => $maintain,
                    'fu_1' => $fu_1,
                    'fu_2' => $fu_2,
                    'fu_3' => $fu_3,
                    'fu_4' => $fu_4,
                    'fu_5' => $fu_5,
                    'sent_royalty' => $sent_royalty,
                    'sent_non_exclusive' => $sent_non_exclusive,
                    'marker' => $marker,
                    'old_new_book' => $old_new_book
                ]);
            }
            DailyReportRani::insert($savedData);
        }
        return true;
    }
    public function getDailyReportIndoIchaNur(){
        $sId = "1TwyTXnBI51TH3tlO8nKhfdB_7o7r76T1fxFXQMZ3OXw";
        $keyMaster = date('Y-m-d')."_daily_report_IndoIchaNur";
        $sheets = "Daily Report";
        $alphaX = "A";
        $alphaY = "V";
        $this->getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
        DailyReportIndoIchaNur::truncate();
        foreach($cached as $key => $keyDaily){
            $cachedDaily = Cache::get($keyDaily, []);
            // dump($cachedDaily);
            $savedData = [];
            foreach ($cachedDaily as $key => $data) {
                // dump($data[0]);
                if(!$data[0]){continue;}
                $date = $this->FormatDateTime($data[0]);
                $contact_way = isset($data[1]) ? $data[1] : null;
                $author = isset($data[2]) ? $data[2] : null;
                $platform = isset($data[3]) ? $data[3] : null;
                $status = isset($data[4]) ? $data[4] : null;
                $inquiries = isset($data[5]) ? $data[5] : null;
                $cbid = isset($data[6]) ? $data[6] : null;
                $old_cbid = isset($data[7]) ? $data[7] : null;
                $author = isset($data[8]) ? $data[8] : null;
                $title = isset($data[9]) ? $data[9] : null;
                $genre = isset($data[10]) ? $data[10] : null;
                $k4 = isset($data[11]) ? $data[11] : null;
                $plot = isset($data[12]) ? $data[12] : null;
                $maintain = isset($data[13]) ? $data[13] : null;
                $fu_1 = isset($data[14]) ? $this->FormatDateTime($data[14]) : null;
                $fu_2 = isset($data[15]) ? $this->FormatDateTime($data[15]) : null;
                $fu_3 = isset($data[16]) ? $this->FormatDateTime($data[16]) : null;
                $fu_4 = isset($data[17]) ? $this->FormatDateTime($data[17]) : null;
                $fu_5 = isset($data[18]) ? $this->FormatDateTime($data[18]) : null;
                $data_sent = isset($data[19]) ? $this->FormatDateTime($data[19]) : null;
                $marker = isset($data[20]) ? $this->FormatDateTime($data[20]) : null;
                $old_new_book = isset($data[21]) ? $this->FormatDateTime($data[21]) : null;
                array_push($savedData, [
                    'date' => $date,
                    'contact_way' => $contact_way,
                    'author_contact' => $author,
                    'platform' => $platform,
                    'status' => $status,
                    'inquiries' => $inquiries,
                    'new_cbid' => $cbid,
                    'old_cbid' => $old_cbid,
                    'author' => $author,
                    'title' => $title,
                    'genre' => $genre,
                    'k4' => $k4,
                    'plot' => $plot,
                    'maintain_account' => $maintain,
                    'fu_1' => $fu_1,
                    'fu_2' => $fu_2,
                    'fu_3' => $fu_3,
                    'fu_4' => $fu_4,
                    'fu_5' => $fu_5,
                    'data_sent' => $data_sent,
                    'marker' => $marker,
                    'old_new_book' => $old_new_book
                ]);
            }
            DailyReportIndoIchaNur::insert($savedData);
        }
        return true;
    }
    public function getDailyReportIndoIrel(){
        $sId = "1UnzYJNDXDuKodPqs2nQW4JUfLdJIxb9nT7XnHcnNcFk";
        $keyMaster = date('Y-m-d')."_daily_report_IndoIrel";
        $sheets = "Daily Report";
        $alphaX = "A";
        $alphaY = "S";
        $this->getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
        DailyReportIndoIrel::truncate();
        foreach($cached as $key => $keyDaily){
            $cachedDaily = Cache::get($keyDaily, []);
            $savedData = [];
            foreach ($cachedDaily as $key => $data) {
                // dump($data);
                if(!$data[0]){continue;}
                $date = $this->FormatDateTime($data[0]);
                $status = isset($data[1]) ? $data[1] : null;
                $date_solved = isset($data[2]) ? $this->FormatDateTime($data[2]) : null;
                $author = isset($data[3]) ? $data[3] : null;
                $inquiries = isset($data[4]) ? $data[4] : null;
                $cbid = isset($data[5]) ? $data[5] : null;
                $title = isset($data[6]) ? $data[6] : null;
                $author = isset($data[7]) ? $data[7] : null;
                $zoom_tutorial = isset($data[8]) ? $data[8] : null;
                $fu_1 = isset($data[9]) ? $this->FormatDateTime($data[9]) : null;
                $fu_2 = isset($data[10]) ? $this->FormatDateTime($data[10]) : null;
                $fu_3 = isset($data[11]) ? $this->FormatDateTime($data[11]) : null;
                $fu_4 = isset($data[12]) ? $this->FormatDateTime($data[12]) : null;
                $fu_5 = isset($data[13]) ? $this->FormatDateTime($data[13]) : null;
                $fu_6 = isset($data[14]) ? $this->FormatDateTime($data[14]) : null;
                $fu_7 = isset($data[15]) ? $this->FormatDateTime($data[15]) : null;
                $fu_8 = isset($data[16]) ? $this->FormatDateTime($data[16]) : null;
                $fu_9 = isset($data[17]) ? $this->FormatDateTime($data[17]) : null;
                $fu_10 = isset($data[18]) ? $this->FormatDateTime($data[18]) : null;
                array_push($savedData, [
                    'date' => $date,
                    'status' => $status,
                    'date_solved' => $date_solved,
                    'author_contact' => $author,
                    'inquiries' => $inquiries,
                    'cbid' => $cbid,
                    'title' => $title,
                    'author' => $author,
                    'zoom_tutorial' => $zoom_tutorial,
                    'fu_1' => $fu_1,
                    'fu_2' => $fu_2,
                    'fu_3' => $fu_3,
                    'fu_4' => $fu_4,
                    'fu_5' => $fu_5,
                    'fu_6' => $fu_6,
                    'fu_7' => $fu_7,
                    'fu_8' => $fu_8,
                    'fu_9' => $fu_9,
                    'fu_10' => $fu_10
                ]);
            }
            DailyReportIndoIrel::insert($savedData);
        }
        return true;
    }
    public function getNonExReport(){
        $sId = "1C_FHAsaNX4lbeQjfMP0i3piXCzIVtHalgFYk5wWJph4";
        $keyMaster = date('Y-m-d')."_daily_report_NonExclusive";
        $sheets = "Daily Report Master";
        $alphaX = "A";
        $alphaY = "AC";
        $this->getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
        NonExclusiveReport::truncate();
        foreach($cached as $key => $keyDaily){
            $cachedDaily = Cache::get($keyDaily, []);
            $savedData = [];
            // dump($cachedDaily);
            foreach ($cachedDaily as $key => $data) {
                if(!$data[0]){continue;}
                $date = isset($data[0]) ? $this->FormatDateTime($data[0]) : null;
                $global_editor = isset($data[1]) ? $data[1] : null;
                $author_contact = isset($data[2]) ? $data[2] : null;
                $platform = isset($data[3]) ? $data[3] : null;
                $username = isset($data[4]) ? $data[4] : null;
                $title = isset($data[5]) ? $data[5] : null;
                $book_status = isset($data[6]) ? $data[6] : null;
                $latest_update = isset($data[7]) ? $data[7] : null;
                $first_touch = isset($data[8]) ? $this->FormatDateTime($data[8]) : null;
                $book_id = isset($data[9]) ? $data[9] : null;
                $sent_e_contract = isset($data[10]) ? $this->FormatDateTime($data[10]) : null;
                $officer = isset($data[11]) ? $data[11] : null;
                $date_sent = isset($data[12]) ? $this->FormatDateTime($data[12]) : null;
                $and_notes = isset($data[13]) ? $data[13] : null;
                $global_editor_notes = isset($data[14]) ? $data[14] : null;
                $solved_date = isset($data[15]) ? $this->FormatDateTime($data[15]) : null;
                $pdf_evidence = isset($data[16]) ? $data[16] : null;
                $rec_e_contract = isset($data[17]) ? $this->FormatDateTime($data[17]) : null;
                $fu_1 = isset($data[18]) ? $this->FormatDateTime($data[18]) : null;
                $fu_2 = isset($data[19]) ? $this->FormatDateTime($data[19]) : null;
                $fu_3 = isset($data[20]) ? $this->FormatDateTime($data[20]) : null;
                $fu_4 = isset($data[21]) ? $this->FormatDateTime($data[21]) : null;
                $fu_5 = isset($data[22]) ? $this->FormatDateTime($data[22]) : null;
                $marker_for_global = isset($data[23]) ? $data[23] : null;
                $marker_for_and = isset($data[24]) ? $data[24] : null;
                $email_sent = isset($data[25]) ? $this->FormatDateTime($data[25]) : null;
                $batch_date = isset($data[26]) ? $this->FormatDateTime($data[26]) : null;
                $and_evidence = isset($data[27]) ? $this->FormatDateTime($data[27]) : null;
                $global_evidence = isset($data[28]) ? $this->FormatDateTime($data[28]) : null;
                array_push($savedData, [
                    'date' => $date,
                    'global_editor' => $global_editor,
                    'author_contact' => $author_contact,
                    'platform' => $platform,
                    'username' => $username,
                    'title' => $title,
                    'book_status' => $book_status,
                    'latest_update' => $latest_update,
                    'first_touch' => $first_touch,
                    'book_id' => $book_id,
                    'sent_e_contract' => $sent_e_contract,
                    'officer' => $officer,
                    'date_sent' => $date_sent,
                    'and_notes' => $and_notes,
                    'global_editor_notes' => $global_editor_notes,
                    'solved_date' => $solved_date,
                    'pdf_evidence' => $pdf_evidence,
                    'rec_e_contract' => $rec_e_contract,
                    'fu_1' => $fu_1,
                    'fu_2' => $fu_2,
                    'fu_3' => $fu_3,
                    'fu_4' => $fu_4,
                    'fu_5' => $fu_5,
                    'marker_for_global' => $marker_for_global,
                    'marker_for_and' => $marker_for_and,
                    'email_sent' => $email_sent,
                    'batch_date' => $batch_date,
                    'and_evidence' => $and_evidence,
                    'global_evidence' => $global_evidence,
                ]);
            }
            NonExclusiveReport::insert($savedData);
        }
        return true;
    }
    public function getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY){
        $master = [];
        Cache::forget($keyMaster);
        $start = 2;
        $end = $start+self::$limit;
        $counter = 0;
        while(true){
            $get_range = $sheets."!".$alphaX.($start).":".$alphaY.$end;
            try {
                $keyDaily = $keyMaster . "_" . $counter;
                Cache::forget($keyDaily);
                // dump($keyDaily);
                $result = self::getApiSpreadsheet($sId, $get_range);
                if(!$result[0][0]){
                    break;
                }
                Cache::put($keyDaily, $result);
                array_push($master, $keyDaily);
                // dump(["A{$start}", $result[0][3]]);
                $start += self::$limit-1;
                $end += self::$limit;
                $counter++;
                // dump(Cache::get($keyDaily));
            } catch (\Throwable $th) {
                break;
            }
        }
        Cache::put($keyMaster, $master);
    }

    /*--------------------------
    | UPDATE VALUE SPREADSHEETS
    -----------------------------*/
    private $month = false;
    private $month_name = false;
    private $month_now = false;
    private $date_start = false;
    private $date_end = false;
    private $page = false;
    public function __construct() {
        $this->month = date('Y-m');
        $this->month_name = date('F Y');
        $this->month_name_now = date('F');
        $this->date_start = date($this->month."-d", strtotime("first day of this month"));
        $this->date_end = date($this->month."-d", strtotime("last day of this month"));
        $this->page = new PageController();
    }
    public function setTeamMonitoringGlobal(){
        $spreadsheetId = "1jxec-kRkWE_38Mnz1H3FgwTvsazJora1dt_79AqO-cc";
        // $title = "Bot-Try Lv. 1 Global Monitoring - ".$this->month_name;
        // $spreadsheetId = $this->CreateNewSpreadsheet($title);

        $n_ame = ["Ame"];
        $n_anna = ["Anna"];
        $n_Carol = ["Carol"];
        $n_Eric = ["Eric"];
        $n_Icha = ["Icha"];
        $n_Lily = ["Lily"];
        $n_Maydewi = ["Maydewi"];
        $n_Rani = ["Rani"];

        try {
            $new_worksheet = "Weekly Report";
            $this->CreateNewWorksheet($spreadsheetId,$new_worksheet);
        } catch (\Throwable $th) {
            $new_worksheet = "Weekly Report";
        }

        $page = $this->page;
        $date = $this->date_start.",".$this->date_end;
        $head = [
            "Global Team",
            "Answer New Authors",
            "N. Auth Non Ex",
            "Follow Up",
            "Follow Up Non Ex",
            "Sent E Contract",
            "Rec. E Contract",
            "Done Non Ex",
            "Royalty"
        ];

        $update_range = $new_worksheet;

        $DateWeekly = $page->WeekFromDate(date('Y-m'));
        foreach($DateWeekly['c_week'] as $key => $v_weekly){
            $values = [];
            $startdate = $DateWeekly['startdate'][$key];
            $enddate = $DateWeekly['enddate'][$key];
            $f_head = [
                $v_weekly." ".$this->month_name_now,
                $startdate,
                $enddate
            ];
            $d_ame = $page->DataAme($startdate,$enddate);
            $d_anna = $page->DataAnna($startdate,$enddate);
            $d_carol = $page->DataCarol($startdate,$enddate);
            $d_eric = $page->DataEric($startdate,$enddate);
            $d_icha = $page->DataIcha($startdate,$enddate);
            $d_lily = $page->DataLily($startdate,$enddate);
            $d_maydewi = $page->DataMaydewi($startdate,$enddate);
            $d_rani = $page->DataRani($startdate,$enddate);
            array_push($values,$f_head);
            array_push($values,$head);
            $v_ame = [
                $n_ame[0],
                $d_ame['daily']->whereNotNull('date')->count(),
                $d_ame['non_ex']->whereNotNull('first_touch')->count(),
                $d_ame['daily']->whereNotNull('fu_1')->count()+$d_ame['daily']->whereNotNull('fu_2')->count()+$d_ame['daily']->whereNotNull('fu_3')->count()+$d_ame['daily']->whereNotNull('fu_4')->count()+$d_ame['daily']->whereNotNull('fu_5')->count(),
                $d_ame['non_ex']->whereNotNull('fu_non_ex_1')->count()+$d_ame['non_ex']->whereNotNull('fu_non_ex_2')->count()+$d_ame['non_ex']->whereNotNull('fu_non_ex_3')->count()+$d_ame['non_ex']->whereNotNull('fu_non_ex_4')->count()+$d_ame['non_ex']->whereNotNull('fu_non_ex_5')->count(),
                $d_ame['non_ex']->whereNotNull('sent_e_contract')->count(),
                $d_ame['non_ex']->whereNotNull('rec_e_contract')->count(),
                $d_ame['non_ex']->whereNotNull('email_sent')->count(),
                $d_ame['non_ex']->whereNotNull('sent_royalty')->count()
            ];
            array_push($values, $v_ame);
            $v_anna = [
                $n_anna[0],
                $d_anna['daily']->whereNotNull('date')->count(),
                $d_anna['non_ex']->whereNotNull('first_touch')->count(),
                $d_anna['daily']->whereNotNull('fu_1')->count()+$d_anna['daily']->whereNotNull('fu_2')->count()+$d_anna['daily']->whereNotNull('fu_3')->count()+$d_anna['daily']->whereNotNull('fu_4')->count()+$d_anna['daily']->whereNotNull('fu_5')->count(),
                $d_anna['non_ex']->whereNotNull('fu_non_ex_1')->count()+$d_anna['non_ex']->whereNotNull('fu_non_ex_2')->count()+$d_anna['non_ex']->whereNotNull('fu_non_ex_3')->count()+$d_anna['non_ex']->whereNotNull('fu_non_ex_4')->count()+$d_anna['non_ex']->whereNotNull('fu_non_ex_5')->count(),
                $d_anna['non_ex']->whereNotNull('sent_e_contract')->count(),
                $d_anna['non_ex']->whereNotNull('rec_e_contract')->count(),
                $d_anna['non_ex']->whereNotNull('email_sent')->count(),
                $d_anna['non_ex']->whereNotNull('sent_royalty')->count()
            ];
            array_push($values, $v_anna);
            $v_carol = [
                $n_Carol[0],
                $d_carol['daily']->whereNotNull('date')->count(),
                $d_carol['non_ex']->whereNotNull('first_touch')->count(),
                $d_carol['daily']->whereNotNull('fu_1')->count()+$d_carol['daily']->whereNotNull('fu_2')->count()+$d_carol['daily']->whereNotNull('fu_3')->count()+$d_carol['daily']->whereNotNull('fu_4')->count()+$d_carol['daily']->whereNotNull('fu_5')->count(),
                $d_carol['non_ex']->whereNotNull('fu_non_ex_1')->count()+$d_carol['non_ex']->whereNotNull('fu_non_ex_2')->count()+$d_carol['non_ex']->whereNotNull('fu_non_ex_3')->count()+$d_carol['non_ex']->whereNotNull('fu_non_ex_4')->count()+$d_carol['non_ex']->whereNotNull('fu_non_ex_5')->count(),
                $d_carol['non_ex']->whereNotNull('sent_e_contract')->count(),
                $d_carol['non_ex']->whereNotNull('rec_e_contract')->count(),
                $d_carol['non_ex']->whereNotNull('email_sent')->count(),
                $d_carol['non_ex']->whereNotNull('sent_royalty')->count()
            ];
            array_push($values, $v_carol);
            $v_eric = [
                $n_Eric[0],
                $d_eric['daily']->whereNotNull('date')->count(),
                $d_eric['non_ex']->whereNotNull('first_touch')->count(),
                $d_eric['daily']->whereNotNull('fu_1')->count()+$d_eric['daily']->whereNotNull('fu_2')->count()+$d_eric['daily']->whereNotNull('fu_3')->count()+$d_eric['daily']->whereNotNull('fu_4')->count()+$d_eric['daily']->whereNotNull('fu_5')->count(),
                $d_eric['non_ex']->whereNotNull('fu_non_ex_1')->count()+$d_eric['non_ex']->whereNotNull('fu_non_ex_2')->count()+$d_eric['non_ex']->whereNotNull('fu_non_ex_3')->count()+$d_eric['non_ex']->whereNotNull('fu_non_ex_4')->count()+$d_eric['non_ex']->whereNotNull('fu_non_ex_5')->count(),
                $d_eric['non_ex']->whereNotNull('sent_e_contract')->count(),
                $d_eric['non_ex']->whereNotNull('rec_e_contract')->count(),
                $d_eric['non_ex']->whereNotNull('email_sent')->count(),
                $d_eric['non_ex']->whereNotNull('sent_royalty')->count()
            ];
            array_push($values, $v_eric);
            $v_icha = [
                $n_Icha[0],
                $d_icha['daily']->whereNotNull('date')->count(),
                $d_icha['non_ex']->whereNotNull('first_touch')->count(),
                $d_icha['daily']->whereNotNull('fu_1')->count()+$d_icha['daily']->whereNotNull('fu_2')->count()+$d_icha['daily']->whereNotNull('fu_3')->count()+$d_icha['daily']->whereNotNull('fu_4')->count()+$d_icha['daily']->whereNotNull('fu_5')->count(),
                $d_icha['non_ex']->whereNotNull('fu_non_ex_1')->count()+$d_icha['non_ex']->whereNotNull('fu_non_ex_2')->count()+$d_icha['non_ex']->whereNotNull('fu_non_ex_3')->count()+$d_icha['non_ex']->whereNotNull('fu_non_ex_4')->count()+$d_icha['non_ex']->whereNotNull('fu_non_ex_5')->count(),
                $d_icha['non_ex']->whereNotNull('sent_e_contract')->count(),
                $d_icha['non_ex']->whereNotNull('rec_e_contract')->count(),
                $d_icha['non_ex']->whereNotNull('email_sent')->count(),
                $d_icha['non_ex']->whereNotNull('sent_royalty')->count()
            ];
            array_push($values, $v_icha);
            $v_lily = [
                $n_Lily[0],
                $d_lily['daily']->whereNotNull('date')->count(),
                $d_lily['non_ex']->whereNotNull('first_touch')->count(),
                $d_lily['daily']->whereNotNull('fu_1')->count()+$d_lily['daily']->whereNotNull('fu_2')->count()+$d_lily['daily']->whereNotNull('fu_3')->count()+$d_lily['daily']->whereNotNull('fu_4')->count()+$d_lily['daily']->whereNotNull('fu_5')->count(),
                $d_lily['non_ex']->whereNotNull('fu_non_ex_1')->count()+$d_lily['non_ex']->whereNotNull('fu_non_ex_2')->count()+$d_lily['non_ex']->whereNotNull('fu_non_ex_3')->count()+$d_lily['non_ex']->whereNotNull('fu_non_ex_4')->count()+$d_lily['non_ex']->whereNotNull('fu_non_ex_5')->count(),
                $d_lily['non_ex']->whereNotNull('sent_e_contract')->count(),
                $d_lily['non_ex']->whereNotNull('rec_e_contract')->count(),
                $d_lily['non_ex']->whereNotNull('email_sent')->count(),
                $d_lily['non_ex']->whereNotNull('sent_royalty')->count()
            ];
            array_push($values, $v_lily);
            $v_maydewi = [
                $n_Maydewi[0],
                $d_maydewi['daily']->whereNotNull('date')->count(),
                $d_maydewi['non_ex']->whereNotNull('first_touch')->count(),
                $d_maydewi['daily']->whereNotNull('fu_1')->count()+$d_maydewi['daily']->whereNotNull('fu_2')->count()+$d_maydewi['daily']->whereNotNull('fu_3')->count()+$d_maydewi['daily']->whereNotNull('fu_4')->count()+$d_maydewi['daily']->whereNotNull('fu_5')->count(),
                $d_maydewi['non_ex']->whereNotNull('fu_non_ex_1')->count()+$d_maydewi['non_ex']->whereNotNull('fu_non_ex_2')->count()+$d_maydewi['non_ex']->whereNotNull('fu_non_ex_3')->count()+$d_maydewi['non_ex']->whereNotNull('fu_non_ex_4')->count()+$d_maydewi['non_ex']->whereNotNull('fu_non_ex_5')->count(),
                $d_maydewi['non_ex']->whereNotNull('sent_e_contract')->count(),
                $d_maydewi['non_ex']->whereNotNull('rec_e_contract')->count(),
                $d_maydewi['non_ex']->whereNotNull('email_sent')->count(),
                $d_maydewi['non_ex']->whereNotNull('sent_royalty')->count()
            ];
            array_push($values, $v_maydewi);
            $v_rani = [
                $n_Rani[0],
                $d_rani['daily']->whereNotNull('date')->count(),
                $d_rani['non_ex']->whereNotNull('first_touch')->count(),
                $d_rani['daily']->whereNotNull('fu_1')->count()+$d_rani['daily']->whereNotNull('fu_2')->count()+$d_rani['daily']->whereNotNull('fu_3')->count()+$d_rani['daily']->whereNotNull('fu_4')->count()+$d_rani['daily']->whereNotNull('fu_5')->count(),
                $d_rani['non_ex']->whereNotNull('fu_non_ex_1')->count()+$d_rani['non_ex']->whereNotNull('fu_non_ex_2')->count()+$d_rani['non_ex']->whereNotNull('fu_non_ex_3')->count()+$d_rani['non_ex']->whereNotNull('fu_non_ex_4')->count()+$d_rani['non_ex']->whereNotNull('fu_non_ex_5')->count(),
                $d_rani['non_ex']->whereNotNull('sent_e_contract')->count(),
                $d_rani['non_ex']->whereNotNull('rec_e_contract')->count(),
                $d_rani['non_ex']->whereNotNull('email_sent')->count(),
                $d_rani['non_ex']->whereNotNull('sent_royalty')->count()
            ];
            array_push($values, $v_rani);
            // dump($values);
            $this->updateTeamMonitoring($spreadsheetId,$values,$update_range);
        }

        try{
            $update_worksheet = "Lv 1 Monitoring";
            $this->UpdateSheetProperties($spreadsheetId, $update_worksheet);
        } catch (\Throwable $th){
            $update_worksheet = "Lv 1 Monitoring";
        }

        $ame = $page->dataGlobalTeamMonitoringAme($date);
        $anna = $page->dataGlobalTeamMonitoringAnna($date);
        $Carol = $page->dataGlobalTeamMonitoringCarol($date);
        $Eric = $page->dataGlobalTeamMonitoringEric($date);
        $Icha = $page->dataGlobalTeamMonitoringIcha($date);
        $Lily = $page->dataGlobalTeamMonitoringLily($date);
        $Maydewi = $page->dataGlobalTeamMonitoringMaydewi($date);
        $Rani = $page->dataGlobalTeamMonitoringRani($date);

        $v_ame = [];
        $v_ame = $this->dataTeamGlobalMonitoring($v_ame,$n_ame,$head,$ame['data']);
        $update_range = $update_worksheet."!A:I";
        $this->updateTeamMonitoring($spreadsheetId,$v_ame,$update_range);

        $v_anna = [];
        $v_anna = $this->dataTeamGlobalMonitoring($v_anna,$n_anna,$head,$anna['data']);
        $update_range = $update_worksheet."!J:R";
        $this->updateTeamMonitoring($spreadsheetId,$v_anna,$update_range);

        $v_Carol = [];
        $v_Carol = $this->dataTeamGlobalMonitoring($v_Carol,$n_Carol,$head,$Carol['data']);
        $update_range = $update_worksheet."!S:AA";
        $this->updateTeamMonitoring($spreadsheetId,$v_Carol,$update_range);

        $v_Eric = [];
        $v_Eric = $this->dataTeamGlobalMonitoring($v_Eric,$n_Eric,$head,$Eric['data']);
        $update_range = $update_worksheet."!AB:AJ";
        $this->updateTeamMonitoring($spreadsheetId,$v_Eric,$update_range);

        $v_Icha = [];
        $v_Icha = $this->dataTeamGlobalMonitoring($v_Icha,$n_Icha,$head,$Icha['data']);
        $update_range = $update_worksheet."!AK:AS";
        $this->updateTeamMonitoring($spreadsheetId,$v_Icha,$update_range);

        $v_Lily = [];
        $v_Lily = $this->dataTeamGlobalMonitoring($v_Lily,$n_Lily,$head,$Lily['data']);
        $update_range = $update_worksheet."!AT:BB";
        $this->updateTeamMonitoring($spreadsheetId,$v_Lily,$update_range);

        $v_Maydewi = [];
        $v_Maydewi = $this->dataTeamGlobalMonitoring($v_Maydewi,$n_Maydewi,$head,$Maydewi['data']);
        $update_range = $update_worksheet."!BC:BK";
        $this->updateTeamMonitoring($spreadsheetId,$v_Maydewi,$update_range);

        $v_Rani = [];
        $v_Rani = $this->dataTeamGlobalMonitoring($v_Rani,$n_Rani,$head,$Rani['data']);
        $update_range = $update_worksheet."!BL:BT";
        $this->updateTeamMonitoring($spreadsheetId,$v_Rani,$update_range);

        return 200;
    }
    public function setTeamMonitoringIndo(){
        $spreadsheetId = "1jxec-kRkWE_38Mnz1H3FgwTvsazJora1dt_79AqO-cc";
    }
    public function dataTeamGlobalMonitoring($values,$name,$head,$person_data){
        array_push($values,$name);
        array_push($values,$head);
        for($i=0;$i<count($person_data);$i++){
            unset($person_data[$i][0]);
            $x = explode('/',$person_data[$i][1]);
            try {
                $date_format = $x[1]."/".$x[0]."/".$x[2];
                $person_data[$i][1] = date('d/m/Y', strtotime($date_format));
                $val = array_values($person_data[$i]);
                array_push($values, $val);
            } catch (\Throwable $th) {
                $val = array_values($person_data[$i]);
                array_push($values, $val);
            }
        }
        return $values;
    }
    public function updateTeamMonitoring($spreadsheetId,$values,$update_range){
        $service = self::ApiSpreadsheet();
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);
        $params = [
            'valueInputOption' => 'RAW'
        ];
        $update_sheet = $service->spreadsheets_values->append($spreadsheetId, $update_range, $body, $params);
        return $update_sheet;
    }
}
