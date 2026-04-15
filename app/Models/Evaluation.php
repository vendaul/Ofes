<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $primaryKey = 'eval_id';

    protected $fillable = [
        'student_id',
        'class_schedule_id',
        'area_code',
        'college_id',
        'period_id',
        'evaluator_type',
        'submitted_by',
        'date_submitted',
        'comment'
    ];

    public $timestamps = false;

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function classSchedule()
    {
        return $this->belongsTo(ClassSchedule::class, 'class_schedule_id', 'id');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by', 'id');
    }

    public function answers()
    {
        return $this->hasMany(EvaluationAnswer::class, 'eval_id');
    }
}
