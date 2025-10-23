<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LevelSubject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'level_id',
        'subject_id',
        'assigned_by'
    ];
}
