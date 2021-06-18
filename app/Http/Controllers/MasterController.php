<?php

namespace App\Http\Controllers;

use App\Models\m_menu;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    public function IndexMasterMenu(){
        return view('admin.pages.master.menu');
    }
}
