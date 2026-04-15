<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\TeachingAssignment;
use App\Models\Evaluation;
use App\Models\EvaluationResult;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use App\Models\ClassSchedule;

class StudentDashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $student = Student::where('account_id', Auth::id())->first();

        if (!$student) {
            return redirect()->back()->with('error', 'Student record not found.');
        }

        $enrolledScheduleIds = $this->getEnrolledScheduleIdsForStudent($student);

        // Get the actual sections from the student's enrolled schedules only.
        $sectionIds = ClassSchedule::whereIn('id', $enrolledScheduleIds)
            ->pluck('section_id')
            ->filter()
            ->unique();
        $sections = Section::whereIn('id', $sectionIds)->get();

        // Compute active period filter
        $activePeriodId = DB::table('system_settings')->where('key', 'active_period_id')->value('value');
        $activePeriod = $activePeriodId ? DB::table('db_periods')->find($activePeriodId) : null;

        // Get class schedules for all student sections (primary source for instructor/workload)
        $scheduleQuery = ClassSchedule::with(['subject', 'section', 'instructor']);

        if ($activePeriod) {
            $scheduleQuery->where('ay', $activePeriod->year)->where('term', $activePeriod->term);
        }


        if ($enrolledScheduleIds->isNotEmpty()) {
            $scheduleQuery->whereIn('id', $enrolledScheduleIds);
        } else {
            // No enrollments -> no schedules.
            $scheduleQuery->whereRaw('1 = 0');
        }

        $schedules = $scheduleQuery->get();

        // Show all unique instructors teaching the relevant subjects from class schedules
        $instructors = $schedules->map(function ($s) {
            return optional($s->instructor);
        })->filter()->unique('empid');


        // Class schedules (DB table schedules) with optional term/AY filter
        $selectedTerm = $request->query('term');
        $selectedAy = $request->query('ay');

        if ($selectedTerm) {
            $schedules = $schedules->filter(function ($s) use ($selectedTerm) {
                return $s->term == $selectedTerm;
            })->values();
        }
        if ($selectedAy) {
            $schedules = $schedules->filter(function ($s) use ($selectedAy) {
                return $s->ay == $selectedAy;
            })->values();
        }

        // Provided term and AY select options for schedule filter
        $scheduleTerms = ClassSchedule::when($enrolledScheduleIds->isNotEmpty(), fn ($q) => $q->whereIn('id', $enrolledScheduleIds))
            ->when($enrolledScheduleIds->isEmpty(), fn ($q) => $q->whereRaw('1=0'))
            ->pluck('term')->filter()->unique()->values();
        $scheduleAys = ClassSchedule::when($enrolledScheduleIds->isNotEmpty(), fn ($q) => $q->whereIn('id', $enrolledScheduleIds))
            ->when($enrolledScheduleIds->isEmpty(), fn ($q) => $q->whereRaw('1=0'))
            ->pluck('ay')->filter()->unique()->values();

        return view('students.dashboard', compact(
            'student',
            'sections',
            'instructors',
            'schedules',
            'scheduleTerms',
            'scheduleAys',
            'selectedTerm',
            'selectedAy'
        ));
    }

    public function schedules(Request $request)
    {
        $student = Student::where('account_id', Auth::id())->first();

        if (!$student) {
            return redirect()->back()->with('error', 'Student record not found.');
        }

        $enrolledScheduleIds = $this->getEnrolledScheduleIdsForStudent($student);

        // Class schedules filtered by section and optional combined term/AY
        $selectedFilter = $request->query('filter');

        $scheduleQuery = ClassSchedule::with(['subject', 'section'])
            ->when($enrolledScheduleIds->isNotEmpty(), fn ($q) => $q->whereIn('id', $enrolledScheduleIds))
            ->when($enrolledScheduleIds->isEmpty(), fn ($q) => $q->whereRaw('1 = 0'));

        if ($selectedFilter) {
            list($term, $ay) = explode(' ', $selectedFilter, 2);
            $scheduleQuery->where('term', $term)->where('ay', $ay);
        }

        $schedules = $scheduleQuery->get();

        // Combined term and AY select options for schedule filter
        $scheduleFilters = ClassSchedule::when($enrolledScheduleIds->isNotEmpty(), fn ($q) => $q->whereIn('id', $enrolledScheduleIds))
            ->when($enrolledScheduleIds->isEmpty(), fn ($q) => $q->whereRaw('1 = 0'))
            ->selectRaw("CONCAT(term, ' ', ay) as combined")
            ->pluck('combined')->filter()->unique()->values();

        return view('students.schedules', compact('schedules', 'scheduleFilters', 'selectedFilter'));
    }

    public function evaluations(Request $request)
    {
        $student = Student::where('account_id', Auth::id())->first();

        if (!$student) {
            return redirect()->back()->with('error', 'Student record not found.');
        }

        $enrolledScheduleIds = $this->getEnrolledScheduleIdsForStudent($student);

        // Determine active period based on system settings
        $activePeriodId = DB::table('system_settings')->where('key', 'active_period_id')->value('value');
        $activePeriod = $activePeriodId ? DB::table('db_periods')->find($activePeriodId) : null;

        // Get schedules and then map to assignments (from class schedule source)
        $scheduleQuery = ClassSchedule::with(['instructor', 'subject', 'section']);

        if ($activePeriod) {
            $scheduleQuery->where('ay', $activePeriod->year)->where('term', $activePeriod->term);
        }


        $selectedInstructorId = $request->query('instructor_id');

        if ($enrolledScheduleIds->isNotEmpty()) {
            $scheduleQuery->whereIn('id', $enrolledScheduleIds);
        } else {
            $scheduleQuery->whereRaw('1 = 0');
        }

        if ($selectedInstructorId) {
            $scheduleQuery->where('instructor_id', $selectedInstructorId);
        }

        $schedules = $scheduleQuery->get();

        $assignments = $schedules->map(function ($schedule) {
            return TeachingAssignment::forClassSchedule($schedule);
        });

        // Student evaluations now use db_students.id only.
        $evaluatedPairs = Evaluation::where('student_id', $student->id)
            ->get()
            ->map(function ($evaluation) {
                if (!$evaluation->class_schedule_id) {
                    return null;
                }
                $schedule = ClassSchedule::find($evaluation->class_schedule_id);
                if (!$schedule) {
                    return null;
                }
                return $schedule->subject_id . ':' . $schedule->instructor_id;
            })->filter()->unique()->toArray();

        // Build a map of this student's evaluations per class schedule.
        $studentEvaluations = Evaluation::where('student_id', $student->id)
            ->whereNotNull('class_schedule_id')
            ->get()
            ->keyBy('class_schedule_id');

        // Mark each assignment evaluated status and attach existing evaluation data.
        $assignments = $assignments->map(function ($assignment) use ($evaluatedPairs, $studentEvaluations) {
            $key = $assignment->subject_id . ':' . $assignment->instructor_id;
            $assignment->evaluated = in_array($key, $evaluatedPairs);
            $assignment->evaluation_result = EvaluationResult::findByClassScheduleId($assignment->id);

            $scheduleId = $assignment->id;
            $studentEval = $studentEvaluations[$scheduleId] ?? null;

            $assignment->student_evaluation_rating = null;
            if ($studentEval) {
                $average = $studentEval->answers()->avg('rating');
                if ($average !== null) {
                    $assignment->student_evaluation_rating = round(min(max($average * 20, 0), 100), 2);
                }
            }

            return $assignment;
        });

        // Remove assignments that are already evaluated for same subject-instructor pair.
        $pendingAssignments = $assignments->filter(function ($assignment) {
            return !$assignment->evaluated;
        });

        // Show all unique instructors teaching this student.
        $instructors = $assignments->map(function ($a) {
            return optional($a->instructor);
        })->filter()->unique('id');

        // Compute average ratings per instructor for the evaluations section.
        $instructorRatings = [];
        foreach ($instructors as $instructor) {
            $assignmentIds = $assignments
                ->where('instructor_id', $instructor->id)
                ->pluck('id');

            $avg = EvaluationResult::whereIn('class_schedule_id', $assignmentIds)->avg('overall_average');
            $instructorRatings[$instructor->id] = $avg ? round($avg, 2) : null;
        }

        $courseCode = $student->course_code;
        $yearLevel = $student->year_level;

        if (!$courseCode || !$yearLevel) {
            $firstSchedule = $schedules->first();
            if ($firstSchedule) {
                $courseCode = $courseCode ?: ($firstSchedule->section->name ?? null);
                $yearLevel = $yearLevel ?: ($firstSchedule->year_level ?? null);
            }
        }

        $evaluationStartDate = DB::table('system_settings')->where('key', 'evaluation_start_date')->value('value');
        $evaluationEndDate = DB::table('system_settings')->where('key', 'evaluation_end_date')->value('value');

        $evaluationStart = $evaluationStartDate ? Carbon::parse($evaluationStartDate)->startOfDay() : null;
        $evaluationEnd = $evaluationEndDate ? Carbon::parse($evaluationEndDate)->endOfDay() : null;
        $now = Carbon::now();

        $evaluationOpen = $evaluationStart && $evaluationEnd && $now->between($evaluationStart, $evaluationEnd);
        $evaluationFuture = $evaluationStart && $now->lt($evaluationStart);
        $evaluationExpired = $evaluationEnd && $now->gt($evaluationEnd);

        return view('students.evaluations', compact('assignments', 'pendingAssignments', 'instructors', 'student', 'instructorRatings', 'courseCode', 'yearLevel', 'selectedInstructorId', 'activePeriod', 'evaluationStartDate', 'evaluationEndDate', 'evaluationOpen', 'evaluationFuture', 'evaluationExpired'));
    }

    /**
     * Get class schedule ids where the student is explicitly enrolled.
     */
    private function getEnrolledScheduleIdsForStudent(Student $student)
    {
        return $student->classScheduleEnrollments()
            ->pluck('class_schedule_id')
            ->filter()
            ->unique()
            ->values();
    }

    /**
     * Get section ids for the student based on their enrolled class schedules.
     */
    private function getSectionIdsForStudent(Student $student)
    {
        return $student->classSchedules()->with('section')->get()->pluck('section.id')->filter()->unique();
    }
}
