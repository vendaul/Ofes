<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'db_subjects';
    protected $primaryKey = 'id';

    protected $fillable = [
        'area_code',
        'code',
        'course_no',
        'name',
        'units',
        'load',
        'tf',
        'lec',
        'lec_sched',
        'lab',
        'lab_sched',
        'lab_wt',
        'tot_hrs',
        'type_id',
        'level_id',
        'lec_subj_id',
        'college_id',
        'is_professional',
        'is_exclusive',
        'is_no_tuition',
        'is_no_grade',
        'is_enclose_units',
        'is_exclude_ave_wt',
        'is_external_source',
        'is_teaching',
        'is_ojt',
        'is_special',
        'lab_hour_multiplier',
        'lab_credit_multiplier',
        'is_rle',
        'created_user_id',
        'updated_user_id',
        'deleted_user_id'
    ];

    protected static function booted(): void
    {
        static::saving(function (Subject $subject): void {
            $code = trim((string) ($subject->code ?? ''));
            $courseNo = trim((string) ($subject->course_no ?? ''));

            // Keep both fields aligned since course code and course number are equivalent.
            if ($code !== '') {
                $subject->code = $code;
                $subject->course_no = $code;
                return;
            }

            if ($courseNo !== '') {
                $subject->course_no = $courseNo;
                $subject->code = $courseNo;
            }
        });
    }

    public function curricula()
    {
        return $this->belongsToMany(Curriculum::class, 'db_curriculum_subjects', 's_code', 'curriculum_id', 'id', 'id');
    }

    public function curriculumSubjects()
    {
        return $this->hasMany(CurriculumSubject::class, 's_code', 'id');
    }

    /**
     * Return the first curriculum subject row (or null). Prefer the mapped records in the join table.
     */
    public function getFirstCurriculumSubjectAttribute()
    {
        return $this->curriculumSubjects->first();
    }

    public function getCurriculumDisplayAttribute()
    {
        $curriculumSubject = $this->firstCurriculumSubject;

        if ($curriculumSubject && $curriculumSubject->curriculum) {
            $curriculum = $curriculumSubject->curriculum;
            if ($curriculum->desc) {
                return $curriculum->code . ' - ' . $curriculum->desc;
            }
            return $curriculum->code;
        }

        $firstCurriculum = $this->curricula->first();
        if (! $firstCurriculum) {
            return null;
        }

        if ($firstCurriculum->desc) {
            return $firstCurriculum->code . ' - ' . $firstCurriculum->desc;
        }

        return $firstCurriculum->code;
    }

    public function getProgramDisplayAttribute()
    {
        $curriculumSubject = $this->firstCurriculumSubject;

        if ($curriculumSubject && $curriculumSubject->curriculum && $curriculumSubject->curriculum->course) {
            return $curriculumSubject->curriculum->course->course_program;
        }

        return optional(optional($this->curricula->first())->course)->course_program;
    }

    public function getSubjectYearAttribute()
    {
        return optional($this->firstCurriculumSubject)->s_year;
    }

    public function getSubjectTermAttribute()
    {
        return optional($this->curriculumSubjects->first())->s_term;
    }

    public function getSubjectUnitsAttribute()
    {
        return optional($this->curriculumSubjects->first())->s_units;
    }
}


