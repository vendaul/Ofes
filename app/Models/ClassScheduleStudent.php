<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassScheduleStudent extends Model
{
    use HasFactory;

    protected $table = 'db_class_schedules_student';

    protected $fillable = [
        'area_code',
        'user_student_id',
        'year_level',
        'period_id',
        'term',
        'ay',
        'class_type',
        'class_status',
        'class_schedule_id',
        'subject_code',
        'remark',
        'created_user_id',
        'updated_user_id',
        'deleted_user_id',
        'dropped_user_id'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'user_student_id', 'id');
    }

    public function classSchedule()
    {
        return $this->belongsTo(ClassSchedule::class, 'class_schedule_id', 'id');
    }
}
