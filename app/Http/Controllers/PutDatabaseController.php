<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PutDatabaseController extends Controller
{
    public function putPerson(){
        $cached['person']['global'] = Cache::get(date('Y-m-d')."PersonGlobal", SheetController::personGlobal());
        $cached['person']['indo'] = Cache::get(date('Y-m-d')."PersonIndo", SheetController::personIndo());
        if($cached){
            Person::truncate();
        }
        foreach($cached['person']['global'] as $key => $data){
            $address = '';
            $name = isset($data[0]) ? $data[0] : "";
            $nik = '';
            $qq_email = isset($data[1]) ? $data[1] : "";
            $email = isset($data[2]) ? $data[2] : "";
            $phone_number = isset($data[4]) ? $data[4] : "";
            $discord = isset($data[3]) ? $data[3] : "";
            $instagram = isset($data[6]) ? $data[6] : "";
            $facebook = isset($data[5]) ? $data[5] : "";
            $status = 'Active';
            $level = '0';
            $team = 'Global';
            $query = Person::create([
                'address' => $address,
                'name' => $name,
                'nik' => $nik,
                'qq_email' => $qq_email,
                'email' => $email,
                'phone_number' => $phone_number,
                'discord' => $discord,
                'instagram' => $instagram,
                'facebook' => $facebook,
                'status' => $status,
                'level' => $level,
                'team' => $team
            ]);
        }
        if($query){
            $x['team'] = "Global";
            $x['data']["status"] = 200;
            $x['data']['table'] = "person";
        }
        foreach($cached['person']['indo'] as $key => $data){
            $address = '';
            $name = isset($data[0]) ? $data[0] : "";
            $nik = '';
            $qq_email = isset($data[1]) ? $data[1] : "";
            $email = isset($data[2]) ? $data[2] : "";
            $phone_number = isset($data[3]) ? $data[3] : "";
            $discord = "";
            $instagram = "";
            $facebook = "";
            $status = 'Active';
            $level = '0';
            $team = 'Indo';
            $query = Person::create([
                'address' => $address,
                'name' => $name,
                'nik' => $nik,
                'qq_email' => $qq_email,
                'email' => $email,
                'phone_number' => $phone_number,
                'discord' => $discord,
                'instagram' => $instagram,
                'facebook' => $facebook,
                'status' => $status,
                'level' => $level,
                'team' => $team
            ]);
        }
        if($query){
            $x['team'] = "Indo";
            $x['data']["status"] = 200;
            $x['data']['table'] = "person";
        }
        return $x;
    }
}