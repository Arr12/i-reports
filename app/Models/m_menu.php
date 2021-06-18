<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class m_menu extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'm_menus';

    public function sub_menus(){
        return $this->hasMany(m_sub_menu::class, 'id_menu');
    }
}