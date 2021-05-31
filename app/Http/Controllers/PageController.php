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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PageController extends Controller
{
    private $personGlobal = [
        'Ame',
        'Anna',
        'Carol',
        'Eric',
        'Icha',
        'Lily',
        'Maydewi',
        'Rani'
    ];
    private $personIndo = [
        'Icha Nur',
        'Irel'
    ];
    public function DateDescribe($year, $month){
        $day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        for($i=1;$i<$day+1;$i++){
            $x = $year."-".(strlen($month)==1 ? "0".$month : $month)."-".(strlen($i)==1 ? "0".$i : $i);
            $date[] = date('Y-m-d', strtotime($x));
        }
        return $date;
    }
    public function dateRange( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
        $dates = [];
        $current = strtotime( $first );
        $last = strtotime( $last );
        while( $current <= $last+2 ) {
            $dates[] = date( $format, $current );
            $current = strtotime( $step, $current );
        }
        return $dates;
    }
    public function WeekFromDate($date){
        $textdt = date($date.'-01');
        $dt= strtotime( $textdt);
        $currdt=$dt;
        $nextmonth=strtotime($textdt."+1 month");
        $i=0;
        $date = [
            'c_week' => [],
            'startdate' => [],
            'enddate' => []
        ];
        do{
            $weekday= date("w",$currdt);
            $nextday=7-$weekday;
            $endday=abs($weekday-6);
            $startarr[$i]=$currdt;
            $endarr[$i]=strtotime(date("Y-m-d",$currdt)."+$endday day");
            $currdt=strtotime(date("Y-m-d",$endarr[$i])."+1 day");
            array_push($date['c_week'],"Week ".($i+1));
            array_push($date["startdate"], date("Y-m-d",$startarr[$i]));
            array_push($date["enddate"], date("Y-m-d",$endarr[$i]));
            $i++;
        }while($endarr[$i-1]<$nextmonth);
        return $date;
    }
    public function GetDateWeekly($request = false){
        $date_i = $request ?: request()->input('m');
        $yd = $this->WeekFromDate($date_i);
        foreach($yd['c_week'] as $key => $data){
            $startdate = $yd['startdate'][$key];
            $enddate = $yd['enddate'][$key];
            $date['option'][] = "<option value='$data,$startdate,$enddate'>$data - $startdate/$enddate</option>";
        }
        return $date;
    }
    public function index(){
        return view('admin.pages.home');
    }
    public function DailyReportAmes(){
        return view('admin.pages.daily-report.global.daily-report-ames');
    }
    public function DailyReportAnnas(){
        return view('admin.pages.daily-report.global.daily-report-anna');
    }
    public function DailyReportCarols(){
        return view('admin.pages.daily-report.global.daily-report-carol');
    }
    public function DailyReportErics(){
        return view('admin.pages.daily-report.global.daily-report-eric');
    }
    public function DailyReportIchas(){
        return view('admin.pages.daily-report.global.daily-report-icha');
    }
    public function DailyReportLilies(){
        return view('admin.pages.daily-report.global.daily-report-lily');
    }
    public function DailyReportMayDewis(){
        return view('admin.pages.daily-report.global.daily-report-maydewi');
    }
    public function DailyReportRanis(){
        return view('admin.pages.daily-report.global.daily-report-rani');
    }
    public function DailyReportIndoIchaNurs(){
        return view('admin.pages.daily-report.indo.daily-report-indo-icha-nur');
    }
    public function DailyReportIndoIrels(){
        return view('admin.pages.daily-report.indo.daily-report-indo-irel');
    }
    public function GlobalTeamMonitoring(){
        $person = $this->personGlobal;
        return view('admin.pages.team-monitoring.global',[
            'person' => $person
        ]);
    }
    public function IndoTeamMonitoring(){
        $person = $this->personIndo;
        return view('admin.pages.team-monitoring.indo',[
            'person' => $person
        ]);
    }
    public function MonthlyReport(){
        return view('admin.pages.all-report.monthly');
    }
    public function WeeklyReport(){
        return view('admin.pages.all-report.weekly');
    }

    /*---------------------------------------
    | LV 0 DAILY REPORT
    -----------------------------------------*/
    public function getDailyReportAmes(){
        $query = DailyReportAme::orderBy('id',
        'DESC')->limit(10000)->get();
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "No.",
            "Date",
            "Status",
            "Media",
            "Author Contact",
            "Inquiries",
            "Platform",
            "Username",
            "Title",
            "Webnovel Username",
            "CBID/Book ID",
            "Title",
            "Genre",
            "Plot",
            "4K+?",
            "Maintain Account",
            "Follow up 1",
            "Follow up 2",
            "Follow up 3",
            "Follow up 4",
            "Follow up 5",
            "Data Sent Royalty",
            "Data Sent Non Exclusive",
            "Marker",
            "Old/New Book"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        foreach($query as $key => $data){
            array_push($data_array['data'], [
                $data->id,
                $data->date,
                $data->status,
                $data->media,
                $data->author_contact,
                $data->inquiries,
                $data->platform,
                $data->platform_user,
                $data->platform_title,
                $data->username,
                $data->cbid,
                $data->title,
                $data->genre,
                $data->plot,
                $data->k4,
                $data->maintain_account,
                $data->fu_1,
                $data->fu_2,
                $data->fu_3,
                $data->fu_4,
                $data->fu_5,
                $data->sent_royalty,
                $data->sent_non_exclusive,
                $data->marker,
                $data->old_new_book
            ]);
        }
        return $data_array;
    }
    public function getDailyReportAnnas(){
        $query = DailyReportAnna::orderBy('id','DESC')->limit(10000)->get();
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = ["No.",
        "Date",
        "Status",
        "Media",
        "Author Contact",
        "Inquiries",
        "Platform",
        "Username",
        "Title",
        "Webnovel Username",
        "CBID/Book ID",
        "Title",
        "Genre",
        "Plot",
        "4K+?",
        "Maintain Account",
        "Follow up 1",
        "Follow up 2",
        "Follow up 3",
        "Follow up 4",
        "Follow up 5",
        "Data Sent Royalty",
        "Data Sent Non Exclusive",
        "Marker",
        "Old/New Book"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        foreach($query as $key => $data){
            array_push($data_array['data'], [
                $data->id,
                $data->date,
                $data->status,
                $data->media,
                $data->author_contact,
                $data->inquiries,
                $data->platform,
                $data->platform_user,
                $data->platform_title,
                $data->username,
                $data->cbid,
                $data->title,
                $data->genre,
                $data->plot,
                $data->k4,
                $data->maintain_account,
                $data->fu_1,
                $data->fu_2,
                $data->fu_3,
                $data->fu_4,
                $data->fu_5,
                $data->sent_royalty,
                $data->sent_non_exclusive,
                $data->marker,
                $data->old_new_book
            ]);
        }
        return $data_array;
    }
    public function getDailyReportCarols(){
        $query = DailyReportCarol::orderBy('id','DESC')->limit(10000)->get();
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = ["No.",
        "Date",
        "Status",
        "Media",
        "Author Contact",
        "Inquiries",
        "Platform",
        "Username",
        "Title",
        "Webnovel Username",
        "CBID/Book ID",
        "Title",
        "Genre",
        "Plot",
        "4K+?",
        "Maintain Account",
        "Follow up 1",
        "Follow up 2",
        "Follow up 3",
        "Follow up 4",
        "Follow up 5",
        "Data Sent Royalty",
        "Data Sent Non Exclusive",
        "Marker",
        "Old/New Book"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        foreach($query as $key => $data){
            array_push($data_array['data'], [
                $data->id,
                $data->date,
                $data->status,
                $data->media,
                $data->author_contact,
                $data->inquiries,
                $data->platform,
                $data->platform_user,
                $data->platform_title,
                $data->username,
                $data->cbid,
                $data->title,
                $data->genre,
                $data->plot,
                $data->k4,
                $data->maintain_account,
                $data->fu_1,
                $data->fu_2,
                $data->fu_3,
                $data->fu_4,
                $data->fu_5,
                $data->sent_royalty,
                $data->sent_non_exclusive,
                $data->marker,
                $data->old_new_book
            ]);
        }
        return $data_array;
    }
    public function getDailyReportErics(){
        $query = DailyReportEric::orderBy('id','DESC')->limit(10000)->get();
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = ["No.",
        "Date",
        "Status",
        "Media",
        "Author Contact",
        "Inquiries",
        "Platform",
        "Username",
        "Title",
        "Webnovel Username",
        "CBID/Book ID",
        "Title",
        "Genre",
        "Plot",
        "4K+?",
        "Maintain Account",
        "Follow up 1",
        "Follow up 2",
        "Follow up 3",
        "Follow up 4",
        "Follow up 5",
        "Data Sent Royalty",
        "Data Sent Non Exclusive",
        "Marker",
        "Old/New Book"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        foreach($query as $key => $data){
            array_push($data_array['data'], [
                $data->id,
                $data->date,
                $data->status,
                $data->media,
                $data->author_contact,
                $data->inquiries,
                $data->platform,
                $data->platform_user,
                $data->platform_title,
                $data->username,
                $data->cbid,
                $data->title,
                $data->genre,
                $data->plot,
                $data->k4,
                $data->maintain_account,
                $data->fu_1,
                $data->fu_2,
                $data->fu_3,
                $data->fu_4,
                $data->fu_5,
                $data->sent_royalty,
                $data->sent_non_exclusive,
                $data->marker,
                $data->old_new_book
            ]);
        }
        return $data_array;
    }
    public function getDailyReportIchas(){
        $query = DailyReportIcha::orderBy('id','DESC')->limit(10000)->get();
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = ["No.",
        "Date",
        "Status",
        "Media",
        "Author Contact",
        "Inquiries",
        "Platform",
        "Username",
        "Title",
        "Webnovel Username",
        "CBID/Book ID",
        "Title",
        "Genre",
        "Plot",
        "4K+?",
        "Maintain Account",
        "Follow up 1",
        "Follow up 2",
        "Follow up 3",
        "Follow up 4",
        "Follow up 5",
        "Data Sent Royalty",
        "Data Sent Non Exclusive",
        "Marker",
        "Old/New Book"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        foreach($query as $key => $data){
            array_push($data_array['data'], [
                $data->id,
                $data->date,
                $data->status,
                $data->media,
                $data->author_contact,
                $data->inquiries,
                $data->platform,
                $data->platform_user,
                $data->platform_title,
                $data->username,
                $data->cbid,
                $data->title,
                $data->genre,
                $data->plot,
                $data->k4,
                $data->maintain_account,
                $data->fu_1,
                $data->fu_2,
                $data->fu_3,
                $data->fu_4,
                $data->fu_5,
                $data->sent_royalty,
                $data->sent_non_exclusive,
                $data->marker,
                $data->old_new_book
            ]);
        }
        return $data_array;
    }
    public function getDailyReportLilies(){
        $query = DailyReportLily::orderBy('id','DESC')->limit(10000)->get();
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = ["No.",
        "Date",
        "Status",
        "Media",
        "Author Contact",
        "Inquiries",
        "Platform",
        "Username",
        "Title",
        "Webnovel Username",
        "CBID/Book ID",
        "Title",
        "Genre",
        "Plot",
        "4K+?",
        "Maintain Account",
        "Follow up 1",
        "Follow up 2",
        "Follow up 3",
        "Follow up 4",
        "Follow up 5",
        "Data Sent Royalty",
        "Data Sent Non Exclusive",
        "Marker",
        "Old/New Book"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        foreach($query as $key => $data){
            array_push($data_array['data'], [
                $data->id,
                $data->date,
                $data->status,
                $data->media,
                $data->author_contact,
                $data->inquiries,
                $data->platform,
                $data->platform_user,
                $data->platform_title,
                $data->username,
                $data->cbid,
                $data->title,
                $data->genre,
                $data->plot,
                $data->k4,
                $data->maintain_account,
                $data->fu_1,
                $data->fu_2,
                $data->fu_3,
                $data->fu_4,
                $data->fu_5,
                $data->sent_royalty,
                $data->sent_non_exclusive,
                $data->marker,
                $data->old_new_book
            ]);
        }
        return $data_array;
    }
    public function getDailyReportMayDewis(){
        $query = DailyReportMaydewi::orderBy('id','DESC')->limit(10000)->get();
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = ["No.",
        "Date",
        "Status",
        "Media",
        "Author Contact",
        "Inquiries",
        "Platform",
        "Username",
        "Title",
        "Webnovel Username",
        "CBID/Book ID",
        "Title",
        "Genre",
        "Plot",
        "4K+?",
        "Maintain Account",
        "Follow up 1",
        "Follow up 2",
        "Follow up 3",
        "Follow up 4",
        "Follow up 5",
        "Data Sent Royalty",
        "Data Sent Non Exclusive",
        "Marker",
        "Old/New Book"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        foreach($query as $key => $data){
            array_push($data_array['data'], [
                $data->id,
                $data->date,
                $data->status,
                $data->media,
                $data->author_contact,
                $data->inquiries,
                $data->platform,
                $data->platform_user,
                $data->platform_title,
                $data->username,
                $data->cbid,
                $data->title,
                $data->genre,
                $data->plot,
                $data->k4,
                $data->maintain_account,
                $data->fu_1,
                $data->fu_2,
                $data->fu_3,
                $data->fu_4,
                $data->fu_5,
                $data->sent_royalty,
                $data->sent_non_exclusive,
                $data->marker,
                $data->old_new_book
            ]);
        }
        return $data_array;
    }
    public function getDailyReportRanis(){
        $query = DailyReportRani::orderBy('id','DESC')->limit(10000)->get();
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = ["No.",
        "Date",
        "Status",
        "Media",
        "Author Contact",
        "Inquiries",
        "Platform",
        "Username",
        "Title",
        "Webnovel Username",
        "CBID/Book ID",
        "Title",
        "Genre",
        "Plot",
        "4K+?",
        "Maintain Account",
        "Follow up 1",
        "Follow up 2",
        "Follow up 3",
        "Follow up 4",
        "Follow up 5",
        "Data Sent Royalty",
        "Data Sent Non Exclusive",
        "Marker",
        "Old/New Book"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        foreach($query as $key => $data){
            array_push($data_array['data'], [
                $data->id,
                $data->date,
                $data->status,
                $data->media,
                $data->author_contact,
                $data->inquiries,
                $data->platform,
                $data->platform_user,
                $data->platform_title,
                $data->username,
                $data->cbid,
                $data->title,
                $data->genre,
                $data->plot,
                $data->k4,
                $data->maintain_account,
                $data->fu_1,
                $data->fu_2,
                $data->fu_3,
                $data->fu_4,
                $data->fu_5,
                $data->sent_royalty,
                $data->sent_non_exclusive,
                $data->marker,
                $data->old_new_book
            ]);
        }
        return $data_array;
    }
    public function getDailyReportIndoIchaNurs(){
        $query = DailyReportIndoIchaNur::orderBy('id','DESC')->limit(10000)->get();
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "No.",
            "Date",
            "Contact Way",
            "Author Contact",
            "Platform",
            "Status",
            "Inquiries",
            "New CBID",
            "Old CBID",
            "Author",
            "Title",
            "Genre",
            "4K+?",
            "Plot",
            "Maintain Account",
            "Follow up 1",
            "Follow up 2",
            "Follow up 3",
            "Follow up 4",
            "Follow up 5",
            "Data Sent",
            "Marker",
            "Old/New Book"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        foreach($query as $key => $data){
            array_push($data_array['data'], [
                $data->id,
                $data->date,
                $data->contact_way,
                $data->author_contact,
                $data->platform,
                $data->status,
                $data->inquiries,
                $data->new_cbid,
                $data->old_cbid,
                $data->author,
                $data->title,
                $data->genre,
                $data->k4,
                $data->plot,
                $data->maintain_account,
                $data->fu_1,
                $data->fu_2,
                $data->fu_3,
                $data->fu_4,
                $data->fu_5,
                $data->data_sent,
                $data->marker,
                $data->old_new_book
            ]);
        }
        return $data_array;
    }
    public function getDailyReportIndoIrels(){
        $query = DailyReportIndoIrel::orderBy('id','DESC')->limit(10000)->get();
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "No.",
            "Date",
            "Status",
            "Date Solved",
            "Author Contact",
            "Inquiries",
            "CBID",
            "Title",
            "Author",
            "Zoom Tutorial",
            "Follow up 1",
            "Follow up 2",
            "Follow up 3",
            "Follow up 4",
            "Follow up 5",
            "Follow up 6",
            "Follow up 7",
            "Follow up 8",
            "Follow up 9",
            "Follow up 10"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        foreach($query as $key => $data){
            array_push($data_array['data'], [
                $data->id,
                $data->date,
                $data->status,
                $data->date_solved,
                $data->author_contact,
                $data->inquiries,
                $data->cbid,
                $data->title,
                $data->author,
                $data->zoom_tutorial,
                $data->fu_1,
                $data->fu_2,
                $data->fu_3,
                $data->fu_4,
                $data->fu_5,
                $data->fu_6,
                $data->fu_7,
                $data->fu_8,
                $data->fu_9,
                $data->fu_10,
            ]);
        }
        return $data_array;
    }

    /*---------------------------------------
    | LV 1 ALL TEAM REPORT
    -----------------------------------------*/
    public function DataAme($startdate, $enddate){
        $d['daily'] = DailyReportAme::whereBetween('date', [$startdate,$enddate])
            ->orderBy('id','DESC')
            ->get();
        $editor = 'Ashley';
        $d['non_ex'] = $this->DataNonExclusive($startdate,$enddate,$editor);
        return $d;
    }
    public function DataAnna($startdate, $enddate){
        $d['daily'] = DailyReportAnna::whereBetween('date', [$startdate,$enddate])
            ->orderBy('id','DESC')
            ->get();
        $editor = 'Erica';
        $d['non_ex'] = $this->DataNonExclusive($startdate,$enddate,$editor);
        return $d;
    }
    public function DataCarol($startdate, $enddate){
        $d['daily'] = DailyReportCarol::whereBetween('date', [$startdate,$enddate])
            ->orderBy('id','DESC')
            ->get();
        $editor = 'Destiny';
        $d['non_ex'] = $this->DataNonExclusive($startdate,$enddate,$editor);
        return $d;
    }
    public function DataEric($startdate, $enddate){
        $d['daily'] = DailyReportEric::whereBetween('date', [$startdate,$enddate])
            ->orderBy('id','DESC')
            ->get();
        $editor = 'Cornelia';
        $d['non_ex'] = $this->DataNonExclusive($startdate,$enddate,$editor);
        return $d;
    }
    public function DataIcha($startdate, $enddate){
        $d['daily'] = DailyReportIcha::whereBetween('date', [$startdate,$enddate])
            ->orderBy('id','DESC')
            ->get();
        $editor = 'Claire';
        $d['non_ex'] = $this->DataNonExclusive($startdate,$enddate,$editor);
        return $d;
    }
    public function DataLily($startdate, $enddate){
        $d['daily'] = DailyReportLily::whereBetween('date', [$startdate,$enddate])
            ->orderBy('id','DESC')
            ->get();
        $editor = 'Ensia';
        $d['non_ex'] = $this->DataNonExclusive($startdate,$enddate,$editor);
        return $d;
    }
    public function DataMaydewi($startdate, $enddate){
        $d['daily'] = DailyReportMaydewi::whereBetween('date', [$startdate,$enddate])
            ->orderBy('id','DESC')
            ->get();
        $editor = 'Serena';
        $d['non_ex'] = $this->DataNonExclusive($startdate,$enddate,$editor);
        return $d;
    }
    public function DataRani($startdate, $enddate){
        $d['daily'] = DailyReportRani::whereBetween('date', [$startdate,$enddate])
            ->orderBy('id','DESC')
            ->get();
        $editor = 'Aurora';
        $d['non_ex'] = $this->DataNonExclusive($startdate,$enddate,$editor);
        return $d;
    }
    public function DataNonExclusive($startdate, $enddate, $editor){
        $dn = NonExclusiveReport::where('global_editor', '=', $editor)
            ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
            ->get();
        return $dn;
    }
    public function getGlobalTeamMonitoring(Request $request){
        $person = Str::slug($request->input('mod'));
        $month = $request->input('mon');
        $date_start = date($month."-d", strtotime("first day of this month"));
        $date_end = date($month."-d", strtotime("last day of this month"));
        $date = $date_start.", ".$date_end;
        if($person == 'ame'){
            $data_array = $this->dataGlobalTeamMonitoringAme($date);
        }
        else if($person == 'anna'){
            $data_array = $this->dataGlobalTeamMonitoringAnna($date);
        }
        else if($person == 'carol'){
            $data_array = $this->dataGlobalTeamMonitoringCarol($date);
        }
        else if($person == 'eric'){
            $data_array = $this->dataGlobalTeamMonitoringEric($date);
        }
        else if($person == 'icha'){
            $data_array = $this->dataGlobalTeamMonitoringIcha($date);
        }
        else if($person == 'lily'){
            $data_array = $this->dataGlobalTeamMonitoringLily($date);
        }
        else if($person == 'maydewi'){
            $data_array = $this->dataGlobalTeamMonitoringMaydewi($date);
        }
        else if($person == 'rani'){
            $data_array = $this->dataGlobalTeamMonitoringRani($date);
        }
        else{
            $data_array = [];
        }
        return $data_array;
    }
    public function dataGlobalTeamMonitoringAme($date){
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "No.",
            "Date",
            "Answer",
            "N. Auth Non Ex",
            "Follow Up",
            "Follow Up Non Ex",
            "Sent E-Contract",
            "Rec. E-Contract",
            "Non Exclusive",
            "Royalty"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $xdate = explode(",", $date);
        $d = $this->DataAme($xdate[0], $xdate[1]);
        // dd($d);
        $dateRange = $this->dateRange($xdate[0],$xdate[1]);
        $no = 1;
        array_push($data_array['data'], [
            $no++,
            "Monthly Total",
            $d['daily']->whereNotNull('date')->count(),
            $d['non_ex']->whereNotNull('first_touch')->count(),
            $d['daily']->whereNotNull('fu_1')->count()+$d['daily']->whereNotNull('fu_2')->count()+$d['daily']->whereNotNull('fu_3')->count()+$d['daily']->whereNotNull('fu_4')->count()+$d['daily']->whereNotNull('fu_5')->count(),
            $d['non_ex']->whereNotNull('fu_non_ex_1')->count()+$d['non_ex']->whereNotNull('fu_non_ex_2')->count()+$d['non_ex']->whereNotNull('fu_non_ex_3')->count()+$d['non_ex']->whereNotNull('fu_non_ex_4')->count()+$d['non_ex']->whereNotNull('fu_non_ex_5')->count(),
            $d['non_ex']->whereNotNull('sent_e_contract')->count(),
            $d['non_ex']->whereNotNull('rec_e_contract')->count(),
            $d['non_ex']->whereNotNull('email_sent')->count(),
            $d['daily']->whereNotNull('sent_royalty')->count()
        ]);
        foreach($dateRange as $key => $date){
            $answer = [];
            $n_auth_non_ex = [];
            $fu_1 = [];
            $fu_2 = [];
            $fu_3 = [];
            $fu_4 = [];
            $fu_5 = [];
            $fu_non_ex_1 = [];
            $fu_non_ex_2 = [];
            $fu_non_ex_3 = [];
            $fu_non_ex_4 = [];
            $fu_non_ex_5 = [];
            $royalty = [];
            $sent_e = [];
            $rec_e = [];
            $non_ex = [];
            foreach ($d['daily']->where('date','=',$date) as $key => $dv) {
                $dv->date!=null ? array_push($answer, $dv->date) : null;
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($d['non_ex']->where('date','=',$date) as $key => $dv) {
                // dump($dv);
                $dv->first_touch!=null ? array_push($n_auth_non_ex, $dv->first_touch) : null;
                $dv->fu_non_ex_1!=null ? array_push($fu_non_ex_1, $dv->fu_non_ex_1) : null;
                $dv->fu_non_ex_2!=null ? array_push($fu_non_ex_2, $dv->fu_non_ex_2) : null;
                $dv->fu_non_ex_3!=null ? array_push($fu_non_ex_3, $dv->fu_non_ex_3) : null;
                $dv->fu_non_ex_4!=null ? array_push($fu_non_ex_4, $dv->fu_non_ex_4) : null;
                $dv->fu_non_ex_5!=null ? array_push($fu_non_ex_5, $dv->fu_non_ex_5) : null;
                $dv->sent_e_contract!=null ? array_push($sent_e, $dv->sent_e_contract) : null;
                $dv->rec_e_contract!=null ? array_push($rec_e, $dv->rec_e_contract) : null;
                $dv->email_sent!=null ? array_push($non_ex, $dv->email_sent) : null;
            }
            $c_answer = count($answer);
            $c_n_auth_non_ex = count($n_auth_non_ex);
            $c_fu = count($fu_1)+count($fu_2)+count($fu_3)+count($fu_4)+count($fu_5);
            $c_fu_non_ex = count($fu_non_ex_1)+count($fu_non_ex_2)+count($fu_non_ex_3)+count($fu_non_ex_4)+count($fu_non_ex_5);
            $c_royalty = count($royalty);
            $c_sent_e = count($sent_e);
            $c_rec_e = count($rec_e);
            $c_non_ex = count($non_ex);
            array_push($data_array['data'], [
                $no++,date('d/m/Y',strtotime($date)),$c_answer,$c_n_auth_non_ex,$c_fu,$c_fu_non_ex,$c_sent_e,$c_rec_e,$c_non_ex,$c_royalty
            ]);
        }
        return $data_array;
    }
    public function dataGlobalTeamMonitoringAnna($date){
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "No.",
            "Date",
            "Answer",
            "N. Auth Non Ex",
            "Follow Up",
            "Follow Up Non Ex",
            "Sent E-Contract",
            "Rec. E-Contract",
            "Non Exclusive",
            "Royalty"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $xdate = explode(",", $date);
        $d = $this->DataAnna($xdate[0], $xdate[1]);
        // dd($d);
        $dateRange = $this->dateRange($xdate[0],$xdate[1]);
        $no = 1;
        array_push($data_array['data'], [
            $no++,
            "Monthly Total",
            $d['daily']->whereNotNull('date')->count(),
            $d['non_ex']->whereNotNull('first_touch')->count(),
            $d['daily']->whereNotNull('fu_1')->count()+$d['daily']->whereNotNull('fu_2')->count()+$d['daily']->whereNotNull('fu_3')->count()+$d['daily']->whereNotNull('fu_4')->count()+$d['daily']->whereNotNull('fu_5')->count(),
            $d['non_ex']->whereNotNull('fu_non_ex_1')->count()+$d['non_ex']->whereNotNull('fu_non_ex_2')->count()+$d['non_ex']->whereNotNull('fu_non_ex_3')->count()+$d['non_ex']->whereNotNull('fu_non_ex_4')->count()+$d['non_ex']->whereNotNull('fu_non_ex_5')->count(),
            $d['non_ex']->whereNotNull('sent_e_contract')->count(),
            $d['non_ex']->whereNotNull('rec_e_contract')->count(),
            $d['non_ex']->whereNotNull('email_sent')->count(),
            $d['daily']->whereNotNull('sent_royalty')->count()
        ]);
        foreach($dateRange as $key => $date){
            $answer = [];
            $n_auth_non_ex = [];
            $fu_1 = [];
            $fu_2 = [];
            $fu_3 = [];
            $fu_4 = [];
            $fu_5 = [];
            $fu_non_ex_1 = [];
            $fu_non_ex_2 = [];
            $fu_non_ex_3 = [];
            $fu_non_ex_4 = [];
            $fu_non_ex_5 = [];
            $royalty = [];
            $sent_e = [];
            $rec_e = [];
            $non_ex = [];
            foreach ($d['daily']->where('date','=',$date) as $key => $dv) {
                $dv->date!=null ? array_push($answer, $dv->date) : null;
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($d['non_ex']->where('date','=',$date) as $key => $dv) {
                // dump($dv);
                $dv->first_touch!=null ? array_push($n_auth_non_ex, $dv->first_touch) : null;
                $dv->fu_non_ex_1!=null ? array_push($fu_non_ex_1, $dv->fu_non_ex_1) : null;
                $dv->fu_non_ex_2!=null ? array_push($fu_non_ex_2, $dv->fu_non_ex_2) : null;
                $dv->fu_non_ex_3!=null ? array_push($fu_non_ex_3, $dv->fu_non_ex_3) : null;
                $dv->fu_non_ex_4!=null ? array_push($fu_non_ex_4, $dv->fu_non_ex_4) : null;
                $dv->fu_non_ex_5!=null ? array_push($fu_non_ex_5, $dv->fu_non_ex_5) : null;
                $dv->sent_e_contract!=null ? array_push($sent_e, $dv->sent_e_contract) : null;
                $dv->rec_e_contract!=null ? array_push($rec_e, $dv->rec_e_contract) : null;
                $dv->email_sent!=null ? array_push($non_ex, $dv->email_sent) : null;
            }
            $c_answer = count($answer);
            $c_n_auth_non_ex = count($n_auth_non_ex);
            $c_fu = count($fu_1)+count($fu_2)+count($fu_3)+count($fu_4)+count($fu_5);
            $c_fu_non_ex = count($fu_non_ex_1)+count($fu_non_ex_2)+count($fu_non_ex_3)+count($fu_non_ex_4)+count($fu_non_ex_5);
            $c_royalty = count($royalty);
            $c_sent_e = count($sent_e);
            $c_rec_e = count($rec_e);
            $c_non_ex = count($non_ex);
            array_push($data_array['data'], [
                $no++,date('d/m/Y',strtotime($date)),$c_answer,$c_n_auth_non_ex,$c_fu,$c_fu_non_ex,$c_sent_e,$c_rec_e,$c_non_ex,$c_royalty
            ]);
        }
        return $data_array;
    }
    public function dataGlobalTeamMonitoringCarol($date){
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "No.",
            "Date",
            "Answer",
            "N. Auth Non Ex",
            "Follow Up",
            "Follow Up Non Ex",
            "Sent E-Contract",
            "Rec. E-Contract",
            "Non Exclusive",
            "Royalty"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $xdate = explode(",", $date);
        $d = $this->DataCarol($xdate[0], $xdate[1]);
        // dd($d);
        $dateRange = $this->dateRange($xdate[0],$xdate[1]);
        $no = 1;
        array_push($data_array['data'], [
            $no++,
            "Monthly Total",
            $d['daily']->whereNotNull('date')->count(),
            $d['non_ex']->whereNotNull('first_touch')->count(),
            $d['daily']->whereNotNull('fu_1')->count()+$d['daily']->whereNotNull('fu_2')->count()+$d['daily']->whereNotNull('fu_3')->count()+$d['daily']->whereNotNull('fu_4')->count()+$d['daily']->whereNotNull('fu_5')->count(),
            $d['non_ex']->whereNotNull('fu_non_ex_1')->count()+$d['non_ex']->whereNotNull('fu_non_ex_2')->count()+$d['non_ex']->whereNotNull('fu_non_ex_3')->count()+$d['non_ex']->whereNotNull('fu_non_ex_4')->count()+$d['non_ex']->whereNotNull('fu_non_ex_5')->count(),
            $d['non_ex']->whereNotNull('sent_e_contract')->count(),
            $d['non_ex']->whereNotNull('rec_e_contract')->count(),
            $d['non_ex']->whereNotNull('email_sent')->count(),
            $d['daily']->whereNotNull('sent_royalty')->count()
        ]);
        foreach($dateRange as $key => $date){
            $answer = [];
            $n_auth_non_ex = [];
            $fu_1 = [];
            $fu_2 = [];
            $fu_3 = [];
            $fu_4 = [];
            $fu_5 = [];
            $fu_non_ex_1 = [];
            $fu_non_ex_2 = [];
            $fu_non_ex_3 = [];
            $fu_non_ex_4 = [];
            $fu_non_ex_5 = [];
            $royalty = [];
            $sent_e = [];
            $rec_e = [];
            $non_ex = [];
            foreach ($d['daily']->where('date','=',$date) as $key => $dv) {
                $dv->date!=null ? array_push($answer, $dv->date) : null;
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($d['non_ex']->where('date','=',$date) as $key => $dv) {
                // dump($dv);
                $dv->first_touch!=null ? array_push($n_auth_non_ex, $dv->first_touch) : null;
                $dv->fu_non_ex_1!=null ? array_push($fu_non_ex_1, $dv->fu_non_ex_1) : null;
                $dv->fu_non_ex_2!=null ? array_push($fu_non_ex_2, $dv->fu_non_ex_2) : null;
                $dv->fu_non_ex_3!=null ? array_push($fu_non_ex_3, $dv->fu_non_ex_3) : null;
                $dv->fu_non_ex_4!=null ? array_push($fu_non_ex_4, $dv->fu_non_ex_4) : null;
                $dv->fu_non_ex_5!=null ? array_push($fu_non_ex_5, $dv->fu_non_ex_5) : null;
                $dv->sent_e_contract!=null ? array_push($sent_e, $dv->sent_e_contract) : null;
                $dv->rec_e_contract!=null ? array_push($rec_e, $dv->rec_e_contract) : null;
                $dv->email_sent!=null ? array_push($non_ex, $dv->email_sent) : null;
            }
            $c_answer = count($answer);
            $c_n_auth_non_ex = count($n_auth_non_ex);
            $c_fu = count($fu_1)+count($fu_2)+count($fu_3)+count($fu_4)+count($fu_5);
            $c_fu_non_ex = count($fu_non_ex_1)+count($fu_non_ex_2)+count($fu_non_ex_3)+count($fu_non_ex_4)+count($fu_non_ex_5);
            $c_royalty = count($royalty);
            $c_sent_e = count($sent_e);
            $c_rec_e = count($rec_e);
            $c_non_ex = count($non_ex);
            array_push($data_array['data'], [
                $no++,date('d/m/Y',strtotime($date)),$c_answer,$c_n_auth_non_ex,$c_fu,$c_fu_non_ex,$c_sent_e,$c_rec_e,$c_non_ex,$c_royalty
            ]);
        }
        return $data_array;
    }
    public function dataGlobalTeamMonitoringEric($date){
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "No.",
            "Date",
            "Answer",
            "N. Auth Non Ex",
            "Follow Up",
            "Follow Up Non Ex",
            "Sent E-Contract",
            "Rec. E-Contract",
            "Non Exclusive",
            "Royalty"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $xdate = explode(",", $date);
        $d = $this->DataEric($xdate[0], $xdate[1]);
        // dd($d);
        $dateRange = $this->dateRange($xdate[0],$xdate[1]);
        $no = 1;
        array_push($data_array['data'], [
            $no++,"Monthly Total",
            $d['daily']->whereNotNull('date')->count(),
            $d['non_ex']->whereNotNull('first_touch')->count(),
            $d['daily']->whereNotNull('fu_1')->count()+$d['daily']->whereNotNull('fu_2')->count()+$d['daily']->whereNotNull('fu_3')->count()+$d['daily']->whereNotNull('fu_4')->count()+$d['daily']->whereNotNull('fu_5')->count(),
            $d['non_ex']->whereNotNull('fu_non_ex_1')->count()+$d['non_ex']->whereNotNull('fu_non_ex_2')->count()+$d['non_ex']->whereNotNull('fu_non_ex_3')->count()+$d['non_ex']->whereNotNull('fu_non_ex_4')->count()+$d['non_ex']->whereNotNull('fu_non_ex_5')->count(),
            $d['non_ex']->whereNotNull('sent_e_contract')->count(),
            $d['non_ex']->whereNotNull('rec_e_contract')->count(),
            $d['non_ex']->whereNotNull('email_sent')->count(),
            $d['daily']->whereNotNull('sent_royalty')->count()
        ]);
        foreach($dateRange as $key => $date){
            $answer = [];
            $n_auth_non_ex = [];
            $fu_1 = [];
            $fu_2 = [];
            $fu_3 = [];
            $fu_4 = [];
            $fu_5 = [];
            $fu_non_ex_1 = [];
            $fu_non_ex_2 = [];
            $fu_non_ex_3 = [];
            $fu_non_ex_4 = [];
            $fu_non_ex_5 = [];
            $royalty = [];
            $sent_e = [];
            $rec_e = [];
            $non_ex = [];
            foreach ($d['daily']->where('date','=',$date) as $key => $dv) {
                $dv->date!=null ? array_push($answer, $dv->date) : null;
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($d['non_ex']->where('date','=',$date) as $key => $dv) {
                // dump($dv);
                $dv->first_touch!=null ? array_push($n_auth_non_ex, $dv->first_touch) : null;
                $dv->fu_non_ex_1!=null ? array_push($fu_non_ex_1, $dv->fu_non_ex_1) : null;
                $dv->fu_non_ex_2!=null ? array_push($fu_non_ex_2, $dv->fu_non_ex_2) : null;
                $dv->fu_non_ex_3!=null ? array_push($fu_non_ex_3, $dv->fu_non_ex_3) : null;
                $dv->fu_non_ex_4!=null ? array_push($fu_non_ex_4, $dv->fu_non_ex_4) : null;
                $dv->fu_non_ex_5!=null ? array_push($fu_non_ex_5, $dv->fu_non_ex_5) : null;
                $dv->sent_e_contract!=null ? array_push($sent_e, $dv->sent_e_contract) : null;
                $dv->rec_e_contract!=null ? array_push($rec_e, $dv->rec_e_contract) : null;
                $dv->email_sent!=null ? array_push($non_ex, $dv->email_sent) : null;
            }
            $c_answer = count($answer);
            $c_n_auth_non_ex = count($n_auth_non_ex);
            $c_fu = count($fu_1)+count($fu_2)+count($fu_3)+count($fu_4)+count($fu_5);
            $c_fu_non_ex = count($fu_non_ex_1)+count($fu_non_ex_2)+count($fu_non_ex_3)+count($fu_non_ex_4)+count($fu_non_ex_5);
            $c_royalty = count($royalty);
            $c_sent_e = count($sent_e);
            $c_rec_e = count($rec_e);
            $c_non_ex = count($non_ex);
            array_push($data_array['data'], [
                $no++,date('d/m/Y',strtotime($date)),$c_answer,$c_n_auth_non_ex,$c_fu,$c_fu_non_ex,$c_sent_e,$c_rec_e,$c_non_ex,$c_royalty
            ]);
        }
        return $data_array;
    }
    public function dataGlobalTeamMonitoringIcha($date){
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "No.",
            "Date",
            "Answer",
            "N. Auth Non Ex",
            "Follow Up",
            "Follow Up Non Ex",
            "Sent E-Contract",
            "Rec. E-Contract",
            "Non Exclusive",
            "Royalty"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $xdate = explode(",", $date);
        $d = $this->DataIcha($xdate[0], $xdate[1]);
        // dd($d);
        $dateRange = $this->dateRange($xdate[0],$xdate[1]);
        $no = 1;
        array_push($data_array['data'], [
            $no++,"Monthly Total",
            $d['daily']->whereNotNull('date')->count(),
            $d['non_ex']->whereNotNull('first_touch')->count(),
            $d['daily']->whereNotNull('fu_1')->count()+$d['daily']->whereNotNull('fu_2')->count()+$d['daily']->whereNotNull('fu_3')->count()+$d['daily']->whereNotNull('fu_4')->count()+$d['daily']->whereNotNull('fu_5')->count(),
            $d['non_ex']->whereNotNull('fu_non_ex_1')->count()+$d['non_ex']->whereNotNull('fu_non_ex_2')->count()+$d['non_ex']->whereNotNull('fu_non_ex_3')->count()+$d['non_ex']->whereNotNull('fu_non_ex_4')->count()+$d['non_ex']->whereNotNull('fu_non_ex_5')->count(),
            $d['non_ex']->whereNotNull('sent_e_contract')->count(),
            $d['non_ex']->whereNotNull('rec_e_contract')->count(),
            $d['non_ex']->whereNotNull('email_sent')->count(),
            $d['daily']->whereNotNull('sent_royalty')->count()
        ]);
        foreach($dateRange as $key => $date){
            $answer = [];
            $n_auth_non_ex = [];
            $fu_1 = [];
            $fu_2 = [];
            $fu_3 = [];
            $fu_4 = [];
            $fu_5 = [];
            $fu_non_ex_1 = [];
            $fu_non_ex_2 = [];
            $fu_non_ex_3 = [];
            $fu_non_ex_4 = [];
            $fu_non_ex_5 = [];
            $royalty = [];
            $sent_e = [];
            $rec_e = [];
            $non_ex = [];
            foreach ($d['daily']->where('date','=',$date) as $key => $dv) {
                $dv->date!=null ? array_push($answer, $dv->date) : null;
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($d['non_ex']->where('date','=',$date) as $key => $dv) {
                // dump($dv);
                $dv->first_touch!=null ? array_push($n_auth_non_ex, $dv->first_touch) : null;
                $dv->fu_non_ex_1!=null ? array_push($fu_non_ex_1, $dv->fu_non_ex_1) : null;
                $dv->fu_non_ex_2!=null ? array_push($fu_non_ex_2, $dv->fu_non_ex_2) : null;
                $dv->fu_non_ex_3!=null ? array_push($fu_non_ex_3, $dv->fu_non_ex_3) : null;
                $dv->fu_non_ex_4!=null ? array_push($fu_non_ex_4, $dv->fu_non_ex_4) : null;
                $dv->fu_non_ex_5!=null ? array_push($fu_non_ex_5, $dv->fu_non_ex_5) : null;
                $dv->sent_e_contract!=null ? array_push($sent_e, $dv->sent_e_contract) : null;
                $dv->rec_e_contract!=null ? array_push($rec_e, $dv->rec_e_contract) : null;
                $dv->email_sent!=null ? array_push($non_ex, $dv->email_sent) : null;
            }
            $c_answer = count($answer);
            $c_n_auth_non_ex = count($n_auth_non_ex);
            $c_fu = count($fu_1)+count($fu_2)+count($fu_3)+count($fu_4)+count($fu_5);
            $c_fu_non_ex = count($fu_non_ex_1)+count($fu_non_ex_2)+count($fu_non_ex_3)+count($fu_non_ex_4)+count($fu_non_ex_5);
            $c_royalty = count($royalty);
            $c_sent_e = count($sent_e);
            $c_rec_e = count($rec_e);
            $c_non_ex = count($non_ex);
            array_push($data_array['data'], [
                $no++,date('d/m/Y',strtotime($date)),$c_answer,$c_n_auth_non_ex,$c_fu,$c_fu_non_ex,$c_sent_e,$c_rec_e,$c_non_ex,$c_royalty
            ]);
        }
        return $data_array;
    }
    public function dataGlobalTeamMonitoringLily($date){
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "No.",
            "Date",
            "Answer",
            "N. Auth Non Ex",
            "Follow Up",
            "Follow Up Non Ex",
            "Sent E-Contract",
            "Rec. E-Contract",
            "Non Exclusive",
            "Royalty"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $xdate = explode(",", $date);
        $d = $this->DataLily($xdate[0], $xdate[1]);
        // dd($d);
        $dateRange = $this->dateRange($xdate[0],$xdate[1]);
        $no = 1;
        array_push($data_array['data'], [
            $no++,"Monthly Total",
            $d['daily']->whereNotNull('date')->count(),
            $d['non_ex']->whereNotNull('first_touch')->count(),
            $d['daily']->whereNotNull('fu_1')->count()+$d['daily']->whereNotNull('fu_2')->count()+$d['daily']->whereNotNull('fu_3')->count()+$d['daily']->whereNotNull('fu_4')->count()+$d['daily']->whereNotNull('fu_5')->count(),
            $d['non_ex']->whereNotNull('fu_non_ex_1')->count()+$d['non_ex']->whereNotNull('fu_non_ex_2')->count()+$d['non_ex']->whereNotNull('fu_non_ex_3')->count()+$d['non_ex']->whereNotNull('fu_non_ex_4')->count()+$d['non_ex']->whereNotNull('fu_non_ex_5')->count(),
            $d['non_ex']->whereNotNull('sent_e_contract')->count(),
            $d['non_ex']->whereNotNull('rec_e_contract')->count(),
            $d['non_ex']->whereNotNull('email_sent')->count(),
            $d['daily']->whereNotNull('sent_royalty')->count()
        ]);
        foreach($dateRange as $key => $date){
            $answer = [];
            $n_auth_non_ex = [];
            $fu_1 = [];
            $fu_2 = [];
            $fu_3 = [];
            $fu_4 = [];
            $fu_5 = [];
            $fu_non_ex_1 = [];
            $fu_non_ex_2 = [];
            $fu_non_ex_3 = [];
            $fu_non_ex_4 = [];
            $fu_non_ex_5 = [];
            $royalty = [];
            $sent_e = [];
            $rec_e = [];
            $non_ex = [];
            foreach ($d['daily']->where('date','=',$date) as $key => $dv) {
                $dv->date!=null ? array_push($answer, $dv->date) : null;
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($d['non_ex']->where('date','=',$date) as $key => $dv) {
                // dump($dv);
                $dv->first_touch!=null ? array_push($n_auth_non_ex, $dv->first_touch) : null;
                $dv->fu_non_ex_1!=null ? array_push($fu_non_ex_1, $dv->fu_non_ex_1) : null;
                $dv->fu_non_ex_2!=null ? array_push($fu_non_ex_2, $dv->fu_non_ex_2) : null;
                $dv->fu_non_ex_3!=null ? array_push($fu_non_ex_3, $dv->fu_non_ex_3) : null;
                $dv->fu_non_ex_4!=null ? array_push($fu_non_ex_4, $dv->fu_non_ex_4) : null;
                $dv->fu_non_ex_5!=null ? array_push($fu_non_ex_5, $dv->fu_non_ex_5) : null;
                $dv->sent_e_contract!=null ? array_push($sent_e, $dv->sent_e_contract) : null;
                $dv->rec_e_contract!=null ? array_push($rec_e, $dv->rec_e_contract) : null;
                $dv->email_sent!=null ? array_push($non_ex, $dv->email_sent) : null;
            }
            $c_answer = count($answer);
            $c_n_auth_non_ex = count($n_auth_non_ex);
            $c_fu = count($fu_1)+count($fu_2)+count($fu_3)+count($fu_4)+count($fu_5);
            $c_fu_non_ex = count($fu_non_ex_1)+count($fu_non_ex_2)+count($fu_non_ex_3)+count($fu_non_ex_4)+count($fu_non_ex_5);
            $c_royalty = count($royalty);
            $c_sent_e = count($sent_e);
            $c_rec_e = count($rec_e);
            $c_non_ex = count($non_ex);
            array_push($data_array['data'], [
                $no++,date('d/m/Y',strtotime($date)),$c_answer,$c_n_auth_non_ex,$c_fu,$c_fu_non_ex,$c_sent_e,$c_rec_e,$c_non_ex,$c_royalty
            ]);
        }
        return $data_array;
    }
    public function dataGlobalTeamMonitoringMaydewi($date){
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "No.",
            "Date",
            "Answer",
            "N. Auth Non Ex",
            "Follow Up",
            "Follow Up Non Ex",
            "Sent E-Contract",
            "Rec. E-Contract",
            "Non Exclusive",
            "Royalty"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $xdate = explode(",", $date);
        $d = $this->DataMaydewi($xdate[0], $xdate[1]);
        // dd($d);
        $dateRange = $this->dateRange($xdate[0],$xdate[1]);
        $no = 1;
        array_push($data_array['data'], [
            $no++,"Monthly Total",
            $d['daily']->whereNotNull('date')->count(),
            $d['non_ex']->whereNotNull('first_touch')->count(),
            $d['daily']->whereNotNull('fu_1')->count()+$d['daily']->whereNotNull('fu_2')->count()+$d['daily']->whereNotNull('fu_3')->count()+$d['daily']->whereNotNull('fu_4')->count()+$d['daily']->whereNotNull('fu_5')->count(),
            $d['non_ex']->whereNotNull('fu_non_ex_1')->count()+$d['non_ex']->whereNotNull('fu_non_ex_2')->count()+$d['non_ex']->whereNotNull('fu_non_ex_3')->count()+$d['non_ex']->whereNotNull('fu_non_ex_4')->count()+$d['non_ex']->whereNotNull('fu_non_ex_5')->count(),
            $d['non_ex']->whereNotNull('sent_e_contract')->count(),
            $d['non_ex']->whereNotNull('rec_e_contract')->count(),
            $d['non_ex']->whereNotNull('email_sent')->count(),
            $d['daily']->whereNotNull('sent_royalty')->count()
        ]);
        foreach($dateRange as $key => $date){
            $answer = [];
            $n_auth_non_ex = [];
            $fu_1 = [];
            $fu_2 = [];
            $fu_3 = [];
            $fu_4 = [];
            $fu_5 = [];
            $fu_non_ex_1 = [];
            $fu_non_ex_2 = [];
            $fu_non_ex_3 = [];
            $fu_non_ex_4 = [];
            $fu_non_ex_5 = [];
            $royalty = [];
            $sent_e = [];
            $rec_e = [];
            $non_ex = [];
            foreach ($d['daily']->where('date','=',$date) as $key => $dv) {
                $dv->date!=null ? array_push($answer, $dv->date) : null;
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($d['non_ex']->where('date','=',$date) as $key => $dv) {
                // dump($dv);
                $dv->first_touch!=null ? array_push($n_auth_non_ex, $dv->first_touch) : null;
                $dv->fu_non_ex_1!=null ? array_push($fu_non_ex_1, $dv->fu_non_ex_1) : null;
                $dv->fu_non_ex_2!=null ? array_push($fu_non_ex_2, $dv->fu_non_ex_2) : null;
                $dv->fu_non_ex_3!=null ? array_push($fu_non_ex_3, $dv->fu_non_ex_3) : null;
                $dv->fu_non_ex_4!=null ? array_push($fu_non_ex_4, $dv->fu_non_ex_4) : null;
                $dv->fu_non_ex_5!=null ? array_push($fu_non_ex_5, $dv->fu_non_ex_5) : null;
                $dv->sent_e_contract!=null ? array_push($sent_e, $dv->sent_e_contract) : null;
                $dv->rec_e_contract!=null ? array_push($rec_e, $dv->rec_e_contract) : null;
                $dv->email_sent!=null ? array_push($non_ex, $dv->email_sent) : null;
            }
            $c_answer = count($answer);
            $c_n_auth_non_ex = count($n_auth_non_ex);
            $c_fu = count($fu_1)+count($fu_2)+count($fu_3)+count($fu_4)+count($fu_5);
            $c_fu_non_ex = count($fu_non_ex_1)+count($fu_non_ex_2)+count($fu_non_ex_3)+count($fu_non_ex_4)+count($fu_non_ex_5);
            $c_royalty = count($royalty);
            $c_sent_e = count($sent_e);
            $c_rec_e = count($rec_e);
            $c_non_ex = count($non_ex);
            array_push($data_array['data'], [
                $no++,date('d/m/Y',strtotime($date)),$c_answer,$c_n_auth_non_ex,$c_fu,$c_fu_non_ex,$c_sent_e,$c_rec_e,$c_non_ex,$c_royalty
            ]);
        }
        return $data_array;
    }
    public function dataGlobalTeamMonitoringRani($date){
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "No.",
            "Date",
            "Answer",
            "N. Auth Non Ex",
            "Follow Up",
            "Follow Up Non Ex",
            "Sent E-Contract",
            "Rec. E-Contract",
            "Non Exclusive",
            "Royalty"
        ];
        $xdate = explode(",", $date);
        $d = $this->DataRani($xdate[0], $xdate[1]);
        // dd($d);
        $dateRange = $this->dateRange($xdate[0],$xdate[1]);
        $no = 1;
        array_push($data_array['data'], [
            $no++,"Monthly Total",
            $d['daily']->whereNotNull('date')->count(),
            $d['non_ex']->whereNotNull('first_touch')->count(),
            $d['daily']->whereNotNull('fu_1')->count()+$d['daily']->whereNotNull('fu_2')->count()+$d['daily']->whereNotNull('fu_3')->count()+$d['daily']->whereNotNull('fu_4')->count()+$d['daily']->whereNotNull('fu_5')->count(),
            $d['non_ex']->whereNotNull('fu_non_ex_1')->count()+$d['non_ex']->whereNotNull('fu_non_ex_2')->count()+$d['non_ex']->whereNotNull('fu_non_ex_3')->count()+$d['non_ex']->whereNotNull('fu_non_ex_4')->count()+$d['non_ex']->whereNotNull('fu_non_ex_5')->count(),
            $d['non_ex']->whereNotNull('sent_e_contract')->count(),
            $d['non_ex']->whereNotNull('rec_e_contract')->count(),
            $d['non_ex']->whereNotNull('email_sent')->count(),
            $d['daily']->whereNotNull('sent_royalty')->count()
        ]);
        foreach($dateRange as $key => $date){
            $answer = [];
            $n_auth_non_ex = [];
            $fu_1 = [];
            $fu_2 = [];
            $fu_3 = [];
            $fu_4 = [];
            $fu_5 = [];
            $fu_non_ex_1 = [];
            $fu_non_ex_2 = [];
            $fu_non_ex_3 = [];
            $fu_non_ex_4 = [];
            $fu_non_ex_5 = [];
            $royalty = [];
            $sent_e = [];
            $rec_e = [];
            $non_ex = [];
            foreach ($d['daily']->where('date','=',$date) as $key => $dv) {
                $dv->date!=null ? array_push($answer, $dv->date) : null;
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($d['non_ex']->where('date','=',$date) as $key => $dv) {
                // dump($dv);
                $dv->first_touch!=null ? array_push($n_auth_non_ex, $dv->first_touch) : null;
                $dv->fu_non_ex_1!=null ? array_push($fu_non_ex_1, $dv->fu_non_ex_1) : null;
                $dv->fu_non_ex_2!=null ? array_push($fu_non_ex_2, $dv->fu_non_ex_2) : null;
                $dv->fu_non_ex_3!=null ? array_push($fu_non_ex_3, $dv->fu_non_ex_3) : null;
                $dv->fu_non_ex_4!=null ? array_push($fu_non_ex_4, $dv->fu_non_ex_4) : null;
                $dv->fu_non_ex_5!=null ? array_push($fu_non_ex_5, $dv->fu_non_ex_5) : null;
                $dv->sent_e_contract!=null ? array_push($sent_e, $dv->sent_e_contract) : null;
                $dv->rec_e_contract!=null ? array_push($rec_e, $dv->rec_e_contract) : null;
                $dv->email_sent!=null ? array_push($non_ex, $dv->email_sent) : null;
            }
            $c_answer = count($answer);
            $c_n_auth_non_ex = count($n_auth_non_ex);
            $c_fu = count($fu_1)+count($fu_2)+count($fu_3)+count($fu_4)+count($fu_5);
            $c_fu_non_ex = count($fu_non_ex_1)+count($fu_non_ex_2)+count($fu_non_ex_3)+count($fu_non_ex_4)+count($fu_non_ex_5);
            $c_royalty = count($royalty);
            $c_sent_e = count($sent_e);
            $c_rec_e = count($rec_e);
            $c_non_ex = count($non_ex);
            array_push($data_array['data'], [
                $no++,date('d/m/Y',strtotime($date)),$c_answer,$c_n_auth_non_ex,$c_fu,$c_fu_non_ex,$c_sent_e,$c_rec_e,$c_non_ex,$c_royalty
            ]);
        }
        return $data_array;
    }
    public function getIndoTeamMonitoring(Request $request){
        $person = Str::slug($request->input('mod'));
        $month = $request->input('mon');
        $date_start = date($month."-d", strtotime("first day of this month"));
        $date_end = date($month."-d", strtotime("last day of this month"));
        $date = $date_start.", ".$date_end;
        if($person == 'irel'){
            $data_array = $this->dataIndoTeamMonitoringIrel($date);
        }
        else if($person == 'icha-nur'){
            $data_array = $this->dataIndoTeamMonitoringIchaNur($date);
        }
        else{
            $data_array = [];
        }
        return $data_array;
    }
    public function dataIndoTeamMonitoringIchaNur($date){
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $xdate = explode(",", $date);
        $d = DailyReportIndoIchaNur::whereBetween('date', [$xdate[0],$xdate[1]])
            ->orderBy('id','DESC')
            ->get();
        $title = [
            "No.",
            "Date",
            "Answer",
            "Follow Up",
            "Royalty",
            "Non Exclusive"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $dateRange = $this->dateRange($xdate[0],$xdate[1]);
        $no = 1;
        array_push($data_array['data'], [
            $no++,"Monthly Total",$d->whereNotNull('date')->count(),$d->whereNotNull('fu_1')->count()+$d->whereNotNull('fu_2')->count()+$d->whereNotNull('fu_3')->count()+$d->whereNotNull('fu_4')->count()+$d->whereNotNull('fu_5')->count(),$d->whereNotNull('data_sent')->count(),'0'
        ]);
        foreach($dateRange as $key => $date){
            $answer = [];
            $fu_1 = [];
            $fu_2 = [];
            $fu_3 = [];
            $fu_4 = [];
            $fu_5 = [];
            $royalty = [];
            foreach ($d->where('date','=',$date) as $key => $dv) {
                $dv->date!=null ? array_push($answer, $dv->date) : null;
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
                $dv->data_sent!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            $answer = count($answer);
            $fu = count($fu_1)+count($fu_2)+count($fu_3)+count($fu_4)+count($fu_5);
            $royalty = count($royalty);
            $non_ex = "-";
                array_push($data_array['data'], [
                $no++,date('d/m/Y',strtotime($date)),$answer,$fu,$royalty,$non_ex
            ]);
        }
        return $data_array;
    }
    public function dataIndoTeamMonitoringIrel($date){
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "No.",
            "Date",
            "Help Authors",
            "Follow Up",
            "Solved Problems"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $xdate = explode(",", $date);
        $d = DailyReportIndoIrel::whereBetween('date', [$xdate[0],$xdate[1]])
            ->orderBy('id','DESC')
            ->get();
        $dateRange = $this->dateRange($xdate[0],$xdate[1]);
        $no = 1;
        array_push($data_array['data'], [
            $no++,"Monthly Total",$d->whereNotNull('date')->count(),$d->whereNotNull('fu_1')->count()+$d->whereNotNull('fu_2')->count()+$d->whereNotNull('fu_3')->count()+$d->whereNotNull('fu_4')->count()+$d->whereNotNull('fu_5')->count(),$d->whereNotNull('date_solved')->count()
        ]);
        foreach($dateRange as $key => $date){
            $answer = [];
            $fu_1 = [];
            $fu_2 = [];
            $fu_3 = [];
            $fu_4 = [];
            $fu_5 = [];
            $solved = [];
            foreach ($d->where('date','=',$date) as $key => $dv) {
                $dv->date!=null ? array_push($answer, $dv->date) : null;
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
                $dv->date_solved!=null ? array_push($solved, $dv->date_solved) : null;
            }
            $answer = count($answer);
            $fu = count($fu_1)+count($fu_2)+count($fu_3)+count($fu_4)+count($fu_5);
            $solved = count($solved);
            array_push($data_array['data'], [
                $no++,date('d/m/Y',strtotime($date)),$answer,$fu,$solved
            ]);
        }
        return $data_array;
    }

    /*---------------------------------------
    | LV 2 WEEKLY REPORT
    -----------------------------------------*/
    public function getWeeklyReport(Request $request){
        $report = $request->input('r');
        $date = explode(',', $request->input('w'));
        if($report == 'global'){
            $x = $this->WeeklyReportDataGlobal($date);
        }else{
            $x = $this->WeeklyReportDataIndo($date);
        }
        return $x;
    }
    public function WeeklyReportDataGlobal($date){
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        // dd($date);
        $week = $date[0];
        $startdate = $date[1];
        $enddate = $date[2];
        $title = [
            "$week - Global Team",
            "Answer New Author",
            "N. Auth Non Ex",
            "Follow Up",
            "Follow Up Non Ex",
            "Sent E Contract",
            "Rec E Contract",
            "Done Non Ex",
            "Royalty"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }

        $dAme = DailyReportAme::whereBetween('date', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        $dAmen = NonExclusiveReport::where('global_editor','=','Ashley')
        ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
        ->get();
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.ames') ."'>Ame</a>",
            $dAme->whereNotNull('date')->count(),$dAmen->whereNotNull('first_touch')->count(),$dAme->whereNotNull('fu_1')->count()+$dAme->whereNotNull('fu_2')->count()+$dAme->whereNotNull('fu_3')->count()+$dAme->whereNotNull('fu_4')->count()+$dAme->whereNotNull('fu_5')->count(),$dAmen->whereNotNull('fu_1')->count()+$dAmen->whereNotNull('fu_2')->count()+$dAmen->whereNotNull('fu_3')->count()+$dAmen->whereNotNull('fu_4')->count()+$dAmen->whereNotNull('fu_5')->count(),$dAmen->whereNotNull('sent_e_contract')->count(),$dAmen->whereNotNull('rec_e_contract')->count(),$dAmen->whereNotNull('email_sent')->count(), $dAme->whereNotNull('sent_royalty')->count()
        ]);

        $dAnna = DailyReportAnna::whereBetween('date', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        $dAnnan = NonExclusiveReport::where('global_editor','=','Erica')
        ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
        ->get();
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.annas') ."'>Anna</a>",
            $dAnna->whereNotNull('date')->count(),$dAnnan->whereNotNull('first_touch')->count(),$dAnna->whereNotNull('fu_1')->count()+$dAnna->whereNotNull('fu_2')->count()+$dAnna->whereNotNull('fu_3')->count()+$dAnna->whereNotNull('fu_4')->count()+$dAnna->whereNotNull('fu_5')->count(),$dAnnan->whereNotNull('fu_1')->count()+$dAnnan->whereNotNull('fu_2')->count()+$dAnnan->whereNotNull('fu_3')->count()+$dAnnan->whereNotNull('fu_4')->count()+$dAnnan->whereNotNull('fu_5')->count(),$dAnnan->whereNotNull('sent_e_contract')->count(),$dAnnan->whereNotNull('rec_e_contract')->count(),$dAnnan->whereNotNull('email_sent')->count(), $dAnna->whereNotNull('sent_royalty')->count()
        ]);

        $dCarol = DailyReportCarol::whereBetween('date', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        $dCaroln = NonExclusiveReport::where('global_editor','=','Ashley')
        ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
        ->get();
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.carols') ."'>Carol</a>",
            $dCarol->whereNotNull('date')->count(),$dCaroln->whereNotNull('first_touch')->count(),$dCarol->whereNotNull('fu_1')->count()+$dCarol->whereNotNull('fu_2')->count()+$dCarol->whereNotNull('fu_3')->count()+$dCarol->whereNotNull('fu_4')->count()+$dCarol->whereNotNull('fu_5')->count(),$dCaroln->whereNotNull('fu_1')->count()+$dCaroln->whereNotNull('fu_2')->count()+$dCaroln->whereNotNull('fu_3')->count()+$dCaroln->whereNotNull('fu_4')->count()+$dCaroln->whereNotNull('fu_5')->count(),$dCaroln->whereNotNull('sent_e_contract')->count(),$dCaroln->whereNotNull('rec_e_contract')->count(),$dCaroln->whereNotNull('email_sent')->count(), $dCarol->whereNotNull('sent_royalty')->count()
        ]);

        $dEric = DailyReportEric::whereBetween('date', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        $dEricn = NonExclusiveReport::where('global_editor','=','Cornelia')
        ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
        ->get();
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.erics') ."'>Eric</a>",
            $dEric->whereNotNull('date')->count(),$dEricn->whereNotNull('first_touch')->count(),$dEric->whereNotNull('fu_1')->count()+$dEric->whereNotNull('fu_2')->count()+$dEric->whereNotNull('fu_3')->count()+$dEric->whereNotNull('fu_4')->count()+$dEric->whereNotNull('fu_5')->count(),$dEricn->whereNotNull('fu_1')->count()+$dEricn->whereNotNull('fu_2')->count()+$dEricn->whereNotNull('fu_3')->count()+$dEricn->whereNotNull('fu_4')->count()+$dEricn->whereNotNull('fu_5')->count(),$dEricn->whereNotNull('sent_e_contract')->count(),$dEricn->whereNotNull('rec_e_contract')->count(),$dEricn->whereNotNull('email_sent')->count(), $dEric->whereNotNull('sent_royalty')->count()
        ]);

        $dIcha = DailyReportIcha::whereBetween('date', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        $dIchan = NonExclusiveReport::where('global_editor','=','Claire')
        ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
        ->get();
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.ichas') ."'>Icha</a>",
            $dIcha->whereNotNull('date')->count(),$dIchan->whereNotNull('first_touch')->count(),$dIcha->whereNotNull('fu_1')->count()+$dIcha->whereNotNull('fu_2')->count()+$dIcha->whereNotNull('fu_3')->count()+$dIcha->whereNotNull('fu_4')->count()+$dIcha->whereNotNull('fu_5')->count(),$dIchan->whereNotNull('fu_1')->count()+$dIchan->whereNotNull('fu_2')->count()+$dIchan->whereNotNull('fu_3')->count()+$dIchan->whereNotNull('fu_4')->count()+$dIchan->whereNotNull('fu_5')->count(),$dIchan->whereNotNull('sent_e_contract')->count(),$dIchan->whereNotNull('rec_e_contract')->count(),$dIchan->whereNotNull('email_sent')->count(), $dIcha->whereNotNull('sent_royalty')->count()
        ]);

        $dLily = DailyReportLily::whereBetween('date', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        $dLilyn = NonExclusiveReport::where('global_editor','=','Ensia')
        ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
        ->get();
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.lilies') ."'>Lily</a>",
            $dLily->whereNotNull('date')->count(),$dLilyn->whereNotNull('first_touch')->count(),$dLily->whereNotNull('fu_1')->count()+$dLily->whereNotNull('fu_2')->count()+$dLily->whereNotNull('fu_3')->count()+$dLily->whereNotNull('fu_4')->count()+$dLily->whereNotNull('fu_5')->count(),$dLilyn->whereNotNull('fu_1')->count()+$dLilyn->whereNotNull('fu_2')->count()+$dLilyn->whereNotNull('fu_3')->count()+$dLilyn->whereNotNull('fu_4')->count()+$dLilyn->whereNotNull('fu_5')->count(),$dLilyn->whereNotNull('sent_e_contract')->count(),$dLilyn->whereNotNull('rec_e_contract')->count(),$dLilyn->whereNotNull('email_sent')->count(), $dLily->whereNotNull('sent_royalty')->count()
        ]);

        $dMaydewi = DailyReportMaydewi::whereBetween('date', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        $dMaydewin = NonExclusiveReport::where('global_editor','=','Serena')
        ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
        ->get();
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.maydewis') ."'>Maydewi</a>",
            $dMaydewi->whereNotNull('date')->count(),$dMaydewin->whereNotNull('first_touch')->count(),$dMaydewi->whereNotNull('fu_1')->count()+$dMaydewi->whereNotNull('fu_2')->count()+$dMaydewi->whereNotNull('fu_3')->count()+$dMaydewi->whereNotNull('fu_4')->count()+$dMaydewi->whereNotNull('fu_5')->count(),$dMaydewin->whereNotNull('fu_1')->count()+$dMaydewin->whereNotNull('fu_2')->count()+$dMaydewin->whereNotNull('fu_3')->count()+$dMaydewin->whereNotNull('fu_4')->count()+$dMaydewin->whereNotNull('fu_5')->count(),$dMaydewin->whereNotNull('sent_e_contract')->count(),$dMaydewin->whereNotNull('rec_e_contract')->count(),$dMaydewin->whereNotNull('email_sent')->count(), $dMaydewi->whereNotNull('sent_royalty')->count()
        ]);

        $dRani = DailyReportRani::whereBetween('date', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        $dRanin = NonExclusiveReport::where('global_editor','=','Aurora')
        ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
        ->get();
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.ranis') ."'>Rani</a>",
            $dRani->whereNotNull('date')->count(),$dRanin->whereNotNull('first_touch')->count(),$dRani->whereNotNull('fu_1')->count()+$dRani->whereNotNull('fu_2')->count()+$dRani->whereNotNull('fu_3')->count()+$dRani->whereNotNull('fu_4')->count()+$dRani->whereNotNull('fu_5')->count(),$dRanin->whereNotNull('fu_1')->count()+$dRanin->whereNotNull('fu_2')->count()+$dRanin->whereNotNull('fu_3')->count()+$dRanin->whereNotNull('fu_4')->count()+$dRanin->whereNotNull('fu_5')->count(),$dRanin->whereNotNull('sent_e_contract')->count(),$dRanin->whereNotNull('rec_e_contract')->count(),$dRanin->whereNotNull('email_sent')->count(), $dRani->whereNotNull('sent_royalty')->count()
        ]);
        return $data_array;
    }
    public function WeeklyReportDataIndo($date){
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        // dd($date);
        $week = $date[0];
        $startdate = $date[1];
        $enddate = $date[2];
        $title = [
            "$week - Indo Team",
            "Answer New Authors",
            "Follow Up Auhtors",
            "Royalty",
            "Non Ex",
            "Help Author",
            "Royalty"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }

        $dIchaNur = DailyReportIndoIchaNur::whereBetween('date', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-indo.icha-nurs') ."'>Icha Nur</a>",
            $dIchaNur->whereNotNull('date')->count(),$dIchaNur->whereNotNull('fu_1')->count()+$dIchaNur->whereNotNull('fu_2')->count()+$dIchaNur->whereNotNull('fu_3')->count()+$dIchaNur->whereNotNull('fu_4')->count()+$dIchaNur->whereNotNull('fu_5')->count(),$dIchaNur->whereNotNull('sent_royalty')->count(),'0','-','-','-'
        ]);

        $dIrel = DailyReportIndoIrel::whereBetween('date', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-indo.irels') ."'>Irel</a>",
            '-',$dIrel->whereNotNull('fu_1')->count()+$dIrel->whereNotNull('fu_2')->count()+$dIrel->whereNotNull('fu_3')->count()+$dIrel->whereNotNull('fu_4')->count()+$dIrel->whereNotNull('fu_5')->count()+$dIrel->whereNotNull('fu_6')->count()+$dIrel->whereNotNull('fu_7')->count()+$dIrel->whereNotNull('fu_8')->count()+$dIrel->whereNotNull('fu_9')->count()+$dIrel->whereNotNull('fu_10')->count(),'-','-',+$dIrel->whereNotNull('date')->count(),$dIrel->whereNotNull('date_solved')->count(),
        ]);
        return $data_array;
    }

    /*---------------------------------------
    | LV 2 MONTHLY REPORT
    -----------------------------------------*/
    public function getMonthlyReport(Request $request){
        $report = $request->input('r');
        $month = $request->input('mon');
        $date_start = date($month."-d", strtotime("first day of this month"));
        $date_end = date($month."-d", strtotime("last day of this month"));
        $date = $date_start.",".$date_end;
        $date = explode(",",$date);
        if($report == 'global'){
            $x = $this->MonthlyReportDataGlobal($date);
        }else{
            $x = $this->MonthlyReportDataIndo($date);
        }
        return $x;
    }
    public function MonthlyReportDataGlobal($date){
        $startdate = $date[0];
        $enddate = $date[1];
        $data_array['columns'] = [];
        $data_array['data'] = [];
        // dd($date);

        /*-----------------------
        | QUERY
        ------------------------*/
        $dAme = DailyReportAme::whereBetween('date', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        $dAmen = NonExclusiveReport::where('global_editor','=','Ashley')
        ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
        ->get();
        $dAnna = DailyReportAnna::whereBetween('date', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        $dAnnan = NonExclusiveReport::where('global_editor','=','Erica')
        ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
        ->get();
        $dCarol = DailyReportCarol::whereBetween('date', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        $dCaroln = NonExclusiveReport::where('global_editor','=','Ashley')
        ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
        ->get();
        $dEric = DailyReportEric::whereBetween('date', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        $dEricn = NonExclusiveReport::where('global_editor','=','Cornelia')
        ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
        ->get();
        $dIcha = DailyReportIcha::whereBetween('date', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        $dIchan = NonExclusiveReport::where('global_editor','=','Claire')
        ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
        ->get();
        $dLily = DailyReportLily::whereBetween('date', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        $dLilyn = NonExclusiveReport::where('global_editor','=','Ensia')
        ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
        ->get();
        $dMaydewi = DailyReportMaydewi::whereBetween('date', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        $dMaydewin = NonExclusiveReport::where('global_editor','=','Serena')
        ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
        ->get();
        $dRani = DailyReportRani::whereBetween('date', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        $dRanin = NonExclusiveReport::where('global_editor','=','Aurora')
        ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
        ->get();

        /* --------------
        / HEAD DATA
        --------------- */
        $title = [
            "Global Team",
            "Answer New Author",
            "N. Auth Non Ex",
            "Follow Up",
            "Follow Up Non Ex",
            "Sent E Contract",
            "Rec E Contract",
            "Done Non Ex",
            "Royalty"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $grand_answer = $dAme->whereNotNull('date')->count()+$dAnna->whereNotNull('date')->count()+$dCarol->whereNotNull('date')->count()+$dEric->whereNotNull('date')->count()+$dIcha->whereNotNull('date')->count()+$dLily->whereNotNull('date')->count()+$dMaydewi->whereNotNull('date')->count()+$dRani->whereNotNull('date')->count();
        $n_auth_non_ex = $dAmen->whereNotNull('first_touch')->count()+$dAnnan->whereNotNull('first_touch')->count()+$dCaroln->whereNotNull('first_touch')->count()+$dEricn->whereNotNull('first_touch')->count()+$dIchan->whereNotNull('first_touch')->count()+$dLilyn->whereNotNull('first_touch')->count()+$dMaydewin->whereNotNull('first_touch')->count()+$dRanin->whereNotNull('first_touch')->count();
        $fu = $dAme->whereNotNull('fu_1')->count()+$dAme->whereNotNull('fu_2')->count()+$dAme->whereNotNull('fu_3')->count()+$dAme->whereNotNull('fu_4')->count()+$dAme->whereNotNull('fu_5')->count()+$dAnna->whereNotNull('fu_1')->count()+$dAnna->whereNotNull('fu_2')->count()+$dAnna->whereNotNull('fu_3')->count()+$dAnna->whereNotNull('fu_4')->count()+$dAnna->whereNotNull('fu_5')->count()+$dCarol->whereNotNull('fu_1')->count()+$dCarol->whereNotNull('fu_2')->count()+$dCarol->whereNotNull('fu_3')->count()+$dCarol->whereNotNull('fu_4')->count()+$dCarol->whereNotNull('fu_5')->count()+$dEric->whereNotNull('fu_1')->count()+$dEric->whereNotNull('fu_2')->count()+$dEric->whereNotNull('fu_3')->count()+$dEric->whereNotNull('fu_4')->count()+$dEric->whereNotNull('fu_5')->count()+$dIcha->whereNotNull('fu_1')->count()+$dIcha->whereNotNull('fu_2')->count()+$dIcha->whereNotNull('fu_3')->count()+$dIcha->whereNotNull('fu_4')->count()+$dIcha->whereNotNull('fu_5')->count()+$dLily->whereNotNull('fu_1')->count()+$dLily->whereNotNull('fu_2')->count()+$dLily->whereNotNull('fu_3')->count()+$dLily->whereNotNull('fu_4')->count()+$dLily->whereNotNull('fu_5')->count()+$dMaydewi->whereNotNull('fu_1')->count()+$dMaydewi->whereNotNull('fu_2')->count()+$dMaydewi->whereNotNull('fu_3')->count()+$dMaydewi->whereNotNull('fu_4')->count()+$dMaydewi->whereNotNull('fu_5')->count()+$dRani->whereNotNull('fu_1')->count()+$dRani->whereNotNull('fu_2')->count()+$dRani->whereNotNull('fu_3')->count()+$dRani->whereNotNull('fu_4')->count()+$dRani->whereNotNull('fu_5')->count();
        $fu_non_ex = $dAmen->whereNotNull('fu_1')->count()+$dAmen->whereNotNull('fu_2')->count()+$dAmen->whereNotNull('fu_3')->count()+$dAmen->whereNotNull('fu_4')->count()+$dAmen->whereNotNull('fu_5')->count()+$dAnnan->whereNotNull('fu_1')->count()+$dAnnan->whereNotNull('fu_2')->count()+$dAnnan->whereNotNull('fu_3')->count()+$dAnnan->whereNotNull('fu_4')->count()+$dAnnan->whereNotNull('fu_5')->count()+$dCaroln->whereNotNull('fu_1')->count()+$dCaroln->whereNotNull('fu_2')->count()+$dCaroln->whereNotNull('fu_3')->count()+$dCaroln->whereNotNull('fu_4')->count()+$dCaroln->whereNotNull('fu_5')->count()+$dEricn->whereNotNull('fu_1')->count()+$dEricn->whereNotNull('fu_2')->count()+$dEricn->whereNotNull('fu_3')->count()+$dEricn->whereNotNull('fu_4')->count()+$dEricn->whereNotNull('fu_5')->count()+$dIchan->whereNotNull('fu_1')->count()+$dIchan->whereNotNull('fu_2')->count()+$dIchan->whereNotNull('fu_3')->count()+$dIchan->whereNotNull('fu_4')->count()+$dIchan->whereNotNull('fu_5')->count()+$dLilyn->whereNotNull('fu_1')->count()+$dLilyn->whereNotNull('fu_2')->count()+$dLilyn->whereNotNull('fu_3')->count()+$dLilyn->whereNotNull('fu_4')->count()+$dLilyn->whereNotNull('fu_5')->count()+$dMaydewin->whereNotNull('fu_1')->count()+$dMaydewin->whereNotNull('fu_2')->count()+$dMaydewin->whereNotNull('fu_3')->count()+$dMaydewin->whereNotNull('fu_4')->count()+$dMaydewin->whereNotNull('fu_5')->count()+$dRanin->whereNotNull('fu_1')->count()+$dRanin->whereNotNull('fu_2')->count()+$dRanin->whereNotNull('fu_3')->count()+$dRanin->whereNotNull('fu_4')->count()+$dRanin->whereNotNull('fu_5')->count();
        $sent_e = $dAmen->whereNotNull('sent_e_contract')->count()+$dAnnan->whereNotNull('sent_e_contract')->count()+$dCaroln->whereNotNull('sent_e_contract')->count()+$dEricn->whereNotNull('sent_e_contract')->count()+$dIchan->whereNotNull('sent_e_contract')->count()+$dLilyn->whereNotNull('sent_e_contract')->count()+$dMaydewin->whereNotNull('sent_e_contract')->count()+$dRanin->whereNotNull('sent_e_contract')->count();
        $rec_e = $dAmen->whereNotNull('rec_e_contract')->count()+$dAnnan->whereNotNull('rec_e_contract')->count()+$dCaroln->whereNotNull('rec_e_contract')->count()+$dEricn->whereNotNull('rec_e_contract')->count()+$dIchan->whereNotNull('rec_e_contract')->count()+$dLilyn->whereNotNull('rec_e_contract')->count()+$dMaydewin->whereNotNull('rec_e_contract')->count()+$dRanin->whereNotNull('rec_e_contract')->count();
        $d_non_ex = $dAmen->whereNotNull('email_sent')->count()+$dAnnan->whereNotNull('email_sent')->count()+$dCaroln->whereNotNull('email_sent')->count()+$dEricn->whereNotNull('email_sent')->count()+$dIchan->whereNotNull('email_sent')->count()+$dLilyn->whereNotNull('email_sent')->count()+$dMaydewin->whereNotNull('email_sent')->count()+$dRanin->whereNotNull('email_sent')->count();
        $d_royalty = $dAme->whereNotNull('sent_royalty')->count()+$dAnna->whereNotNull('sent_royalty')->count()+$dCarol->whereNotNull('sent_royalty')->count()+$dEric->whereNotNull('sent_royalty')->count()+$dIcha->whereNotNull('sent_royalty')->count()+$dLily->whereNotNull('sent_royalty')->count()+$dMaydewin->whereNotNull('sent_royalty')->count()+$dRanin->whereNotNull('sent_royalty')->count();
        array_push($data_array['data'], [
            "- -Total",$grand_answer,$n_auth_non_ex,$fu,$fu_non_ex,$sent_e,$rec_e,$d_non_ex,$d_royalty
        ]);
        $counter_person = count($this->personGlobal)+count($this->personIndo);
        array_push($data_array['data'], [
            "- Average",$grand_answer/$counter_person,$n_auth_non_ex/$counter_person,$fu/$counter_person,$fu_non_ex/$counter_person,$sent_e/$counter_person,$rec_e/$counter_person,$d_non_ex/$counter_person,$d_royalty/$counter_person
        ]);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.ames') ."'>Ame</a>",
            $dAme->whereNotNull('date')->count(),$dAmen->whereNotNull('first_touch')->count(),$dAme->whereNotNull('fu_1')->count()+$dAme->whereNotNull('fu_2')->count()+$dAme->whereNotNull('fu_3')->count()+$dAme->whereNotNull('fu_4')->count()+$dAme->whereNotNull('fu_5')->count(),$dAmen->whereNotNull('fu_1')->count()+$dAmen->whereNotNull('fu_2')->count()+$dAmen->whereNotNull('fu_3')->count()+$dAmen->whereNotNull('fu_4')->count()+$dAmen->whereNotNull('fu_5')->count(),$dAmen->whereNotNull('sent_e_contract')->count(),$dAmen->whereNotNull('rec_e_contract')->count(),$dAmen->whereNotNull('email_sent')->count(), $dAme->whereNotNull('sent_royalty')->count()
        ]);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.annas') ."'>Anna</a>",
            $dAnna->whereNotNull('date')->count(),$dAnnan->whereNotNull('first_touch')->count(),$dAnna->whereNotNull('fu_1')->count()+$dAnna->whereNotNull('fu_2')->count()+$dAnna->whereNotNull('fu_3')->count()+$dAnna->whereNotNull('fu_4')->count()+$dAnna->whereNotNull('fu_5')->count(),$dAnnan->whereNotNull('fu_1')->count()+$dAnnan->whereNotNull('fu_2')->count()+$dAnnan->whereNotNull('fu_3')->count()+$dAnnan->whereNotNull('fu_4')->count()+$dAnnan->whereNotNull('fu_5')->count(),$dAnnan->whereNotNull('sent_e_contract')->count(),$dAnnan->whereNotNull('rec_e_contract')->count(),$dAnnan->whereNotNull('email_sent')->count(), $dAnna->whereNotNull('sent_royalty')->count()
        ]);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.carols') ."'>Carol</a>",
            $dCarol->whereNotNull('date')->count(),$dCaroln->whereNotNull('first_touch')->count(),$dCarol->whereNotNull('fu_1')->count()+$dCarol->whereNotNull('fu_2')->count()+$dCarol->whereNotNull('fu_3')->count()+$dCarol->whereNotNull('fu_4')->count()+$dCarol->whereNotNull('fu_5')->count(),$dCaroln->whereNotNull('fu_1')->count()+$dCaroln->whereNotNull('fu_2')->count()+$dCaroln->whereNotNull('fu_3')->count()+$dCaroln->whereNotNull('fu_4')->count()+$dCaroln->whereNotNull('fu_5')->count(),$dCaroln->whereNotNull('sent_e_contract')->count(),$dCaroln->whereNotNull('rec_e_contract')->count(),$dCaroln->whereNotNull('email_sent')->count(), $dCarol->whereNotNull('sent_royalty')->count()
        ]);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.erics') ."'>Eric</a>",
            $dEric->whereNotNull('date')->count(),$dEricn->whereNotNull('first_touch')->count(),$dEric->whereNotNull('fu_1')->count()+$dEric->whereNotNull('fu_2')->count()+$dEric->whereNotNull('fu_3')->count()+$dEric->whereNotNull('fu_4')->count()+$dEric->whereNotNull('fu_5')->count(),$dEricn->whereNotNull('fu_1')->count()+$dEricn->whereNotNull('fu_2')->count()+$dEricn->whereNotNull('fu_3')->count()+$dEricn->whereNotNull('fu_4')->count()+$dEricn->whereNotNull('fu_5')->count(),$dEricn->whereNotNull('sent_e_contract')->count(),$dEricn->whereNotNull('rec_e_contract')->count(),$dEricn->whereNotNull('email_sent')->count(), $dEric->whereNotNull('sent_royalty')->count()
        ]);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.ichas') ."'>Icha</a>",
            $dIcha->whereNotNull('date')->count(),$dIchan->whereNotNull('first_touch')->count(),$dIcha->whereNotNull('fu_1')->count()+$dIcha->whereNotNull('fu_2')->count()+$dIcha->whereNotNull('fu_3')->count()+$dIcha->whereNotNull('fu_4')->count()+$dIcha->whereNotNull('fu_5')->count(),$dIchan->whereNotNull('fu_1')->count()+$dIchan->whereNotNull('fu_2')->count()+$dIchan->whereNotNull('fu_3')->count()+$dIchan->whereNotNull('fu_4')->count()+$dIchan->whereNotNull('fu_5')->count(),$dIchan->whereNotNull('sent_e_contract')->count(),$dIchan->whereNotNull('rec_e_contract')->count(),$dIchan->whereNotNull('email_sent')->count(), $dIcha->whereNotNull('sent_royalty')->count()
        ]);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.lilies') ."'>Lily</a>",
            $dLily->whereNotNull('date')->count(),$dLilyn->whereNotNull('first_touch')->count(),$dLily->whereNotNull('fu_1')->count()+$dLily->whereNotNull('fu_2')->count()+$dLily->whereNotNull('fu_3')->count()+$dLily->whereNotNull('fu_4')->count()+$dLily->whereNotNull('fu_5')->count(),$dLilyn->whereNotNull('fu_1')->count()+$dLilyn->whereNotNull('fu_2')->count()+$dLilyn->whereNotNull('fu_3')->count()+$dLilyn->whereNotNull('fu_4')->count()+$dLilyn->whereNotNull('fu_5')->count(),$dLilyn->whereNotNull('sent_e_contract')->count(),$dLilyn->whereNotNull('rec_e_contract')->count(),$dLilyn->whereNotNull('email_sent')->count(), $dLily->whereNotNull('sent_royalty')->count()
        ]);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.maydewis') ."'>Maydewi</a>",
            $dMaydewi->whereNotNull('date')->count(),$dMaydewin->whereNotNull('first_touch')->count(),$dMaydewi->whereNotNull('fu_1')->count()+$dMaydewi->whereNotNull('fu_2')->count()+$dMaydewi->whereNotNull('fu_3')->count()+$dMaydewi->whereNotNull('fu_4')->count()+$dMaydewi->whereNotNull('fu_5')->count(),$dMaydewin->whereNotNull('fu_1')->count()+$dMaydewin->whereNotNull('fu_2')->count()+$dMaydewin->whereNotNull('fu_3')->count()+$dMaydewin->whereNotNull('fu_4')->count()+$dMaydewin->whereNotNull('fu_5')->count(),$dMaydewin->whereNotNull('sent_e_contract')->count(),$dMaydewin->whereNotNull('rec_e_contract')->count(),$dMaydewin->whereNotNull('email_sent')->count(), $dMaydewi->whereNotNull('sent_royalty')->count()
        ]);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.ranis') ."'>Rani</a>",
            $dRani->whereNotNull('date')->count(),$dRanin->whereNotNull('first_touch')->count(),$dRani->whereNotNull('fu_1')->count()+$dRani->whereNotNull('fu_2')->count()+$dRani->whereNotNull('fu_3')->count()+$dRani->whereNotNull('fu_4')->count()+$dRani->whereNotNull('fu_5')->count(),$dRanin->whereNotNull('fu_1')->count()+$dRanin->whereNotNull('fu_2')->count()+$dRanin->whereNotNull('fu_3')->count()+$dRanin->whereNotNull('fu_4')->count()+$dRanin->whereNotNull('fu_5')->count(),$dRanin->whereNotNull('sent_e_contract')->count(),$dRanin->whereNotNull('rec_e_contract')->count(),$dRanin->whereNotNull('email_sent')->count(), $dRani->whereNotNull('sent_royalty')->count()
        ]);
        return $data_array;
    }
    public function MonthlyReportDataIndo($date){
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $startdate = $date[0];
        $enddate = $date[1];
        $dIchaNur = DailyReportIndoIchaNur::whereBetween('date', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        $dIrel = DailyReportIndoIrel::whereBetween('date', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        $title = [
            "Indo Team",
            "Answer New Authors",
            "Follow Up Auhtors",
            "Royalty",
            "Non Ex",
            "Help Author",
            "Royalty"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-indo.icha-nurs') ."'>Icha Nur</a>",
            $dIchaNur->whereNotNull('date')->count(),$dIchaNur->whereNotNull('fu_1')->count()+$dIchaNur->whereNotNull('fu_2')->count()+$dIchaNur->whereNotNull('fu_3')->count()+$dIchaNur->whereNotNull('fu_4')->count()+$dIchaNur->whereNotNull('fu_5')->count(),$dIchaNur->whereNotNull('sent_royalty')->count(),'0','-','-','-'
        ]);

        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-indo.irels') ."'>Irel</a>",
            '-',$dIrel->whereNotNull('fu_1')->count()+$dIrel->whereNotNull('fu_2')->count()+$dIrel->whereNotNull('fu_3')->count()+$dIrel->whereNotNull('fu_4')->count()+$dIrel->whereNotNull('fu_5')->count()+$dIrel->whereNotNull('fu_6')->count()+$dIrel->whereNotNull('fu_7')->count()+$dIrel->whereNotNull('fu_8')->count()+$dIrel->whereNotNull('fu_9')->count()+$dIrel->whereNotNull('fu_10')->count(),'-','-',+$dIrel->whereNotNull('date')->count(),$dIrel->whereNotNull('date_solved')->count(),
        ]);
        return $data_array;
    }
}