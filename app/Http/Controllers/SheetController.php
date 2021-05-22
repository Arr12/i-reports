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
    public function GetDailyReport(Request $request){
        /*----------
        / GLOBAL
        --------------*/
        if($request->input('d') == 'ame'){
            $this->getDailyReportAme();
        }
        else if($request->input('d') == 'anna'){
            $this->getDailyReportAnna();
        }
        else if($request->input('d') == 'carol'){
            $this->getDailyReportCarol();
        }
        else if($request->input('d') == 'eric'){
            $this->getDailyReportEric();
        }
        else if($request->input('d') == 'icha'){
            $this->getDailyReportIcha();
        }
        else if($request->input('d') == 'lily'){
            $this->getDailyReportLily();
        }
        else if($request->input('d') == 'maydewi'){
            $this->getDailyReportMayDewi();
        }
        else if($request->input('d') == 'rani'){
            $this->getDailyReportRani();
        }
        /*-----------
        / INDO
        --------------*/
        else if($request->input('d') == 'indo-ichanur'){
            $this->getDailyReportIndoIchaNur();
        }
        else if($request->input('d') == 'indo-irel'){
            $this->getDailyReportIndoIrel();
        }
        else if($request->input('d') == 'all'){
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
}