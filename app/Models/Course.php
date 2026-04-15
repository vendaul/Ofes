<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'db_courses';

    protected $guarded = [];

    public function curriculums()
    {
        return $this->hasMany(Curriculum::class, 'course_id', 'id');
    }
}
