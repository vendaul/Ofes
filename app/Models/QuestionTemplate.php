<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'questions', // JSON field for storing questions
        'user_id', // If templates are user-specific
        // additional metadata
        'template_date',
        'semester',
        'school_year',
    ];
}
