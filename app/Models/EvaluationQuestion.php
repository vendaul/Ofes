<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationQuestion extends Model
{
    protected $table = 'evaluation_questions';
    protected $primaryKey = 'question_id';
    public $timestamps = false;

    protected $fillable = [
        'question_text',
        'category'
    ];
}
