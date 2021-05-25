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
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PageController extends Controller
{
    private $month = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
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
    private function Year(){
        for($i=0;$i<6;$i++){
            $x[] = date('Y')-$i;
        }
        return $x;
    }
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
    public function index(){
        return view('pages.home');
    }
    public function DailyReportAmes(){
        return view('pages.daily-report.global.daily-report-ames');
    }
    public function DailyReportAnnas(){
        return view('pages.daily-report.global.daily-report-anna');
    }
    public function DailyReportCarols(){
        return view('pages.daily-report.global.daily-report-carol');
    }
    public function DailyReportErics(){
        return view('pages.daily-report.global.daily-report-eric');
    }
    public function DailyReportIchas(){
        return view('pages.daily-report.global.daily-report-icha');
    }
    public function DailyReportLilies(){
        return view('pages.daily-report.global.daily-report-lily');
    }
    public function DailyReportMayDewis(){
        return view('pages.daily-report.global.daily-report-maydewi');
    }
    public function DailyReportRanis(){
        return view('pages.daily-report.global.daily-report-rani');
    }
    public function DailyReportIndoIchaNurs(){
        return view('pages.daily-report.indo.daily-report-indo-icha-nur');
    }
    public function DailyReportIndoIrels(){
        return view('pages.daily-report.indo.daily-report-indo-irel');
    }
    public function GlobalTeamMonitoring(){
        $person = $this->personGlobal;
        $month = $this->month;
        return view('pages.team-monitoring.global',[
            'person' => $person,
            'month' => $month,
            'year' => $this->Year()
        ]);
    }
    public function IndoTeamMonitoring(){
        $person = $this->personIndo;
        $month = $this->month;
        return view('pages.team-monitoring.indo',[
            'person' => $person,
            'month' => $month,
            'year' => $this->Year()
        ]);
    }
    public function MonthlyReport(){
        return view('pages.all-report.weekly');
    }
    public function WeeklyReport(){
        return view('pages.all-report.monthly');
    }
    public function getDailyReportAmes(){
        $query = DailyReportAme::orderBy('id',
        'DESC')->limit(10000)->get();
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
        $d = DailyReportAme::whereBetween('date', [$xdate[0],$xdate[1]])
            ->orderBy('id','DESC')
            ->get();
        $dn = NonExclusiveReport::where('global_editor','=','Ashley')
        ->whereBetween('date', [$xdate[0],$xdate[1]])->orderBy('id','DESC')
        ->get();
        // dd($d);
        $dateRange = $this->dateRange($xdate[0],$xdate[1]);
        $no = 1;
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
            foreach ($d->where('date','=',$date) as $key => $dv) {
                $dv->date!=null ? array_push($answer, $dv->date) : null;
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($dn->where('date','=',$date) as $key => $dv) {
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
        $d = DailyReportAnna::whereBetween('date', [$xdate[0],$xdate[1]])
            ->orderBy('id','DESC')
            ->get();
        $dn = NonExclusiveReport::where('global_editor','=','Erica')
        ->whereBetween('date', [$xdate[0],$xdate[1]])->orderBy('id','DESC')
        ->get();
        // dd($d);
        $dateRange = $this->dateRange($xdate[0],$xdate[1]);
        $no = 1;
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
            foreach ($d->where('date','=',$date) as $key => $dv) {
                $dv->date!=null ? array_push($answer, $dv->date) : null;
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($dn->where('date','=',$date) as $key => $dv) {
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
        $d = DailyReportCarol::whereBetween('date', [$xdate[0],$xdate[1]])
            ->orderBy('id','DESC')
            ->get();
        $dn = NonExclusiveReport::where('global_editor','=','Destiny')
        ->whereBetween('date', [$xdate[0],$xdate[1]])->orderBy('id','DESC')
        ->get();
        // dd($d);
        $dateRange = $this->dateRange($xdate[0],$xdate[1]);
        $no = 1;
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
            foreach ($d->where('date','=',$date) as $key => $dv) {
                $dv->date!=null ? array_push($answer, $dv->date) : null;
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($dn->where('date','=',$date) as $key => $dv) {
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
        $d = DailyReportEric::whereBetween('date', [$xdate[0],$xdate[1]])
            ->orderBy('id','DESC')
            ->get();
        $dn = NonExclusiveReport::where('global_editor','=','Cornelia')
        ->whereBetween('date', [$xdate[0],$xdate[1]])->orderBy('id','DESC')
        ->get();
        // dd($d);
        $dateRange = $this->dateRange($xdate[0],$xdate[1]);
        $no = 1;
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
            foreach ($d->where('date','=',$date) as $key => $dv) {
                $dv->date!=null ? array_push($answer, $dv->date) : null;
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($dn->where('date','=',$date) as $key => $dv) {
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
        $d = DailyReportIcha::whereBetween('date', [$xdate[0],$xdate[1]])
            ->orderBy('id','DESC')
            ->get();
        $dn = NonExclusiveReport::where('global_editor','=','Claire')
        ->whereBetween('date', [$xdate[0],$xdate[1]])->orderBy('id','DESC')
        ->get();
        // dd($d);
        $dateRange = $this->dateRange($xdate[0],$xdate[1]);
        $no = 1;
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
            foreach ($d->where('date','=',$date) as $key => $dv) {
                $dv->date!=null ? array_push($answer, $dv->date) : null;
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($dn->where('date','=',$date) as $key => $dv) {
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
        $d = DailyReportLily::whereBetween('date', [$xdate[0],$xdate[1]])
            ->orderBy('id','DESC')
            ->get();
        $dn = NonExclusiveReport::where('global_editor','=','Ensia')
        ->whereBetween('date', [$xdate[0],$xdate[1]])->orderBy('id','DESC')
        ->get();
        // dd($d);
        $dateRange = $this->dateRange($xdate[0],$xdate[1]);
        $no = 1;
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
            foreach ($d->where('date','=',$date) as $key => $dv) {
                $dv->date!=null ? array_push($answer, $dv->date) : null;
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($dn->where('date','=',$date) as $key => $dv) {
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
        $d = DailyReportMaydewi::whereBetween('date', [$xdate[0],$xdate[1]])
            ->orderBy('id','DESC')
            ->get();
        $dn = NonExclusiveReport::where('global_editor','=','Serena')
        ->whereBetween('date', [$xdate[0],$xdate[1]])->orderBy('id','DESC')
        ->get();
        // dd($d);
        $dateRange = $this->dateRange($xdate[0],$xdate[1]);
        $no = 1;
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
            foreach ($d->where('date','=',$date) as $key => $dv) {
                $dv->date!=null ? array_push($answer, $dv->date) : null;
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($dn->where('date','=',$date) as $key => $dv) {
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
        $d = DailyReportRani::whereBetween('date', [$xdate[0],$xdate[1]])
            ->orderBy('id','DESC')
            ->get();
        $dn = NonExclusiveReport::where('global_editor','=','Aurora')
        ->whereBetween('date', [$xdate[0],$xdate[1]])->orderBy('id','DESC')
        ->get();
        // dd($d);
        $dateRange = $this->dateRange($xdate[0],$xdate[1]);
        $no = 1;
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
            foreach ($d->where('date','=',$date) as $key => $dv) {
                $dv->date!=null ? array_push($answer, $dv->date) : null;
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($dn->where('date','=',$date) as $key => $dv) {
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
        $xdate = explode(",", $date);
        $d = DailyReportIndoIchaNur::whereBetween('date', [$xdate[0],$xdate[1]])
            ->orderBy('id','DESC')
            ->get();
        $dateRange = $this->dateRange($xdate[0],$xdate[1]);
        $no = 1;
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
}