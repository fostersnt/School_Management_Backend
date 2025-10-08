<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_name',
        'db_name',
        'subdomain'
    ];

    public function verifySubdomain($subdomain)
    {
        $exists = $this->where('subdomain', $subdomain)->exists();
        return $exists;
    }

    public function createTenantSpecificDatabase($db_name)
    {
        $db_host        =   env("DB_HOST", 'N/A');
        $db_username    =   env('DB_USERNAME', 'N/A');
        $db_password    =   env("DB_PASSWORD");
    }
}
