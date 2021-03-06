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
use App\Models\ReportSpamNovelListFromRanking;
use App\Models\ReportSpamWNUncoractedNovelList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use function Symfony\Component\VarDumper\Dumper\esc;

class UpdateController extends Controller
{
    public  $page = false;
    public function __construct(){
        $this->page = new PageController;
    }
    public function addValueReport(Request $request){
        $id = $request->input('id');
        $row = $request->input('row');
        $p = $request->input('p');
        $date = $request->input('date')!='' ? $request->input('date') : date('Y-m-d');
        switch ($p) {
            case 'ame':
                $data = DailyReportAme::findOrFail($id);
                switch ($row) {
                    case 'fu':
                        $kolom = ['fu_1','fu_2','fu_3','fu_4','fu_5'];
                        break;
                    case 'sent_royalty':
                        $kolom = ['sent_royalty'];
                        break;
                    case 'sent_non_exclusive':
                        $kolom = ['sent_non_exclusive'];
                        break;
                    default:
                        $kolom = [];
                        break;
                }
                $data_inputed = 0;
                foreach ($kolom as $key => $value) {
                    if (!$data[$value]) {
                        $isi = [
                            $value => $date
                        ];
                        $data->update($isi);
                        Cache::forget('cache-ame');
                        $this->page->DataAmeCached();
                        return ['data' => 200];
                    }
                    else{
                        $data_inputed++;
                    }
                }
                if($data_inputed == 5){
                    foreach ($kolom as $key => $value) {
                        $last_fu = Cache::get('number-fu-ame-'.$id, 1);
                        if($value == 'fu_'.$last_fu){
                            $isi = [
                                $value => $date
                            ];
                            $data->update($isi);
                            Cache::forget('number-fu-ame-'.$id);
                            Cache::put('number-fu-ame-'.$id, $last_fu==5?$last_fu=1:$last_fu+1);
                            Cache::forget('cache-ame');
                            $this->page->DataAmeCached();
                            return ['data' => 200];
                        }
                    }
                }
                break;
            case 'anna' :
                $data = DailyReportAnna::findOrFail($id);
                switch ($row) {
                    case 'fu':
                        $kolom = ['fu_1','fu_2','fu_3','fu_4','fu_5'];
                        break;
                    case 'sent_royalty':
                        $kolom = ['sent_royalty'];
                        break;
                    case 'sent_non_exclusive':
                        $kolom = ['sent_non_exclusive'];
                        break;
                    default:
                        $kolom = [];
                        break;
                }
                $data_inputed = 0;
                foreach ($kolom as $key => $value) {
                    if (!$data[$value]) {
                        $isi = [
                            $value => $date
                        ];
                        $data->update($isi);
                        Cache::forget('cache-anna');
                        $this->page->DataAnnaCached();
                        return ['data' => 200];
                    }else{
                        $data_inputed++;
                    }
                }
                if($data_inputed == 5){
                    foreach ($kolom as $key => $value) {
                        $last_fu = Cache::get('number-fu-anna-'.$id, 1);
                        if($value == 'fu_'.$last_fu){
                            $isi = [
                                $value => $date
                            ];
                            $data->update($isi);
                            Cache::forget('number-fu-anna-'.$id);
                            Cache::put('number-fu-anna-'.$id, $last_fu==5?$last_fu=1:$last_fu+1);
                            Cache::forget('cache-anna');
                            $this->page->DataAnnaCached();
                            return ['data' => 200];
                        }
                    }
                }
                break;
            case 'carol' :
                $data = DailyReportCarol::findOrFail($id);
                switch ($row) {
                    case 'fu':
                        $kolom = ['fu_1','fu_2','fu_3','fu_4','fu_5'];
                        break;
                    case 'sent_royalty':
                        $kolom = ['sent_royalty'];
                        break;
                    case 'sent_non_exclusive':
                        $kolom = ['sent_non_exclusive'];
                        break;
                    default:
                        $kolom = [];
                        break;
                }
                $data_inputed = 0;
                foreach ($kolom as $key => $value) {
                    if (!$data[$value]) {
                        $isi = [
                            $value => $date
                        ];
                        $data->update($isi);
                        Cache::forget('cache-carol');
                        $this->page->DataCarolCached();
                        return ['data' => 200];
                    }else{
                        $data_inputed++;
                    }
                }
                if($data_inputed == 5){
                    foreach ($kolom as $key => $value) {
                        $last_fu = Cache::get('number-fu-carol-'.$id, 1);
                        if($value == 'fu_'.$last_fu){
                            $isi = [
                                $value => $date
                            ];
                            $data->update($isi);
                            Cache::forget('number-fu-carol-'.$id);
                            Cache::put('number-fu-carol-'.$id, $last_fu==5?$last_fu=1:$last_fu+1);
                            Cache::forget('cache-carol');
                            $this->page->DataCarolCached();
                            return ['data' => 200];
                        }
                    }
                }
                break;
            case 'eric' :
                $data = DailyReportEric::findOrFail($id);
                switch ($row) {
                    case 'fu':
                        $kolom = ['fu_1','fu_2','fu_3','fu_4','fu_5'];
                        break;
                    case 'sent_royalty':
                        $kolom = ['sent_royalty'];
                        break;
                    case 'sent_non_exclusive':
                        $kolom = ['sent_non_exclusive'];
                        break;
                    default:
                        $kolom = [];
                        break;
                }
                $data_inputed = 0;
                foreach ($kolom as $key => $value) {
                    if (!$data[$value]) {
                        $isi = [
                            $value => $date
                        ];
                        $data->update($isi);
                        Cache::forget('cache-eric');
                        $this->page->DataEricCached();
                        return ['data' => 200];
                    } else {
                        $data_inputed++;
                    }
                }
                if($data_inputed == 5){
                    foreach ($kolom as $key => $value) {
                        $last_fu = Cache::get('number-fu-eric-'.$id, 1);
                        if($value == 'fu_'.$last_fu){
                            $isi = [
                                $value => $date
                            ];
                            $data->update($isi);
                            Cache::forget('number-fu-eric-'.$id);
                            Cache::put('number-fu-eric-'.$id, $last_fu==5?$last_fu=1:$last_fu+1);
                            Cache::forget('cache-eric');
                            $this->page->DataEricCached();
                            return ['data' => 200];
                        }
                    }
                }
                break;
            case 'icha' :
                $data = DailyReportIcha::findOrFail($id);
                switch ($row) {
                    case 'fu':
                        $kolom = ['fu_1','fu_2','fu_3','fu_4','fu_5'];
                        break;
                    case 'sent_royalty':
                        $kolom = ['sent_royalty'];
                        break;
                    case 'sent_non_exclusive':
                        $kolom = ['sent_non_exclusive'];
                        break;
                    default:
                        $kolom = [];
                        break;
                }
                $data_inputed = 0;
                foreach ($kolom as $key => $value) {
                    if (!$data[$value]) {
                        $isi = [
                            $value => $date
                        ];
                        $data->update($isi);
                        Cache::forget('cache-icha');
                        $this->page->DataIchaCached();
                        return ['data' => 200];
                    } else {
                        $data_inputed++;
                    }
                }
                if($data_inputed == 5){
                    foreach ($kolom as $key => $value) {
                        $last_fu = Cache::get('number-fu-icha-'.$id, 1);
                        if($value == 'fu_'.$last_fu){
                            $isi = [
                                $value => $date
                            ];
                            $data->update($isi);
                            Cache::forget('number-fu-icha-'.$id);
                            Cache::put('number-fu-icha-'.$id, $last_fu==5?$last_fu=1:$last_fu+1);
                            Cache::forget('cache-icha');
                            $this->page->DataIchaCached();
                            return ['data' => 200];
                        }
                    }
                }
                break;
            case 'lily' :
                $data = DailyReportLily::findOrFail($id);
                switch ($row) {
                    case 'fu':
                        $kolom = ['fu_1','fu_2','fu_3','fu_4','fu_5'];
                        break;
                    case 'sent_royalty':
                        $kolom = ['sent_royalty'];
                        break;
                    case 'sent_non_exclusive':
                        $kolom = ['sent_non_exclusive'];
                        break;
                    default:
                        $kolom = [];
                        break;
                }
                $data_inputed = 0;
                foreach ($kolom as $key => $value) {
                    if (!$data[$value]) {
                        $isi = [
                            $value => $date
                        ];
                        $data->update($isi);
                        Cache::forget('cache-lily');
                        $this->page->DataLilyCached();
                        return ['data' => 200];
                    } else {
                        $data_inputed++;
                    }
                }
                if($data_inputed == 5){
                    foreach ($kolom as $key => $value) {
                        $last_fu = Cache::get('number-fu-lily-'.$id, 1);
                        if($value == 'fu_'.$last_fu){
                            $isi = [
                                $value => $date
                            ];
                            $data->update($isi);
                            Cache::forget('number-fu-lily-'.$id);
                            Cache::put('number-fu-lily-'.$id, $last_fu==5?$last_fu=1:$last_fu+1);
                            Cache::forget('cache-lily');
                            $this->page->DataLilyCached();
                            return ['data' => 200];
                        }
                    }
                }
                break;
            case 'maydewi' :
                $data = DailyReportMaydewi::findOrFail($id);
                switch ($row) {
                    case 'fu':
                        $kolom = ['fu_1','fu_2','fu_3','fu_4','fu_5'];
                        break;
                    case 'sent_royalty':
                        $kolom = ['sent_royalty'];
                        break;
                    case 'sent_non_exclusive':
                        $kolom = ['sent_non_exclusive'];
                        break;
                    default:
                        $kolom = [];
                        break;
                }
                $data_inputed = 0;
                foreach ($kolom as $key => $value) {
                    if (!$data[$value]) {
                        $isi = [
                            $value => $date
                        ];
                        $data->update($isi);
                        Cache::forget('cache-maydewi');
                        $this->page->DataMaydewiCached();
                        return ['data' => 200];
                    } else {
                        $data_inputed++;
                    }
                }
                if($data_inputed == 5){
                    foreach ($kolom as $key => $value) {
                        $last_fu = Cache::get('number-fu-ame-'.$id, 1);
                        if($value == 'fu_'.$last_fu){
                            $isi = [
                                $value => $date
                            ];
                            $data->update($isi);
                            Cache::forget('number-fu-ame-'.$id);
                            Cache::put('number-fu-ame-'.$id, $last_fu==5?$last_fu=1:$last_fu+1);
                            Cache::forget('cache-ame');
                            $this->page->DataAmeCached();
                            return ['data' => 200];
                        }
                    }
                }
                break;
            case 'rani' :
                $data = DailyReportRani::findOrFail($id);
                switch ($row) {
                    case 'fu':
                        $kolom = ['fu_1','fu_2','fu_3','fu_4','fu_5'];
                        break;
                    case 'sent_royalty':
                        $kolom = ['sent_royalty'];
                        break;
                    case 'sent_non_exclusive':
                        $kolom = ['sent_non_exclusive'];
                        break;
                    default:
                        $kolom = [];
                        break;
                }
                $data_inputed = 0;
                foreach ($kolom as $key => $value) {
                    if (!$data[$value]) {
                        $isi = [
                            $value => $date
                        ];
                        $data->update($isi);
                        Cache::forget('cache-rani');
                        $this->page->DataRaniCached();
                        return ['data' => 200];
                    } else {
                        $data_inputed++;
                    }
                }
                if($data_inputed == 5){
                    foreach ($kolom as $key => $value) {
                        $last_fu = Cache::get('number-fu-rani-'.$id, 1);
                        if($value == 'fu_'.$last_fu){
                            $isi = [
                                $value => $date
                            ];
                            $data->update($isi);
                            Cache::forget('number-fu-rani-'.$id);
                            Cache::put('number-fu-rani-'.$id, $last_fu==5?$last_fu=1:$last_fu+1);
                            Cache::forget('cache-rani');
                            $this->page->DataRaniCached();
                            return ['data' => 200];
                        }
                    }
                }
                break;
            case 'icha-nur' :
                $data = DailyReportIndoIchaNur::findOrFail($id);
                switch ($row) {
                    case 'fu':
                        $kolom = ['fu_1','fu_2','fu_3','fu_4','fu_5'];
                        break;
                    case 'data_sent':
                        $kolom = ['data_sent'];
                        break;
                    default:
                        $kolom = [];
                        break;
                }
                $data_inputed = 0;
                foreach ($kolom as $key => $value) {
                    if (!$data[$value]) {
                        $isi = [
                            $value => $date
                        ];
                        $data->update($isi);
                        Cache::forget('cache-ichanur');
                        $this->page->DataIndoIchaNurCached();
                        return ['data' => 200];
                    } else {
                        $data_inputed++;
                    }
                }
                if($data_inputed == 5){
                    foreach ($kolom as $key => $value) {
                        $last_fu = Cache::get('number-fu-ichanur-'.$id, 1);
                        if($value == 'fu_'.$last_fu){
                            $isi = [
                                $value => $date
                            ];
                            $data->update($isi);
                            Cache::forget('number-fu-ichanur-'.$id);
                            Cache::put('number-fu-ichanur-'.$id, $last_fu==5?$last_fu=1:$last_fu+1);
                            Cache::forget('cache-ichanur');
                            $this->page->DataIndoIchaNurCached();
                            return ['data' => 200];
                        }
                    }
                }
                break;
            case 'irel' :
                $data = DailyReportIndoIrel::findOrFail($id);
                switch ($row) {
                    case 'fu':
                        $kolom = ['fu_1','fu_2','fu_3','fu_4','fu_5','fu_6','fu_7','fu_8','fu_9','fu_10'];
                        break;
                    default:
                        $kolom = [];
                        break;
                }
                $data_inputed = 0;
                foreach ($kolom as $key => $value) {
                    if (!$data[$value]) {
                        $isi = [
                            $value => $date
                        ];
                        $data->update($isi);
                        Cache::forget('cache-irel');
                        $this->page->DataIndoIrelCached();
                        return ['data' => 200];
                    } else {
                        $data_inputed++;
                    }
                }
                if($data_inputed == 10){
                    foreach ($kolom as $key => $value) {
                        $last_fu = Cache::get('number-fu-irel-'.$id, 1);
                        if($value == 'fu_'.$last_fu){
                            $isi = [
                                $value => $date
                            ];
                            $data->update($isi);
                            Cache::forget('number-fu-irel-'.$id);
                            Cache::put('number-fu-irel-'.$id, $last_fu==5?$last_fu=1:$last_fu+1);
                            Cache::forget('cache-irel');
                            $this->page->DataIndoIrelCached();
                            return ['data' => 200];
                        }
                    }
                }
                break;
            case 'non-exclusive' :
                $data = NonExclusiveReport::findOrFail($id);
                switch ($row) {
                    case 'first_touch':
                        $kolom = ['first_touch'];
                        break;
                    case 'sent_e_contract':
                        $kolom = ['sent_e_contract'];
                        break;
                    case 'data_sent':
                        $kolom = ['data_sent'];
                        break;
                    case 'solved_date':
                        $kolom = ['solved_date'];
                        break;
                    case 'rec_e_contract':
                        $kolom = ['rec_e_contract'];
                        break;
                    case 'fu':
                        $kolom = ['fu_1','fu_2','fu_3','fu_4','fu_5'];
                        break;
                    case 'email_sent':
                        $kolom = ['email_sent'];
                        break;
                    case 'batch_date':
                        $kolom = ['batch_date'];
                        break;
                    default:
                        $kolom = [];
                        break;
                }
                $data_inputed = 0;
                foreach ($kolom as $key => $value) {
                    if (!$data[$value]) {
                        $isi = [
                            $value => $date
                        ];
                        $data->update($isi);
                        Cache::forget('cache-non-exclusive');
                        $this->page->DataNonExCached();
                        return ['data' => 200];
                    } else {
                        $data_inputed++;
                    }
                }
                if($data_inputed == 5){
                    foreach ($kolom as $key => $value) {
                        $last_fu = Cache::get('number-fu-non-exclusive-'.$id, 1);
                        if($value == 'fu_'.$last_fu){
                            $isi = [
                                $value => $date
                            ];
                            $data->update($isi);
                            Cache::forget('number-fu-non-exclusive-'.$id);
                            Cache::put('number-fu-non-exclusive-'.$id, $last_fu==5?$last_fu=1:$last_fu+1);
                            Cache::forget('cache-non-exclusive');
                            $this->page->DataNonExCached();
                            return ['data' => 200];
                        }
                    }
                }
                break;
            case 'mangatoon' :
                $data = ReportSpamMangatoonNovelList::findOrFail($id);
                switch ($row) {
                    case 'date_feedback_received':
                        $kolom = ['date_feedback_received'];
                        break;
                    default:
                        $kolom = [];
                        break;
                }
                foreach ($kolom as $key => $value) {
                    if (!$data[$value]) {
                        $isi = [
                            $value => $date
                        ];
                        $data->update($isi);
                        Cache::forget('cache-mangatoon');
                        $this->page->DataSpamMangatoonCached();
                        return ['data' => 200];
                    }
                }
                break;
            case 'wn-uncontracted' :
                $data = ReportSpamWNUncoractedNovelList::findOrFail($id);
                switch ($row) {
                    case 'date_feedback_received':
                        $kolom = ['date_feedback_received'];
                        break;
                    default:
                        $kolom = [];
                        break;
                }
                foreach ($kolom as $key => $value) {
                    if (!$data[$value]) {
                        $isi = [
                            $value => $date
                        ];
                        $data->update($isi);
                        Cache::forget('cache-wn_uncontracted');
                        $this->page->DataUncontractedWNCached();
                        return ['data' => 200];
                    }
                }
                break;
            default:
                $data = [];
                break;
        }
        return ['data' => null];
    }
    public function editValueReport(Request $request){
        $id = $request->input('id');
        $p = $request->input('p');
        switch ($p) {
            case 'ame':
                $data = DailyReportAme::findOrFail($id);
                $username = $request->input('username') != '' ? 1 : 0;
                $cbid = $request->input('cbid') != '' ? 1 : 0;
                $title = $request->input('title') != '' ? 1 : 0;
                $genre = $request->input('genre') != '' ? 1 : 0;
                $plot = $request->input('plot') != '' ? 1 : 0;
                $k4 = $request->input('k4') != '' ? 1 : 0;
                $maintain_account = $request->input('maintain_account') != '' ? 1 : 0;
                $marker = $username + $cbid + $title + $genre + $plot + $k4 + $maintain_account;
                $isi = [
                    "date" => $request->input('date'),
                    "status" => $request->input('status'),
                    "media" => $request->input('media'),
                    "author_contact" => $request->input('author_contact'),
                    "inquiries" => $request->input('inquiries'),
                    "platform" => $request->input('platform'),
                    "platform_user" => $request->input('platform_user'),
                    "platform_title" => $request->input('platform_title'),
                    "username" => $request->input('username'),
                    "cbid" => $request->input('cbid'),
                    "title" => $request->input('title'),
                    "genre" => $request->input('genre'),
                    "plot" => $request->input('plot'),
                    "k4" => $request->input('k4'),
                    "maintain_account" => $request->input('maintain_account'),
                    "marker" => $marker,
                    "old_new_book" => $request->input('old_new_book'),
                ];
                try {
                    $data->update($isi);
                    Cache::forget('cache-ame');
                    $this->page->DataAmeCached();
                    return ['data' => 200];
                } catch (\Throwable $th) {
                    return ['data' => null];
                }
                break;
            case 'anna' :
                $data = DailyReportAnna::findOrFail($id);
                $username = $request->input('username') != '' ? 1 : 0;
                $cbid = $request->input('cbid') != '' ? 1 : 0;
                $title = $request->input('title') != '' ? 1 : 0;
                $genre = $request->input('genre') != '' ? 1 : 0;
                $plot = $request->input('plot') != '' ? 1 : 0;
                $k4 = $request->input('k4') != '' ? 1 : 0;
                $maintain_account = $request->input('maintain_account') != '' ? 1 : 0;
                $marker = $username + $cbid + $title + $genre + $plot + $k4 + $maintain_account;
                $isi = [
                    "date" => $request->input('date'),
                    "status" => $request->input('status'),
                    "media" => $request->input('media'),
                    "author_contact" => $request->input('author_contact'),
                    "inquiries" => $request->input('inquiries'),
                    "platform" => $request->input('platform'),
                    "platform_user" => $request->input('platform_user'),
                    "platform_title" => $request->input('platform_title'),
                    "username" => $request->input('username'),
                    "cbid" => $request->input('cbid'),
                    "title" => $request->input('title'),
                    "genre" => $request->input('genre'),
                    "plot" => $request->input('plot'),
                    "k4" => $request->input('k4'),
                    "maintain_account" => $request->input('maintain_account'),
                    "marker" => $marker,
                    "old_new_book" => $request->input('old_new_book'),
                ];
                try {
                    $data->update($isi);
                    Cache::forget('cache-anna');
                    $this->page->DataAnnaCached();
                    return ['data' => 200];
                } catch (\Throwable $th) {
                    return ['data' => null];
                }
                break;
            case 'carol' :
                $data = DailyReportCarol::findOrFail($id);
                $username = $request->input('username') != '' ? 1 : 0;
                $cbid = $request->input('cbid') != '' ? 1 : 0;
                $title = $request->input('title') != '' ? 1 : 0;
                $genre = $request->input('genre') != '' ? 1 : 0;
                $plot = $request->input('plot') != '' ? 1 : 0;
                $k4 = $request->input('k4') != '' ? 1 : 0;
                $maintain_account = $request->input('maintain_account') != '' ? 1 : 0;
                $marker = $username + $cbid + $title + $genre + $plot + $k4 + $maintain_account;
                $isi = [
                    "date" => $request->input('date'),
                    "status" => $request->input('status'),
                    "media" => $request->input('media'),
                    "author_contact" => $request->input('author_contact'),
                    "inquiries" => $request->input('inquiries'),
                    "platform" => $request->input('platform'),
                    "platform_user" => $request->input('platform_user'),
                    "platform_title" => $request->input('platform_title'),
                    "username" => $request->input('username'),
                    "cbid" => $request->input('cbid'),
                    "title" => $request->input('title'),
                    "genre" => $request->input('genre'),
                    "plot" => $request->input('plot'),
                    "k4" => $request->input('k4'),
                    "maintain_account" => $request->input('maintain_account'),
                    "marker" => $marker,
                    "old_new_book" => $request->input('old_new_book'),
                ];
                try {
                    $data->update($isi);
                    Cache::forget('cache-carol');
                    $this->page->DataCarolCached();
                    return ['data' => 200];
                } catch (\Throwable $th) {
                    return ['data' => null];
                }
                break;
            case 'eric' :
                $data = DailyReportEric::findOrFail($id);
                $username = $request->input('username') != '' ? 1 : 0;
                $cbid = $request->input('cbid') != '' ? 1 : 0;
                $title = $request->input('title') != '' ? 1 : 0;
                $genre = $request->input('genre') != '' ? 1 : 0;
                $plot = $request->input('plot') != '' ? 1 : 0;
                $k4 = $request->input('k4') != '' ? 1 : 0;
                $maintain_account = $request->input('maintain_account') != '' ? 1 : 0;
                $marker = $username + $cbid + $title + $genre + $plot + $k4 + $maintain_account;
                $isi = [
                    "date" => $request->input('date'),
                    "status" => $request->input('status'),
                    "media" => $request->input('media'),
                    "author_contact" => $request->input('author_contact'),
                    "inquiries" => $request->input('inquiries'),
                    "platform" => $request->input('platform'),
                    "platform_user" => $request->input('platform_user'),
                    "platform_title" => $request->input('platform_title'),
                    "username" => $request->input('username'),
                    "cbid" => $request->input('cbid'),
                    "title" => $request->input('title'),
                    "genre" => $request->input('genre'),
                    "plot" => $request->input('plot'),
                    "k4" => $request->input('k4'),
                    "maintain_account" => $request->input('maintain_account'),
                    "marker" => $marker,
                    "old_new_book" => $request->input('old_new_book'),
                ];
                try {
                    $data->update($isi);
                    Cache::forget('cache-eric');
                    $this->page->DataEricCached();
                    return ['data' => 200];
                } catch (\Throwable $th) {
                    return ['data' => null];
                }
                break;
            case 'icha' :
                $data = DailyReportIcha::findOrFail($id);
                $username = $request->input('username') != '' ? 1 : 0;
                $cbid = $request->input('cbid') != '' ? 1 : 0;
                $title = $request->input('title') != '' ? 1 : 0;
                $genre = $request->input('genre') != '' ? 1 : 0;
                $plot = $request->input('plot') != '' ? 1 : 0;
                $k4 = $request->input('k4') != '' ? 1 : 0;
                $maintain_account = $request->input('maintain_account') != '' ? 1 : 0;
                $marker = $username + $cbid + $title + $genre + $plot + $k4 + $maintain_account;
                $isi = [
                    "date" => $request->input('date'),
                    "status" => $request->input('status'),
                    "media" => $request->input('media'),
                    "author_contact" => $request->input('author_contact'),
                    "inquiries" => $request->input('inquiries'),
                    "platform" => $request->input('platform'),
                    "platform_user" => $request->input('platform_user'),
                    "platform_title" => $request->input('platform_title'),
                    "username" => $request->input('username'),
                    "cbid" => $request->input('cbid'),
                    "title" => $request->input('title'),
                    "genre" => $request->input('genre'),
                    "plot" => $request->input('plot'),
                    "k4" => $request->input('k4'),
                    "maintain_account" => $request->input('maintain_account'),
                    "marker" => $marker,
                    "old_new_book" => $request->input('old_new_book'),
                ];
                try {
                    $data->update($isi);
                    Cache::forget('cache-icha');
                    $this->page->DataIchaCached();
                    return ['data' => 200];
                } catch (\Throwable $th) {
                    return ['data' => null];
                }
                break;
            case 'lily' :
                $data = DailyReportLily::findOrFail($id);
                $username = $request->input('username') != '' ? 1 : 0;
                $cbid = $request->input('cbid') != '' ? 1 : 0;
                $title = $request->input('title') != '' ? 1 : 0;
                $genre = $request->input('genre') != '' ? 1 : 0;
                $plot = $request->input('plot') != '' ? 1 : 0;
                $k4 = $request->input('k4') != '' ? 1 : 0;
                $maintain_account = $request->input('maintain_account') != '' ? 1 : 0;
                $marker = $username + $cbid + $title + $genre + $plot + $k4 + $maintain_account;
                $isi = [
                    "date" => $request->input('date'),
                    "status" => $request->input('status'),
                    "media" => $request->input('media'),
                    "author_contact" => $request->input('author_contact'),
                    "inquiries" => $request->input('inquiries'),
                    "platform" => $request->input('platform'),
                    "platform_user" => $request->input('platform_user'),
                    "platform_title" => $request->input('platform_title'),
                    "username" => $request->input('username'),
                    "cbid" => $request->input('cbid'),
                    "title" => $request->input('title'),
                    "genre" => $request->input('genre'),
                    "plot" => $request->input('plot'),
                    "k4" => $request->input('k4'),
                    "maintain_account" => $request->input('maintain_account'),
                    "marker" => $marker,
                    "old_new_book" => $request->input('old_new_book'),
                ];
                try {
                    $data->update($isi);
                    Cache::forget('cache-lily');
                    $this->page->DataLilyCached();
                    return ['data' => 200];
                } catch (\Throwable $th) {
                    return ['data' => null];
                }
                break;
            case 'maydewi' :
                $data = DailyReportMaydewi::findOrFail($id);
                $username = $request->input('username') != '' ? 1 : 0;
                $cbid = $request->input('cbid') != '' ? 1 : 0;
                $title = $request->input('title') != '' ? 1 : 0;
                $genre = $request->input('genre') != '' ? 1 : 0;
                $plot = $request->input('plot') != '' ? 1 : 0;
                $k4 = $request->input('k4') != '' ? 1 : 0;
                $maintain_account = $request->input('maintain_account') != '' ? 1 : 0;
                $marker = $username + $cbid + $title + $genre + $plot + $k4 + $maintain_account;
                $isi = [
                    "date" => $request->input('date'),
                    "status" => $request->input('status'),
                    "media" => $request->input('media'),
                    "author_contact" => $request->input('author_contact'),
                    "inquiries" => $request->input('inquiries'),
                    "platform" => $request->input('platform'),
                    "platform_user" => $request->input('platform_user'),
                    "platform_title" => $request->input('platform_title'),
                    "username" => $request->input('username'),
                    "cbid" => $request->input('cbid'),
                    "title" => $request->input('title'),
                    "genre" => $request->input('genre'),
                    "plot" => $request->input('plot'),
                    "k4" => $request->input('k4'),
                    "maintain_account" => $request->input('maintain_account'),
                    "marker" => $marker,
                    "old_new_book" => $request->input('old_new_book'),
                ];
                try {
                    $data->update($isi);
                    Cache::forget('cache-maydewi');
                    $this->page->DataMaydewiCached();
                    return ['data' => 200];
                } catch (\Throwable $th) {
                    return ['data' => null];
                }
                break;
            case 'rani' :
                $data = DailyReportRani::findOrFail($id);
                $username = $request->input('username') != '' ? 1 : 0;
                $cbid = $request->input('cbid') != '' ? 1 : 0;
                $title = $request->input('title') != '' ? 1 : 0;
                $genre = $request->input('genre') != '' ? 1 : 0;
                $plot = $request->input('plot') != '' ? 1 : 0;
                $k4 = $request->input('k4') != '' ? 1 : 0;
                $maintain_account = $request->input('maintain_account') != '' ? 1 : 0;
                $marker = $username + $cbid + $title + $genre + $plot + $k4 + $maintain_account;
                $isi = [
                    "date" => $request->input('date'),
                    "status" => $request->input('status'),
                    "media" => $request->input('media'),
                    "author_contact" => $request->input('author_contact'),
                    "inquiries" => $request->input('inquiries'),
                    "platform" => $request->input('platform'),
                    "platform_user" => $request->input('platform_user'),
                    "platform_title" => $request->input('platform_title'),
                    "username" => $request->input('username'),
                    "cbid" => $request->input('cbid'),
                    "title" => $request->input('title'),
                    "genre" => $request->input('genre'),
                    "plot" => $request->input('plot'),
                    "k4" => $request->input('k4'),
                    "maintain_account" => $request->input('maintain_account'),
                    "marker" => $marker,
                    "old_new_book" => $request->input('old_new_book'),
                ];
                try {
                    $data->update($isi);
                    Cache::forget('cache-rani');
                    $this->page->DataRaniCached();
                    return ['data' => 200];
                } catch (\Throwable $th) {
                    return ['data' => null];
                }
                break;
            case 'icha-nur' :
                $data = DailyReportIndoIchaNur::findOrFail($id);
                $new_cbid =  $request->input('new_cbid') != '' ? 1 : 0;
                $author = $request->input('author') != '' ? 1 : 0;
                $title = $request->input('title') != '' ? 1 : 0;
                $genre = $request->input('genre') != '' ? 1 : 0;
                $k4 = $request->input('k4') != '' ? 1 : 0;
                $plot = $request->input('plot') != '' ? 1 : 0;
                $maintain_account = $request->input('maintain_account') != '' ? 1 : 0;
                $marker = $new_cbid + $author + $title + $genre + $k4 + $plot + $maintain_account;
                $isi = [
                    "date" => $request->input('date'),
                    "contact_way" => $request->input('contact_way'),
                    "author_contact" => $request->input('author_contact'),
                    "platform" => $request->input('platform'),
                    "status" => $request->input('status'),
                    "inquiries" => $request->input('inquiries'),
                    "new_cbid" => $request->input('new_cbid'),
                    "old_cbid" => $request->input('old_cbid'),
                    "author" => $request->input('author'),
                    "title" => $request->input('title'),
                    "genre" => $request->input('genre'),
                    "k4" => $request->input('k4'),
                    "plot" => $request->input('plot'),
                    "maintain_account" => $request->input('maintain_account'),
                    "marker" => $marker,
                    "old_new_book" => $request->input('old_new_book'),
                ];
                try {
                    $data->update($isi);
                    Cache::forget('cache-ichanur');
                    $this->page->DataIndoIchaNurCached();
                    return ['data' => 200];
                } catch (\Throwable $th) {
                    return ['data' => null];
                }
                break;
            case 'irel' :
                $data = DailyReportIndoIrel::findOrFail($id);
                $isi = [
                    "date" => $request->input('date'),
                    "status" => $request->input('status'),
                    "date_solved" => $request->input('date_solved'),
                    "author_contact" => $request->input('author_contact'),
                    "inquiries" => $request->input('inquiries'),
                    "cbid" => $request->input('cbid'),
                    "title" => $request->input('title'),
                    "author" => $request->input('author'),
                    "zoom_tutorial" => $request->input('zoom_tutorial'),
                ];
                try {
                    $data->update($isi);
                    Cache::forget('cache-irel');
                    $this->page->DataIndoIrelCached();
                    return ['data' => 200];
                } catch (\Throwable $th) {
                    return ['data' => null];
                }
                break;
            case 'non-exclusive' :
                $data = NonExclusiveReport::findOrFail($id);
                $solved_date = 0;
                $rec_e_contract = 0;
                foreach ($data as $key => $value) {
                    $solved_date = $value->solved_date != '' ?? $solved_date+1;
                    $rec_e_contract = $value->rec_e_contract != '' ?? $rec_e_contract+1;
                }
                $book_id = $request->input('book_id') != '' ? 1 : 0;
                $pdf_evidence = $request->input('pdf_evidence') != '' ? 1 : 0;
                $marker_for_global = $book_id + $pdf_evidence + $solved_date + $rec_e_contract;
                $marker_for_and = $solved_date + $rec_e_contract;
                $isi = [
                    "date" => $request->input('date'),
                    "global_editor" => $request->input('global_editor'),
                    "author_contact" => $request->input('author_contact'),
                    "platform" => $request->input('platform'),
                    "username" => $request->input('username'),
                    "title" => $request->input('title'),
                    "book_status" => $request->input('book_status'),
                    "latest_update" => $request->input('latest_update'),
                    "book_id" => $request->input('book_id'),
                    "sent_e_contract" => $request->input('sent_e_contract'),
                    "officer" => $request->input('officer'),
                    "date_sent" => $request->input('date_sent'),
                    "and_notes" => $request->input('and_notes'),
                    "global_editor_notes" => $request->input('global_editor_notes'),
                    "pdf_evidence" => $request->input('pdf_evidence'),
                    "marker_for_global" => $marker_for_global,
                    "marker_for_and" => $marker_for_and,
                    "and_evidence" => $request->input('and_evidence'),
                    "global_evidence" => $request->input('global_evidence'),
                ];
                try {
                    $data->update($isi);
                    Cache::forget('cache-non-exclusive');
                    $this->page->DataNonExCached();
                    return ['data' => 200];
                } catch (\Throwable $th) {
                    return ['data' => null];
                }
                break;
            case 'mangatoon' :
                $data = ReportSpamMangatoonNovelList::findOrFail($id);
                $isi = [
                    "date" => $request->input('date'),
                    "reasons" => $request->input('reasons'),
                    "book_name" => $request->input('book_name'),
                    "author_name" => $request->input('author_name'),
                    "view" => $request->input('view'),
                    "likes" => $request->input('likes'),
                    "ratings" => $request->input('ratings'),
                    "update_status" => $request->input('update_status'),
                    "tags" => $request->input('tags'),
                    "episodes" => $request->input('episodes'),
                    "link" => $request->input('link'),
                    "screenshot_from_wave" => $request->input('screenshot_from_wave'),
                    "author_feedback" => $request->input('author_feedback'),
                    "comment_from_wave" => $request->input('comment_from_wave'),
                ];
                try {
                    $data->update($isi);
                    Cache::forget('cache-mangatoon');
                    $this->page->DataSpamMangatoonCached();
                    return ['data' => 200];
                } catch (\Throwable $th) {
                    return ['data' => null];
                }
                break;
            case 'wn-uncontracted' :
                $data = ReportSpamWNUncoractedNovelList::findOrFail($id);
                $isi = [
                    "date" => $request->input('date'),
                    "reasons" => $request->input('reasons'),
                    "editor" => $request->input('editor'),
                    "cbid" => $request->input('cbid'),
                    "book_title" => $request->input('book_title'),
                    "author_name" => $request->input('author_name'),
                    "discord_contact" => $request->input('discord_contact'),
                    "other_contact_way" => $request->input('other_contact_way'),
                    "genre" => $request->input('genre'),
                    "total_chapter" => $request->input('total_chapter'),
                    "chapter_within_7_days" => $request->input('chapter_within_7_days'),
                    "collection" => $request->input('collection'),
                    "status_ongoing" => $request->input('status_ongoing'),
                    "FL_ML" => $request->input('FL_ML'),
                    "feedback_from_author" => $request->input('feedback_from_author'),
                    "note" => $request->input('note'),
                ];
                try {
                    $data->update($isi);
                    Cache::forget('cache-wn_uncontracted');
                    $this->page->DataUncontractedWNCached();
                    return ['data' => 200];
                } catch (\Throwable $th) {
                    return ['data' => null];
                }
                break;
            case 'novel-list-ranking' :
                $data = ReportSpamNovelListFromRanking::findOrFail($id);
                $isi = [
                    "cbid" => $request->input('cbid'),
                    "book_title" => $request->input('book_title'),
                    "author_name" => $request->input('author_name'),
                    "author_contact" => $request->input('discord_contact'),
                    "genre" => $request->input('genre'),
                    "total_chapter" => $request->input('total_chapter'),
                    "chapter_within_7_days" => $request->input('chapter_within_7_days'),
                    "collection" => $request->input('collection'),
                    "status_ongoing" => $request->input('status_ongoing'),
                    "FL_ML" => $request->input('FL_ML'),
                    "editor" => $request->input('feedback_from_author'),
                    "note" => $request->input('note'),
                ];
                try {
                    $data->update($isi);
                    return ['data' => 200];
                } catch (\Throwable $th) {
                    return ['data' => null];
                }
                break;
            default:
                $data = [];
                break;
        }
        // dd($request);
    }
}
