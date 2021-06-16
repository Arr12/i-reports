<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LatestImport extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'latest_import';
}
