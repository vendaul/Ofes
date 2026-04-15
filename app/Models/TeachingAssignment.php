<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ClassSchedule;

class TeachingAssignment extends Model
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
        'section_id',
        'class_size',
        'class_max_size',
        'is_dissolved',
        'is_merged',
    ];

    public function getAsignIdAttribute()
    {
        return $this->id;
    }

    public function getRouteKeyName()
    {
        return 'id';
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id', 'id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
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

    /**
     * Return a TeachingAssignment-like instance for an existing ClassSchedule.
     * No teaching_assignments DB table dependency is required.
     */
    public static function forClassSchedule(ClassSchedule $schedule)
    {
        $assignment = self::find($schedule->id);

        if (!$assignment) {
            $assignment = new self($schedule->toArray());
            $assignment->setRelation('subject', $schedule->subject);
            $assignment->setRelation('section', $schedule->section);
            $assignment->setRelation('instructor', $schedule->instructor);
        }

        return $assignment;
    }
}

