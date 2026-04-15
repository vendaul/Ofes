<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    protected $table = 'db_curriculums';

    protected $guarded = [];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'db_curriculum_subjects', 'curriculum_id', 's_code', 'id', 'id');
    }
}
