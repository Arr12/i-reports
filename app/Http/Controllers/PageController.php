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
use App\Models\m_inquiries;
use App\Models\m_media;
use App\Models\m_platform;
use App\Models\NonExclusiveReport;
use App\Models\ReportSpamMangatoonNovelList;
use App\Models\ReportSpamNovelListFromRanking;
use App\Models\ReportSpamRoyalRoadNovelList;
use App\Models\ReportSpamWNUncoractedNovelList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PageController extends Controller
{
    private $data_show = 7000;
    public $personGlobal = [
        'Ame',
        'Anna',
        'Carol',
        'Eric',
        'Icha',
        'Lily',
        'Maydewi',
        'Rani'
    ];
    public $personIndo = [
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
        // $textdt = date($date.'-01');
        $textdt = date($date.'-01', strtotime('first Week'));
        $textdt = date('Y-m-d', strtotime($textdt.'-1 days'));
        // dd($textdt);
        $dt= strtotime( $textdt);
        $currdt=$dt;
        $nextmonth=strtotime($textdt."+1 month");
        $i=0;
        $date = [
            'c_week' => [],
            'startdate' => [],
            'daystart' => [],
            'enddate' => [],
            'dayend' => [],
        ];
        do{
            $weekday= date("w",$currdt);
            $endday=abs($weekday-7);
            $startarr[$i]=$currdt;
            $endarr[$i]=strtotime(date("Y-m-d",$currdt)."+$endday day");
            $currdt=strtotime(date("Y-m-d",$endarr[$i])."+1 day");
            array_push($date['c_week'],"Week ".($i+1));
            array_push($date["startdate"], date("Y-m-d",$startarr[$i]));
            array_push($date["daystart"], date("D",$startarr[$i]));
            array_push($date["enddate"], date("Y-m-d",$endarr[$i]));
            array_push($date["dayend"], date("D",$endarr[$i]));
            $i++;
        }while($endarr[$i-1]<$nextmonth);
        return $date;
    }
    public function WeekFromDateFriday($date){
        // $textdt = date($date.'-01');
        $textdt = date($date.'-01', strtotime('first Week'));
        $textdt = date('Y-m-d', strtotime($textdt.'-3 days'));
        // dd($textdt);
        $dt= strtotime( $textdt);
        $currdt=$dt;
        $nextmonth=strtotime($textdt."+1 month");
        $i=0;
        $date = [
            'c_week' => [],
            'startdate' => [],
            'daystart' => [],
            'enddate' => [],
            'dayend' => [],
        ];
        do{
            $weekday= date("w",$currdt);
            $endday=6;
            $startarr[$i]=$currdt;
            $endarr[$i]=strtotime(date("Y-m-d",$currdt)."+$endday day");
            $currdt=strtotime(date("Y-m-d",$endarr[$i])."+1 day");
            array_push($date['c_week'],"Week ".($i+1));
            array_push($date["startdate"], date("Y-m-d",$startarr[$i]));
            array_push($date["daystart"], date("D",$startarr[$i]));
            array_push($date["enddate"], date("Y-m-d",$endarr[$i]));
            array_push($date["dayend"], date("D",$endarr[$i]));
            $i++;
        }while($endarr[$i-1]<$nextmonth);
        return $date;
    }
    public function GetDateWeekly($request = false){
        /* -----------
        | Senin - Minggu
        ------------------ */
        $date_i = $request ?: request()->input('m');
        $yd = $this->WeekFromDate($date_i);
        foreach($yd['c_week'] as $key => $data){
            $startdate = $yd['startdate'][$key];
            $enddate = $yd['enddate'][$key];
            $date['option'][] = "<option value='$data,$startdate,$enddate'>$data - $startdate/$enddate</option>";
        }
        return $date;
    }
    public function GetDateWeeklyFriday($request = false){
        $date_i = $request ?: request()->input('m');
        $yd = $this->WeekFromDateFriday($date_i);
        foreach($yd['c_week'] as $key => $data){
            $startdate = $yd['startdate'][$key];
            $enddate = $yd['enddate'][$key];
            $date['option'][] = "<option value='$data,$startdate,$enddate'>$data - $startdate/$enddate</option>";
        }
        return $date;
    }
    public function CachedDataSelects(){
        $cached = Cache::get('data-selects', false);
        if(!$cached){
            $s = 60 * 60 * 24;
            $cached = Cache::remember('data-selects', $s, function(){
                $platform = m_platform::orderBy('name','ASC')->get();
                $media = m_media::orderBy('name','ASC')->get();
                $inquiries = m_inquiries::orderBy('name','ASC')->get();
                $arr = [
                    'platform' => $platform,
                    'media' => $media,
                    'inquiries' => $inquiries
                ];
                return $arr;
            });
            $selects = $cached;
        }else{
            $selects = $cached;
        }
        return $selects;
    }
    public function index(){
        $arr = [];
        $result = array_merge($this->personGlobal, $this->personIndo);
        $arr['person'] = $result;
        $arr['data'] = [
            DailyReportAme::select('date')->where('created_at','>=',"NOW()")->count(),
            DailyReportAnna::select('date')->where('created_at','>=',"NOW()")->count(),
            DailyReportCarol::select('date')->where('created_at','>=',"NOW()")->count(),
            DailyReportEric::select('date')->where('created_at','>=',"NOW()")->count(),
            DailyReportIcha::select('date')->where('created_at','>=',"NOW()")->count(),
            DailyReportLily::select('date')->where('created_at','>=',"NOW()")->count(),
            DailyReportMaydewi::select('date')->where('created_at','>=',"NOW()")->count(),
            DailyReportRani::select('date')->where('created_at','>=',"NOW()")->count(),
            DailyReportIndoIchaNur::select('date')->where('created_at','>=',"NOW()")->count(),
            DailyReportIndoIrel::select('date')->where('created_at','>=',"NOW()")->count(),
        ];
        return view('admin.pages.home', [
            'datas' => $arr
        ]);
    }
    public function DailyReportMarker(){
        return view('admin.pages.daily-report.marker.daily-report-complete-marker',[
            'personGlobal' => $this->personGlobal,
            'personIndo' => $this->personIndo
        ]);
    }
    public function DailyReportAmes(){
        $selects = $this->CachedDataSelects();
        return view('admin.pages.daily-report.global.daily-report-ames', [
            'selects' => $selects,
        ]);
    }
    public function DailyReportAnnas(){
        $selects = $this->CachedDataSelects();
        return view('admin.pages.daily-report.global.daily-report-anna', [
            'selects' => $selects,
        ]);
    }
    public function DailyReportCarols(){
        $selects = $this->CachedDataSelects();
        return view('admin.pages.daily-report.global.daily-report-carol', [
            'selects' => $selects,
        ]);
    }
    public function DailyReportErics(){
        $selects = $this->CachedDataSelects();
        return view('admin.pages.daily-report.global.daily-report-eric', [
            'selects' => $selects,
        ]);
    }
    public function DailyReportIchas(){
        $selects = $this->CachedDataSelects();
        return view('admin.pages.daily-report.global.daily-report-icha', [
            'selects' => $selects,
        ]);
    }
    public function DailyReportLilies(){
        $selects = $this->CachedDataSelects();
        return view('admin.pages.daily-report.global.daily-report-lily', [
            'selects' => $selects,
        ]);
    }
    public function DailyReportMayDewis(){
        $selects = $this->CachedDataSelects();
        return view('admin.pages.daily-report.global.daily-report-maydewi', [
            'selects' => $selects,
        ]);
    }
    public function DailyReportRanis(){
        $selects = $this->CachedDataSelects();
        return view('admin.pages.daily-report.global.daily-report-rani', [
            'selects' => $selects,
        ]);
    }
    public function DailyReportIndoIchaNurs(){
        $selects = $this->CachedDataSelects();
        return view('admin.pages.daily-report.indo.daily-report-indo-icha-nur', [
            'selects' => $selects,
        ]);
    }
    public function DailyReportIndoIrels(){
        $selects = $this->CachedDataSelects();
        return view('admin.pages.daily-report.indo.daily-report-indo-irel', [
            'selects' => $selects,
        ]);
    }
    public function SpamMangatoonNovelList(){
        return view('admin.pages.spam.mangatoon');
    }
    public function SpamRoyalRoadNovelList(){
        return view('admin.pages.spam.royalroad');
    }
    public function SpamWNUncontractedNovelList(){
        return view('admin.pages.spam.wn-uncontracted');
    }
    public function SpamNovelListFromRanking(){
        return view('admin.pages.spam.novel-list-from-ranking');
    }
    public function NonExclusiveReport(){
        $selects = $this->CachedDataSelects();
        return view('admin.pages.non-exclusive-report.non-exclusive', [
            'selects' => $selects,
        ]);
    }
    public function ReportToSunny(){
        return view('admin.pages.report-to-sunny.report-to-sunny');
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

    /* ---------------------------------
    / DATA GLOBAL & NON EXCLUSIVE WITH DATE
    -------------------------------- */
    public function DataAme($startdate, $enddate){
        $d['daily'] = DailyReportAme::whereBetween('date', [$startdate,$enddate])
            ->orderBy('id','DESC')
            ->get();
        $d['fu_1'] = DailyReportAme::select('fu_1')->whereBetween('fu_1', [$startdate,$enddate])->get();
        $d['fu_2'] = DailyReportAme::select('fu_2')->whereBetween('fu_2', [$startdate,$enddate])->get();
        $d['fu_3'] = DailyReportAme::select('fu_3')->whereBetween('fu_3', [$startdate,$enddate])->get();
        $d['fu_4'] = DailyReportAme::select('fu_4')->whereBetween('fu_4', [$startdate,$enddate])->get();
        $d['fu_5'] = DailyReportAme::select('fu_5')->whereBetween('fu_5', [$startdate,$enddate])->get();
        $d['sent_royalty'] = DailyReportAme::select('sent_royalty')->whereBetween('sent_royalty', [$startdate,$enddate])->get();

        $editor = 'Ashley';
        $d['non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)
            ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
            ->get();
        $d['first_touch'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('first_touch', [$startdate,$enddate])->get();
        $d['fu_1_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_1', [$startdate,$enddate])->get();
        $d['fu_2_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_2', [$startdate,$enddate])->get();
        $d['fu_3_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_3', [$startdate,$enddate])->get();
        $d['fu_4_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_4', [$startdate,$enddate])->get();
        $d['fu_5_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_5', [$startdate,$enddate])->get();
        $d['sent_e_contract'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('sent_e_contract', [$startdate,$enddate])->get();
        $d['rec_e_contract'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('rec_e_contract', [$startdate,$enddate])->get();
        $d['email_sent'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('email_sent', [$startdate,$enddate])->get();

        return $d;
    }
    public function DataAnna($startdate, $enddate){
        $d['daily'] = DailyReportAnna::whereBetween('date', [$startdate,$enddate])
            ->orderBy('id','DESC')
            ->get();
        $d['fu_1'] = DailyReportAnna::select('fu_1')->whereBetween('fu_1', [$startdate,$enddate])->get();
        $d['fu_2'] = DailyReportAnna::select('fu_2')->whereBetween('fu_2', [$startdate,$enddate])->get();
        $d['fu_3'] = DailyReportAnna::select('fu_3')->whereBetween('fu_3', [$startdate,$enddate])->get();
        $d['fu_4'] = DailyReportAnna::select('fu_4')->whereBetween('fu_4', [$startdate,$enddate])->get();
        $d['fu_5'] = DailyReportAnna::select('fu_5')->whereBetween('fu_5', [$startdate,$enddate])->get();
        $d['sent_royalty'] = DailyReportAnna::select('sent_royalty')->whereBetween('sent_royalty', [$startdate,$enddate])->get();

        $editor = 'Erica';
        $d['non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)
            ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
            ->get();
        $d['first_touch'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('first_touch', [$startdate,$enddate])->get();
        $d['fu_1_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_1', [$startdate,$enddate])->get();
        $d['fu_2_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_2', [$startdate,$enddate])->get();
        $d['fu_3_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_3', [$startdate,$enddate])->get();
        $d['fu_4_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_4', [$startdate,$enddate])->get();
        $d['fu_5_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_5', [$startdate,$enddate])->get();
        $d['sent_e_contract'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('sent_e_contract', [$startdate,$enddate])->get();
        $d['rec_e_contract'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('rec_e_contract', [$startdate,$enddate])->get();
        $d['email_sent'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('email_sent', [$startdate,$enddate])->get();
        return $d;
    }
    public function DataCarol($startdate, $enddate){
        $d['daily'] = DailyReportCarol::whereBetween('date', [$startdate,$enddate])
            ->orderBy('id','DESC')
            ->get();
        $d['fu_1'] = DailyReportCarol::select('fu_1')->whereBetween('fu_1', [$startdate,$enddate])->get();
        $d['fu_2'] = DailyReportCarol::select('fu_2')->whereBetween('fu_2', [$startdate,$enddate])->get();
        $d['fu_3'] = DailyReportCarol::select('fu_3')->whereBetween('fu_3', [$startdate,$enddate])->get();
        $d['fu_4'] = DailyReportCarol::select('fu_4')->whereBetween('fu_4', [$startdate,$enddate])->get();
        $d['fu_5'] = DailyReportCarol::select('fu_5')->whereBetween('fu_5', [$startdate,$enddate])->get();
        $d['sent_royalty'] = DailyReportCarol::select('sent_royalty')->whereBetween('sent_royalty', [$startdate,$enddate])->get();

        $editor = 'Destiny';
        $d['non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)
            ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
            ->get();
        $d['first_touch'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('first_touch', [$startdate,$enddate])->get();
        $d['fu_1_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_1', [$startdate,$enddate])->get();
        $d['fu_2_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_2', [$startdate,$enddate])->get();
        $d['fu_3_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_3', [$startdate,$enddate])->get();
        $d['fu_4_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_4', [$startdate,$enddate])->get();
        $d['fu_5_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_5', [$startdate,$enddate])->get();
        $d['sent_e_contract'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('sent_e_contract', [$startdate,$enddate])->get();
        $d['rec_e_contract'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('rec_e_contract', [$startdate,$enddate])->get();
        $d['email_sent'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('email_sent', [$startdate,$enddate])->get();

        return $d;
    }
    public function DataEric($startdate, $enddate){
        $d['daily'] = DailyReportEric::whereBetween('date', [$startdate,$enddate])
            ->orderBy('id','DESC')
            ->get();
        $d['fu_1'] = DailyReportEric::select('fu_1')->whereBetween('fu_1', [$startdate,$enddate])->get();
        $d['fu_2'] = DailyReportEric::select('fu_2')->whereBetween('fu_2', [$startdate,$enddate])->get();
        $d['fu_3'] = DailyReportEric::select('fu_3')->whereBetween('fu_3', [$startdate,$enddate])->get();
        $d['fu_4'] = DailyReportEric::select('fu_4')->whereBetween('fu_4', [$startdate,$enddate])->get();
        $d['fu_5'] = DailyReportEric::select('fu_5')->whereBetween('fu_5', [$startdate,$enddate])->get();
        $d['sent_royalty'] = DailyReportEric::select('sent_royalty')->whereBetween('sent_royalty', [$startdate,$enddate])->get();

        $editor = 'Cornelia';
        $d['non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)
            ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
            ->get();
        $d['first_touch'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('first_touch', [$startdate,$enddate])->get();
        $d['fu_1_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_1', [$startdate,$enddate])->get();
        $d['fu_2_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_2', [$startdate,$enddate])->get();
        $d['fu_3_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_3', [$startdate,$enddate])->get();
        $d['fu_4_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_4', [$startdate,$enddate])->get();
        $d['fu_5_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_5', [$startdate,$enddate])->get();
        $d['sent_e_contract'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('sent_e_contract', [$startdate,$enddate])->get();
        $d['rec_e_contract'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('rec_e_contract', [$startdate,$enddate])->get();
        $d['email_sent'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('email_sent', [$startdate,$enddate])->get();

        return $d;
    }
    public function DataIcha($startdate, $enddate){
        $d['daily'] = DailyReportIcha::whereBetween('date', [$startdate,$enddate])
            ->orderBy('id','DESC')
            ->get();
        $d['fu_1'] = DailyReportIcha::select('fu_1')->whereBetween('fu_1', [$startdate,$enddate])->get();
        $d['fu_2'] = DailyReportIcha::select('fu_2')->whereBetween('fu_2', [$startdate,$enddate])->get();
        $d['fu_3'] = DailyReportIcha::select('fu_3')->whereBetween('fu_3', [$startdate,$enddate])->get();
        $d['fu_4'] = DailyReportIcha::select('fu_4')->whereBetween('fu_4', [$startdate,$enddate])->get();
        $d['fu_5'] = DailyReportIcha::select('fu_5')->whereBetween('fu_5', [$startdate,$enddate])->get();
        $d['sent_royalty'] = DailyReportIcha::select('sent_royalty')->whereBetween('sent_royalty', [$startdate,$enddate])->get();

        $editor = 'Claire';
        $d['non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)
            ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
            ->get();
        $d['first_touch'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('first_touch', [$startdate,$enddate])->get();
        $d['fu_1_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_1', [$startdate,$enddate])->get();
        $d['fu_2_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_2', [$startdate,$enddate])->get();
        $d['fu_3_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_3', [$startdate,$enddate])->get();
        $d['fu_4_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_4', [$startdate,$enddate])->get();
        $d['fu_5_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_5', [$startdate,$enddate])->get();
        $d['sent_e_contract'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('sent_e_contract', [$startdate,$enddate])->get();
        $d['rec_e_contract'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('rec_e_contract', [$startdate,$enddate])->get();
        $d['email_sent'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('email_sent', [$startdate,$enddate])->get();

        return $d;
    }
    public function DataLily($startdate, $enddate){
        $d['daily'] = DailyReportLily::whereBetween('date', [$startdate,$enddate])
            ->orderBy('id','DESC')
            ->get();
        $d['fu_1'] = DailyReportLily::select('fu_1')->whereBetween('fu_1', [$startdate,$enddate])->get();
        $d['fu_2'] = DailyReportLily::select('fu_2')->whereBetween('fu_2', [$startdate,$enddate])->get();
        $d['fu_3'] = DailyReportLily::select('fu_3')->whereBetween('fu_3', [$startdate,$enddate])->get();
        $d['fu_4'] = DailyReportLily::select('fu_4')->whereBetween('fu_4', [$startdate,$enddate])->get();
        $d['fu_5'] = DailyReportLily::select('fu_5')->whereBetween('fu_5', [$startdate,$enddate])->get();
        $d['sent_royalty'] = DailyReportLily::select('sent_royalty')->whereBetween('sent_royalty', [$startdate,$enddate])->get();

        $editor = 'Ensia';
        $d['non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)
            ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
            ->get();
        $d['first_touch'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('first_touch', [$startdate,$enddate])->get();
        $d['fu_1_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_1', [$startdate,$enddate])->get();
        $d['fu_2_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_2', [$startdate,$enddate])->get();
        $d['fu_3_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_3', [$startdate,$enddate])->get();
        $d['fu_4_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_4', [$startdate,$enddate])->get();
        $d['fu_5_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_5', [$startdate,$enddate])->get();
        $d['sent_e_contract'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('sent_e_contract', [$startdate,$enddate])->get();
        $d['rec_e_contract'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('rec_e_contract', [$startdate,$enddate])->get();
        $d['email_sent'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('email_sent', [$startdate,$enddate])->get();

        return $d;
    }
    public function DataMaydewi($startdate, $enddate){
        $d['daily'] = DailyReportMaydewi::whereBetween('date', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        $d['fu_1'] = DailyReportMaydewi::select('fu_1')->whereBetween('fu_1', [$startdate,$enddate])->get();
        $d['fu_2'] = DailyReportMaydewi::select('fu_2')->whereBetween('fu_2', [$startdate,$enddate])->get();
        $d['fu_3'] = DailyReportMaydewi::select('fu_3')->whereBetween('fu_3', [$startdate,$enddate])->get();
        $d['fu_4'] = DailyReportMaydewi::select('fu_4')->whereBetween('fu_4', [$startdate,$enddate])->get();
        $d['fu_5'] = DailyReportMaydewi::select('fu_5')->whereBetween('fu_5', [$startdate,$enddate])->get();
        $d['sent_royalty'] = DailyReportMaydewi::select('sent_royalty')->whereBetween('sent_royalty', [$startdate,$enddate])->get();

        $editor = 'Serena';
        $d['non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)
            ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
            ->get();
        $d['first_touch'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('first_touch', [$startdate,$enddate])->get();
        $d['fu_1_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_1', [$startdate,$enddate])->get();
        $d['fu_2_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_2', [$startdate,$enddate])->get();
        $d['fu_3_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_3', [$startdate,$enddate])->get();
        $d['fu_4_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_4', [$startdate,$enddate])->get();
        $d['fu_5_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_5', [$startdate,$enddate])->get();
        $d['sent_e_contract'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('sent_e_contract', [$startdate,$enddate])->get();
        $d['rec_e_contract'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('rec_e_contract', [$startdate,$enddate])->get();
        $d['email_sent'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('email_sent', [$startdate,$enddate])->get();

        return $d;
    }
    public function DataRani($startdate, $enddate){
        $d['daily'] = DailyReportRani::whereBetween('date', [$startdate,$enddate])
            ->orderBy('id','DESC')
            ->get();
        $d['fu_1'] = DailyReportRani::select('fu_1')->whereBetween('fu_1', [$startdate,$enddate])->get();
        $d['fu_2'] = DailyReportRani::select('fu_2')->whereBetween('fu_2', [$startdate,$enddate])->get();
        $d['fu_3'] = DailyReportRani::select('fu_3')->whereBetween('fu_3', [$startdate,$enddate])->get();
        $d['fu_4'] = DailyReportRani::select('fu_4')->whereBetween('fu_4', [$startdate,$enddate])->get();
        $d['fu_5'] = DailyReportRani::select('fu_5')->whereBetween('fu_5', [$startdate,$enddate])->get();
        $d['sent_royalty'] = DailyReportRani::select('sent_royalty')->whereBetween('sent_royalty', [$startdate,$enddate])->get();

        $editor = 'Aurora';
        $d['non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)
            ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
            ->get();
        $d['first_touch'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('first_touch', [$startdate,$enddate])->get();
        $d['fu_1_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_1', [$startdate,$enddate])->get();
        $d['fu_2_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_2', [$startdate,$enddate])->get();
        $d['fu_3_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_3', [$startdate,$enddate])->get();
        $d['fu_4_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_4', [$startdate,$enddate])->get();
        $d['fu_5_non_ex'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('fu_5', [$startdate,$enddate])->get();
        $d['sent_e_contract'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('sent_e_contract', [$startdate,$enddate])->get();
        $d['rec_e_contract'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('rec_e_contract', [$startdate,$enddate])->get();
        $d['email_sent'] = NonExclusiveReport::where('global_editor', '=', $editor)->whereBetween('email_sent', [$startdate,$enddate])->get();
        return $d;
    }
    public function DataNonExclusive($startdate, $enddate, $editor){
        $dn = NonExclusiveReport::where('global_editor', '=', $editor)
            ->whereBetween('date', [$startdate,$enddate])->orderBy('id','DESC')
            ->get();
        return $dn;
    }
    /* ---------------------------------
    / DATA GLOBAL & NON EXCLUSIVE NON DATE
    -------------------------------- */
    public function DataAmeCached(){
        $keyNonEx = "cache-ame";
        $cached = Cache::get($keyNonEx, false);
        if(!$cached){
            $query = DailyReportAme::orderBy('id', 'DESC')->limit($this->data_show)->get();
            Cache::put($keyNonEx, $query);
        } else {
            $query = $cached;
        }
        return $query;
    }
    public function DataAnnaCached(){
        $keyNonEx = "cache-anna";
        $cached = Cache::get($keyNonEx, false);
        if(!$cached){
            $query = DailyReportAnna::orderBy('id', 'DESC')->limit($this->data_show)->get();
            Cache::put($keyNonEx, $query);
        } else {
            $query = $cached;
        }
        return $query;
    }
    public function DataCarolCached(){
        $keyNonEx = "cache-carol";
        $cached = Cache::get($keyNonEx, false);
        if(!$cached){
            $query = DailyReportCarol::orderBy('id', 'DESC')->limit($this->data_show)->get();
            Cache::put($keyNonEx, $query);
        } else {
            $query = $cached;
        }
        return $query;
    }
    public function DataEricCached(){
        $keyNonEx = "cache-eric";
        $cached = Cache::get($keyNonEx, false);
        if(!$cached){
            $query = DailyReportEric::orderBy('id', 'DESC')->limit($this->data_show)->get();
            Cache::put($keyNonEx, $query);
        } else {
            $query = $cached;
        }
        return $query;
    }
    public function DataIchaCached(){
        $keyNonEx = "cache-icha";
        $cached = Cache::get($keyNonEx, false);
        if(!$cached){
            $query = DailyReportIcha::orderBy('id', 'DESC')->limit($this->data_show)->get();
            Cache::put($keyNonEx, $query);
        } else {
            $query = $cached;
        }
        return $query;
    }
    public function DataLilyCached(){
        $keyNonEx = "cache-lily";
        $cached = Cache::get($keyNonEx, false);
        if(!$cached){
            $query = DailyReportLily::orderBy('id', 'DESC')->limit($this->data_show)->get();
            Cache::put($keyNonEx, $query);
        } else {
            $query = $cached;
        }
        return $query;
    }
    public function DataMaydewiCached(){
        $keyNonEx = "cache-maydewi";
        $cached = Cache::get($keyNonEx, false);
        if(!$cached){
            $query = DailyReportMaydewi::orderBy('id', 'DESC')->limit($this->data_show)->get();
            Cache::put($keyNonEx, $query);
        } else {
            $query = $cached;
        }
        return $query;
    }
    public function DataRaniCached(){
        $keyNonEx = "cache-rani";
        $cached = Cache::get($keyNonEx, false);
        if(!$cached){
            $query = DailyReportRani::orderBy('id', 'DESC')->limit($this->data_show)->get();
            Cache::put($keyNonEx, $query);
        } else {
            $query = $cached;
        }
        return $query;
    }
    public function DataNonExCached(){
        $keyNonEx = "cache-non-exclusive";
        $cached = Cache::get($keyNonEx, false);
        if(!$cached){
            $query = NonExclusiveReport::orderBy('id', 'DESC')->limit($this->data_show)->get();
            Cache::put($keyNonEx, $query);
        } else {
            $query = $cached;
        }
        return $query;
    }

    /* ----------------------------------
    / DATA INDO WITH DATE
    --------------------------------- */
    public function DataIndoIchaNur($startdate, $enddate){
        $d['daily'] = DailyReportIndoIchaNur::whereBetween('date', [$startdate,$enddate])
            ->orderBy('id','DESC')
            ->get();
        $d['fu_1'] = DailyReportIndoIchaNur::select('fu_1')->whereBetween('fu_1', [$startdate,$enddate])->get();
        $d['fu_2'] = DailyReportIndoIchaNur::select('fu_2')->whereBetween('fu_2', [$startdate,$enddate])->get();
        $d['fu_3'] = DailyReportIndoIchaNur::select('fu_3')->whereBetween('fu_3', [$startdate,$enddate])->get();
        $d['fu_4'] = DailyReportIndoIchaNur::select('fu_4')->whereBetween('fu_4', [$startdate,$enddate])->get();
        $d['fu_5'] = DailyReportIndoIchaNur::select('fu_5')->whereBetween('fu_5', [$startdate,$enddate])->get();
        $d['data_sent'] = DailyReportIndoIchaNur::select('data_sent')->whereBetween('data_sent', [$startdate,$enddate])->get();

        return $d;
    }
    public function DataIndoIrel($startdate,$enddate){
        $d['daily'] = DailyReportIndoIrel::whereBetween('date', [$startdate,$enddate])
            ->orderBy('id','DESC')
            ->get();
        $d['fu_1'] = DailyReportIndoIrel::select('fu_1')->whereBetween('fu_1', [$startdate,$enddate])->get();
        $d['fu_2'] = DailyReportIndoIrel::select('fu_2')->whereBetween('fu_2', [$startdate,$enddate])->get();
        $d['fu_3'] = DailyReportIndoIrel::select('fu_3')->whereBetween('fu_3', [$startdate,$enddate])->get();
        $d['fu_4'] = DailyReportIndoIrel::select('fu_4')->whereBetween('fu_4', [$startdate,$enddate])->get();
        $d['fu_5'] = DailyReportIndoIrel::select('fu_5')->whereBetween('fu_5', [$startdate,$enddate])->get();
        $d['fu_6'] = DailyReportIndoIrel::select('fu_6')->whereBetween('fu_6', [$startdate,$enddate])->get();
        $d['fu_7'] = DailyReportIndoIrel::select('fu_7')->whereBetween('fu_7', [$startdate,$enddate])->get();
        $d['fu_8'] = DailyReportIndoIrel::select('fu_8')->whereBetween('fu_8', [$startdate,$enddate])->get();
        $d['fu_9'] = DailyReportIndoIrel::select('fu_9')->whereBetween('fu_9', [$startdate,$enddate])->get();
        $d['fu_10'] = DailyReportIndoIrel::select('fu_10')->whereBetween('fu_10', [$startdate,$enddate])->get();
        $d['date_solved'] = DailyReportIndoIrel::select('date_solved')->whereBetween('date_solved', [$startdate,$enddate])->get();
        return $d;
    }
    /* ----------------------------------
    / DATA INDO NON DATE
    --------------------------------- */
    public function DataIndoIchaNurCached(){
        $keyNonEx = "cache-ichanur";
        $cached = Cache::get($keyNonEx, false);
        if(!$cached){
            $query = DailyReportIndoIchaNur::orderBy('id', 'DESC')->limit($this->data_show)->get();
            Cache::put($keyNonEx, $query);
        } else {
            $query = $cached;
        }
        return $query;
    }
    public function DataIndoIrelCached(){
        $keyNonEx = "cache-irel";
        $cached = Cache::get($keyNonEx, false);
        if(!$cached){
            $query = DailyReportIndoIrel::orderBy('id', 'DESC')->limit($this->data_show)->get();
            Cache::put($keyNonEx, $query);
        } else {
            $query = $cached;
        }
        return $query;
    }

    /* -------------------------------
    / DATA SPAM WITH DATE
    --------------------------------- */
    public function DataSpamMangatoon($startdate,$enddate){
        $d['daily'] = ReportSpamMangatoonNovelList::whereBetween('date', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        $d['date_feedback_received'] = ReportSpamMangatoonNovelList::select('date_feedback_received')->whereBetween('date_feedback_received', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        return $d;
    }
    public function DataUncontractedWN($startdate,$enddate){
        $d['daily'] = ReportSpamWNUncoractedNovelList::whereBetween('date', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        $d['date_feedback_received'] = ReportSpamWNUncoractedNovelList::select('date_feedback_received')->whereBetween('date_feedback_received', [$startdate,$enddate])
        ->orderBy('id','DESC')
        ->get();
        return $d;
    }
    /* -------------------------------
    / DATA SPAM NON DATE
    --------------------------------- */
    public function DataSpamMangatoonCached(){
        $keyNonEx = "cache-mangatoon";
        $cached = Cache::get($keyNonEx, false);
        if(!$cached){
            $query = ReportSpamMangatoonNovelList::orderBy('id', 'DESC')->limit($this->data_show)->get();
            Cache::put($keyNonEx, $query);
        } else {
            $query = $cached;
        }
        return $query;
    }
    public function DataUncontractedWNCached(){
        $keyNonEx = "cache-wn_uncontracted";
        $cached = Cache::get($keyNonEx, false);
        if(!$cached){
            $query = ReportSpamWNUncoractedNovelList::orderBy('id', 'DESC')->limit($this->data_show)->get();
            Cache::put($keyNonEx, $query);
        } else {
            $query = $cached;
        }
        return $query;
    }

    /*---------------------------------------
    | DAILY REPORT MARKER
    -----------------------------------------*/
    public function getDailyReportMarker(Request $request){
        if($request->input('d') == 'indo'){
            $x = $this->ReportMarkerIndo();
        } else {
            $person = $request->input('p') ?: false;
            $x = $this->ReportMarkerGlobal($person);
        }
        return $x;
    }
    public function ReportMarkerIndo(){
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "No.",
            "Person",
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
        $key = 'marker-indo';
        $cached = Cache::get($key, false);
        if(!$cached){
            $s = 60 * 60 * 24;
            $cached = Cache::remember($key, $s,function(){
                $query = DailyReportIndoIchaNur::where('marker', '=', '7')->orderBy('id', 'DESC')->limit($this->data_show)->get();
                return $query;
            });
            $query = $cached;
        }else{
            $query = $cached;
        }
        $no = 1;
        foreach($query as $key => $data){
            array_push($data_array['data'], [
                $no++,
                "Icha Nur",
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
                $data->old_new_book,
            ]);
        }
        return $data_array;
    }
    public function ReportMarkerGlobal($p){
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "No.",
            "Person",
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
        if(!$p || $p == ''){
            $persons = $this->personGlobal;
        } else {
            $persons = [$p];
        }
        $no = 1;
        foreach($persons as $key => $person){
            switch ($person) {
                case 'Ame':
                    $query = DailyReportAme::where('marker', '=', '7')->orderBy('id', 'DESC')->limit($this->data_show)->get();
                    foreach($query as $key => $data){
                        array_push($data_array['data'], [
                            $no++,
                            $person,
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
                    break;
                case 'Anna':
                    $key = 'marker-global-anna';
                    $cached = Cache::get($key, false);
                    if(!$cached){
                        $s = 60 * 60 * 24;
                        $cached = Cache::remember($key, $s,function(){
                            $query = DailyReportAnna::where('marker', '=', '7')->orderBy('id', 'DESC')->limit($this->data_show)->get();
                            return $query;
                        });
                        $query = $cached;
                    }else{
                        $query = $cached;
                    }
                    foreach($query as $key => $data){
                        array_push($data_array['data'], [
                            $no++,
                            $person,
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
                    break;
                case 'Carol' :
                    $query = DailyReportCarol::where('marker', '=', '7')->orderBy('id', 'DESC')->limit($this->data_show)->get();
                    foreach($query as $key => $data){
                        array_push($data_array['data'], [
                            $no++,
                            $person,
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
                    break;
                case 'Eric' :
                    $query = DailyReportEric::where('marker', '=', '7')->orderBy('id', 'DESC')->limit($this->data_show)->get();
                    foreach($query as $key => $data){
                        array_push($data_array['data'], [
                            $no++,
                            $person,
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
                    break;
                case 'Icha' :
                    $query = DailyReportIcha::where('marker', '=', '7')->orderBy('id', 'DESC')->limit($this->data_show)->get();
                    foreach($query as $key => $data){
                        array_push($data_array['data'], [
                            $no++,
                            $person,
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
                    break;
                case 'Lily' :
                    $query = DailyReportLily::where('marker', '=', '7')->orderBy('id', 'DESC')->limit($this->data_show)->get();
                    foreach($query as $key => $data){
                        array_push($data_array['data'], [
                            $no++,
                            $person,
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
                    break;
                case 'Maydewi' :
                    $query = DailyReportMaydewi::where('marker', '=', '7')->orderBy('id', 'DESC')->limit($this->data_show)->get();
                    foreach($query as $key => $data){
                        array_push($data_array['data'], [
                            $no++,
                            $person,
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
                    break;
                case 'Rani' :
                    $query = DailyReportRani::where('marker', '=', '7')->orderBy('id', 'DESC')->limit($this->data_show)->get();
                    foreach($query as $key => $data){
                        array_push($data_array['data'], [
                            $no++,
                            $person,
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
                    break;
                default:
                    $query = [];
                    break;
            }
        }
        return $data_array;
    }

    /*---------------------------------------
    | LV 0 DAILY REPORT
    -----------------------------------------*/
    public function getDailyReportAmes(){
        $where = request()->input('where');
        $marker = request()->input('marker');
        if(isset($marker) && $marker!=''){
            $query = DailyReportAme::where('marker', '=', $marker)->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else if(isset($where) && $where != ''){
            $query = DailyReportAme::where('author_contact','ilike', '%'.$where.'%')->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else if((isset($where) && $where != '') && (isset($marker) && $marker != '')){
            $query = DailyReportAme::where('author_contact','ilike', '%'.$where.'%')->where('marker', '=', $marker)->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else{
            $query = $this->DataAmeCached();
        }
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "Edit",
            "Add Date",
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
            $btn = "<button type='button' id='BtnModalEdit' data-id='$data->id' data-date='$data->date' data-status='$data->status' data-media='$data->media' data-author_contact='$data->author_contact' data-inquiries='$data->inquiries' data-platform='$data->platform' data-platform_user='$data->platform_user' data-platform_title='$data->platform_title' data-username='$data->username' data-cbid='$data->cbid' data-title='$data->title' data-genre='$data->genre' data-plot='$data->plot' data-k4='$data->k4' data-maintain_account='$data->maintain_account' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#editModal'><i class='material-icons'>edit</i></button>";
            $btn2 = "<button type='button' id='BtnModalFollow' data-id='$data->id' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#followModal'><i class='material-icons'>add</i></button>";
            array_push($data_array['data'], [
                $btn,
                $btn2,
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
        $where = request()->input('where');
        $marker = request()->input('marker');
        if(isset($marker) && $marker!=''){
            $query = DailyReportAnna::where('marker', '=', $marker)->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else if(isset($where) && $where != ''){
            $query = DailyReportAnna::where('author_contact','ilike', '%'.$where.'%')->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else if((isset($where) && $where != '') && (isset($marker) && $marker != '')){
            $query = DailyReportAnna::where('author_contact','ilike', '%'.$where.'%')->where('marker', '=', $marker)->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else{
            $query = $this->DataAnnaCached();
        }
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "Edit",
            "Add Date",
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
            $btn = "<button type='button' id='BtnModalEdit' data-id='$data->id' data-date='$data->date' data-status='$data->status' data-media='$data->media' data-author_contact='$data->author_contact' data-inquiries='$data->inquiries' data-platform='$data->platform' data-platform_user='$data->platform_user' data-platform_title='$data->platform_title' data-username='$data->username' data-cbid='$data->cbid' data-title='$data->title' data-genre='$data->genre' data-plot='$data->plot' data-k4='$data->k4' data-maintain_account='$data->maintain_account' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#editModal'><i class='material-icons'>edit</i></button>";
            $btn2 = "<button type='button' id='BtnModalFollow' data-id='$data->id' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#followModal'><i class='material-icons'>add</i></button>";
            array_push($data_array['data'], [
                $btn,
                $btn2,
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
        $where = request()->input('where');
        $marker = request()->input('marker');
        if(isset($marker) && $marker!=''){
            $query = DailyReportCarol::where('marker', '=', $marker)->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else if(isset($where) && $where != ''){
            $query = DailyReportCarol::where('author_contact','ilike', '%'.$where.'%')->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else if((isset($where) && $where != '') && (isset($marker) && $marker != '')){
            $query = DailyReportCarol::where('author_contact','ilike', '%'.$where.'%')->where('marker', '=', $marker)->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else{
            $query = $this->DataCarolCached();
        }
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "Edit",
            "Add Date",
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
            $btn = "<button type='button' id='BtnModalEdit' data-id='$data->id' data-date='$data->date' data-status='$data->status' data-media='$data->media' data-author_contact='$data->author_contact' data-inquiries='$data->inquiries' data-platform='$data->platform' data-platform_user='$data->platform_user' data-platform_title='$data->platform_title' data-username='$data->username' data-cbid='$data->cbid' data-title='$data->title' data-genre='$data->genre' data-plot='$data->plot' data-k4='$data->k4' data-maintain_account='$data->maintain_account' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#editModal'><i class='material-icons'>edit</i></button>";
            $btn2 = "<button type='button' id='BtnModalFollow' data-id='$data->id' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#followModal'><i class='material-icons'>add</i></button>";
            array_push($data_array['data'], [
                $btn,
                $btn2,
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
        $where = request()->input('where');
        $marker = request()->input('marker');
        if(isset($marker) && $marker!=''){
            $query = DailyReportEric::where('marker', '=', $marker)->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else if(isset($where) && $where != ''){
            $query = DailyReportEric::where('author_contact','ilike', '%'.$where.'%')->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else if((isset($where) && $where != '') && (isset($marker) && $marker != '')){
            $query = DailyReportEric::where('author_contact','ilike', '%'.$where.'%')->where('marker', '=', $marker)->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else{
            $query = $this->DataEricCached();
        }
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "Edit",
            "Add Date",
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
            $btn = "<button type='button' id='BtnModalEdit' data-id='$data->id' data-date='$data->date' data-status='$data->status' data-media='$data->media' data-author_contact='$data->author_contact' data-inquiries='$data->inquiries' data-platform='$data->platform' data-platform_user='$data->platform_user' data-platform_title='$data->platform_title' data-username='$data->username' data-cbid='$data->cbid' data-title='$data->title' data-genre='$data->genre' data-plot='$data->plot' data-k4='$data->k4' data-maintain_account='$data->maintain_account' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#editModal'><i class='material-icons'>edit</i></button>";
            $btn2 = "<button type='button' id='BtnModalFollow' data-id='$data->id' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#followModal'><i class='material-icons'>add</i></button>";
            array_push($data_array['data'], [
                $btn,
                $btn2,
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
        $where = request()->input('where');
        $marker = request()->input('marker');
        if(isset($marker) && $marker!=''){
            $query = DailyReportIcha::where('marker', '=', $marker)->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else if(isset($where) && $where != ''){
            $query = DailyReportIcha::where('author_contact','ilike', '%'.$where.'%')->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else if((isset($where) && $where != '') && (isset($marker) && $marker != '')){
            $query = DailyReportIcha::where('author_contact','ilike', '%'.$where.'%')->where('marker', '=', $marker)->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else{
            $query = $this->DataIchaCached();
        }
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "Edit",
            "Add Date",
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
            $btn = "<button type='button' id='BtnModalEdit' data-id='$data->id' data-date='$data->date' data-status='$data->status' data-media='$data->media' data-author_contact='$data->author_contact' data-inquiries='$data->inquiries' data-platform='$data->platform' data-platform_user='$data->platform_user' data-platform_title='$data->platform_title' data-username='$data->username' data-cbid='$data->cbid' data-title='$data->title' data-genre='$data->genre' data-plot='$data->plot' data-k4='$data->k4' data-maintain_account='$data->maintain_account' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#editModal'><i class='material-icons'>edit</i></button>";
            $btn2 = "<button type='button' id='BtnModalFollow' data-id='$data->id' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#followModal'><i class='material-icons'>add</i></button>";
            array_push($data_array['data'], [
                $btn,
                $btn2,
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
        $where = request()->input('where');
        $marker = request()->input('marker');
        if(isset($marker) && $marker!=''){
            $query = DailyReportLily::where('marker', '=', $marker)->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else if(isset($where) && $where != ''){
            $query = DailyReportLily::where('author_contact','ilike', '%'.$where.'%')->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else if((isset($where) && $where != '') && (isset($marker) && $marker != '')){
            $query = DailyReportLily::where('author_contact','ilike', '%'.$where.'%')->where('marker', '=', $marker)->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else{
            $query = $this->DataLilyCached();
        }
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "Edit",
            "Add Date",
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
            $btn = "<button type='button' id='BtnModalEdit' data-id='$data->id' data-date='$data->date' data-status='$data->status' data-media='$data->media' data-author_contact='$data->author_contact' data-inquiries='$data->inquiries' data-platform='$data->platform' data-platform_user='$data->platform_user' data-platform_title='$data->platform_title' data-username='$data->username' data-cbid='$data->cbid' data-title='$data->title' data-genre='$data->genre' data-plot='$data->plot' data-k4='$data->k4' data-maintain_account='$data->maintain_account' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#editModal'><i class='material-icons'>edit</i></button>";
            $btn2 = "<button type='button' id='BtnModalFollow' data-id='$data->id' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#followModal'><i class='material-icons'>add</i></button>";
            array_push($data_array['data'], [
                $btn,
                $btn2,
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
        $where = request()->input('where');
        $marker = request()->input('marker');
        if(isset($marker) && $marker!=''){
            $query = DailyReportMaydewi::where('marker', '=', $marker)->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else if(isset($where) && $where != ''){
            $query = DailyReportMaydewi::where('author_contact','ilike', '%'.$where.'%')->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else if((isset($where) && $where != '') && (isset($marker) && $marker != '')){
            $query = DailyReportMaydewi::where('author_contact','ilike', '%'.$where.'%')->where('marker', '=', $marker)->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else{
            $query = $this->DataMaydewiCached();
        }
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "Edit",
            "Add Date",
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
            $btn = "<button type='button' id='BtnModalEdit' data-id='$data->id' data-date='$data->date' data-status='$data->status' data-media='$data->media' data-author_contact='$data->author_contact' data-inquiries='$data->inquiries' data-platform='$data->platform' data-platform_user='$data->platform_user' data-platform_title='$data->platform_title' data-username='$data->username' data-cbid='$data->cbid' data-title='$data->title' data-genre='$data->genre' data-plot='$data->plot' data-k4='$data->k4' data-maintain_account='$data->maintain_account' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#editModal'><i class='material-icons'>edit</i></button>";
            $btn2 = "<button type='button' id='BtnModalFollow' data-id='$data->id' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#followModal'><i class='material-icons'>add</i></button>";
            array_push($data_array['data'], [
                $btn,
                $btn2,
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
        $where = request()->input('where');
        $marker = request()->input('marker');
        if(isset($marker) && $marker!=''){
            $query = DailyReportRani::where('marker', '=', $marker)->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else if(isset($where) && $where != ''){
            $query = DailyReportRani::where('author_contact','ilike', '%'.$where.'%')->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else if((isset($where) && $where != '') && (isset($marker) && $marker != '')){
            $query = DailyReportRani::where('author_contact','ilike', '%'.$where.'%')->where('marker', '=', $marker)->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else{
            $query = $this->DataRaniCached();
        }
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "Edit",
            "Add Date",
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
            $btn = "<button type='button' id='BtnModalEdit' data-id='$data->id' data-date='$data->date' data-status='$data->status' data-media='$data->media' data-author_contact='$data->author_contact' data-inquiries='$data->inquiries' data-platform='$data->platform' data-platform_user='$data->platform_user' data-platform_title='$data->platform_title' data-username='$data->username' data-cbid='$data->cbid' data-title='$data->title' data-genre='$data->genre' data-plot='$data->plot' data-k4='$data->k4' data-maintain_account='$data->maintain_account' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#editModal'><i class='material-icons'>edit</i></button>";
            $btn2 = "<button type='button' id='BtnModalFollow' data-id='$data->id' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#followModal'><i class='material-icons'>add</i></button>";
            array_push($data_array['data'], [
                $btn,
                $btn2,
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
        $where = request()->input('where');
        $marker = request()->input('marker');
        if(isset($marker) && $marker!=''){
            $query = DailyReportIndoIchaNur::where('marker', '=', $marker)->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else if(isset($where) && $where != ''){
            $query = DailyReportIndoIchaNur::where('author_contact','ilike', '%'.$where.'%')->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else if((isset($where) && $where != '') && (isset($marker) && $marker != '')){
            $query = DailyReportIndoIchaNur::where('author_contact','ilike', '%'.$where.'%')->where('marker', '=', $marker)->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        else{
            $query = $this->DataIndoIchaNurCached();
        }
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "Edit",
            "Add Date",
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
            $btn = "<button type='button' id='BtnModalEdit' data-id='$data->id' data-date='$data->date' data-contact_way='$data->contact_way' data-author_contact='$data->author_contact' data-platform='$data->platform' data-status='$data->status' data-inquiries='$data->inquiries' data-new_cbid='$data->new_cbid' data-old_cbid='$data->old_cbid' data-author='$data->author' data-title='$data->title' data-genre='$data->genre' data-k4='$data->k4' data-plot='$data->plot' data-maintain_account='$data->maintain_account' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#editModal'><i class='material-icons'>edit</i></button>";
            $btn2 = "<button type='button' id='BtnModalFollow' data-id='$data->id' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#followModal'><i class='material-icons'>add</i></button>";
            array_push($data_array['data'], [
                $btn,
                $btn2,
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
        if(request()->input('where') && request()->input('where') != ''){
            $query = DailyReportIndoIrel::where('author_contact','ilike', '%'.request()->input('where').'%')->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }else{
            $query = $this->DataIndoIrelCached();
        }
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "Edit",
            "Add Date",
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
            $btn = "<button type='button' id='BtnModalEdit' data-id='$data->id' data-date='$data->date' data-status='$data->status' data-date_solved='$data->date_solved' data-author_contact='$data->author_contact' data-cbid='$data->cbid' data-title='$data->title' data-author='$data->author' data-zoom_tutorial='$data->zoom_tutorial' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#editModal'><i class='material-icons'>edit</i></button>";
            $btn2 = "<button type='button' id='BtnModalFollow' data-id='$data->id' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#followModal'><i class='material-icons'>add</i></button>";
            array_push($data_array['data'], [
                $btn,
                $btn2,
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

    /*-----------------------------
    | REPORT TO SUNNY
    -------------------------------*/
    public function getReportToSunny(Request $request){
        $report = $request->input('r');
        $type = $request->input('type');
        $category = $request->input('c');
        if($category == 'all'){
            $x = $this->ReportSunnyDataGlobal();
        }else{
            $date = [];
            if($type != 'ready'){
                $x = explode(',', $request->input('w'));
                array_push($date, $x[0]);
                array_push($date, $x[1]);
                array_push($date, $x[2]);
            } else {
                $DateWeekly = $this->WeekFromDateFriday(date('Y-m'));
                $Date = date('Y-m-d');
                foreach($DateWeekly['c_week'] as $key => $v_weekly){
                    $startdate = $DateWeekly['startdate'][$key];
                    $enddate = $DateWeekly['enddate'][$key];
                    $Date = date('Y-m-d');
                    if($Date >= date('Y-m-d',strtotime($startdate)) && $Date <= date('Y-m-d',strtotime($enddate))){
                        $y = $startdate.",".$enddate;
                        $x = explode(",",$y);
                        array_push($date, $v_weekly);
                    }
                }
                array_push($date, $x[0]);
                array_push($date, $x[1]);
            }

            if($report == 'spam'){
                $x = $this->WeeklyReportSunnySpam($date);
            }
            else if($report == 'indo'){
                $x = $this->WeeklyReportSunnyIndo($date);
            }
            else{
                $x = $this->WeeklyReportSunnyGlobal($date);
            }
        }
        return $x;
    }
    public function ReportSunnyDataGlobal(){
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $no=1;
        $title = [
            "No.", "Person", "MIA", "Reject", "Fan Fiction", "Non Fiction", "Non English", "Assisted by Other"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $persons = $this->personGlobal;
        foreach($persons as $key => $person){
            switch($person){
                case "Ame" :
                    $query = DailyReportAme::select('inquiries')->orderBy('id', 'ASC')->get();
                    array_push($data_array['data'], [
                        $no++,
                        $person,
                    ]);
                    for($i=2; $i<count($title); $i++){
                        $total = $query->where('inquiries','=',$title[$i])->whereNotNull('inquiries')->count();
                        array_push($data_array['data'][$key],$total);
                    }
                    break;
                case "Anna" :
                    $query = DailyReportAnna::select('inquiries')->orderBy('id', 'ASC')->get();
                    array_push($data_array['data'], [
                        $no++,
                        $person,
                    ]);
                    for($i=2; $i<count($title); $i++){
                        $total = $query->where('inquiries','=',$title[$i])->whereNotNull('inquiries')->count();
                        array_push($data_array['data'][$key],$total);
                    }
                    break;
                case "Carol" :
                    $query = DailyReportCarol::select('inquiries')->orderBy('id', 'ASC')->get();
                    array_push($data_array['data'], [
                        $no++,
                        $person,
                    ]);
                    for($i=2; $i<count($title); $i++){
                        $total = $query->where('inquiries','=',$title[$i])->whereNotNull('inquiries')->count();
                        array_push($data_array['data'][$key],$total);
                    }
                    break;
                case "Eric" :
                    $query = DailyReportEric::select('inquiries')->orderBy('id', 'ASC')->get();
                    array_push($data_array['data'], [
                        $no++,
                        $person,
                    ]);
                    for($i=2; $i<count($title); $i++){
                        $total = $query->where('inquiries','=',$title[$i])->whereNotNull('inquiries')->count();
                        array_push($data_array['data'][$key],$total);
                    }
                    break;
                case "Icha" :
                    $query = DailyReportIcha::select('inquiries')->orderBy('id', 'ASC')->get();
                    array_push($data_array['data'], [
                        $no++,
                        $person,
                    ]);
                    for($i=2; $i<count($title); $i++){
                        $total = $query->where('inquiries','=',$title[$i])->whereNotNull('inquiries')->count();
                        array_push($data_array['data'][$key],$total);
                    }
                    break;
                case "Lily" :
                    $query = DailyReportLily::select('inquiries')->orderBy('id', 'ASC')->get();
                    array_push($data_array['data'], [
                        $no++,
                        $person,
                    ]);
                    for($i=2; $i<count($title); $i++){
                        $total = $query->where('inquiries','=',$title[$i])->whereNotNull('inquiries')->count();
                        array_push($data_array['data'][$key],$total);
                    }
                    break;
                case "Maydewi" :
                    $query = DailyReportMaydewi::select('inquiries')->orderBy('id', 'ASC')->get();
                    array_push($data_array['data'], [
                        $no++,
                        $person,
                    ]);
                    for($i=2; $i<count($title); $i++){
                        $total = $query->where('inquiries','=',$title[$i])->whereNotNull('inquiries')->count();
                        array_push($data_array['data'][$key],$total);
                    }
                    break;
                case "Rani" :
                    $query = DailyReportRani::select('inquiries')->orderBy('id', 'ASC')->get();
                    array_push($data_array['data'], [
                        $no++,
                        $person,
                    ]);
                    for($i=2; $i<count($title); $i++){
                        $total = $query->where('inquiries','=',$title[$i])->whereNotNull('inquiries')->count();
                        array_push($data_array['data'][$key],$total);
                    }
                    break;
                default :
                    $query = [];
                    break;
            }
        }
        array_push($data_array['data'], [
            $no++,
            "Total",
        ]);
        for($i=2; $i<count($title); $i++){
            $total = 0;
            foreach($persons as $key => $person){
                $total += $data_array['data'][$key][$i];
            }
            array_push($data_array['data'][$key+1],$total);
        }
        array_push($data_array['data'], [
            $no++,
            "Average",
        ]);
        for($i=2; $i<count($title); $i++){
            $total = 0;
            foreach($persons as $key => $person){
                $total += $data_array['data'][$key][$i];
            }
            array_push($data_array['data'][$key+2],$total/count($persons));
        }
        return $data_array;
    }
    public function WeeklyReportSunnyGlobal($date){
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        // dd($date);
        $week = $date[0];
        $startdate = $date[1];
        $enddate = $date[2];
        $persons = $this->personGlobal;
        $title = [
            "No.",
            "Global",
            "$week"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $no = 1;
        $arr_titles = ["Email", "Facebook", "Whatsapp", "Instagram", "Discord"];
        foreach($arr_titles as $key1 => $arr_title){
            array_push($data_array['data'], [
                $no++,
                $arr_title,
            ]);
            $counter_new_author = 0;
            $counter_new_author_spam = 0;
            foreach($persons as $key => $person){
                switch($person){
                    case "Ame" :
                        $query = DailyReportAme::select('media')->where('status','=','New Author')->whereBetween('date',[$startdate,$enddate])->orderBy('id', 'ASC')->get();
                        $query2 = DailyReportAme::select('media')->where('status','=','New Author Spam')->whereBetween('date',[$startdate,$enddate])->orderBy('id', 'ASC')->get();
                        break;
                    case "Anna" :
                        $query = DailyReportAnna::select('media')->where('status','=','New Author')->whereBetween('date',[$startdate,$enddate])->orderBy('id', 'ASC')->get();
                        $query2 = DailyReportAnna::select('media')->where('status','=','New Author Spam')->whereBetween('date',[$startdate,$enddate])->orderBy('id', 'ASC')->get();
                        break;
                    case "Carol" :
                        $query = DailyReportCarol::select('media')->where('status','=','New Author')->whereBetween('date',[$startdate,$enddate])->orderBy('id', 'ASC')->get();
                        $query2 = DailyReportCarol::select('media')->where('status','=','New Author Spam')->whereBetween('date',[$startdate,$enddate])->orderBy('id', 'ASC')->get();
                        break;
                    case "Eric" :
                        $query = DailyReportEric::select('media')->where('status','=','New Author')->whereBetween('date',[$startdate,$enddate])->orderBy('id', 'ASC')->get();
                        $query2 = DailyReportEric::select('media')->where('status','=','New Author Spam')->whereBetween('date',[$startdate,$enddate])->orderBy('id', 'ASC')->get();
                        break;
                    case "Icha" :
                        $query = DailyReportIcha::select('media')->where('status','=','New Author')->whereBetween('date',[$startdate,$enddate])->orderBy('id', 'ASC')->get();
                        $query2 = DailyReportIcha::select('media')->where('status','=','New Author Spam')->whereBetween('date',[$startdate,$enddate])->orderBy('id', 'ASC')->get();
                        break;
                    case "Lily" :
                        $query = DailyReportLily::select('media')->where('status','=','New Author')->whereBetween('date',[$startdate,$enddate])->orderBy('id', 'ASC')->get();
                        $query2 = DailyReportLily::select('media')->where('status','=','New Author Spam')->whereBetween('date',[$startdate,$enddate])->orderBy('id', 'ASC')->get();
                        break;
                    case "Maydewi" :
                        $query = DailyReportMaydewi::select('media')->where('status','=','New Author')->whereBetween('date',[$startdate,$enddate])->orderBy('id', 'ASC')->get();
                        $query2 = DailyReportMaydewi::select('media')->where('status','=','New Author Spam')->whereBetween('date',[$startdate,$enddate])->orderBy('id', 'ASC')->get();
                        break;
                    case "Rani" :
                        $query = DailyReportRani::select('media')->where('status','=','New Author')->whereBetween('date',[$startdate,$enddate])->orderBy('id', 'ASC')->get();
                        $query2 = DailyReportRani::select('media')->where('status','=','New Author Spam')->whereBetween('date',[$startdate,$enddate])->orderBy('id', 'ASC')->get();
                        break;
                    default :
                        $query = [];
                        $query2 = [];
                        break;
                }
                $counter_new_author += $query->where('media','=',$arr_title)->whereNotNull('media')->count();
                $counter_new_author_spam += $query2->where('media','=',$arr_title)->whereNotNull('media')->count();
            }
            array_push($data_array['data'][$key1], $counter_new_author + $counter_new_author_spam);
        }
        return $data_array;
    }
    public function WeeklyReportSunnyIndo($date){
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $week = $date[0];
        $startdate = $date[1];
        $enddate = $date[2];

        $title = [
            "No.",
            "Global",
            "$week"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $arr_titles = ["Email", "Facebook", "Whatsapp", "Instagram", "Discord"];
        $no = 1;
        foreach($arr_titles as $key1 => $arr_title){
            array_push($data_array['data'], [
                $no++,
                $arr_title,
            ]);
            $query = DailyReportIndoIchaNur::select('contact_way')->whereBetween('date',[$startdate,$enddate])->orderBy('id', 'ASC')->get();
            $c_data = $query->where('contact_way','=',$arr_title)->whereNotNull('contact_way')->count();
            array_push($data_array['data'][$key1], $c_data);
        }
        return $data_array;
    }
    public function WeeklyReportSunnySpam($date){
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        // dd($date);
        $week = $date[0];
        $startdate = $date[1];
        $enddate = $date[2];
        $spams = ["Mangatoon","WN Uncontracted"];
        $title = [
            "No.",
            "Spam Team",
            "Invitation Sent",
            "Messages Received"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $no = 1;
        foreach($spams as $key1 => $spam){
            array_push($data_array['data'], [
                $no++,
                $spam,
            ]);
            $counter_invitation = 0;
            $counter_received = 0;
            switch($spam){
                case "Mangatoon" :
                    $query = ReportSpamMangatoonNovelList::whereBetween('date',[$startdate,$enddate])->orderBy('id', 'ASC')->get();
                    break;
                case "WN Uncontracted" :
                    $query = ReportSpamWNUncoractedNovelList::whereBetween('date',[$startdate,$enddate])->orderBy('id', 'ASC')->get();
                    break;
                default :
                    $query = [];
                    break;
            }
            $counter_invitation += $query->whereNotNull('date')->count();
            array_push($data_array['data'][$key1], $counter_invitation);
            $counter_received += $query->whereNotNull('date')->count();
            array_push($data_array['data'][$key1], $counter_received);
        }
        return $data_array;
    }

    /*---------------------------------------
    | REPORT SPAM
    -----------------------------------------*/
    public function getSpamMangatoonNovelList(){
        if(request()->input('where') && request()->input('where') != ''){
            $query = ReportSpamMangatoonNovelList::where('author_name','ilike','%'.request()->input('where').'%')->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }else{
            $query = $this->DataSpamMangatoonCached();
        }
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "Edit",
            "Add Date",
            "No.",
            "Date Invitation Sent",
            "Book Name",
            "Author Name",
            "Views",
            "Likes",
            "Ratings",
            "Update Status",
            "Tags",
            "Episodes",
            "Link",
            "Screenshot from Wave",
            "Date Feedback Received",
            "Author's Feedback",
            "Comments From Wave",
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        foreach($query as $key => $data){
            $btn = "<button type='button' id='BtnModalEdit' data-id='$data->id' data-date='$data->date' data-book_name='$data->book_name' data-author_name='$data->author_name' data-views='$data->views' data-likes='$data->likes' data-ratings='$data->ratings' data-update_status='$data->update_status' data-tags='$data->tags' data-episodes='$data->episodes' data-link='$data->link' data-screenshot_from_wave='$data->screenshot_from_wave' data-author_feedback='$data->author_feedback' data-comment_from_wave='$data->comment_from_wave' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#editModal'><i class='material-icons'>edit</i></button>";
            $btn2 = "<button type='button' id='BtnModalFollow' data-id='$data->id' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#followModal'><i class='material-icons'>add</i></button>";
            array_push($data_array['data'], [
                $btn,
                $btn2,
                $data->id,
                $data->date,
                $data->book_name,
                $data->author_name,
                $data->views,
                $data->likes,
                $data->ratings,
                $data->update_status,
                $data->tags,
                $data->episodes,
                $data->link ,
                $data->screenshot_from_wave,
                $data->date_feedback_received,
                $data->author_feedback,
                $data->comment_from_wave
            ]);
        }
        return $data_array;
    }
    public function getSpamRoyalRoadNovelList(){
        if(request()->input('where') && request()->input('where') != ''){
            $query = ReportSpamRoyalRoadNovelList::where('author','ilike','%'.request()->input('where').'%')->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }else{
            $query = ReportSpamRoyalRoadNovelList::orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "Edit",
            "Add Date",
            "No.",
            "Date Invitation Sent",
            "Editor",
            "Title",
            "Author",
            "URL",
            "Type",
            "Followers",
            "Pages",
            "Chapters",
            "Views",
            "Latest Updates",
            "tags6",
            "Tag1",
            "Tag2",
            "Tag3",
            "Tag4",
            "Tag5",
            "Tag6",
            "tags7",
            "tags8",
            "tags9",
            "tags10",
            "tags11",
            "Date Feedback Received",
            "Feedback From Author",
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        foreach($query as $key => $data){
            $btn = "<button type='button' id='BtnModalEdit' data-id='$data->id' data-date='$data->date' data-book_name='$data->book_name' data-author_name='$data->author_name' data-views='$data->views' data-likes='$data->likes' data-ratings='$data->ratings' data-update_status='$data->update_status' data-tags='$data->tags' data-episodes='$data->episodes' data-link='$data->link' data-screenshot_from_wave='$data->screenshot_from_wave' data-author_feedback='$data->author_feedback' data-comment_from_wave='$data->comment_from_wave' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#editModal'><i class='material-icons'>edit</i></button>";
            $btn2 = "<button type='button' id='BtnModalFollow' data-id='$data->id' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#followModal'><i class='material-icons'>add</i></button>";
            array_push($data_array['data'], [
                $btn,
                $btn2,
                $data->id,
                $data->date,
                $data->editor,
                $data->title,
                $data->author,
                $data->url,
                $data->type,
                $data->followers,
                $data->pages,
                $data->chapters,
                $data->views,
                $data->latest_update,
                $data->tags6,
                $data->tag1,
                $data->tag2,
                $data->tag3,
                $data->tag4,
                $data->tag5,
                $data->tag6,
                $data->tags7,
                $data->tags8,
                $data->tags9,
                $data->tags10,
                $data->tags11,
                $data->date_feedback_received,
                $data->feedback_from_author,
            ]);
        }
        return $data_array;
    }
    public function getSpamWNUncontractedNovelList(){
        if(request()->input('where') && request()->input('where') != ''){
            $query = ReportSpamWNUncoractedNovelList::where('author_name','ilike',"%".request()->input('where')."%")->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }else{
            $query = $this->DataUncontractedWNCached();
        }
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "Edit",
            "Add Date",
            "No.",
            "Date Invitation Sent",
            "Editor",
            "CBID",
            "Book Title",
            "Author name",
            "Discord's Contact",
            "Other Contact Way",
            "Genre",
            "Total Chapter",
            "Chapter within 7 days",
            "Collection",
            "Ongoing/Completed",
            "FL/ML",
            "Date Feedback Received",
            "Feedback From Author",
            "Note"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        foreach($query as $key => $data){
            $btn = "<button type='button' id='BtnModalEdit' data-id='$data->id' data-date='$data->date' data-book_title='$data->book_title' data-author_name='$data->author_name' data-discord_contact='$data->discord_contact' data-other_contact_way='$data->other_contact_way' data-genre='$data->genre' data-total_chapter='$data->total_chapter' data-chapter_within_7_days='$data->chapter_within_7_days' data-collection='$data->collection' data-status_ongoing='$data->status_ongoing' data-FL_ML='$data->FL_ML' data-feedback_from_author='$data->feedback_from_author' data-note='$data->note' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#editModal'><i class='material-icons'>edit</i></button>";
            $btn2 = "<button type='button' id='BtnModalFollow' data-id='$data->id' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#followModal'><i class='material-icons'>add</i></button>";
            array_push($data_array['data'], [
                $btn,
                $btn2,
                $data->id,
                $data->date,
                $data->editor,
                $data->cbid,
                $data->book_title,
                $data->author_name,
                $data->discord_contact,
                $data->other_contact_way,
                $data->genre,
                $data->total_chapter,
                $data->chapter_within_7_days ,
                $data->collection,
                $data->status_ongoing,
                $data->FL_ML,
                $data->date_feedback_received,
                $data->feedback_from_author,
                $data->note
            ]);
        }
        return $data_array;
    }
    public function getSpamNovelListFromRanking(){
        if(request()->input('where') && request()->input('where') != ''){
            $query = ReportSpamNovelListFromRanking::where('author_contact','ilike',"%".request()->input('where')."%")->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }else{
            $query = ReportSpamNovelListFromRanking::orderBy('id', 'DESC')->limit($this->data_show)->get();
        }
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "Edit",
            "No.",
            "CBID",
            "Book Title",
            "Author name",
            "Author's Contact",
            "Genre",
            "Total Chapter",
            "Chapter within 7 days",
            "Collections",
            "Ongoing/Completed",
            "FL/ML",
            "Editor",
            "Note",
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        foreach($query as $key => $data){
            $btn = "<button type='button' id='BtnModalEdit' data-id='$data->id' data-date='$data->date' data-book_title='$data->book_title' data-author_name='$data->author_name' data-discord_contact='$data->discord_contact' data-other_contact_way='$data->other_contact_way' data-genre='$data->genre' data-total_chapter='$data->total_chapter' data-chapter_within_7_days='$data->chapter_within_7_days' data-collection='$data->collection' data-status_ongoing='$data->status_ongoing' data-FL_ML='$data->FL_ML' data-feedback_from_author='$data->feedback_from_author' data-note='$data->note' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#editModal'><i class='material-icons'>edit</i></button>";
            array_push($data_array['data'], [
                $btn,
                $data->id,
                $data->cbid,
                $data->book_title,
                $data->author_name,
                $data->author_contact,
                $data->genre,
                $data->total_chapter,
                $data->chapter_within_7_days,
                $data->collection,
                $data->status_ongoing,
                $data->FL_ML ,
                $data->editor,
                $data->note,
            ]);
        }
        return $data_array;
    }

    /*---------------------------------------
    | NON EXCLUSIVE REPORT
    -----------------------------------------*/
    public function getNonExclusiveReport(){
        $where = request()->input('where');
        if($where && $where != ''){
            $query = NonExclusiveReport::where('author_contact','ilike', '%'.$where.'%')->orderBy('id', 'DESC')->limit($this->data_show)->get();
        }else{
            $query = $this->DataNonExCached();
        }
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "Edit",
            "Add Date",
            "No.",
            "Date",
            "Global Editor",
            "Author Contact",
            "Platform",
            "Username",
            "Title",
            "Book Status",
            "Latest Chapter",
            "First Touch",
            "Book ID",
            "Sent E-Cont",
            "Officer",
            "Date",
            "AnD Notes",
            "Global Editor Notes",
            "Solved Date",
            "PDF Evidence",
            "Rec. E-cont",
            "Follow up 1",
            "Follow up 2",
            "Follow up 3",
            "Follow up 4",
            "Follow up 5",
            "Marker for Global",
            "Marker for AnD",
            "Email Sent",
            "Batch Date",
            "AnD Evidence",
            "Global Evidence"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        foreach($query as $key => $data){
            $btn = "<button type='button' id='BtnModalEdit' data-id='$data->id' data-date='$data->date' data-global_editor='$data->global_editor' data-author_contact='$data->author_contact' data-platform='$data->platform' data-username='$data->username' data-title='$data->title' data-book_status='$data->book_status' data-latest_update='$data->latest_update' data-first_touch='$data->first_touch' data-book_id='$data->book_id' data-sent_e_contract='$data->sent_e_contract' data-officer='$data->officer' data-date_sent='$data->date_sent' data-and_notes='$data->and_notes' data-global_editor_notes='$data->global_editor_notes' data-solved_date='$data->solved_date' data-pdf_evidence='$data->pdf_evidence' data-rec_e_contract='$data->rec_e_contract' data-fu_1='$data->fu_1' data-fu_2='$data->fu_2' data-fu_3='$data->fu_3' data-fu_4='$data->fu_4' data-fu_5='$data->fu_5' data-email_sent='$data->email_sent' data-batch_date='$data->batch_date' data-and_evidence='$data->and_evidence' data-global_evidence='$data->global_evidence' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#editModal'><i class='material-icons'>edit</i></button>";
            $btn2 = "<button type='button' id='BtnModalFollow' data-id='$data->id' class='btn btn-primary waves-effect m-r-20' data-toggle='modal' data-target='#followModal'><i class='material-icons'>add</i></button>";
            array_push($data_array['data'], [
                $btn,
                $btn2,
                $data->id,
                $data->date,
                $data->global_editor,
                $data->author_contact,
                $data->platform,
                $data->username,
                $data->title,
                $data->book_status,
                $data->latest_update,
                $data->first_touch,
                $data->book_id,
                $data->sent_e_contract,
                $data->officer,
                $data->date_sent,
                $data->and_notes,
                $data->global_editor_notes,
                $data->solved_date,
                $data->pdf_evidence,
                $data->rec_e_contract,
                $data->fu_1,
                $data->fu_2,
                $data->fu_3,
                $data->fu_4,
                $data->fu_5,
                $data->marker_for_global,
                $data->marker_for_and,
                $data->email_sent,
                $data->batch_date,
                $data->and_evidence,
                $data->global_evidence,
            ]);
        }
        return $data_array;
    }

    /*---------------------------------------
    | LV 1 ALL TEAM REPORT
    -----------------------------------------*/
    public function getGlobalTeamMonitoring(Request $request){
        $person = Str::slug($request->input('mod'));
        $month = $request->input('mon');
        $DateWeekly = $this->WeekFromDate($month);
        $date_start = date("Y-m-d", strtotime($DateWeekly['startdate'][0]));
        $date_end = date("Y-m-d", strtotime(end($DateWeekly['enddate'])));
        // $date_start = date($month."-d", strtotime("first day of this month"));
        // $date_end = date($month."-d", strtotime("last day of this month"));
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
            $d['first_touch']->whereNotNull('first_touch')->count(),
            $d['fu_1']->whereNotNull('fu_1')->count()+$d['fu_2']->whereNotNull('fu_2')->count()+$d['fu_3']->whereNotNull('fu_3')->count()+$d['fu_4']->whereNotNull('fu_4')->count()+$d['fu_5']->whereNotNull('fu_5')->count(),
            $d['fu_1_non_ex']->whereNotNull('fu_1')->count()+$d['fu_2_non_ex']->whereNotNull('fu_2')->count()+$d['fu_3_non_ex']->whereNotNull('fu_3')->count()+$d['fu_4_non_ex']->whereNotNull('fu_4')->count()+$d['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $d['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $d['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $d['email_sent']->whereNotNull('email_sent')->count(),
            $d['sent_royalty']->whereNotNull('sent_royalty')->count()
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
            }
            foreach ($d['sent_royalty']->where('sent_royalty','=',$date) as $key => $dv) {
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($d['fu_1']->where('fu_1','=',$date) as $key => $dv){
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
            }
            foreach ($d['fu_2']->where('fu_2','=',$date) as $key => $dv){
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
            }
            foreach ($d['fu_3']->where('fu_3','=',$date) as $key => $dv){
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
            }
            foreach ($d['fu_4']->where('fu_4','=',$date) as $key => $dv){
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
            }
            foreach ($d['fu_5']->where('fu_5','=',$date) as $key => $dv){
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
            }
            foreach ($d['first_touch']->where('first_touch','=',$date) as $key => $dv) {
                $dv->first_touch!=null ? array_push($n_auth_non_ex, $dv->first_touch) : null;
            }
            foreach ($d['fu_1_non_ex']->where('fu_1','=',$date) as $key => $dv) {
                $dv->fu_1!=null ? array_push($fu_non_ex_1, $dv->fu_1) : null;
            }
            foreach ($d['fu_2_non_ex']->where('fu_2','=',$date) as $key => $dv) {
                $dv->fu_2!=null ? array_push($fu_non_ex_2, $dv->fu_2) : null;
            }
            foreach ($d['fu_3_non_ex']->where('fu_3','=',$date) as $key => $dv) {
                $dv->fu_3!=null ? array_push($fu_non_ex_3, $dv->fu_3) : null;
            }
            foreach ($d['fu_4_non_ex']->where('fu_4','=',$date) as $key => $dv) {
                $dv->fu_4!=null ? array_push($fu_non_ex_4, $dv->fu_4) : null;
            }
            foreach ($d['fu_5_non_ex']->where('fu_5','=',$date) as $key => $dv) {
                $dv->fu_5!=null ? array_push($fu_non_ex_5, $dv->fu_5) : null;
            }
            foreach ($d['sent_e_contract']->where('sent_e_contract','=',$date) as $key => $dv) {
                $dv->sent_e_contract!=null ? array_push($sent_e, $dv->sent_e_contract) : null;
            }
            foreach ($d['rec_e_contract']->where('rec_e_contract','=',$date) as $key => $dv) {
                $dv->rec_e_contract!=null ? array_push($rec_e, $dv->rec_e_contract) : null;
            }
            foreach ($d['email_sent']->where('email_sent','=',$date) as $key => $dv) {
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
            $d['first_touch']->whereNotNull('first_touch')->count(),
            $d['fu_1']->whereNotNull('fu_1')->count()+$d['fu_2']->whereNotNull('fu_2')->count()+$d['fu_3']->whereNotNull('fu_3')->count()+$d['fu_4']->whereNotNull('fu_4')->count()+$d['fu_5']->whereNotNull('fu_5')->count(),
            $d['fu_1_non_ex']->whereNotNull('fu_1')->count()+$d['fu_2_non_ex']->whereNotNull('fu_2')->count()+$d['fu_3_non_ex']->whereNotNull('fu_3')->count()+$d['fu_4_non_ex']->whereNotNull('fu_4')->count()+$d['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $d['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $d['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $d['email_sent']->whereNotNull('email_sent')->count(),
            $d['sent_royalty']->whereNotNull('sent_royalty')->count()
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
            }
            foreach ($d['sent_royalty']->where('sent_royalty','=',$date) as $key => $dv) {
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($d['fu_1']->where('fu_1','=',$date) as $key => $dv){
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
            }
            foreach ($d['fu_2']->where('fu_2','=',$date) as $key => $dv){
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
            }
            foreach ($d['fu_3']->where('fu_3','=',$date) as $key => $dv){
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
            }
            foreach ($d['fu_4']->where('fu_4','=',$date) as $key => $dv){
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
            }
            foreach ($d['fu_5']->where('fu_5','=',$date) as $key => $dv){
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
            }
            foreach ($d['first_touch']->where('first_touch','=',$date) as $key => $dv) {
                $dv->first_touch!=null ? array_push($n_auth_non_ex, $dv->first_touch) : null;
            }
            foreach ($d['fu_1_non_ex']->where('fu_1','=',$date) as $key => $dv) {
                $dv->fu_1!=null ? array_push($fu_non_ex_1, $dv->fu_1) : null;
            }
            foreach ($d['fu_2_non_ex']->where('fu_2','=',$date) as $key => $dv) {
                $dv->fu_2!=null ? array_push($fu_non_ex_2, $dv->fu_2) : null;
            }
            foreach ($d['fu_3_non_ex']->where('fu_3','=',$date) as $key => $dv) {
                $dv->fu_3!=null ? array_push($fu_non_ex_3, $dv->fu_3) : null;
            }
            foreach ($d['fu_4_non_ex']->where('fu_4','=',$date) as $key => $dv) {
                $dv->fu_4!=null ? array_push($fu_non_ex_4, $dv->fu_4) : null;
            }
            foreach ($d['fu_5_non_ex']->where('fu_5','=',$date) as $key => $dv) {
                $dv->fu_5!=null ? array_push($fu_non_ex_5, $dv->fu_5) : null;
            }
            foreach ($d['sent_e_contract']->where('sent_e_contract','=',$date) as $key => $dv) {
                $dv->sent_e_contract!=null ? array_push($sent_e, $dv->sent_e_contract) : null;
            }
            foreach ($d['rec_e_contract']->where('rec_e_contract','=',$date) as $key => $dv) {
                $dv->rec_e_contract!=null ? array_push($rec_e, $dv->rec_e_contract) : null;
            }
            foreach ($d['email_sent']->where('email_sent','=',$date) as $key => $dv) {
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
            $d['first_touch']->whereNotNull('first_touch')->count(),
            $d['fu_1']->whereNotNull('fu_1')->count()+$d['fu_2']->whereNotNull('fu_2')->count()+$d['fu_3']->whereNotNull('fu_3')->count()+$d['fu_4']->whereNotNull('fu_4')->count()+$d['fu_5']->whereNotNull('fu_5')->count(),
            $d['fu_1_non_ex']->whereNotNull('fu_1')->count()+$d['fu_2_non_ex']->whereNotNull('fu_2')->count()+$d['fu_3_non_ex']->whereNotNull('fu_3')->count()+$d['fu_4_non_ex']->whereNotNull('fu_4')->count()+$d['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $d['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $d['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $d['email_sent']->whereNotNull('email_sent')->count(),
            $d['sent_royalty']->whereNotNull('sent_royalty')->count()
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
            }
            foreach ($d['sent_royalty']->where('sent_royalty','=',$date) as $key => $dv) {
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($d['fu_1']->where('fu_1','=',$date) as $key => $dv){
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
            }
            foreach ($d['fu_2']->where('fu_2','=',$date) as $key => $dv){
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
            }
            foreach ($d['fu_3']->where('fu_3','=',$date) as $key => $dv){
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
            }
            foreach ($d['fu_4']->where('fu_4','=',$date) as $key => $dv){
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
            }
            foreach ($d['fu_5']->where('fu_5','=',$date) as $key => $dv){
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
            }
            foreach ($d['first_touch']->where('first_touch','=',$date) as $key => $dv) {
                $dv->first_touch!=null ? array_push($n_auth_non_ex, $dv->first_touch) : null;
            }
            foreach ($d['fu_1_non_ex']->where('fu_1','=',$date) as $key => $dv) {
                $dv->fu_1!=null ? array_push($fu_non_ex_1, $dv->fu_1) : null;
            }
            foreach ($d['fu_2_non_ex']->where('fu_2','=',$date) as $key => $dv) {
                $dv->fu_2!=null ? array_push($fu_non_ex_2, $dv->fu_2) : null;
            }
            foreach ($d['fu_3_non_ex']->where('fu_3','=',$date) as $key => $dv) {
                $dv->fu_3!=null ? array_push($fu_non_ex_3, $dv->fu_3) : null;
            }
            foreach ($d['fu_4_non_ex']->where('fu_4','=',$date) as $key => $dv) {
                $dv->fu_4!=null ? array_push($fu_non_ex_4, $dv->fu_4) : null;
            }
            foreach ($d['fu_5_non_ex']->where('fu_5','=',$date) as $key => $dv) {
                $dv->fu_5!=null ? array_push($fu_non_ex_5, $dv->fu_5) : null;
            }
            foreach ($d['sent_e_contract']->where('sent_e_contract','=',$date) as $key => $dv) {
                $dv->sent_e_contract!=null ? array_push($sent_e, $dv->sent_e_contract) : null;
            }
            foreach ($d['rec_e_contract']->where('rec_e_contract','=',$date) as $key => $dv) {
                $dv->rec_e_contract!=null ? array_push($rec_e, $dv->rec_e_contract) : null;
            }
            foreach ($d['email_sent']->where('email_sent','=',$date) as $key => $dv) {
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
            $no++,
            "Monthly Total",
            $d['daily']->whereNotNull('date')->count(),
            $d['first_touch']->whereNotNull('first_touch')->count(),
            $d['fu_1']->whereNotNull('fu_1')->count()+$d['fu_2']->whereNotNull('fu_2')->count()+$d['fu_3']->whereNotNull('fu_3')->count()+$d['fu_4']->whereNotNull('fu_4')->count()+$d['fu_5']->whereNotNull('fu_5')->count(),
            $d['fu_1_non_ex']->whereNotNull('fu_1')->count()+$d['fu_2_non_ex']->whereNotNull('fu_2')->count()+$d['fu_3_non_ex']->whereNotNull('fu_3')->count()+$d['fu_4_non_ex']->whereNotNull('fu_4')->count()+$d['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $d['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $d['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $d['email_sent']->whereNotNull('email_sent')->count(),
            $d['sent_royalty']->whereNotNull('sent_royalty')->count()
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
            }
            foreach ($d['sent_royalty']->where('sent_royalty','=',$date) as $key => $dv) {
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($d['fu_1']->where('fu_1','=',$date) as $key => $dv){
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
            }
            foreach ($d['fu_2']->where('fu_2','=',$date) as $key => $dv){
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
            }
            foreach ($d['fu_3']->where('fu_3','=',$date) as $key => $dv){
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
            }
            foreach ($d['fu_4']->where('fu_4','=',$date) as $key => $dv){
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
            }
            foreach ($d['fu_5']->where('fu_5','=',$date) as $key => $dv){
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
            }
            foreach ($d['first_touch']->where('first_touch','=',$date) as $key => $dv) {
                $dv->first_touch!=null ? array_push($n_auth_non_ex, $dv->first_touch) : null;
            }
            foreach ($d['fu_1_non_ex']->where('fu_1','=',$date) as $key => $dv) {
                $dv->fu_1!=null ? array_push($fu_non_ex_1, $dv->fu_1) : null;
            }
            foreach ($d['fu_2_non_ex']->where('fu_2','=',$date) as $key => $dv) {
                $dv->fu_2!=null ? array_push($fu_non_ex_2, $dv->fu_2) : null;
            }
            foreach ($d['fu_3_non_ex']->where('fu_3','=',$date) as $key => $dv) {
                $dv->fu_3!=null ? array_push($fu_non_ex_3, $dv->fu_3) : null;
            }
            foreach ($d['fu_4_non_ex']->where('fu_4','=',$date) as $key => $dv) {
                $dv->fu_4!=null ? array_push($fu_non_ex_4, $dv->fu_4) : null;
            }
            foreach ($d['fu_5_non_ex']->where('fu_5','=',$date) as $key => $dv) {
                $dv->fu_5!=null ? array_push($fu_non_ex_5, $dv->fu_5) : null;
            }
            foreach ($d['sent_e_contract']->where('sent_e_contract','=',$date) as $key => $dv) {
                $dv->sent_e_contract!=null ? array_push($sent_e, $dv->sent_e_contract) : null;
            }
            foreach ($d['rec_e_contract']->where('rec_e_contract','=',$date) as $key => $dv) {
                $dv->rec_e_contract!=null ? array_push($rec_e, $dv->rec_e_contract) : null;
            }
            foreach ($d['email_sent']->where('email_sent','=',$date) as $key => $dv) {
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
            $no++,
            "Monthly Total",
            $d['daily']->whereNotNull('date')->count(),
            $d['first_touch']->whereNotNull('first_touch')->count(),
            $d['fu_1']->whereNotNull('fu_1')->count()+$d['fu_2']->whereNotNull('fu_2')->count()+$d['fu_3']->whereNotNull('fu_3')->count()+$d['fu_4']->whereNotNull('fu_4')->count()+$d['fu_5']->whereNotNull('fu_5')->count(),
            $d['fu_1_non_ex']->whereNotNull('fu_1')->count()+$d['fu_2_non_ex']->whereNotNull('fu_2')->count()+$d['fu_3_non_ex']->whereNotNull('fu_3')->count()+$d['fu_4_non_ex']->whereNotNull('fu_4')->count()+$d['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $d['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $d['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $d['email_sent']->whereNotNull('email_sent')->count(),
            $d['sent_royalty']->whereNotNull('sent_royalty')->count()
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
            }
            foreach ($d['sent_royalty']->where('sent_royalty','=',$date) as $key => $dv) {
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($d['fu_1']->where('fu_1','=',$date) as $key => $dv){
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
            }
            foreach ($d['fu_2']->where('fu_2','=',$date) as $key => $dv){
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
            }
            foreach ($d['fu_3']->where('fu_3','=',$date) as $key => $dv){
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
            }
            foreach ($d['fu_4']->where('fu_4','=',$date) as $key => $dv){
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
            }
            foreach ($d['fu_5']->where('fu_5','=',$date) as $key => $dv){
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
            }
            foreach ($d['first_touch']->where('first_touch','=',$date) as $key => $dv) {
                $dv->first_touch!=null ? array_push($n_auth_non_ex, $dv->first_touch) : null;
            }
            foreach ($d['fu_1_non_ex']->where('fu_1','=',$date) as $key => $dv) {
                $dv->fu_1!=null ? array_push($fu_non_ex_1, $dv->fu_1) : null;
            }
            foreach ($d['fu_2_non_ex']->where('fu_2','=',$date) as $key => $dv) {
                $dv->fu_2!=null ? array_push($fu_non_ex_2, $dv->fu_2) : null;
            }
            foreach ($d['fu_3_non_ex']->where('fu_3','=',$date) as $key => $dv) {
                $dv->fu_3!=null ? array_push($fu_non_ex_3, $dv->fu_3) : null;
            }
            foreach ($d['fu_4_non_ex']->where('fu_4','=',$date) as $key => $dv) {
                $dv->fu_4!=null ? array_push($fu_non_ex_4, $dv->fu_4) : null;
            }
            foreach ($d['fu_5_non_ex']->where('fu_5','=',$date) as $key => $dv) {
                $dv->fu_5!=null ? array_push($fu_non_ex_5, $dv->fu_5) : null;
            }
            foreach ($d['sent_e_contract']->where('sent_e_contract','=',$date) as $key => $dv) {
                $dv->sent_e_contract!=null ? array_push($sent_e, $dv->sent_e_contract) : null;
            }
            foreach ($d['rec_e_contract']->where('rec_e_contract','=',$date) as $key => $dv) {
                $dv->rec_e_contract!=null ? array_push($rec_e, $dv->rec_e_contract) : null;
            }
            foreach ($d['email_sent']->where('email_sent','=',$date) as $key => $dv) {
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
            $no++,
            "Monthly Total",
            $d['daily']->whereNotNull('date')->count(),
            $d['first_touch']->whereNotNull('first_touch')->count(),
            $d['fu_1']->whereNotNull('fu_1')->count()+$d['fu_2']->whereNotNull('fu_2')->count()+$d['fu_3']->whereNotNull('fu_3')->count()+$d['fu_4']->whereNotNull('fu_4')->count()+$d['fu_5']->whereNotNull('fu_5')->count(),
            $d['fu_1_non_ex']->whereNotNull('fu_1')->count()+$d['fu_2_non_ex']->whereNotNull('fu_2')->count()+$d['fu_3_non_ex']->whereNotNull('fu_3')->count()+$d['fu_4_non_ex']->whereNotNull('fu_4')->count()+$d['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $d['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $d['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $d['email_sent']->whereNotNull('email_sent')->count(),
            $d['sent_royalty']->whereNotNull('sent_royalty')->count()
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
            }
            foreach ($d['sent_royalty']->where('sent_royalty','=',$date) as $key => $dv) {
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($d['fu_1']->where('fu_1','=',$date) as $key => $dv){
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
            }
            foreach ($d['fu_2']->where('fu_2','=',$date) as $key => $dv){
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
            }
            foreach ($d['fu_3']->where('fu_3','=',$date) as $key => $dv){
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
            }
            foreach ($d['fu_4']->where('fu_4','=',$date) as $key => $dv){
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
            }
            foreach ($d['fu_5']->where('fu_5','=',$date) as $key => $dv){
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
            }
            foreach ($d['first_touch']->where('first_touch','=',$date) as $key => $dv) {
                $dv->first_touch!=null ? array_push($n_auth_non_ex, $dv->first_touch) : null;
            }
            foreach ($d['fu_1_non_ex']->where('fu_1','=',$date) as $key => $dv) {
                $dv->fu_1!=null ? array_push($fu_non_ex_1, $dv->fu_1) : null;
            }
            foreach ($d['fu_2_non_ex']->where('fu_2','=',$date) as $key => $dv) {
                $dv->fu_2!=null ? array_push($fu_non_ex_2, $dv->fu_2) : null;
            }
            foreach ($d['fu_3_non_ex']->where('fu_3','=',$date) as $key => $dv) {
                $dv->fu_3!=null ? array_push($fu_non_ex_3, $dv->fu_3) : null;
            }
            foreach ($d['fu_4_non_ex']->where('fu_4','=',$date) as $key => $dv) {
                $dv->fu_4!=null ? array_push($fu_non_ex_4, $dv->fu_4) : null;
            }
            foreach ($d['fu_5_non_ex']->where('fu_5','=',$date) as $key => $dv) {
                $dv->fu_5!=null ? array_push($fu_non_ex_5, $dv->fu_5) : null;
            }
            foreach ($d['sent_e_contract']->where('sent_e_contract','=',$date) as $key => $dv) {
                $dv->sent_e_contract!=null ? array_push($sent_e, $dv->sent_e_contract) : null;
            }
            foreach ($d['rec_e_contract']->where('rec_e_contract','=',$date) as $key => $dv) {
                $dv->rec_e_contract!=null ? array_push($rec_e, $dv->rec_e_contract) : null;
            }
            foreach ($d['email_sent']->where('email_sent','=',$date) as $key => $dv) {
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
            $no++,
            "Monthly Total",
            $d['daily']->whereNotNull('date')->count(),
            $d['first_touch']->whereNotNull('first_touch')->count(),
            $d['fu_1']->whereNotNull('fu_1')->count()+$d['fu_2']->whereNotNull('fu_2')->count()+$d['fu_3']->whereNotNull('fu_3')->count()+$d['fu_4']->whereNotNull('fu_4')->count()+$d['fu_5']->whereNotNull('fu_5')->count(),
            $d['fu_1_non_ex']->whereNotNull('fu_1')->count()+$d['fu_2_non_ex']->whereNotNull('fu_2')->count()+$d['fu_3_non_ex']->whereNotNull('fu_3')->count()+$d['fu_4_non_ex']->whereNotNull('fu_4')->count()+$d['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $d['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $d['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $d['email_sent']->whereNotNull('email_sent')->count(),
            $d['sent_royalty']->whereNotNull('sent_royalty')->count()
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
            }
            foreach ($d['sent_royalty']->where('sent_royalty','=',$date) as $key => $dv) {
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($d['fu_1']->where('fu_1','=',$date) as $key => $dv){
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
            }
            foreach ($d['fu_2']->where('fu_2','=',$date) as $key => $dv){
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
            }
            foreach ($d['fu_3']->where('fu_3','=',$date) as $key => $dv){
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
            }
            foreach ($d['fu_4']->where('fu_4','=',$date) as $key => $dv){
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
            }
            foreach ($d['fu_5']->where('fu_5','=',$date) as $key => $dv){
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
            }
            foreach ($d['first_touch']->where('first_touch','=',$date) as $key => $dv) {
                $dv->first_touch!=null ? array_push($n_auth_non_ex, $dv->first_touch) : null;
            }
            foreach ($d['fu_1_non_ex']->where('fu_1','=',$date) as $key => $dv) {
                $dv->fu_1!=null ? array_push($fu_non_ex_1, $dv->fu_1) : null;
            }
            foreach ($d['fu_2_non_ex']->where('fu_2','=',$date) as $key => $dv) {
                $dv->fu_2!=null ? array_push($fu_non_ex_2, $dv->fu_2) : null;
            }
            foreach ($d['fu_3_non_ex']->where('fu_3','=',$date) as $key => $dv) {
                $dv->fu_3!=null ? array_push($fu_non_ex_3, $dv->fu_3) : null;
            }
            foreach ($d['fu_4_non_ex']->where('fu_4','=',$date) as $key => $dv) {
                $dv->fu_4!=null ? array_push($fu_non_ex_4, $dv->fu_4) : null;
            }
            foreach ($d['fu_5_non_ex']->where('fu_5','=',$date) as $key => $dv) {
                $dv->fu_5!=null ? array_push($fu_non_ex_5, $dv->fu_5) : null;
            }
            foreach ($d['sent_e_contract']->where('sent_e_contract','=',$date) as $key => $dv) {
                $dv->sent_e_contract!=null ? array_push($sent_e, $dv->sent_e_contract) : null;
            }
            foreach ($d['rec_e_contract']->where('rec_e_contract','=',$date) as $key => $dv) {
                $dv->rec_e_contract!=null ? array_push($rec_e, $dv->rec_e_contract) : null;
            }
            foreach ($d['email_sent']->where('email_sent','=',$date) as $key => $dv) {
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
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $xdate = explode(",", $date);
        $d = $this->DataRani($xdate[0], $xdate[1]);
        // dd($d);
        $dateRange = $this->dateRange($xdate[0],$xdate[1]);
        $no = 1;
        array_push($data_array['data'], [
            $no++,
            "Monthly Total",
            $d['daily']->whereNotNull('date')->count(),
            $d['first_touch']->whereNotNull('first_touch')->count(),
            $d['fu_1']->whereNotNull('fu_1')->count()+$d['fu_2']->whereNotNull('fu_2')->count()+$d['fu_3']->whereNotNull('fu_3')->count()+$d['fu_4']->whereNotNull('fu_4')->count()+$d['fu_5']->whereNotNull('fu_5')->count(),
            $d['fu_1_non_ex']->whereNotNull('fu_1')->count()+$d['fu_2_non_ex']->whereNotNull('fu_2')->count()+$d['fu_3_non_ex']->whereNotNull('fu_3')->count()+$d['fu_4_non_ex']->whereNotNull('fu_4')->count()+$d['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $d['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $d['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $d['email_sent']->whereNotNull('email_sent')->count(),
            $d['sent_royalty']->whereNotNull('sent_royalty')->count()
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
            }
            foreach ($d['sent_royalty']->where('sent_royalty','=',$date) as $key => $dv) {
                $dv->sent_royalty!=null ? array_push($royalty, $dv->sent_royalty) : null;
            }
            foreach ($d['fu_1']->where('fu_1','=',$date) as $key => $dv){
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
            }
            foreach ($d['fu_2']->where('fu_2','=',$date) as $key => $dv){
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
            }
            foreach ($d['fu_3']->where('fu_3','=',$date) as $key => $dv){
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
            }
            foreach ($d['fu_4']->where('fu_4','=',$date) as $key => $dv){
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
            }
            foreach ($d['fu_5']->where('fu_5','=',$date) as $key => $dv){
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
            }
            foreach ($d['first_touch']->where('first_touch','=',$date) as $key => $dv) {
                $dv->first_touch!=null ? array_push($n_auth_non_ex, $dv->first_touch) : null;
            }
            foreach ($d['fu_1_non_ex']->where('fu_1','=',$date) as $key => $dv) {
                $dv->fu_1!=null ? array_push($fu_non_ex_1, $dv->fu_1) : null;
            }
            foreach ($d['fu_2_non_ex']->where('fu_2','=',$date) as $key => $dv) {
                $dv->fu_2!=null ? array_push($fu_non_ex_2, $dv->fu_2) : null;
            }
            foreach ($d['fu_3_non_ex']->where('fu_3','=',$date) as $key => $dv) {
                $dv->fu_3!=null ? array_push($fu_non_ex_3, $dv->fu_3) : null;
            }
            foreach ($d['fu_4_non_ex']->where('fu_4','=',$date) as $key => $dv) {
                $dv->fu_4!=null ? array_push($fu_non_ex_4, $dv->fu_4) : null;
            }
            foreach ($d['fu_5_non_ex']->where('fu_5','=',$date) as $key => $dv) {
                $dv->fu_5!=null ? array_push($fu_non_ex_5, $dv->fu_5) : null;
            }
            foreach ($d['sent_e_contract']->where('sent_e_contract','=',$date) as $key => $dv) {
                $dv->sent_e_contract!=null ? array_push($sent_e, $dv->sent_e_contract) : null;
            }
            foreach ($d['rec_e_contract']->where('rec_e_contract','=',$date) as $key => $dv) {
                $dv->rec_e_contract!=null ? array_push($rec_e, $dv->rec_e_contract) : null;
            }
            foreach ($d['email_sent']->where('email_sent','=',$date) as $key => $dv) {
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
        $DateWeekly = $this->WeekFromDate($month);
        $date_start = date("Y-m-d", strtotime($DateWeekly['startdate'][0]));
        $date_end = date("Y-m-d", strtotime(end($DateWeekly['enddate'])));
        // $date_start = date($month."-d", strtotime("first day of this month"));
        // $date_end = date($month."-d", strtotime("last day of this month"));
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
        $d = $this->DataIndoIchaNur($xdate[0],$xdate[1]);
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
            $no++,
            "Monthly Total",
            $d['daily']->whereNotNull('date')->count(),
            $d['fu_1']->whereNotNull('fu_1')->count()+
            $d['fu_2']->whereNotNull('fu_2')->count()+
            $d['fu_3']->whereNotNull('fu_3')->count()+
            $d['fu_4']->whereNotNull('fu_4')->count()+
            $d['fu_5']->whereNotNull('fu_5')->count(),
            $d['data_sent']->whereNotNull('data_sent')->count(),
            '0'
        ]);
        foreach($dateRange as $key => $date){
            $answer = [];
            $fu_1 = [];
            $fu_2 = [];
            $fu_3 = [];
            $fu_4 = [];
            $fu_5 = [];
            $royalty = [];
            foreach ($d['daily']->where('date','=',$date) as $key => $dv) {
                $dv->date!=null ? array_push($answer, $dv->date) : null;
            }
            foreach ($d['fu_1']->where('fu_1','=',$date) as $key => $dv) {
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
            }
            foreach ($d['fu_2']->where('fu_2','=',$date) as $key => $dv) {
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
            }
            foreach ($d['fu_3']->where('fu_3','=',$date) as $key => $dv) {
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
            }
            foreach ($d['fu_4']->where('fu_4','=',$date) as $key => $dv) {
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
            }
            foreach ($d['fu_5']->where('fu_5','=',$date) as $key => $dv) {
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
            }
            foreach ($d['data_sent']->where('data_sent','=',$date) as $key => $dv) {
                $dv->data_sent!=null ? array_push($royalty, $dv->data_sent) : null;
            }
            $answer = count($answer);
            $fu = count($fu_1)+count($fu_2)+count($fu_3)+count($fu_4)+count($fu_5);
            $royalty = count($royalty);
            $non_ex = "";
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
        $d = $this->DataIndoIrel($xdate[0],$xdate[1]);
        $dateRange = $this->dateRange($xdate[0],$xdate[1]);
        $no = 1;
        array_push($data_array['data'], [
            $no++,
            "Monthly Total",
            $d['daily']->whereNotNull('date')->count(),
            $d['fu_1']->whereNotNull('fu_1')->count()+
            $d['fu_2']->whereNotNull('fu_2')->count()+
            $d['fu_3']->whereNotNull('fu_3')->count()+
            $d['fu_4']->whereNotNull('fu_4')->count()+
            $d['fu_5']->whereNotNull('fu_5')->count()+
            $d['fu_6']->whereNotNull('fu_6')->count()+
            $d['fu_7']->whereNotNull('fu_7')->count()+
            $d['fu_8']->whereNotNull('fu_8')->count()+
            $d['fu_9']->whereNotNull('fu_9')->count()+
            $d['fu_10']->whereNotNull('fu_10')->count(),
            $d['date_solved']->whereNotNull('date_solved')->count()
        ]);
        foreach($dateRange as $key => $date){
            $answer = [];
            $fu_1 = [];
            $fu_2 = [];
            $fu_3 = [];
            $fu_4 = [];
            $fu_5 = [];
            $fu_6 = [];
            $fu_7 = [];
            $fu_8 = [];
            $fu_9 = [];
            $fu_10 = [];
            $solved = [];
            foreach ($d['daily']->where('date','=',$date) as $key => $dv) {
                $dv->date!=null ? array_push($answer, $dv->date) : null;
            }
            foreach ($d['fu_1']->where('fu_1','=',$date) as $key => $dv) {
                $dv->fu_1!=null ? array_push($fu_1, $dv->fu_1) : null;
            }
            foreach ($d['fu_2']->where('fu_2','=',$date) as $key => $dv) {
                $dv->fu_2!=null ? array_push($fu_2, $dv->fu_2) : null;
            }
            foreach ($d['fu_3']->where('fu_3','=',$date) as $key => $dv) {
                $dv->fu_3!=null ? array_push($fu_3, $dv->fu_3) : null;
            }
            foreach ($d['fu_4']->where('fu_4','=',$date) as $key => $dv) {
                $dv->fu_4!=null ? array_push($fu_4, $dv->fu_4) : null;
            }
            foreach ($d['fu_5']->where('fu_5','=',$date) as $key => $dv) {
                $dv->fu_5!=null ? array_push($fu_5, $dv->fu_5) : null;
            }
            foreach ($d['fu_6']->where('fu_6','=',$date) as $key => $dv) {
                $dv->fu_6!=null ? array_push($fu_6, $dv->fu_6) : null;
            }
            foreach ($d['fu_7']->where('fu_7','=',$date) as $key => $dv) {
                $dv->fu_7!=null ? array_push($fu_7, $dv->fu_7) : null;
            }
            foreach ($d['fu_8']->where('fu_8','=',$date) as $key => $dv) {
                $dv->fu_8!=null ? array_push($fu_8, $dv->fu_8) : null;
            }
            foreach ($d['fu_9']->where('fu_9','=',$date) as $key => $dv) {
                $dv->fu_9!=null ? array_push($fu_9, $dv->fu_9) : null;
            }
            foreach ($d['fu_10']->where('fu_10','=',$date) as $key => $dv) {
                $dv->fu_10!=null ? array_push($fu_10, $dv->fu_10) : null;
            }
            foreach ($d['date_solved']->where('date_solved','=',$date) as $key => $dv) {
                $dv->date_solved!=null ? array_push($solved, $dv->date_solved) : null;
            }
            $answer = count($answer);
            $fu = count($fu_1)+count($fu_2)+count($fu_3)+count($fu_4)+count($fu_5)+count($fu_6)+count($fu_7)+count($fu_8)+count($fu_9)+count($fu_10);
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
        $month = $request->input('mon');
        $type = $request->input('type');
        $date = [];
        if($type != 'ready'){
            $x = explode(',', $request->input('w'));
            array_push($date, $x[0]);
            array_push($date, $x[1]);
            array_push($date, $x[2]);
        } else {
            $DateWeekly = $this->WeekFromDate(date('Y-m'));
            $Date = date('Y-m-d');
            foreach($DateWeekly['c_week'] as $key => $v_weekly){
                $startdate = $DateWeekly['startdate'][$key];
                $enddate = $DateWeekly['enddate'][$key];
                $Date = date('Y-m-d');
                if($Date >= date('Y-m-d',strtotime($startdate)) && $Date <= date('Y-m-d',strtotime($enddate))){
                    $y = $startdate.",".$enddate;
                    $x = explode(",",$y);
                    array_push($date, $v_weekly);
                }
            }
            array_push($date, $x[0]);
            array_push($date, $x[1]);
        }

        if($report == 'global'){
            $x = $this->WeeklyReportDataGlobal($date);
        }else{
            $x = $this->WeeklyReportDataIndo($date);
        }
        return $x;
    }
    public function WeeklyReportDataGlobal($date){
        // dd($date);
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

        $ame = $this->DataAme($startdate,$enddate);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.ames') ."'>Ame</a>",
            $ame['daily']->whereNotNull('date')->count(),
            $ame['first_touch']->whereNotNull('first_touch')->count(),
            $ame['fu_1']->whereNotNull('fu_1')->count()+
            $ame['fu_2']->whereNotNull('fu_2')->count()+
            $ame['fu_3']->whereNotNull('fu_3')->count()+
            $ame['fu_4']->whereNotNull('fu_4')->count()+
            $ame['fu_5']->whereNotNull('fu_5')->count(),
            $ame['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $ame['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $ame['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $ame['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $ame['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $ame['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $ame['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $ame['email_sent']->whereNotNull('email_sent')->count(),
            $ame['sent_royalty']->whereNotNull('sent_royalty')->count()
        ]);

        $anna = $this->DataAnna($startdate,$enddate);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.annas') ."'>Anna</a>",
            $anna['daily']->whereNotNull('date')->count(),
            $anna['first_touch']->whereNotNull('first_touch')->count(),
            $anna['fu_1']->whereNotNull('fu_1')->count()+
            $anna['fu_2']->whereNotNull('fu_2')->count()+
            $anna['fu_3']->whereNotNull('fu_3')->count()+
            $anna['fu_4']->whereNotNull('fu_4')->count()+
            $anna['fu_5']->whereNotNull('fu_5')->count(),
            $anna['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $anna['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $anna['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $anna['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $anna['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $anna['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $anna['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $anna['email_sent']->whereNotNull('email_sent')->count(),
            $anna['sent_royalty']->whereNotNull('sent_royalty')->count()
        ]);

        $carol = $this->DataCarol($startdate,$enddate);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.carols') ."'>Carol</a>",
            $carol['daily']->whereNotNull('date')->count(),
            $carol['first_touch']->whereNotNull('first_touch')->count(),
            $carol['fu_1']->whereNotNull('fu_1')->count()+
            $carol['fu_2']->whereNotNull('fu_2')->count()+
            $carol['fu_3']->whereNotNull('fu_3')->count()+
            $carol['fu_4']->whereNotNull('fu_4')->count()+
            $carol['fu_5']->whereNotNull('fu_5')->count(),
            $carol['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $carol['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $carol['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $carol['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $carol['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $carol['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $carol['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $carol['email_sent']->whereNotNull('email_sent')->count(),
            $carol['sent_royalty']->whereNotNull('sent_royalty')->count()
        ]);

        $eric = $this->DataEric($startdate,$enddate);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.erics') ."'>Eric</a>",
            $eric['daily']->whereNotNull('date')->count(),
            $eric['first_touch']->whereNotNull('first_touch')->count(),
            $eric['fu_1']->whereNotNull('fu_1')->count()+
            $eric['fu_2']->whereNotNull('fu_2')->count()+
            $eric['fu_3']->whereNotNull('fu_3')->count()+
            $eric['fu_4']->whereNotNull('fu_4')->count()+
            $eric['fu_5']->whereNotNull('fu_5')->count(),
            $eric['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $eric['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $eric['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $eric['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $eric['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $eric['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $eric['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $eric['email_sent']->whereNotNull('email_sent')->count(),
            $eric['sent_royalty']->whereNotNull('sent_royalty')->count()
        ]);

        $icha = $this->DataIcha($startdate,$enddate);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.ichas') ."'>Icha</a>",
            $icha['daily']->whereNotNull('date')->count(),
            $icha['first_touch']->whereNotNull('first_touch')->count(),
            $icha['fu_1']->whereNotNull('fu_1')->count()+
            $icha['fu_2']->whereNotNull('fu_2')->count()+
            $icha['fu_3']->whereNotNull('fu_3')->count()+
            $icha['fu_4']->whereNotNull('fu_4')->count()+
            $icha['fu_5']->whereNotNull('fu_5')->count(),
            $icha['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $icha['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $icha['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $icha['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $icha['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $icha['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $icha['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $icha['email_sent']->whereNotNull('email_sent')->count(),
            $icha['sent_royalty']->whereNotNull('sent_royalty')->count()
        ]);

        $lily = $this->DataLily($startdate,$enddate);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.lilies') ."'>Lily</a>",
            $lily['daily']->whereNotNull('date')->count(),
            $lily['first_touch']->whereNotNull('first_touch')->count(),
            $lily['fu_1']->whereNotNull('fu_1')->count()+
            $lily['fu_2']->whereNotNull('fu_2')->count()+
            $lily['fu_3']->whereNotNull('fu_3')->count()+
            $lily['fu_4']->whereNotNull('fu_4')->count()+
            $lily['fu_5']->whereNotNull('fu_5')->count(),
            $lily['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $lily['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $lily['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $lily['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $lily['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $lily['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $lily['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $lily['email_sent']->whereNotNull('email_sent')->count(),
            $lily['sent_royalty']->whereNotNull('sent_royalty')->count()
        ]);

        $maydewi = $this->DataMaydewi($startdate,$enddate);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.maydewis') ."'>Maydewi</a>",
            $maydewi['daily']->whereNotNull('date')->count(),
            $maydewi['first_touch']->whereNotNull('first_touch')->count(),
            $maydewi['fu_1']->whereNotNull('fu_1')->count()+
            $maydewi['fu_2']->whereNotNull('fu_2')->count()+
            $maydewi['fu_3']->whereNotNull('fu_3')->count()+
            $maydewi['fu_4']->whereNotNull('fu_4')->count()+
            $maydewi['fu_5']->whereNotNull('fu_5')->count(),
            $maydewi['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $maydewi['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $maydewi['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $maydewi['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $maydewi['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $maydewi['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $maydewi['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $maydewi['email_sent']->whereNotNull('email_sent')->count(),
            $maydewi['sent_royalty']->whereNotNull('sent_royalty')->count()
        ]);

        $rani = $this->DataRani($startdate,$enddate);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.ranis') ."'>Rani</a>",
            $rani['daily']->whereNotNull('date')->count(),
            $rani['first_touch']->whereNotNull('first_touch')->count(),
            $rani['fu_1']->whereNotNull('fu_1')->count()+
            $rani['fu_2']->whereNotNull('fu_2')->count()+
            $rani['fu_3']->whereNotNull('fu_3')->count()+
            $rani['fu_4']->whereNotNull('fu_4')->count()+
            $rani['fu_5']->whereNotNull('fu_5')->count(),
            $rani['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $rani['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $rani['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $rani['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $rani['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $rani['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $rani['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $rani['email_sent']->whereNotNull('email_sent')->count(),
            $rani['sent_royalty']->whereNotNull('sent_royalty')->count()
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

        $dIchaNur = $this->DataIndoIchaNur($startdate, $enddate);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-indo.icha-nurs') ."'>Icha Nur</a>",
            $dIchaNur['daily']->whereNotNull('date')->count(),
            $dIchaNur['fu_1']->whereNotNull('fu_1')->count()+
            $dIchaNur['fu_2']->whereNotNull('fu_2')->count()+
            $dIchaNur['fu_3']->whereNotNull('fu_3')->count()+
            $dIchaNur['fu_4']->whereNotNull('fu_4')->count()+
            $dIchaNur['fu_5']->whereNotNull('fu_5')->count(),
            $dIchaNur['data_sent']->whereNotNull('data_sent')->count(),
            '0',
            '',
            '',
            ''
        ]);

        $dIrel = $this->DataIndoIrel($startdate, $enddate);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-indo.irels') ."'>Irel</a>",
            '-',
            $dIrel['fu_1']->whereNotNull('fu_1')->count()+
            $dIrel['fu_2']->whereNotNull('fu_2')->count()+
            $dIrel['fu_3']->whereNotNull('fu_3')->count()+
            $dIrel['fu_4']->whereNotNull('fu_4')->count()+
            $dIrel['fu_5']->whereNotNull('fu_5')->count()+
            $dIrel['fu_6']->whereNotNull('fu_6')->count()+
            $dIrel['fu_7']->whereNotNull('fu_7')->count()+
            $dIrel['fu_8']->whereNotNull('fu_8')->count()+
            $dIrel['fu_9']->whereNotNull('fu_9')->count()+
            $dIrel['fu_10']->whereNotNull('fu_10')->count(),
            '-',
            '-',
            $dIrel['daily']->whereNotNull('date')->count(),
            $dIrel['date_solved']->whereNotNull('date_solved')->count(),
        ]);
        return $data_array;
    }

    /*---------------------------------------
    | LV 2 MONTHLY REPORT
    -----------------------------------------*/
    public function getMonthlyReport(Request $request){
        $report = $request->input('r');
        $month = $request->input('mon');
        $type = $request->input('type');
        if($type != 'ready'){
            $DateWeekly = $this->WeekFromDate($month);
        } else {
            $DateWeekly = $this->WeekFromDate(date('Y-m'));
        }
        $date_start = date("Y-m-d", strtotime($DateWeekly['startdate'][0]));
        $date_end = date("Y-m-d", strtotime(end($DateWeekly['enddate'])));

        $date = $date_start.",".$date_end;
        $date = explode(",",$date);
        if($report == 'global'){
            $x = $this->MonthlyReportDataGlobal($date);
        }
        else if($report == 'spam'){
            $x = $this->MonthlyReportDataSpam($date);
        }
        else{
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
        $ame = $this->DataAme($startdate,$enddate);
        $anna = $this->DataAnna($startdate,$enddate);
        $carol = $this->DataCarol($startdate,$enddate);
        $eric = $this->DataEric($startdate,$enddate);
        $icha = $this->DataIcha($startdate,$enddate);
        $lily = $this->DataLily($startdate,$enddate);
        $maydewi = $this->DataMaydewi($startdate,$enddate);
        $rani = $this->DataRani($startdate,$enddate);

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
        $grand_answer = $ame['daily']->whereNotNull('date')->count()+$anna['daily']->whereNotNull('date')->count()+$carol['daily']->whereNotNull('date')->count()+$eric['daily']->whereNotNull('date')->count()+$icha['daily']->whereNotNull('date')->count()+$lily['daily']->whereNotNull('date')->count()+$maydewi['daily']->whereNotNull('date')->count()+$rani['daily']->whereNotNull('date')->count();
        $n_auth_non_ex = $ame['first_touch']->whereNotNull('first_touch')->count()+$anna['first_touch']->whereNotNull('first_touch')->count()+$carol['first_touch']->whereNotNull('first_touch')->count()+$eric['first_touch']->whereNotNull('first_touch')->count()+$icha['first_touch']->whereNotNull('first_touch')->count()+$lily['first_touch']->whereNotNull('first_touch')->count()+$maydewi['first_touch']->whereNotNull('first_touch')->count()+$rani['first_touch']->whereNotNull('first_touch')->count();
        $fu = $ame['fu_1']->whereNotNull('fu_1')->count()+$ame['fu_2']->whereNotNull('fu_2')->count()+$ame['fu_3']->whereNotNull('fu_3')->count()+$ame['fu_4']->whereNotNull('fu_4')->count()+$ame['fu_5']->whereNotNull('fu_5')->count()+
            $anna['fu_1']->whereNotNull('fu_1')->count()+$anna['fu_2']->whereNotNull('fu_2')->count()+$anna['fu_3']->whereNotNull('fu_3')->count()+$anna['fu_4']->whereNotNull('fu_4')->count()+$anna['fu_5']->whereNotNull('fu_5')->count()+
            $carol['fu_1']->whereNotNull('fu_1')->count()+$carol['fu_2']->whereNotNull('fu_2')->count()+$carol['fu_3']->whereNotNull('fu_3')->count()+$carol['fu_4']->whereNotNull('fu_4')->count()+$carol['fu_5']->whereNotNull('fu_5')->count()+
            $eric['fu_1']->whereNotNull('fu_1')->count()+$eric['fu_2']->whereNotNull('fu_2')->count()+$eric['fu_3']->whereNotNull('fu_3')->count()+$eric['fu_4']->whereNotNull('fu_4')->count()+$eric['fu_5']->whereNotNull('fu_5')->count()+
            $icha['fu_1']->whereNotNull('fu_1')->count()+$icha['fu_2']->whereNotNull('fu_2')->count()+$icha['fu_3']->whereNotNull('fu_3')->count()+$icha['fu_4']->whereNotNull('fu_4')->count()+$icha['fu_5']->whereNotNull('fu_5')->count()+
            $lily['fu_1']->whereNotNull('fu_1')->count()+$lily['fu_2']->whereNotNull('fu_2')->count()+$lily['fu_3']->whereNotNull('fu_3')->count()+$lily['fu_4']->whereNotNull('fu_4')->count()+$lily['fu_5']->whereNotNull('fu_5')->count()+
            $maydewi['fu_1']->whereNotNull('fu_1')->count()+$maydewi['fu_2']->whereNotNull('fu_2')->count()+$maydewi['fu_3']->whereNotNull('fu_3')->count()+$maydewi['fu_4']->whereNotNull('fu_4')->count()+$maydewi['fu_5']->whereNotNull('fu_5')->count()+
            $rani['fu_1']->whereNotNull('fu_1')->count()+$rani['fu_2']->whereNotNull('fu_2')->count()+$rani['fu_3']->whereNotNull('fu_3')->count()+$rani['fu_4']->whereNotNull('fu_4')->count()+$rani['fu_5']->whereNotNull('fu_5')->count();
        $fu_non_ex = $ame['fu_1_non_ex']->whereNotNull('fu_1')->count()+$ame['fu_2_non_ex']->whereNotNull('fu_2')->count()+$ame['fu_3_non_ex']->whereNotNull('fu_3')->count()+$ame['fu_4_non_ex']->whereNotNull('fu_4')->count()+$ame['fu_5_non_ex']->whereNotNull('fu_5')->count()+
            $anna['fu_1_non_ex']->whereNotNull('fu_1')->count()+$anna['fu_2_non_ex']->whereNotNull('fu_2')->count()+$anna['fu_3_non_ex']->whereNotNull('fu_3')->count()+$anna['fu_4_non_ex']->whereNotNull('fu_4')->count()+$anna['fu_5_non_ex']->whereNotNull('fu_5')->count()+
            $carol['fu_1_non_ex']->whereNotNull('fu_1')->count()+$carol['fu_2_non_ex']->whereNotNull('fu_2')->count()+$carol['fu_3_non_ex']->whereNotNull('fu_3')->count()+$carol['fu_4_non_ex']->whereNotNull('fu_4')->count()+$carol['fu_5_non_ex']->whereNotNull('fu_5')->count()+
            $eric['fu_1_non_ex']->whereNotNull('fu_1')->count()+$eric['fu_2_non_ex']->whereNotNull('fu_2')->count()+$eric['fu_3_non_ex']->whereNotNull('fu_3')->count()+$eric['fu_4_non_ex']->whereNotNull('fu_4')->count()+$eric['fu_5_non_ex']->whereNotNull('fu_5')->count()+
            $icha['fu_1_non_ex']->whereNotNull('fu_1')->count()+$icha['fu_2_non_ex']->whereNotNull('fu_2')->count()+$icha['fu_3_non_ex']->whereNotNull('fu_3')->count()+$icha['fu_4_non_ex']->whereNotNull('fu_4')->count()+$icha['fu_5_non_ex']->whereNotNull('fu_5')->count()+
            $lily['fu_1_non_ex']->whereNotNull('fu_1')->count()+$lily['fu_2_non_ex']->whereNotNull('fu_2')->count()+$lily['fu_3_non_ex']->whereNotNull('fu_3')->count()+$lily['fu_4_non_ex']->whereNotNull('fu_4')->count()+$lily['fu_5_non_ex']->whereNotNull('fu_5')->count()+
            $maydewi['fu_1_non_ex']->whereNotNull('fu_1')->count()+$maydewi['fu_2_non_ex']->whereNotNull('fu_2')->count()+$maydewi['fu_3_non_ex']->whereNotNull('fu_3')->count()+$maydewi['fu_4_non_ex']->whereNotNull('fu_4')->count()+$maydewi['fu_5_non_ex']->whereNotNull('fu_5')->count()+
            $rani['fu_1_non_ex']->whereNotNull('fu_1')->count()+$rani['fu_2_non_ex']->whereNotNull('fu_2')->count()+$rani['fu_3_non_ex']->whereNotNull('fu_3')->count()+$rani['fu_4_non_ex']->whereNotNull('fu_4')->count()+$rani['fu_5_non_ex']->whereNotNull('fu_5')->count();
        $sent_e = $ame['sent_e_contract']->whereNotNull('sent_e_contract')->count()+$anna['sent_e_contract']->whereNotNull('sent_e_contract')->count()+$carol['sent_e_contract']->whereNotNull('sent_e_contract')->count()+$eric['sent_e_contract']->whereNotNull('sent_e_contract')->count()+$icha['sent_e_contract']->whereNotNull('sent_e_contract')->count()+$lily['sent_e_contract']->whereNotNull('sent_e_contract')->count()+$maydewi['sent_e_contract']->whereNotNull('sent_e_contract')->count()+$rani['sent_e_contract']->whereNotNull('sent_e_contract')->count();
        $rec_e = $ame['rec_e_contract']->whereNotNull('rec_e_contract')->count()+$anna['rec_e_contract']->whereNotNull('rec_e_contract')->count()+$carol['rec_e_contract']->whereNotNull('rec_e_contract')->count()+$eric['rec_e_contract']->whereNotNull('rec_e_contract')->count()+$icha['rec_e_contract']->whereNotNull('rec_e_contract')->count()+$lily['rec_e_contract']->whereNotNull('rec_e_contract')->count()+$maydewi['rec_e_contract']->whereNotNull('rec_e_contract')->count()+$rani['rec_e_contract']->whereNotNull('rec_e_contract')->count();
        $d_non_ex = $ame['email_sent']->whereNotNull('email_sent')->count()+$anna['email_sent']->whereNotNull('email_sent')->count()+$carol['email_sent']->whereNotNull('email_sent')->count()+$eric['email_sent']->whereNotNull('email_sent')->count()+$icha['email_sent']->whereNotNull('email_sent')->count()+$lily['email_sent']->whereNotNull('email_sent')->count()+$maydewi['email_sent']->whereNotNull('email_sent')->count()+$rani['email_sent']->whereNotNull('email_sent')->count();
        $d_royalty = $ame['sent_royalty']->whereNotNull('sent_royalty')->count()+$anna['sent_royalty']->whereNotNull('sent_royalty')->count()+$carol['sent_royalty']->whereNotNull('sent_royalty')->count()+$eric['sent_royalty']->whereNotNull('sent_royalty')->count()+$icha['sent_royalty']->whereNotNull('sent_royalty')->count()+$lily['sent_royalty']->whereNotNull('sent_royalty')->count()+$maydewi['sent_royalty']->whereNotNull('sent_royalty')->count()+$rani['sent_royalty']->whereNotNull('sent_royalty')->count();
        array_push($data_array['data'], [
            "- -Total",$grand_answer,$n_auth_non_ex,$fu,$fu_non_ex,$sent_e,$rec_e,$d_non_ex,$d_royalty
        ]);
        $counter_person = count($this->personGlobal);
        array_push($data_array['data'], [
            "- Average",$grand_answer/$counter_person,$n_auth_non_ex/$counter_person,$fu/$counter_person,$fu_non_ex/$counter_person,$sent_e/$counter_person,$rec_e/$counter_person,$d_non_ex/$counter_person,$d_royalty/$counter_person
        ]);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.ames') ."'>Ame</a>",
            $ame['daily']->whereNotNull('date')->count(),
            $ame['first_touch']->whereNotNull('first_touch')->count(),
            $ame['fu_1']->whereNotNull('fu_1')->count()+
            $ame['fu_2']->whereNotNull('fu_2')->count()+
            $ame['fu_3']->whereNotNull('fu_3')->count()+
            $ame['fu_4']->whereNotNull('fu_4')->count()+
            $ame['fu_5']->whereNotNull('fu_5')->count(),
            $ame['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $ame['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $ame['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $ame['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $ame['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $ame['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $ame['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $ame['email_sent']->whereNotNull('email_sent')->count(),
            $ame['sent_royalty']->whereNotNull('sent_royalty')->count()
        ]);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.annas') ."'>Anna</a>",
            $anna['daily']->whereNotNull('date')->count(),
            $anna['first_touch']->whereNotNull('first_touch')->count(),
            $anna['fu_1']->whereNotNull('fu_1')->count()+
            $anna['fu_2']->whereNotNull('fu_2')->count()+
            $anna['fu_3']->whereNotNull('fu_3')->count()+
            $anna['fu_4']->whereNotNull('fu_4')->count()+
            $anna['fu_5']->whereNotNull('fu_5')->count(),
            $anna['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $anna['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $anna['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $anna['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $anna['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $anna['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $anna['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $anna['email_sent']->whereNotNull('email_sent')->count(),
            $anna['sent_royalty']->whereNotNull('sent_royalty')->count()
        ]);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.carols') ."'>Carol</a>",
            $carol['daily']->whereNotNull('date')->count(),
            $carol['first_touch']->whereNotNull('first_touch')->count(),
            $carol['fu_1']->whereNotNull('fu_1')->count()+
            $carol['fu_2']->whereNotNull('fu_2')->count()+
            $carol['fu_3']->whereNotNull('fu_3')->count()+
            $carol['fu_4']->whereNotNull('fu_4')->count()+
            $carol['fu_5']->whereNotNull('fu_5')->count(),
            $carol['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $carol['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $carol['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $carol['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $carol['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $carol['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $carol['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $carol['email_sent']->whereNotNull('email_sent')->count(),
            $carol['sent_royalty']->whereNotNull('sent_royalty')->count()
        ]);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.erics') ."'>Eric</a>",
            $eric['daily']->whereNotNull('date')->count(),
            $eric['first_touch']->whereNotNull('first_touch')->count(),
            $eric['fu_1']->whereNotNull('fu_1')->count()+
            $eric['fu_2']->whereNotNull('fu_2')->count()+
            $eric['fu_3']->whereNotNull('fu_3')->count()+
            $eric['fu_4']->whereNotNull('fu_4')->count()+
            $eric['fu_5']->whereNotNull('fu_5')->count(),
            $eric['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $eric['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $eric['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $eric['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $eric['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $eric['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $eric['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $eric['email_sent']->whereNotNull('email_sent')->count(),
            $eric['sent_royalty']->whereNotNull('sent_royalty')->count()
        ]);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.ichas') ."'>Icha</a>",
            $icha['daily']->whereNotNull('date')->count(),
            $icha['first_touch']->whereNotNull('first_touch')->count(),
            $icha['fu_1']->whereNotNull('fu_1')->count()+
            $icha['fu_2']->whereNotNull('fu_2')->count()+
            $icha['fu_3']->whereNotNull('fu_3')->count()+
            $icha['fu_4']->whereNotNull('fu_4')->count()+
            $icha['fu_5']->whereNotNull('fu_5')->count(),
            $icha['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $icha['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $icha['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $icha['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $icha['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $icha['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $icha['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $icha['email_sent']->whereNotNull('email_sent')->count(),
            $icha['sent_royalty']->whereNotNull('sent_royalty')->count()
        ]);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.lilies') ."'>Lily</a>",
            $lily['daily']->whereNotNull('date')->count(),
            $lily['first_touch']->whereNotNull('first_touch')->count(),
            $lily['fu_1']->whereNotNull('fu_1')->count()+
            $lily['fu_2']->whereNotNull('fu_2')->count()+
            $lily['fu_3']->whereNotNull('fu_3')->count()+
            $lily['fu_4']->whereNotNull('fu_4')->count()+
            $lily['fu_5']->whereNotNull('fu_5')->count(),
            $lily['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $lily['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $lily['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $lily['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $lily['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $lily['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $lily['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $lily['email_sent']->whereNotNull('email_sent')->count(),
            $lily['sent_royalty']->whereNotNull('sent_royalty')->count()
        ]);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.maydewis') ."'>Maydewi</a>",
            $maydewi['daily']->whereNotNull('date')->count(),
            $maydewi['first_touch']->whereNotNull('first_touch')->count(),
            $maydewi['fu_1']->whereNotNull('fu_1')->count()+
            $maydewi['fu_2']->whereNotNull('fu_2')->count()+
            $maydewi['fu_3']->whereNotNull('fu_3')->count()+
            $maydewi['fu_4']->whereNotNull('fu_4')->count()+
            $maydewi['fu_5']->whereNotNull('fu_5')->count(),
            $maydewi['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $maydewi['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $maydewi['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $maydewi['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $maydewi['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $maydewi['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $maydewi['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $maydewi['email_sent']->whereNotNull('email_sent')->count(),
            $maydewi['sent_royalty']->whereNotNull('sent_royalty')->count()
        ]);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-global.ranis') ."'>Rani</a>",
            $rani['daily']->whereNotNull('date')->count(),
            $rani['first_touch']->whereNotNull('first_touch')->count(),
            $rani['fu_1']->whereNotNull('fu_1')->count()+
            $rani['fu_2']->whereNotNull('fu_2')->count()+
            $rani['fu_3']->whereNotNull('fu_3')->count()+
            $rani['fu_4']->whereNotNull('fu_4')->count()+
            $rani['fu_5']->whereNotNull('fu_5')->count(),
            $rani['fu_1_non_ex']->whereNotNull('fu_1')->count()+
            $rani['fu_2_non_ex']->whereNotNull('fu_2')->count()+
            $rani['fu_3_non_ex']->whereNotNull('fu_3')->count()+
            $rani['fu_4_non_ex']->whereNotNull('fu_4')->count()+
            $rani['fu_5_non_ex']->whereNotNull('fu_5')->count(),
            $rani['sent_e_contract']->whereNotNull('sent_e_contract')->count(),
            $rani['rec_e_contract']->whereNotNull('rec_e_contract')->count(),
            $rani['email_sent']->whereNotNull('email_sent')->count(),
            $rani['sent_royalty']->whereNotNull('sent_royalty')->count()
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
        $dIchaNur = $this->DataIndoIchaNur($startdate,$enddate);
        $dIrel = $this->DataIndoIrel($startdate,$enddate);
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
            $dIchaNur['daily']->whereNotNull('date')->count(),
            $dIchaNur['fu_1']->whereNotNull('fu_1')->count()+
            $dIchaNur['fu_2']->whereNotNull('fu_2')->count()+
            $dIchaNur['fu_3']->whereNotNull('fu_3')->count()+
            $dIchaNur['fu_4']->whereNotNull('fu_4')->count()+
            $dIchaNur['fu_5']->whereNotNull('fu_5')->count(),
            $dIchaNur['data_sent']->whereNotNull('data_sent')->count(),
            '0',
            '',
            '',
            ''
        ]);
        array_push($data_array['data'], [
            "<a target='_blank' href='". route('daily-report-indo.irels') ."'>Irel</a>",
            '-',
            $dIrel['fu_1']->whereNotNull('fu_1')->count()+
            $dIrel['fu_2']->whereNotNull('fu_2')->count()+
            $dIrel['fu_3']->whereNotNull('fu_3')->count()+
            $dIrel['fu_4']->whereNotNull('fu_4')->count()+
            $dIrel['fu_5']->whereNotNull('fu_5')->count()+
            $dIrel['fu_6']->whereNotNull('fu_6')->count()+
            $dIrel['fu_7']->whereNotNull('fu_7')->count()+
            $dIrel['fu_8']->whereNotNull('fu_8')->count()+
            $dIrel['fu_9']->whereNotNull('fu_9')->count()+
            $dIrel['fu_10']->whereNotNull('fu_10')->count(),
            '-',
            '-',
            $dIrel['daily']->whereNotNull('date')->count(),
            $dIrel['date_solved']->whereNotNull('date_solved')->count(),
        ]);
        return $data_array;
    }
    public function MonthlyReportDataSpam($date){
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $startdate = $date[0];
        $enddate = $date[1];
        $dMangatoon = $this->DataSpamMangatoon($startdate,$enddate);
        $dUncontractedWN = $this->DataUncontractedWN($startdate,$enddate);
        $title = [
            "Spam Team",
            "Platform",
            "Invitation Sent",
            "Author Replied"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        array_push($data_array['data'], [
            'Esy',
            "Mangatoon",
            $dMangatoon['daily']->whereNotNull('date')->count(),
            $dMangatoon['date_feedback_received']->whereNotNull('date_feedback_received')->count(),
        ]);
        array_push($data_array['data'], [
            'Global Team',
            "Uncontracted WN",
            $dUncontractedWN['daily']->whereNotNull('date')->count(),
            $dUncontractedWN['date_feedback_received']->whereNotNull('date_feedback_received')->count(),
        ]);
        return $data_array;
    }
}