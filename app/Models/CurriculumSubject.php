<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurriculumSubject extends Model
{
    protected $table = 'db_curriculum_subjects';

    protected $guarded = [];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'curriculum_id', 'id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 's_code', 'id');
    }
}
