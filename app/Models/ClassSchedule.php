<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSchedule extends Model

{
    protected $table = 'db_class_schedules';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'area_code',
        'period_id',
        'college_id',
        'course_id',
        'term',
        'ay',
        'schedule_code',
        'subject_id',
        'instructor_id',
        'year_level',
        'is_with_lec',
        'lec_week_day',
        'lec_start_time',
        'lec_end_time',
        'lec_room_id',
        'is_with_lab',
        'lab_week_day',
        'lab_start_time',
        'lab_end_time',
        'lab_room_id',
        'section_id',
        'class_size',
        'class_size_ireg',
        'class_max_size',
        'class_ext_size',
        'is_dissolved',
        'is_merged',
        'merged_class_id',
        'is_parent_merged',
        'is_with_complete_entry',
        'is_approved_grade',
        'is_approved_grade_entry',
        'is_class_list_check',
        'is_creditable',
        'is_shared_faculty',
        'shared_faculty_id',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id', 'id');
    }
    public function students()
{
    return $this->belongsToMany(
        Student::class,
        'db_class_schedules_student',
        'class_schedule_id',
        'user_student_id'
    );
}
}
