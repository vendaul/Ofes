<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instructor extends User
{
    // Use users table where user_role = '2'
    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'empid',
        'fname',
        'lname',
        'email',
        'password',
        'user_role',
        'department',
        'college',
        'position',
        'designation',
        'contact',
        'profile_picture',
        'last_login_at',
    ];

    // Only get users with instructor role
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('instructors', function ($query) {
            $query->where('user_role', '2');
        });
    }

    public function assignments()
    {
        return $this->hasMany(TeachingAssignment::class, 'instructor_id', 'id');
    }
}
