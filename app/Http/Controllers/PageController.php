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
use App\Models\Person as Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
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
        return view('pages.team-monitoring.global');
    }
    public function IndoTeamMonitoring(){
        return view('pages.team-monitoring.indo');
    }
    public function MonthlyReport(){
        return view('pages.all-report.weekly');
    }
    public function WeeklyReport(){
        return view('pages.all-report.monthly');
    }
    public function getDailyReportAmes(){
        $query = DailyReportAme::orderBy('id','DESC')->limit(10000)->get();
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
}