<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupervisorAssignment extends Model
{
    use HasFactory;

    protected $table = 'supervisor_assignments';

    protected $fillable = [
        'supervisor_id',
        'instructor_id',
        'class_schedule_id',
    ];

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id', 'id');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id', 'id');
    }

    public function classSchedule()
    {
        return $this->belongsTo(ClassSchedule::class, 'class_schedule_id', 'id');
    }
}
