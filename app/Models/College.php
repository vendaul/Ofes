<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    protected $table = 'db_colleges';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'area_code',
        'name',
        'prefix',
        'head_officer',
    ];
}