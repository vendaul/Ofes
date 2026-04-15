<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\College;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_MAP = [
        'admin' => '1',
        'instructor' => '2',
        'student' => '3',
        'super_admin' => '4'  // Added for admin user in database
    ];

    const DISPLAY_MAP = [
        '1' => 'admin',
        '2' => 'instructor',
        '3' => 'student',
        '4' => 'admin',  // Admin user has role '4'
        'admin' => 'admin',  // backward compatibility
        'instr' => 'instructor',
        'stud' => 'student'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'empid',
        'fname',
        'mname',
        'lname',
        'extension',
        'fullname',
        'maiden_name',
        'birthday',
        'birthplace',
        'citizenship',
        'citizenship_type',
        'citizenship_country',
        'gender',
        'tribe',
        'marital_status',
        'other_status',
        'religion',
        'disability',
        'specialization',
        'height',
        'weight',
        'bloodtype',
        'gsis_id',
        'umid_id',
        'pagibig_id',
        'philhealth_id',
        'sss_id',
        'philsys_id',
        'tin_no',
        'agency_emp_no',
        'add1_lot_no',
        'add1_street',
        'add1_sub',
        'add1_brgy',
        'add1_city',
        'add1_prov',
        'add1_zip',
        'add2_lot_no',
        'add2_street',
        'add2_sub',
        'add2_brgy',
        'add2_city',
        'add2_prov',
        'add2_zip',
        'tel_no',
        'contact',
        'slug',
        'email',
        'password',
        'areacode',
        'academic_rank',
        'hep',
        'hep_course',
        'hep_units',
        'user_access',
        'user_data_man_role',
        'sias_role',
        'user_role',
        'user_hr_role',
        'user_dept_role',
        'user_data_role',
        'department',
        'college',
        'position',
        'parentethical_id',
        'emp_cat',
        'r_status',
        'deactivation_reason',
        'is_approved',
        'validator_comment',
        'account_id',
        'is_admin',
        'user_journal_role',
        'remember_token',
        'activated_at',
        'last_login',
        'verification_code',
        'verification_created_at',
        'is_requested_vrc',
        'password_reset_at',
        'created_user_id',
        'deleted_user_id',
        'activated_user_id',
        'is_project_based',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthday' => 'date',
        'activated_at' => 'datetime',
        'last_login' => 'datetime',
        'verification_created_at' => 'datetime',
        'password_reset_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the display role name.
     */
    public function getDisplayRoleAttribute()
    {
        return self::DISPLAY_MAP[trim($this->user_role)] ?? trim($this->user_role);
    }

    /**
     * Get the trimmed user role.
     */
    public function getUserRoleAttribute($value)
    {
        return trim($value);
    }

    /**
     * Get the user's full name.
     */
    public function getNameAttribute()
    {
        return $this->fullname ?: ($this->fname . ' ' . $this->mname . ' ' . $this->lname);
    }

    public function collegeRelation()
    {
        return $this->belongsTo(College::class, 'college');
    }
}
