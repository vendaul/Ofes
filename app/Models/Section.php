<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = 'db_sections';
    protected $primaryKey = 'id';

    protected $fillable = [
        'area_code',
        'code',
        'name',
        'college_id',
        'course_id',
        'level_id',
        'year',
        'is_open',
        'schedule',
        'created_user_id',
        'updated_user_id',
        'deleted_user_id'
    ];

    public function classSchedules()
    {
        return $this->hasMany(ClassSchedule::class, 'section_id', 'id');
    }
}
