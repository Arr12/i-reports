<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    public function index(){
        return view('page.home');
    }
    public function person(){
        return view('page.person');
    }
    public function dataPerson(){
        $cached['person']['global'] = Cache::get(date('Y-m-d')."PersonGlobal", SheetController::personGlobal());
        $cached['person']['indo'] = Cache::get(date('Y-m-d')."PersonIndo", SheetController::personIndo());

        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = ["No.", "Name", "Team", "Address", "QQEmail", "Email", "Phone", "Discord", "Instagram", "Facebook", "Status", "Level"];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $number = 1;
        foreach($cached['person']['global'] as $key => $data){
            $address = '';
            $name = isset($data[0]) ? $data[0] : "";
            $team = 'Global';
            $qq_email = isset($data[1]) ? $data[1] : "";
            $email = isset($data[2]) ? $data[2] : "";
            $phone_number = isset($data[4]) ? $data[4] : "";
            $discord = isset($data[3]) ? $data[3] : "";
            $instagram = isset($data[6]) ? $data[6] : "";
            $facebook = isset($data[5]) ? $data[5] : "";
            $status = 'Active';
            $level = '0';
            array_push($data_array['data'], [$number++,$name,$team,$address,$qq_email,$email,$phone_number,$discord,$instagram,$facebook,$status,$level]);
        }
        foreach($cached['person']['indo'] as $key => $data){
            $address = '';
            $name = isset($data[0]) ? $data[0] : "";
            $team = 'Indo';
            $qq_email = isset($data[1]) ? $data[1] : "";
            $email = isset($data[2]) ? $data[2] : "";
            $phone_number = isset($data[3]) ? $data[3] : "";
            $discord = "";
            $instagram = "";
            $facebook = "";
            $status = 'Active';
            $level = '0';
            array_push($data_array['data'], [$number++,$name,$team,$address,$qq_email,$email,$phone_number,$discord,$instagram,$facebook,$status,$level,$team]);
        }
        return $data_array;
    }
}