<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportSpamNovelListFromRanking extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = "report_spam_novel_list_from_ranking";
}
