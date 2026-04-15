<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'db_students';
    protected $primaryKey = 'id';

    public function evaluations()
    {
        // For now, return empty collection since evaluations reference students table
        // and Student model uses db_students. User can migrate data separately if needed.
        return collect();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'account_id');
    }

    public $timestamps = false;

    protected $fillable = [
        'account_id',
        'sid',
        'lname',
        'fname',
        'mname',
        'extname',
        'fullname',
        'course_code',
        'year_level',
        'area_code',
        'college_code',
        'email',
        'has_account',
        'password',
        'student_status',
        'account_id'
    ];

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFirstNameAttribute()
    {
        return $this->fname;
    }

    public function getLastNameAttribute()
    {
        return $this->lname;
    }

    public function classScheduleEnrollments()
    {
        return $this->hasMany(ClassScheduleStudent::class, 'user_student_id', 'id');
    }
    public function classSchedules()
    {
        return $this->belongsToMany(
            ClassSchedule::class,
            'db_class_schedules_student',
            'user_student_id',
            'class_schedule_id'
        );
    }
}
