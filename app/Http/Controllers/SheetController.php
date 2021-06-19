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
use App\Models\ReportSpamMangatoonNovelList;
use App\Models\ReportSpamNovelListFromRanking as ModelsReportSpamNovelListFromRanking;
use App\Models\ReportSpamRoyalRoadNovelList;
use App\Models\ReportSpamWNUncoractedNovelList;
use Google_Service_Sheets_BatchUpdateSpreadsheetRequest;
use Google_Service_Sheets_Request;
use Google_Service_Sheets_Spreadsheet;
use Google_Service_Sheets_UpdateSheetPropertiesRequest;
use Google_Service_Sheets_ValueRange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
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
    public function check($array, $value){
        return isset($array[$value]) ? $array[$value] : null;
    }

    /*--------------------------
    | CONFIG SPREADSHEET
    ----------------------------*/
    public static function ApiSpreadsheet(){
        $client = new \Google_Client();
        $client->setApplicationName('Google Sheets and PHP');
        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');
        $client->setAuthConfig(public_path('credentials/credentials.json'));
        $service = new \Google_Service_Sheets($client);
        return $service;
    }
    public static function getApiSpreadsheet($spreadsheetId, $get_range){
        $service = self::ApiSpreadsheet();
        //Request to get data from spreadsheet.
        $response = $service->spreadsheets_values->get($spreadsheetId, $get_range);
        $values = $response->getValues();
        return $values;
    }
    public function CreateNewFolderApi($title){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://45.76.182.41:8000/create-folder/'.$title,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
    public function CreateNewSpreadsheet($title,$folder_id){
        $curl = curl_init();
        $title = str_replace(" ", "%20", $title);
        curl_setopt_array($curl, array(
        CURLOPT_URL => "http://45.76.182.41:8000/create-spreadsheet/$folder_id/$title",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
    public function DuplicateSpreadsheet($spreadsheetId_old,$folder_id,$title){
        $curl = curl_init();
        $title = str_replace(" ", "%20", $title);
        curl_setopt_array($curl, array(
        CURLOPT_URL => "http://45.76.182.41:8000/duplicate-spreadsheet/$spreadsheetId_old/$folder_id/$title",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
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
    public function BlankRowSheetAll($spreadsheetId, $sheetId){
        $service = $this->ApiSpreadsheet();
        $request = new \Google_Service_Sheets_UpdateCellsRequest([
            'updateCells' => [
                'range' => [
                    'sheetId' => $sheetId
                ],
                'fields' => "*"
            ]
        ]);
        $requests[] = $request;

        $requestBody = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest();
        $requestBody->setRequests($requests);
        $response = $service->spreadsheets->batchUpdate($spreadsheetId, $requestBody);
        return $response;
    }
    public function DeleteRowSheet($sId, $sheetId, $startRowIndex, $endRowIndex){
        $service = $this->ApiSpreadsheet();
        $deleteOperation = array(
            'range' => array(
                'sheetId'   => $sheetId,
                'dimension' => 'ROWS',
                'startIndex'=> $startRowIndex,
                'endIndex'  => $endRowIndex
            )
        );
        $deletable_row[] = new Google_Service_Sheets_Request(
            array('deleteDimension' =>  $deleteOperation)
        );
        $delete_body    = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest(array(
                'requests' => $deletable_row
            )
        );
        //var_dump($delete_body);
        $result = $service->spreadsheets->batchUpdate($sId, $delete_body);
        return $result;
    }
    public function insertValuesIntoFirstRow($spreadsheetId,$values,$update_range,$endindex,$sheetId){
        $service = self::ApiSpreadsheet();
        $request = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
            'requests' => [
                'insertDimension' => [
                    'range' => [
                        "sheetId" => $sheetId,
                        "startIndex" => 0,
                        "endIndex" => $endindex,
                        "dimension" => "ROWS"
                    ]
                ]
            ]
        ]);
        $service->spreadsheets->batchUpdate($spreadsheetId, $request);
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);
        $params = [
            'valueInputOption' => 'RAW'
        ];
        $service->spreadsheets_values->append($spreadsheetId, $update_range, $body, $params);
        return true;
    }
    public function ChangeColor($spreadsheetId, $sheetId,$color,$startRowIndex,$endRowIndex,$startColumnIndex,$endColumnIndex){
        $service = self::ApiSpreadsheet();
        $request = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
            'updateCells' => [
                'rows' => array([
                  'values' => array(
                      [
                        'userEnteredFormat' => [
                            'backgroundColor' => $color
                        ]
                    ]
                  )
                ]),
                'range' => [
                    'sheetId' => $sheetId,
                    'startRowIndex' => $startRowIndex,
                    'endRowIndex' => $endRowIndex,
                    'startColumnIndex' => $startColumnIndex,
                    'endColumnIndex' => $endColumnIndex
                ],
                'fields' => 'userEnteredFormat'
            ]
        ]);
        $service->spreadsheets->batchUpdate($spreadsheetId, $request);
        return true;
    }
    public function DeleteFuncRowSheet($spreadsheetId,$sheetId){
        $start = 0;
        $endindex = 1000;
        while(true){
            try {
                $this->DeleteRowSheet($spreadsheetId, $sheetId, $start, $endindex);
            } catch (\Throwable $th) {
                break;
            }
        }
        $this->BlankRowSheetAll($spreadsheetId, $sheetId);
    }

    /*----------------------------
    | GET DATA SPREADSHEET
    -------------------------------*/
    public function GetDailyReport($request = false){
        /*----------
        / GLOBAL
        --------------*/
        $daily = $request ?: request()->input('d');
        if($daily == 'ame'){
            Artisan::call('daily:ame');
        }
        else if($daily == 'anna'){
            Artisan::call('daily:anna');
        }
        else if($daily == 'carol'){
            Artisan::call('daily:carol');
        }
        else if($daily == 'eric'){
            Artisan::call('daily:eric');
        }
        else if($daily == 'icha'){
            Artisan::call('daily:icha');
        }
        else if($daily == 'lily'){
            Artisan::call('daily:lily');
        }
        else if($daily == 'maydewi'){
            Artisan::call('daily:maydewi');
        }
        else if($daily == 'rani'){
            Artisan::call('daily:rani');
        }
        /*-----------
        / INDO
        --------------*/
        else if($daily == 'indo-ichanur'){
            Artisan::call('daily:icha-nur');
        }
        else if($daily == 'indo-irel'){
            Artisan::call('daily:irel');
        }
        /*-----------
        / SPAM
        --------------*/
        else if($daily == 'mangatoon'){
            Artisan::call('daily:mangatoon');
        }
        else if($daily == 'royalroad'){
            $this->getSpamRoyalRoadNovelList();
        }
        else if($daily == 'wn-uncontracted'){
            Artisan::call('daily:wn-uncontracted');
        }
        else if($daily == 'novel-list-ranking'){
            Artisan::call('daily:novel-list-ranking');
        }
        /*-----------
        / EXCLUSIVE
        --------------*/
        else if($daily == 'non-exclusive'){
            Artisan::call('daily:non-exclusive');
        }
        else if($daily == 'all'){
            Artisan::call('daily:ame');
            Artisan::call('daily:anna');
            Artisan::call('daily:carol');
            Artisan::call('daily:eric');
            Artisan::call('daily:icha');
            Artisan::call('daily:lily');
            Artisan::call('daily:maydewi');
            Artisan::call('daily:rani');
            Artisan::call('daily:icha-nur');
            Artisan::call('daily:irel');
            Artisan::call('daily:non-exclusive');
            Artisan::call('daily:mangatoon');
            Artisan::call('daily:wn-uncontracted');
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
        /** ---------------------
        * DUPLICATE SPREADSHEET
        ------------------------- */
        $title = "Backup Global ".$sheets." Ame - ".date('Y-m-d H:i:s');
        $folder_id = "1Up6Uz0BPHiFxZcpDx-qz-gUUdF9z6cdm";
        $this->DuplicateSpreadsheet($sId,$folder_id,$title);

        /** ---------------------
        * TRUNCATE DATABASE
        ------------------------- */
        DailyReportAme::truncate();

        /** ---------------------
        * GET DATA & INSERT TO DB
        ------------------------- */
        $this->getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
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
                    'old_new_book' => $old_new_book,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            // dd($savedData);
            DailyReportAme::insert($savedData);
            Cache::forget('cache-ame');
            $this->page->DataAmeCached();
        }
        return true;
    }
    public function getDailyReportAnna(){
        $sId = "1tCKH2zgvAv313WaJeEnxYKdt5BHOo7jGg3QI6WM2qhM";
        $keyMaster = date('Y-m-d')."_daily_report_anna";
        $sheets = "Daily Report";
        $alphaX = "A";
        $alphaY = "X";
        /** ---------------------
        * DUPLICATE SPREADSHEET
        ------------------------- */
        $title = "Backup Global ".$sheets." Anna - ".date('Y-m-d H:i:s');
        $folder_id = "1uGCqZqbJg9b6g1wlEAcb-_wZQyRTA3_4";
        $this->DuplicateSpreadsheet($sId,$folder_id,$title);

        /** ---------------------
        * TRUNCATE DATABASE
        ------------------------- */
        DailyReportAnna::truncate();

        /** ---------------------
        * GET DATA & INSERT TO DB
        ------------------------- */
        $this->getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
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
                    'old_new_book' => $old_new_book,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            DailyReportAnna::insert($savedData);
            Cache::forget('cache-anna');
            $this->page->DataAnnaCached();
        }
        return true;
    }
    public function getDailyReportCarol(){
        $sId = "1zaxZhyxO0CCJUE-vYV5olkgHPKx43AIY0NunYgIDKno";
        $keyMaster = date('Y-m-d')."_daily_report_carol";
        $sheets = "Daily Report";
        $alphaX = "A";
        $alphaY = "X";
        /** ---------------------
        * DUPLICATE SPREADSHEET
        ------------------------- */
        $title = "Backup Global ".$sheets." Carol - ".date('Y-m-d H:i:s');
        $folder_id = "14GDv3tcFvDsi8cGgYhvkybwShBIQYLgA";
        $this->DuplicateSpreadsheet($sId,$folder_id,$title);

        /** ---------------------
        * TRUNCATE DATABASE
        ------------------------- */
        DailyReportCarol::truncate();

        /** ---------------------
        * GET DATA & INSERT TO DB
        ------------------------- */
        $this->getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
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
                    'old_new_book' => $old_new_book,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            DailyReportCarol::insert($savedData);
            Cache::forget('cache-carol');
            $this->page->DataCarolCached();
        }
        return true;
    }
    public function getDailyReportEric(){
        $sId = "1Fu3-AE0Wr9RlCxr2qcpaU-x6V5BpMSAbb71sbhPscTM";
        $keyMaster = date('Y-m-d')."_daily_report_eric";
        $sheets = "Daily Report";
        $alphaX = "A";
        $alphaY = "X";
        /** ---------------------
        * DUPLICATE SPREADSHEET
        ------------------------- */
        $title = "Backup Global ".$sheets." Eric - ".date('Y-m-d H:i:s');
        $folder_id = "1tS_H_w0rkzbbc7YSmV0oACEhHiA_fxfQ";
        $this->DuplicateSpreadsheet($sId,$folder_id,$title);

        /** ---------------------
        * TRUNCATE DATABASE
        ------------------------- */
        DailyReportEric::truncate();

        /** ---------------------
        * GET DATA & INSERT TO DB
        ------------------------- */
        $this->getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
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
                    'old_new_book' => $old_new_book,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            DailyReportEric::insert($savedData);
            Cache::forget('cache-eric');
            $this->page->DataEricCached();
        }
        return true;
    }
    public function getDailyReportIcha(){
        $sId = "12B_FXZkuDush0Nmv1lY1iBxE1GCOk2TplP4gdkX9OvE";
        $keyMaster = date('Y-m-d')."_daily_report_icha";
        $sheets = "Daily Report";
        $alphaX = "A";
        $alphaY = "X";
        /** ---------------------
        * DUPLICATE SPREADSHEET
        ------------------------- */
        $title = "Backup Global ".$sheets." Icha - ".date('Y-m-d H:i:s');
        $folder_id = "1_3zo7MglZLCAEKuIVgK9YWuPY7yZnj-y";
        $this->DuplicateSpreadsheet($sId,$folder_id,$title);

        /** ---------------------
        * TRUNCATE DATABASE
        ------------------------- */
        DailyReportIcha::truncate();

        /** ---------------------
        * GET DATA & INSERT TO DB
        ------------------------- */
        $this->getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
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
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            DailyReportIcha::insert($savedData);
            Cache::forget('cache-icha');
            $this->page->DataIchaCached();
        }
        return true;
    }
    public function getDailyReportLily(){
        $sId = "1hgkVhoRsILCJQeO4i5_EMXvlNbsfmB2_9k8Fg-Cx8eg";
        $keyMaster = date('Y-m-d')."_daily_report_lily";
        $sheets = "Daily Report";
        $alphaX = "A";
        $alphaY = "X";
        /** ---------------------
        * DUPLICATE SPREADSHEET
        ------------------------- */
        $title = "Backup Global ".$sheets." Lily - ".date('Y-m-d H:i:s');
        $folder_id = "13K9xak83s38up6ac0z4F5lGvDtQVkhHF";
        $this->DuplicateSpreadsheet($sId,$folder_id,$title);

        /** ---------------------
        * TRUNCATE DATABASE
        ------------------------- */
        DailyReportLily::truncate();

        /** ---------------------
        * GET DATA & INSERT TO DB
        ------------------------- */
        $this->getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
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
                    'old_new_book' => $old_new_book,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            DailyReportLily::insert($savedData);
            Cache::forget('cache-lily');
            $this->page->DataLilyCached();
        }
        return true;
    }
    public function getDailyReportMayDewi(){
        $sId = "1zFrtw-fGgSBIImwk7t2LRPnxKMq3_hsxhgI3mLa0ksw";
        $keyMaster = date('Y-m-d')."_daily_report_maydewi";
        $sheets = "Daily Report";
        $alphaX = "A";
        $alphaY = "X";
        /** ---------------------
        * DUPLICATE SPREADSHEET
        ------------------------- */
        $title = "Backup Global ".$sheets." Maydewi - ".date('Y-m-d H:i:s');
        $folder_id = "1gzeeNe0M4F74DjlR7gPsSQGu0bj8LN78";
        $this->DuplicateSpreadsheet($sId,$folder_id,$title);

        /** ---------------------
        * TRUNCATE DATABASE
        ------------------------- */
        DailyReportMayDewi::truncate();

        /** ---------------------
        * GET DATA & INSERT TO DB
        ------------------------- */
        $this->getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
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
                    'old_new_book' => $old_new_book,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            DailyReportMaydewi::insert($savedData);
            Cache::forget('cache-maydewi');
            $this->page->DataMaydewiCached();
        }
        return true;
    }
    public function getDailyReportRani(){
        $sId = "12pPkC3NIAdc8JQvaz5su4uVxCjURI1YzviwStk6m5QY";
        $keyMaster = date('Y-m-d')."_daily_report_Rani";
        $sheets = "Daily Report";
        $alphaX = "A";
        $alphaY = "X";

        /** ---------------------
        * DUPLICATE SPREADSHEET
        ------------------------- */
        $title = "Backup Global ".$sheets." Rani - ".date('Y-m-d H:i:s');
        $folder_id = "1s3UQZyjlPhdHxp_Q_w0LSEt6jZtFwGZO";
        $this->DuplicateSpreadsheet($sId,$folder_id,$title);

        /** ---------------------
        * TRUNCATE DATABASE
        ------------------------- */
        DailyReportRani::truncate();

        /** ---------------------
        * GET DATA & INSERT TO DB
        ------------------------- */
        $this->getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
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
                    'old_new_book' => $old_new_book,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            DailyReportRani::insert($savedData);
            Cache::forget('cache-rani');
            $this->page->DataRaniCached();
        }
        return true;
    }
    public function getDailyReportIndoIchaNur(){
        $sId = "1TwyTXnBI51TH3tlO8nKhfdB_7o7r76T1fxFXQMZ3OXw";
        $keyMaster = date('Y-m-d')."_daily_report_IndoIchaNur";
        $sheets = "Daily Report";
        $alphaX = "A";
        $alphaY = "V";

        /** ---------------------
        * DUPLICATE SPREADSHEET
        ------------------------- */
        $title = "Backup Indo ".$sheets." Icha Nur - ".date('Y-m-d H:i:s');
        $folder_id = "14Ez0EHDe2md0RcT7maNjevUGKcxG9leB";
        $this->DuplicateSpreadsheet($sId,$folder_id,$title);

        /** ---------------------
        * TRUNCATE DATABASE
        ------------------------- */
        DailyReportIndoIchaNur::truncate();

        /** ---------------------
        * GET DATA & INSERT TO DB
        ------------------------- */
        $this->getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
        foreach($cached as $key => $keyDaily){
            $cachedDaily = Cache::get($keyDaily, []);
            // dump($cachedDaily);
            $savedData = [];
            foreach ($cachedDaily as $key => $data) {
                // dump($data[2]);
                if(!$data[0]){continue;}
                $date = $this->FormatDateTime($data[0]);
                $contact_way = isset($data[1]) ? $data[1] : null;
                $author_contact = isset($data[2]) ? $data[2] : null;
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
                $marker = isset($data[20]) ? $data[20] : null;
                $old_new_book = isset($data[21]) ? $data[21] : null;
                array_push($savedData, [
                    'date' => $date,
                    'contact_way' => $contact_way,
                    'author_contact' => $author_contact,
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
                    'old_new_book' => $old_new_book,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            DailyReportIndoIchaNur::insert($savedData);
            Cache::forget('cache-ichanur');
            $this->page->DataIndoIchaNurCached();
        }
        return true;
    }
    public function getDailyReportIndoIrel(){
        $sId = "1UnzYJNDXDuKodPqs2nQW4JUfLdJIxb9nT7XnHcnNcFk";
        $keyMaster = date('Y-m-d')."_daily_report_IndoIrel";
        $sheets = "Daily Report";
        $alphaX = "A";
        $alphaY = "S";

        /** ---------------------
        * DUPLICATE SPREADSHEET
        ------------------------- */
        $title = "Backup Indo ".$sheets." Irel - ".date('Y-m-d H:i:s');
        $folder_id = "1FKSe8ylQtIowXwzd3mRj0k3NSknN_7ex";
        $this->DuplicateSpreadsheet($sId,$folder_id,$title);

        /** ---------------------
        * TRUNCATE DATABASE
        ------------------------- */
        DailyReportIndoIrel::truncate();

        /** ---------------------
        * GET DATA & INSERT TO DB
        ------------------------- */
        $this->getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
        foreach($cached as $key => $keyDaily){
            $cachedDaily = Cache::get($keyDaily, []);
            $savedData = [];
            foreach ($cachedDaily as $key => $data) {
                // dump($data);
                if(!$data[0]){continue;}
                $date = $this->FormatDateTime($data[0]);
                $status = isset($data[1]) ? $data[1] : null;
                $date_solved = isset($data[2]) ? $this->FormatDateTime($data[2]) : null;
                $author_contact = isset($data[3]) ? $data[3] : null;
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
                    'author_contact' => $author_contact,
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
                    'fu_10' => $fu_10,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            DailyReportIndoIrel::insert($savedData);
            Cache::forget('cache-irel');
            $this->page->DataIndoIrelCached();
        }
        return true;
    }
    public function getNonExReport(){
        $sId = "1C_FHAsaNX4lbeQjfMP0i3piXCzIVtHalgFYk5wWJph4";
        $keyMaster = date('Y-m-d')."_daily_report_NonExclusive";
        $sheets = "Daily Report Master";
        $alphaX = "A";
        $alphaY = "AC";

        /** ---------------------
        * DUPLICATE SPREADSHEET
        ------------------------- */
        $title = "Backup Non Exclusive Report - ".date('Y-m-d H:i:s');
        $folder_id = "1qV1PIKvw-3MZtAPtNLeJUXassnjEMSMu";
        $this->DuplicateSpreadsheet($sId,$folder_id,$title);

        /** ---------------------
        * TRUNCATE DATABASE
        ------------------------- */
        NonExclusiveReport::truncate();

        /** ---------------------
        * GET DATA & INSERT TO DB
        ------------------------- */
        $this->getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
        foreach($cached as $key => $keyDaily){
            $cachedDaily = Cache::get($keyDaily, []);
            $savedData = [];
            // dump($cachedDaily);
            foreach ($cachedDaily as $key => $data) {
                if(!$data[1]){continue;}
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
                $and_evidence = isset($data[27]) ? $data[27] : null;
                $global_evidence = isset($data[28]) ? $data[28] : null;
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
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            NonExclusiveReport::insert($savedData);
            Cache::forget('cache-non-exclusive');
            $this->page->DataNonExCached();
        }
        return true;
    }
    public function getSpamRoyalRoadNovelList(){
        $sId = "1yF8dhx2Emy7O31mZmxTvlLVwiuy1RcI8Boa_0bO0Nlk";
        $keyMaster = date('Y-m-d')."_daily_report_SpamRoyalRoad";
        $sheets = "books(1)";
        $alphaX = "A";
        $alphaY = "Y";

        /** ---------------------
        * DUPLICATE SPREADSHEET
        ------------------------- */
        $title = "Backup Spam RoyalRoad Novel List - ".date('Y-m-d H:i:s');
        $folder_id = "1qV1PIKvw-3MZtAPtNLeJUXassnjEMSMu";
        $this->DuplicateSpreadsheet($sId,$folder_id,$title);

        /** ---------------------
        * TRUNCATE DATABASE
        ------------------------- */
        ReportSpamRoyalRoadNovelList::truncate();

        /** ---------------------
        * GET DATA & INSERT TO DB
        ------------------------- */
        $this->getReportSpamData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
        foreach($cached as $key => $keyDaily){
            $cachedDaily = Cache::get($keyDaily, []);
            $savedData = [];
            foreach ($cachedDaily as $key => $data) {
                // if(!$data[0]){continue;}
                // dump($data);
                $date = isset($data[0]) ? $this->FormatDateTime($data[0]) : null;
                array_push($savedData, [
                    'date' => $date,
                    'editor' => $this->check($data,1),
                    'title' => $this->check($data,2),
                    'author' => $this->check($data,3),
                    'url' => $this->check($data,4),
                    'type' => $this->check($data,5),
                    'followers' => $this->check($data,6),
                    'pages' => $this->check($data,7),
                    'chapters' => $this->check($data,8),
                    'views' => $this->check($data,9),
                    'latest_update' => $this->check($data,10),
                    'tags6' => $this->check($data,11),
                    'tag1' => $this->check($data,12),
                    'tag2' => $this->check($data,13),
                    'tag3' => $this->check($data,14),
                    'tag4' => $this->check($data,15),
                    'tag5' => $this->check($data,16),
                    'tag6' => $this->check($data,17),
                    'tags7' => $this->check($data,18),
                    'tags8' => $this->check($data,19),
                    'tags9' => $this->check($data,20),
                    'tags10' => $this->check($data,21),
                    'tags11' => $this->check($data,22),
                    'date_feedback_received' => $this->check($data,23),
                    'feedback_from_author' => $this->check($data,24),
                ]);
            }
            ReportSpamRoyalRoadNovelList::insert($savedData);
        }
        return true;
    }
    public function getSpamMangatoonNovelList(){
        $sId = "1Y7i5p0iuRI3NU374Z3w0j0Ow8w3Wg5hdkaUccpRgKbE";
        $keyMaster = date('Y-m-d')."_daily_report_SpamMangatoon";
        $sheets = "books(1)";
        $alphaX = "A";
        $alphaY = "N";

        /** ---------------------
        * DUPLICATE SPREADSHEET
        ------------------------- */
        $title = "Backup Spam Mangatoon Novel List - ".date('Y-m-d H:i:s');
        $folder_id = "1iHviLSnXCKU5VyjQwGUVcxhj8ZR6MSTr";
        $this->DuplicateSpreadsheet($sId,$folder_id,$title);

        /** ---------------------
        * TRUNCATE DATABASE
        ------------------------- */
        ReportSpamMangatoonNovelList::truncate();

        /** ---------------------
        * GET DATA & INSERT TO DB
        ------------------------- */
        $this->getReportSpamData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
        foreach($cached as $key => $keyDaily){
            $cachedDaily = Cache::get($keyDaily, []);
            $savedData = [];
            foreach ($cachedDaily as $key => $data) {
                // if(!$data[0]){continue;}
                // dump($data);
                $date = isset($data[0]) ? $this->FormatDateTime($data[0]) : null;
                $reasons = isset($data[1]) ? $data[1] : null;
                $book_name = isset($data[2]) ? $data[2] : null;
                $author_name = isset($data[3]) ? $data[3] : null;
                $views = isset($data[4]) ? $data[4] : null;
                $likes = isset($data[5]) ? $data[5] : null;
                $ratings = isset($data[6]) ? $data[6] : null;
                $update_status = isset($data[7]) ? $data[7] : null;
                $tags = isset($data[8]) ? $data[8] : null;
                $episodes = isset($data[9]) ? $data[9] : null;
                $link = isset($data[10]) ? $data[10] : null;
                $screenshot_from_wave = isset($data[11]) ? $data[11] : null;
                $date_feedback_received = isset($data[12]) ? $this->FormatDateTime($data[12]) : null;
                $author_feedback = isset($data[13]) ? $data[13] : null;
                $comment_from_wave = isset($data[14]) ? $data[14] : null;
                array_push($savedData, [
                    'date' => $date,
                    'reasons' => $reasons,
                    'book_name' => $book_name,
                    'author_name' => $author_name,
                    'views' => $views,
                    'likes' => $likes,
                    'ratings' => $ratings,
                    'update_status' => $update_status,
                    'tags' => $tags,
                    'episodes' => $episodes,
                    'link'  => $link,
                    'screenshot_from_wave' => $screenshot_from_wave,
                    'date_feedback_received' => $date_feedback_received,
                    'author_feedback' => $author_feedback,
                    'comment_from_wave' => $comment_from_wave
                ]);
            }
            // dump($savedData);
            ReportSpamMangatoonNovelList::insert($savedData);
            Cache::forget('cache-mangatoon');
            $this->page->DataSpamMangatoonCached();
        }
        return true;
    }
    public function getSpamWNUncontractedNovelList(){
        $sId = "1c7ib7eh9KvT-GhAAFAgSUPyKlnzxK_cGvFe8UhPSkJA";
        $keyMaster = date('Y-m-d')."_daily_report_SpamWNUncontracted";
        $sheets = "books(1)";
        $alphaX = "A";
        $alphaY = "P";

        /** ---------------------
        * DUPLICATE SPREADSHEET
        ------------------------- */
        $title = "Backup Spam WN Uncontracted Novel List - ".date('Y-m-d H:i:s');
        $folder_id = "1HBZwDT50W2r0A7-DJAIzsBW8xEzoxi1s";
        $this->DuplicateSpreadsheet($sId,$folder_id,$title);

        /** ---------------------
        * TRUNCATE DATABASE
        ------------------------- */
        ReportSpamWNUncoractedNovelList::truncate();

        /** ---------------------
        * GET DATA & INSERT TO DB
        ------------------------- */
        $this->getReportSpamData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
        foreach($cached as $key => $keyDaily){
            $cachedDaily = Cache::get($keyDaily, []);
            $savedData = [];
            foreach ($cachedDaily as $key => $data) {
                // if(!$data[0]){continue;}
                // dump($data);
                $date = isset($data[0]) ? $this->FormatDateTime($data[0]) : null;
                $reasons = $this->check($data,1);
                $editor = $this->check($data,2);
                $cbid = $this->check($data,3);
                $book_title = $this->check($data,4);
                $author_name = $this->check($data,5);
                $discord_contact = $this->check($data,6);
                $other_contact_way = $this->check($data,7);
                $genre = $this->check($data,8);
                $total_chapter = $this->check($data,9);
                $chapter_within_7_days = $this->check($data,10);
                $collection = $this->check($data,11);
                $status_ongoing = $this->check($data,12);
                $FL_ML = $this->check($data,13);
                $date_feedback_received = isset($data[14]) ? $this->FormatDateTime($data[14]) : null;
                $feedback_from_author = $this->check($data,15);
                $note = $this->check($data,16);
                array_push($savedData, [
                    'date' => $date,
                    'reasons' => $reasons,
                    'editor' => $editor,
                    'cbid' => $cbid,
                    'book_title' => $book_title,
                    'author_name' => $author_name,
                    'discord_contact' => $discord_contact,
                    'other_contact_way' => $other_contact_way,
                    'genre' => $genre,
                    'total_chapter' => $total_chapter,
                    'chapter_within_7_days'  => $chapter_within_7_days,
                    'collection' => $collection,
                    'status_ongoing' => $status_ongoing,
                    'FL_ML' => $FL_ML,
                    'date_feedback_received' => $date_feedback_received,
                    'feedback_from_author' => $feedback_from_author,
                    'note' => $note
                ]);
            }
            // dd($savedData);
            ReportSpamWNUncoractedNovelList::insert($savedData);
            Cache::forget('cache-wn_uncontracted');
            $this->page->DataUncontractedWNCached();
        }
        return true;
    }
    public function getSpamNovelListFromRanking(){
        $sId = "1c7ib7eh9KvT-GhAAFAgSUPyKlnzxK_cGvFe8UhPSkJA";
        $keyMaster = date('Y-m-d')."_daily_report_SpamNovelListFromRanking";
        $sheets = "Novel List from Ranking";
        $alphaX = "A";
        $alphaY = "L";

        /** ---------------------
        * TRUNCATE DATABASE
        ------------------------- */
        ModelsReportSpamNovelListFromRanking::truncate();

        /** ---------------------
        * GET DATA & INSERT TO DB
        ------------------------- */
        $this->getReportSpamData($sId,$keyMaster,$sheets,$alphaX,$alphaY);
        $cached = Cache::get($keyMaster, []);
        foreach($cached as $key => $keyDaily){
            $cachedDaily = Cache::get($keyDaily, []);
            $savedData = [];
            foreach ($cachedDaily as $key => $data) {
                // dump($data);
                $cbid = isset($data[0]) ? $data[0] : null;
                $book_title = isset($data[1]) ? $data[1] : null;
                $author_name = isset($data[2]) ? $data[2] : null;
                $author_contact = isset($data[3]) ? $data[3] : null;
                $genre = isset($data[4]) ? $data[4] : null;
                $total_chapter = isset($data[5]) ? $data[5] : null;
                $chapter_within_7_days = isset($data[6]) ? $data[6] : null;
                $collection = isset($data[7]) ? $data[7] : null;
                $status_ongoing = isset($data[8]) ? $data[8] : null;
                $FL_ML = isset($data[9]) ? $data[9] : null;
                $editor = isset($data[10]) ? $data[10] : null;
                $note = isset($data[11]) ? $data[11] : null;
                array_push($savedData, [
                    'cbid' => $cbid,
                    'book_title' => $book_title,
                    'author_name' => $author_name,
                    'author_contact' => $author_contact,
                    'genre' => $genre,
                    'total_chapter' => $total_chapter,
                    'chapter_within_7_days' => $chapter_within_7_days,
                    'collection' => $collection,
                    'status_ongoing' => $status_ongoing,
                    'FL_ML'  => $FL_ML,
                    'editor' => $editor,
                    'note' => $note
                ]);
            }
            ModelsReportSpamNovelListFromRanking::insert($savedData);
        }
        return true;
    }
    public function getDailyReportData($sId,$keyMaster,$sheets,$alphaX,$alphaY){
        $master = [];
        Cache::forget($keyMaster);
        $start = 1;
        $end = $start+self::$limit;
        $counter = 0;
        while(true){
            $get_range = $sheets."!".$alphaX.($start+1).":".$alphaY.$end;
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
                $start+=self::$limit;
                $end+=self::$limit;
                $counter++;
                // dump(Cache::get($keyDaily));
            } catch (\Throwable $th) {
                break;
            }
        }
        Cache::put($keyMaster, $master);
    }
    public function getReportSpamData($sId,$keyMaster,$sheets,$alphaX,$alphaY){
        $master = [];
        Cache::forget($keyMaster);
        $start = 1;
        $end = $start+self::$limit;
        $counter = 0;
        while(true){
            $get_range = $sheets."!".$alphaX.($start+1).":".$alphaY.$end;
            try {
                $keyDaily = $keyMaster . "_" . $counter;
                Cache::forget($keyDaily);
                // dump($keyDaily);
                $result = self::getApiSpreadsheet($sId, $get_range);
                // dd($result);
                if(!$result[0][3]){
                    break;
                }
                Cache::put($keyDaily, $result);
                array_push($master, $keyDaily);
                // dump(["A{$start}", $result[0][3]]);
                $start += self::$limit;
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
    private $date_start = false;
    private $date_end = false;
    private $page = false;
    private $n_ame = [];
    private $n_anna = [];
    private $n_Carol = [];
    private $n_Eric = [];
    private $n_Icha = [];
    private $n_Lily = [];
    private $n_Maydewi = [];
    private $n_Rani = [];
    private $n_indo_irel = [];
    private $n_indo_icha = [];
    private $head_global = [];
    private $head_indo = [];
    private $head_indo_b = [];
    public function __construct() {
        $this->month = date('Y-m');
        $this->month_name = date('F Y');
        $this->month_name_now = date('F');
        $this->date_start = date($this->month."-d", strtotime("first day of this month"));
        $this->date_end = date($this->month."-d", strtotime("last day of this month"));
        $this->page = new PageController();
        $this->n_ame = ["Ame"];
        $this->n_anna = ["Anna"];
        $this->n_Carol = ["Carol"];
        $this->n_Eric = ["Eric"];
        $this->n_Icha = ["Icha"];
        $this->n_Lily = ["Lily"];
        $this->n_Maydewi = ["Maydewi"];
        $this->n_Rani = ["Rani"];
        $this->n_indo_irel = ["Irel"];
        $this->n_indo_icha = ["Icha Nur"];
        $this->head_global = [
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
        $this->head_indo = [
            "Indo Team",
            "Answer New Authors",
            "Follow Up Authors",
            "Royalty",
            "Non Exclusive"
        ];
        $this->head_indo_b = [
            "",
            "Help Authors",
            "Follow Up Authors",
            "Solved Problems"
        ];
    }

    /* --------------------------
    | Lv 1 REPORT TEAM MONITORING
    ----------------------------- */
    public function total($array, $value){
        $x = 0;
        foreach($array as $a){
            $x += $a[$value];
            // dump($a);
        }
        return $x;
    }
    public function average($array, $value){
        $x = 0;
        foreach($array as $a){
            $x += $a[$value]/count($this->page->personGlobal);
            // dump($a);
        }
        return $x;
    }
    public function TeamMonitoringGlobal(){
        Artisan::call('set:team-monitoring-global');
    }
    public function setTeamMonitoringGlobal(){
        // $spreadsheetId = "1jxec-kRkWE_38Mnz1H3FgwTvsazJora1dt_79AqO-cc";
        $title = "Lv. 1 Global Monitoring - ".$this->month_name;
        $folder_id = "1HnZsqHGzJVuhXC84wtW-cVjj1OnAUIzu";
        $create = $this->CreateNewSpreadsheet($title, $folder_id);
        $data = json_decode($create);
        $spreadsheetId = $data->spreadsheet_id;

        try {
            $new_worksheet = "Weekly Report";
            $this->CreateNewWorksheet($spreadsheetId,$new_worksheet);
        } catch (\Throwable $th) {
            $new_worksheet = "Weekly Report";
        }
        $page = $this->page;

        /* --------------------------
        | FUNCTION FORMAT REPORT WEEKLY
        -------------------------------- */
        $date = $this->date_start.",".$this->date_end;
        $update_range = $new_worksheet;
        $level = "1";
        $values = $this->ReportWeeklyGlobalFormat($page,$level);
        $this->updateTeamMonitoring($spreadsheetId,$values,$update_range);

        try{
            $update_worksheet = "Lv 1 Monitoring";
            $this->UpdateSheetProperties($spreadsheetId, $update_worksheet);
        } catch (\Throwable $th){
            $update_worksheet = "Lv 1 Monitoring";
        }

        $ame = $page->dataGlobalTeamMonitoringAme($date);
        $v_ame = [];
        $v_ame = $this->dataSanitizerTeamMonitoring($v_ame,$this->n_ame,$this->head_global,$ame['data']);
        $update_range = $update_worksheet."!A:I";
        $this->updateTeamMonitoring($spreadsheetId,$v_ame,$update_range);

        $anna = $page->dataGlobalTeamMonitoringAnna($date);
        $v_anna = [];
        $v_anna = $this->dataSanitizerTeamMonitoring($v_anna,$this->n_anna,$this->head_global,$anna['data']);
        $update_range = $update_worksheet."!J:R";
        $this->updateTeamMonitoring($spreadsheetId,$v_anna,$update_range);

        $Carol = $page->dataGlobalTeamMonitoringCarol($date);
        $v_Carol = [];
        $v_Carol = $this->dataSanitizerTeamMonitoring($v_Carol,$this->n_Carol,$this->head_global,$Carol['data']);
        $update_range = $update_worksheet."!S:AA";
        $this->updateTeamMonitoring($spreadsheetId,$v_Carol,$update_range);

        $Eric = $page->dataGlobalTeamMonitoringEric($date);
        $v_Eric = [];
        $v_Eric = $this->dataSanitizerTeamMonitoring($v_Eric,$this->n_Eric,$this->head_global,$Eric['data']);
        $update_range = $update_worksheet."!AB:AJ";
        $this->updateTeamMonitoring($spreadsheetId,$v_Eric,$update_range);

        $Icha = $page->dataGlobalTeamMonitoringIcha($date);
        $v_Icha = [];
        $v_Icha = $this->dataSanitizerTeamMonitoring($v_Icha,$this->n_Icha,$this->head_global,$Icha['data']);
        $update_range = $update_worksheet."!AK:AS";
        $this->updateTeamMonitoring($spreadsheetId,$v_Icha,$update_range);

        $Lily = $page->dataGlobalTeamMonitoringLily($date);
        $v_Lily = [];
        $v_Lily = $this->dataSanitizerTeamMonitoring($v_Lily,$this->n_Lily,$this->head_global,$Lily['data']);
        $update_range = $update_worksheet."!AT:BB";
        $this->updateTeamMonitoring($spreadsheetId,$v_Lily,$update_range);

        $Maydewi = $page->dataGlobalTeamMonitoringMaydewi($date);
        $v_Maydewi = [];
        $v_Maydewi = $this->dataSanitizerTeamMonitoring($v_Maydewi,$this->n_Maydewi,$this->head_global,$Maydewi['data']);
        $update_range = $update_worksheet."!BC:BK";
        $this->updateTeamMonitoring($spreadsheetId,$v_Maydewi,$update_range);

        $Rani = $page->dataGlobalTeamMonitoringRani($date);
        $v_Rani = [];
        $v_Rani = $this->dataSanitizerTeamMonitoring($v_Rani,$this->n_Rani,$this->head_global,$Rani['data']);
        $update_range = $update_worksheet."!BL:BT";
        $this->updateTeamMonitoring($spreadsheetId,$v_Rani,$update_range);

        return 200;
    }
    public function ReportWeeklyGlobalFormat($page, $level){
        $Date = date('Y-m-d');
        $values = [];
        $DateWeekly = $page->WeekFromDate(date('Y-m'));
        $DateWeekly['startdate'] = array_reverse($DateWeekly['startdate']);
        $DateWeekly['enddate'] = array_reverse($DateWeekly['enddate']);
        $DateWeekly['c_week'] = array_reverse($DateWeekly['c_week']);
        foreach($DateWeekly['c_week'] as $key => $v_weekly){
            $startdate = $DateWeekly['startdate'][$key];
            $enddate = $DateWeekly['enddate'][$key];
            if($level == '1'){
                $values = $this->ReportWeeklyGlobalFormatSanitizer($values, $v_weekly, $startdate, $enddate);
            }else{
                if($Date >= date('Y-m-d',strtotime($startdate)) && $Date <= date('Y-m-d',strtotime($enddate))){
                    $values = $this->ReportWeeklyGlobalFormatSanitizer($values, $v_weekly, $startdate, $enddate);
                }
            }
            // dump($values);
        }
        return $values;
    }
    public function ReportWeeklyGlobalFormatSanitizer($values, $v_weekly, $startdate, $enddate){
        $page = $this->page;
        $head = $this->head_global;
        $n_ame = $this->n_ame;
        $n_anna = $this->n_anna;
        $n_Carol = $this->n_Carol;
        $n_Eric = $this->n_Eric;
        $n_Icha = $this->n_Icha;
        $n_Lily = $this->n_Lily;
        $n_Maydewi = $this->n_Maydewi;
        $n_Rani = $this->n_Rani;
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
            $d_ame['first_touch']->whereNotNull('first_touch')->count(),
            $d_ame['fu_1']->whereNotNull('fu_1')->count()+
            $d_ame['fu_2']->whereNotNull('fu_2')->count()+
            $d_ame['fu_3']->whereNotNull('fu_3')->count()+
            $d_ame['fu_4']->whereNotNull('fu_4')->count()+
            $d_ame['fu_5']->whereNotNull('fu_5')->count(),
            $d_ame['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $d_ame['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $d_ame['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $d_ame['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $d_ame['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $d_ame['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $d_ame['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $d_ame['email_sent']->whereNotNull('email_sent')->count(),
            $d_ame['sent_royalty']->whereNotNull('sent_royalty')->count()
        ];
        array_push($values, $v_ame);
        $v_anna = [
            $n_anna[0],
            $d_anna['daily']->whereNotNull('date')->count(),
            $d_anna['first_touch']->whereNotNull('first_touch')->count(),
            $d_anna['fu_1']->whereNotNull('fu_1')->count()+
            $d_anna['fu_2']->whereNotNull('fu_2')->count()+
            $d_anna['fu_3']->whereNotNull('fu_3')->count()+
            $d_anna['fu_4']->whereNotNull('fu_4')->count()+
            $d_anna['fu_5']->whereNotNull('fu_5')->count(),
            $d_anna['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $d_anna['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $d_anna['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $d_anna['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $d_anna['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $d_anna['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $d_anna['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $d_anna['email_sent']->whereNotNull('email_sent')->count(),
            $d_anna['sent_royalty']->whereNotNull('sent_royalty')->count()
        ];
        array_push($values, $v_anna);
        $v_carol = [
            $n_Carol[0],
            $d_carol['daily']->whereNotNull('date')->count(),
            $d_carol['first_touch']->whereNotNull('first_touch')->count(),
            $d_carol['fu_1']->whereNotNull('fu_1')->count()+
            $d_carol['fu_2']->whereNotNull('fu_2')->count()+
            $d_carol['fu_3']->whereNotNull('fu_3')->count()+
            $d_carol['fu_4']->whereNotNull('fu_4')->count()+
            $d_carol['fu_5']->whereNotNull('fu_5')->count(),
            $d_carol['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $d_carol['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $d_carol['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $d_carol['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $d_carol['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $d_carol['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $d_carol['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $d_carol['email_sent']->whereNotNull('email_sent')->count(),
            $d_carol['sent_royalty']->whereNotNull('sent_royalty')->count()
        ];
        array_push($values, $v_carol);
        $v_eric = [
            $n_Eric[0],
            $d_eric['daily']->whereNotNull('date')->count(),
            $d_eric['first_touch']->whereNotNull('first_touch')->count(),
            $d_eric['fu_1']->whereNotNull('fu_1')->count()+
            $d_eric['fu_2']->whereNotNull('fu_2')->count()+
            $d_eric['fu_3']->whereNotNull('fu_3')->count()+
            $d_eric['fu_4']->whereNotNull('fu_4')->count()+
            $d_eric['fu_5']->whereNotNull('fu_5')->count(),
            $d_eric['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $d_eric['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $d_eric['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $d_eric['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $d_eric['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $d_eric['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $d_eric['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $d_eric['email_sent']->whereNotNull('email_sent')->count(),
            $d_eric['sent_royalty']->whereNotNull('sent_royalty')->count()
        ];
        array_push($values, $v_eric);
        $v_icha = [
            $n_Icha[0],
            $d_icha['daily']->whereNotNull('date')->count(),
            $d_icha['first_touch']->whereNotNull('first_touch')->count(),
            $d_icha['fu_1']->whereNotNull('fu_1')->count()+
            $d_icha['fu_2']->whereNotNull('fu_2')->count()+
            $d_icha['fu_3']->whereNotNull('fu_3')->count()+
            $d_icha['fu_4']->whereNotNull('fu_4')->count()+
            $d_icha['fu_5']->whereNotNull('fu_5')->count(),
            $d_icha['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $d_icha['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $d_icha['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $d_icha['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $d_icha['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $d_icha['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $d_icha['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $d_icha['email_sent']->whereNotNull('email_sent')->count(),
            $d_icha['sent_royalty']->whereNotNull('sent_royalty')->count()
        ];
        array_push($values, $v_icha);
        $v_lily = [
            $n_Lily[0],
            $d_lily['daily']->whereNotNull('date')->count(),
            $d_lily['first_touch']->whereNotNull('first_touch')->count(),
            $d_lily['fu_1']->whereNotNull('fu_1')->count()+
            $d_lily['fu_2']->whereNotNull('fu_2')->count()+
            $d_lily['fu_3']->whereNotNull('fu_3')->count()+
            $d_lily['fu_4']->whereNotNull('fu_4')->count()+
            $d_lily['fu_5']->whereNotNull('fu_5')->count(),
            $d_lily['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $d_lily['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $d_lily['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $d_lily['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $d_lily['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $d_lily['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $d_lily['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $d_lily['email_sent']->whereNotNull('email_sent')->count(),
            $d_lily['sent_royalty']->whereNotNull('sent_royalty')->count()
        ];
        array_push($values, $v_lily);
        $v_maydewi = [
            $n_Maydewi[0],
            $d_maydewi['daily']->whereNotNull('date')->count(),
            $d_maydewi['first_touch']->whereNotNull('first_touch')->count(),
            $d_maydewi['fu_1']->whereNotNull('fu_1')->count()+
            $d_maydewi['fu_2']->whereNotNull('fu_2')->count()+
            $d_maydewi['fu_3']->whereNotNull('fu_3')->count()+
            $d_maydewi['fu_4']->whereNotNull('fu_4')->count()+
            $d_maydewi['fu_5']->whereNotNull('fu_5')->count(),
            $d_maydewi['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $d_maydewi['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $d_maydewi['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $d_maydewi['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $d_maydewi['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $d_maydewi['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $d_maydewi['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $d_maydewi['email_sent']->whereNotNull('email_sent')->count(),
            $d_maydewi['sent_royalty']->whereNotNull('sent_royalty')->count()
        ];
        array_push($values, $v_maydewi);
        $v_rani = [
            $n_Rani[0],
            $d_rani['daily']->whereNotNull('date')->count(),
            $d_rani['first_touch']->whereNotNull('first_touch')->count(),
            $d_rani['fu_1']->whereNotNull('fu_1')->count()+
            $d_rani['fu_2']->whereNotNull('fu_2')->count()+
            $d_rani['fu_3']->whereNotNull('fu_3')->count()+
            $d_rani['fu_4']->whereNotNull('fu_4')->count()+
            $d_rani['fu_5']->whereNotNull('fu_5')->count(),
            $d_rani['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $d_rani['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $d_rani['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $d_rani['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $d_rani['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $d_rani['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $d_rani['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $d_rani['email_sent']->whereNotNull('email_sent')->count(),
            $d_rani['sent_royalty']->whereNotNull('sent_royalty')->count()
        ];
        array_push($values, $v_rani);

        $x = ["Total"];
        foreach ($v_rani as $key => $value) {
            if($key == 0){continue;}
            array_push($x, $this->total([$v_ame,$v_anna,$v_carol,$v_eric,$v_icha,$v_lily,$v_maydewi,$v_rani], $key));
        }
        array_push($values, $x);
        $y = ["Average"];
        foreach ($v_rani as $key => $value) {
            if($key == 0){continue;}
            array_push($y, $this->average([$v_ame,$v_anna,$v_carol,$v_eric,$v_icha,$v_lily,$v_maydewi,$v_rani], $key));
        }
        array_push($values, $y);
        return $values;
    }
    public function TeamMonitoringIndo(){
        Artisan::call('set:team-monitoring-indo');
    }
    public function setTeamMonitoringIndo(){
        // $spreadsheetId = "1jxec-kRkWE_38Mnz1H3FgwTvsazJora1dt_79AqO-cc";

        $title = "Lv. 1 Indo Monitoring - ".$this->month_name;
        $folder_id = "1_HV9_g9bXz2zi1efjstfF5LMF93ePWIH";
        $create = $this->CreateNewSpreadsheet($title, $folder_id);
        $data = json_decode($create);
        $spreadsheetId = $data->spreadsheet_id;
        $page = $this->page;
        $date = $this->date_start.",".$this->date_end;

        try {
            $update_worksheet = "Lv 1 Monitoring Indo";
            $this->UpdateSheetProperties($spreadsheetId, $update_worksheet);
        } catch (\Throwable $th) {
            $update_worksheet = "Lv 1 Monitoring Indo";
        }

        $indo_ichanur = $page->dataIndoTeamMonitoringIchaNur($date);
        $v_indo_ichanur = [];
        $v_indo_ichanur = $this->dataSanitizerTeamMonitoring($v_indo_ichanur,$this->n_indo_icha,$this->head_indo,$indo_ichanur['data']);
        $update_range = $update_worksheet."!A:E";
        $this->updateTeamMonitoring($spreadsheetId,$v_indo_ichanur,$update_range);

        $indo_irel = $page->dataIndoTeamMonitoringIrel($date);
        $v_indo_irel = [];
        $v_indo_irel = $this->dataSanitizerTeamMonitoring($v_indo_irel,$this->n_indo_irel,$this->head_indo_b,$indo_irel['data']);
        $update_range = $update_worksheet."!F:I";
        $this->updateTeamMonitoring($spreadsheetId,$v_indo_irel,$update_range);

        try {
            $new_worksheet = "Weekly Report Indo";
            $this->CreateNewWorksheet($spreadsheetId,$new_worksheet);
        } catch (\Throwable $th) {
            $new_worksheet = "Weekly Report Indo";
        }

        /*--------------------------
        | FUNCTION FORMAT REPORT WEEKLY
        -----------------------------*/
        $update_range = $new_worksheet;
        $level = "1";
        $values = $this->ReportWeeklyIndoFormat($page,$level);
        $this->updateTeamMonitoring($spreadsheetId,$values,$update_range);

        return 200;
    }
    public function ReportWeeklyIndoFormat($page,$level){
        $values = [];
        $DateWeekly = $page->WeekFromDate(date('Y-m'));
        $DateWeekly['startdate'] = array_reverse($DateWeekly['startdate']);
        $DateWeekly['enddate'] = array_reverse($DateWeekly['enddate']);
        $DateWeekly['c_week'] = array_reverse($DateWeekly['c_week']);
        foreach($DateWeekly['c_week'] as $key => $v_weekly){
            $startdate = $DateWeekly['startdate'][$key];
            $enddate = $DateWeekly['enddate'][$key];
            $Date = date('Y-m-d');
            if($level == '1'){
                $values = $this->ReportWeeklyIndoFormatSanitizer($values,$v_weekly,$startdate,$enddate);
            }else{
                if($Date >= date('Y-m-d',strtotime($startdate)) && $Date <= date('Y-m-d',strtotime($enddate))){
                    $values = $this->ReportWeeklyIndoFormatSanitizer($values,$v_weekly,$startdate,$enddate);
                }
            }
        }
        return $values;
    }
    public function ReportWeeklyIndoFormatSanitizer($values,$v_weekly,$startdate,$enddate){
        $head = $this->head_indo;
        $head_b = $this->head_indo_b;
        $n_indo_icha = $this->n_indo_icha;
        $n_indo_irel = $this->n_indo_irel;
        $page = $this->page;
        $f_head = [
            $v_weekly." ".$this->month_name_now,
            $startdate,
            $enddate
        ];
        $d_ichanur = $page->DataIndoIchaNur($startdate,$enddate);
        array_push($values, $f_head);
        array_push($values, $head);
        $v_indo_icha = [
            $n_indo_icha[0],
            $d_ichanur['daily']->whereNotNull('date')->count(),
            $d_ichanur['fu_1']->whereNotNull('fu_1')->count()+
            $d_ichanur['fu_2']->whereNotNull('fu_2')->count()+
            $d_ichanur['fu_3']->whereNotNull('fu_3')->count()+
            $d_ichanur['fu_4']->whereNotNull('fu_4')->count()+
            $d_ichanur['fu_5']->whereNotNull('fu_5')->count(),
            $d_ichanur['data_sent']->whereNotNull('data_sent')->count(),
            "0"
        ];
        array_push($values, $v_indo_icha);

        $d_irel = $page->DataIndoIrel($startdate,$enddate);
        array_push($values, $head_b);
        $v_indo_irel = [
            $n_indo_irel[0],
            $d_irel['daily']->whereNotNull('date')->count(),
            $d_irel['fu_1']->whereNotNull('fu_1')->count()+
            $d_irel['fu_2']->whereNotNull('fu_2')->count()+
            $d_irel['fu_3']->whereNotNull('fu_3')->count()+
            $d_irel['fu_4']->whereNotNull('fu_4')->count()+
            $d_irel['fu_5']->whereNotNull('fu_5')->count()+
            $d_irel['fu_6']->whereNotNull('fu_6')->count()+
            $d_irel['fu_7']->whereNotNull('fu_7')->count()+
            $d_irel['fu_8']->whereNotNull('fu_8')->count()+
            $d_irel['fu_9']->whereNotNull('fu_9')->count()+
            $d_irel['fu_10']->whereNotNull('fu_10')->count(),
            $d_irel['date_solved']->whereNotNull('date_solved')->count(),
        ];
        array_push($values, $v_indo_irel);
        return $values;
    }
    public function dataSanitizerTeamMonitoring($values,$name,$head,$person_data){
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

    /*--------------------------
    | Lv 2 REPORT MONTHLY
    -----------------------------*/
    public function AllTeamReportWeekly(){
        Artisan::call('set:all-team-report-weekly');
    }
    public function setAllTeamReportWeekly(){
        // TRIAL
        // $spreadsheetId = "1jxec-kRkWE_38Mnz1H3FgwTvsazJora1dt_79AqO-cc";
        // REAL
        $spreadsheetId = "16xGw6KdeUzxASEnsXuIKIPawD5Dx6lSi52NohPp5u5s";

        /** ---------------------
        * DUPLICATE SPREADSHEET
        ------------------------- */
        $title = "Backup Weekly Lv. 2 Reports All Team Final - ".date('Y-m-d H:i:s');
        $folder_id = "1yclgli3iOw_E36t4EOCgSs9Bc5VhsMkN";
        $this->DuplicateSpreadsheet($spreadsheetId,$folder_id,$title);

        /*--------------------------
        | INSERT TO LEVEL 2 GLOBAL WEEKLY
        --------------------------------- */
        // TRIAL
        // $sheetId = 182102069;

        // REAL
        $sheetId = 0;

        $page = $this->page;
        $DateWeekly = $page->WeekFromDate(date('Y-m'));
        $new_worksheet = "Global Weekly Report";
        $level = "2";
        $values = $this->ReportWeeklyGlobalFormat($page, $level);
        $update_range = $new_worksheet."!A1:I1";
        $endindex = 12;
        $this->insertValuesIntoFirstRow($spreadsheetId,$values,$update_range,$endindex,$sheetId);

        /*--------------------------
        | INSERT TO LEVEL 2 INDO WEEKLY
        -----------------------------*/
        // TRIAL
        // $sheetId = 820330505;

        // REAL
        $sheetId = 441103606;

        $new_worksheet = "Indo Weekly Report";
        $level = "2";
        $values = $this->ReportWeeklyIndoFormat($page,$level);
        $update_range = $new_worksheet."!A1:I1";
        $endindex = 5;
        $this->insertValuesIntoFirstRow($spreadsheetId,$values,$update_range,$endindex,$sheetId);

        return 200;
    }
    public function AllTeamReportMonthly(){
        Artisan::call('set:all-team-report-monthly');
    }
    public function setAllTeamReportMonthly(){
        // TRIAL
        // $spreadsheetId = "1jxec-kRkWE_38Mnz1H3FgwTvsazJora1dt_79AqO-cc";
        // $sheetId = 468929916;

        // REAL
        $spreadsheetId = "16xGw6KdeUzxASEnsXuIKIPawD5Dx6lSi52NohPp5u5s";
        $sheetId = 1190393942;

        /** ---------------------
        * DUPLICATE SPREADSHEET
        ------------------------- */
        $title = "Backup Monthly Lv. 2 Reports All Team Final - ".date('Y-m-d H:i:s');
        $folder_id = "1xwJ_Meym3eVlsXC37c0aO-S3NDemv-tw";
        $this->DuplicateSpreadsheet($spreadsheetId,$folder_id,$title);

        /** ------------------------------
        * SET INTO SPREADSHEET FIRST ROW
        ---------------------------------- */
        $page = $this->page;
        $DateWeekly = $page->WeekFromDate(date('Y-m'));

        $new_worksheet = "Monthly Report";
        $date_start = date("Y-m-d", strtotime($DateWeekly['startdate'][0]));
        $date_end = date("Y-m-d", strtotime(end($DateWeekly['enddate'])));
        $date = $date_start.",".$date_end;
        $date = explode(",",$date);

        $values = [];

        $head_a = [date('F Y',strtotime($this->month))];
        array_push($values,$head_a);
        $head_ia = [
            "Indo Team",
            "Answer New Authors",
            "Follow Up Authors",
            "Royalty",
        ];
        array_push($values,$head_ia);
        $datas = $this->page->MonthlyReportDataIndo($date);
        foreach ($datas['data'] as $key => $value) {
            if($key == 0){
                $data['icha'] = [
                    $this->page->personIndo[$key],
                    $value[1],
                    $value[2],
                    $value[3],
                ];
            }
            else{
                $data['irel'] = [
                    $this->page->personIndo[$key],
                    $value[2],
                    $value[5],
                    $value[6],
                ];
            }
        }
        array_push($values, $data['icha']);
        $head_ib = [
            "",
            "Help",
            "Follow Up",
            "Solved Problem",
        ];
        array_push($values,$head_ib);
        array_push($values, $data['irel']);
        $head_g = [
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
        array_push($values,$head_g);
        $datas = $this->page->MonthlyReportDataGlobal($date);
        foreach ($datas['data'] as $key => $value) {
            if($key == 0){
                $value[0] = "Total";
                array_push($values, $value);
            }
            else if($key == 1){
                $value[0] = "Average";
                array_push($values, $value);
            }
            else{
                $value[0] = $this->page->personGlobal[$key-2];
                array_push($values, $value);
            }
        }
        $head_ic = [
            "Spam Team",
            "Platform",
            "Invitation Sent",
            "Author Replied"
        ];
        $datas = $this->page->MonthlyReportDataSpam($date);
        array_push($values,$head_ic);
        foreach ($datas['data'] as $key => $value) {
            array_push($values, $value);
        }
        // dd($values);
        $update_range = $new_worksheet."!A1:I1";
        $endindex = 22;
        $this->insertValuesIntoFirstRow($spreadsheetId,$values,$update_range,$endindex,$sheetId);
    }
}