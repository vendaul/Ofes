<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationAnswer extends Model
{
    protected $table = 'evaluation_answers';
    protected $primaryKey = 'answer_id';

    protected $fillable = [
        'eval_id',
        'question_id',
        'rating'
    ];

    public $timestamps = false;

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class, 'eval_id');
    }

    public function question()
    {
        return $this->belongsTo(EvaluationQuestion::class, 'question_id');
    }
}