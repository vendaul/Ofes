<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'areas';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'area_code',
        'area_name',
        'area_address',
    ];

    public function colleges()
    {
        return $this->hasMany(College::class, 'area_code', 'area_code');
    }
}
