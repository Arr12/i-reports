<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonExclusiveReport extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'non_exclusive_reports';
}
