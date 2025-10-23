<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'document_id',
        'user_id',
        'uploaded_by'
    ];
}
