<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Evaluation;
use App\Models\TeachingAssignment;
use App\Models\EvaluationResult;
use App\Models\ClassScheduleStudent;
use App\Models\College;

class DashboardController extends Controller
{
    public function notifyPendingStudents(Request $request)
    {
        $user = auth()->user();
        $userRole = $user->user_role ?? null;
        if (!$user || !in_array($userRole, ['admin', '1', 'super_admin'], true)) {
            abort(403, 'Unauthorized (role=' . ($userRole ?? 'null') . ')');
        }

        $request->validate([
            'class_schedule_id' => 'required|integer|exists:db_class_schedules,id',
            'area_code' => 'nullable|string',
            'college_id' => 'nullable',
            'view' => 'nullable|string',
            'progress_assignment_id' => 'nullable|integer',
        ]);

        $classScheduleId = (int) $request->input('class_schedule_id');
        $assignment = TeachingAssignment::with(['subject', 'section', 'instructor'])->find($classScheduleId);

        if (!$assignment) {
            return redirect()->route('admin.dashboard')->with('error', 'Class schedule not found.');
        }

        $evaluationEndDate = DB::table('system_settings')->where('key', 'evaluation_end_date')->value('value');
        $deadlineText = $evaluationEndDate ? date('F d, Y', strtotime($evaluationEndDate)) : 'the configured evaluation deadline';
        $subjectName = $assignment->subject?->name ?? 'your assigned subject';
        $teacherName = $assignment->instructor?->name ?? 'your subject teacher';

        $classScheduleStudentsTable = (new ClassScheduleStudent())->getTable();
        $pendingStudents = DB::table($classScheduleStudentsTable . ' as css')
            ->join('db_students as s', 's.id', '=', 'css.user_student_id')
            ->leftJoin('users as u', 'u.id', '=', 's.account_id')
            ->leftJoin('evaluations as e', function ($join) use ($classScheduleId) {
                $join->on('e.student_id', '=', 'css.user_student_id')
                    ->where('e.class_schedule_id', '=', $classScheduleId);
            })
            ->where('css.class_schedule_id', $classScheduleId)
            ->whereNull('e.eval_id')
            ->where(function ($query) {
                $query->whereNotNull('u.email')
                    ->where('u.email', '!=', '')
                    ->orWhere(function ($inner) {
                        $inner->whereNotNull('s.email')
                            ->where('s.email', '!=', '');
                    });
            })
            ->selectRaw('COALESCE(NULLIF(u.email, ""), NULLIF(s.email, "")) as email, s.fname, s.lname, s.sid')
            ->distinct()
            ->get();

        $sentCount = 0;
        $failedCount = 0;

        // Notification template: edit these lines to change the message sent to students.
        $emailSubjectTemplate = 'OFES Reminder: Please evaluate %s';
        $emailBodyTemplate = "Good day %s,\n\n"
            . "This is a reminder to complete your Student Evaluation for: %s.\n"
            . "Subject Teacher: %s\n"
            . "Please submit your evaluation on or before %s.\n\n"
            . "Kaya mag evaluate ka tanga Thank you.";

        foreach ($pendingStudents as $student) {
            $studentName = trim(($student->fname ?? '') . ' ' . ($student->lname ?? ''));
            $message = sprintf(
                $emailBodyTemplate,
                ($studentName !== '' ? $studentName : 'Student'),
                $subjectName,
                $teacherName,
                $deadlineText
            );
            $emailSubject = sprintf($emailSubjectTemplate, $subjectName);

            try {
                Mail::raw($message, function ($mail) use ($student, $emailSubject) {
                    $mail->to($student->email)
                        ->subject($emailSubject);
                });
                $sentCount++;
            } catch (\Throwable $e) {
                $failedCount++;
                \Log::error('Failed to send SET reminder email', [
                    'class_schedule_id' => $classScheduleId,
                    'student_sid' => $student->sid ?? null,
                    'recipient' => $student->email ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $redirectParams = array_filter([
            'area_code' => $request->input('area_code'),
            'college_id' => $request->input('college_id'),
            'view' => $request->input('view'),
            'progress_assignment_id' => $request->input('progress_assignment_id', $classScheduleId),
        ], fn ($value) => $value !== null && $value !== '');

        $successRedirectParams = array_filter([
            'area_code' => $request->input('area_code'),
            'college_id' => $request->input('college_id'),
        ], fn ($value) => $value !== null && $value !== '');

        if ($sentCount > 0) {
            $message = 'Students notified.';
            if ($failedCount > 0) {
                $message .= " {$failedCount} email(s) failed to send.";
            }
            return redirect()->route('admin.dashboard', $successRedirectParams)->with('success', $message);
        }

        if ($pendingStudents->isEmpty()) {
            return redirect()->route('admin.dashboard', $redirectParams)
                ->with('info', 'No pending students with valid email addresses to notify.');
        }

        return redirect()->route('admin.dashboard', $redirectParams)
            ->with('error', 'Unable to send notification emails. Please check mail configuration.');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $userRole = $user->user_role ?? null;
        if (!$user || !in_array($userRole, ['admin', '1', 'super_admin'], true)) {
            abort(403, 'Unauthorized (role=' . ($userRole ?? 'null') . ')');
        }

        $areaSessionKey = 'admin_dashboard.area_code';
        $collegeSessionKey = 'admin_dashboard.college_id';

        if ($request->has('area_code')) {
            $areaCode = trim((string) $request->query('area_code'));
            $areaCode = $areaCode !== '' ? $areaCode : null;
            $request->session()->put($areaSessionKey, $areaCode);
        } else {
            $areaCode = $request->session()->get($areaSessionKey);
        }

        if ($request->has('college_id')) {
            $collegeId = trim((string) $request->query('college_id'));
            $collegeId = $collegeId !== '' ? $collegeId : null;
            $request->session()->put($collegeSessionKey, $collegeId);
        } else {
            $collegeId = $request->session()->get($collegeSessionKey);
        }
        $allowedCardViews = [
            'total-instructors',
            'faculty-evaluated',
            'student-evaluations',
            'evaluation-completion',
            'assignments-evaluated',
        ];
        $selectedCardView = in_array($request->query('view'), $allowedCardViews, true)
            ? $request->query('view')
            : null;
        $selectedProgressAssignmentId = (int) $request->query('progress_assignment_id', 0);

        $activePeriodId = DB::table('system_settings')->where('key', 'active_period_id')->value('value');
        $activePeriod = $activePeriodId ? DB::table('db_periods')->find($activePeriodId) : null;
        $evaluationDeadline = DB::table('system_settings')->where('key', 'evaluation_end_date')->value('value');

        $ay = $activePeriod ? $activePeriod->year : null;
        $term = $activePeriod ? $activePeriod->term : null;

        $areaCodes = TeachingAssignment::whereNotNull('area_code')
            ->distinct()
            ->orderBy('area_code')
            ->pluck('area_code')
            ->toArray();

        $areaOptions = DB::table('areas')
            ->whereIn('area_code', $areaCodes)
            ->orderBy('area_name')
            ->pluck('area_name', 'area_code')
            ->toArray();

        // ensure all area codes appear even if missing from areas master table
        foreach ($areaCodes as $code) {
            if (!isset($areaOptions[$code])) {
                $areaOptions[$code] = $code;
            }
        }

        $collegeOptions = College::query()
            ->when($areaCode, function ($q) use ($areaCode) {
                return $q->where('area_code', $areaCode);
            })
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $scheduleAy = TeachingAssignment::whereNotNull('ay')->distinct()->pluck('ay')->toArray();
        $studentAy = ClassScheduleStudent::whereNotNull('ay')->distinct()->pluck('ay')->toArray();
        $possibleAy = collect(array_merge($scheduleAy, $studentAy))->filter()->unique()->values()->toArray();

        $periodAy = DB::table('db_periods')
            ->whereNotNull('year')
            ->distinct()
            ->orderBy('year')
            ->pluck('year')
            ->toArray();

        $ayOptions = collect(array_merge($periodAy, $possibleAy))
            ->filter()->unique()->sort()->values()->toArray();

        $scheduleTerm = TeachingAssignment::whereNotNull('term')->distinct()->pluck('term')->toArray();
        $studentTerm = ClassScheduleStudent::whereNotNull('term')->distinct()->pluck('term')->toArray();
        $possibleTerm = collect(array_merge($scheduleTerm, $studentTerm))->filter()->unique()->values()->toArray();

        $periodTerm = DB::table('db_periods')
            ->whereNotNull('term')
            ->distinct()
            ->orderBy('term')
            ->pluck('term')
            ->toArray();

        $termOptions = collect(array_merge($periodTerm, $possibleTerm))
            ->filter()->unique()->sort()->values()->toArray();

        $termMap = [
            'First Trimester' => 'First',
            'Second Trimester' => 'Second',
            'Third Trimester' => 'Third',
            'First Semester' => 'First',
            'Second Semester' => 'Second',
            'Third Semester' => 'Third',
            'Middle Term' => 'First',
            'Summer' => 'First',
            'Midyear' => 'First'
        ];

        $assignmentQuery = TeachingAssignment::query();

        // Always constrain admin dashboard data to the active period.
        if ($activePeriod) {
            $assignmentQuery->where('ay', $activePeriod->year);
            $termCandidates = [$activePeriod->term];
            if (isset($termMap[$activePeriod->term])) {
                $termCandidates[] = $termMap[$activePeriod->term];
            }
            $termCandidates = array_unique($termCandidates);
            $assignmentQuery->whereIn('term', $termCandidates);
        } else {
            // Prevent showing all historical records when no active period is configured.
            $assignmentQuery->whereRaw('0 = 1');
        }

        if ($areaCode) {
            $assignmentQuery->where('area_code', $areaCode);
        }
        if ($collegeId) {
            $assignmentQuery->where('college_id', $collegeId);
        }

        $filteredAssignmentIds = $assignmentQuery->pluck('id')->toArray();

        $instructorDirectory = User::query()
            ->where(function ($query) {
                $query->where('user_role', 'instructor')
                    ->orWhere('user_role', 'instr')
                    ->orWhere('user_role', '2');
            })
            ->when($areaCode, function ($query) use ($areaCode) {
                return $query->where('areacode', $areaCode);
            })
            ->when($collegeId, function ($query) use ($collegeId) {
                return $query->where('college', $collegeId);
            })
            ->get(['id', 'empid', 'fullname', 'fname', 'mname', 'lname', 'areacode', 'college']);

        $totalInstructors = $instructorDirectory->count();

        $totalAssignments = $assignmentQuery->count();

        $evaluatedAssignmentIds = EvaluationResult::whereIn('class_schedule_id', $filteredAssignmentIds)
            ->pluck('class_schedule_id')
            ->filter()
            ->unique();

        $evaluatedAssignments = TeachingAssignment::whereIn('id', $evaluatedAssignmentIds)->count();

        $instructorsEvaluated = TeachingAssignment::whereIn('id', $evaluatedAssignmentIds)
            ->whereNotNull('instructor_id')
            ->distinct('instructor_id')
            ->count('instructor_id');

        $classScheduleStudentsTable = (new ClassScheduleStudent())->getTable();
        $studentEvaluations = 0;
        $studentEvaluators = 0;

        if (!empty($filteredAssignmentIds)) {
            $enrolledEvaluationQuery = DB::table('evaluations as e')
                ->join($classScheduleStudentsTable . ' as css', function ($join) {
                    $join->on('e.class_schedule_id', '=', 'css.class_schedule_id')
                        ->on('e.student_id', '=', 'css.user_student_id');
                })
                ->whereIn('e.class_schedule_id', $filteredAssignmentIds);

            $studentEvaluations = (int) (clone $enrolledEvaluationQuery)
                ->selectRaw('COUNT(DISTINCT e.class_schedule_id, e.student_id) as total')
                ->value('total');

            $studentEvaluators = (int) (clone $enrolledEvaluationQuery)
                ->distinct('e.student_id')
                ->count('e.student_id');
        }

        $totalExpectedEvaluations = 0;
        if (!empty($filteredAssignmentIds)) {
            $totalExpectedEvaluations = (int) ClassScheduleStudent::query()
                ->whereIn('class_schedule_id', $filteredAssignmentIds)
                ->selectRaw('COUNT(DISTINCT class_schedule_id, user_student_id) as total')
                ->value('total');
        }

        $studentEvaluationCompletion = $totalExpectedEvaluations > 0
            ? round(min(100, ($studentEvaluations / $totalExpectedEvaluations) * 100), 2)
            : 0;

        $instructorCompletionRate = $totalInstructors > 0 ? round(($instructorsEvaluated / $totalInstructors) * 100, 2) : 0;
        $assignmentCompletionRate = $totalAssignments > 0 ? round(($evaluatedAssignments / $totalAssignments) * 100, 2) : 0;

        $filteredAssignments = $assignmentQuery->with(['subject', 'section', 'instructor'])->get();
        $filteredAssignmentIds = $filteredAssignments->pluck('id')->all();
        $sectionIds = $filteredAssignments->pluck('section_id')->filter()->unique()->values()->all();

        $studentRowCountsBySchedule = [];
        $evaluatedStudentsBySchedule = [];
        $sectionStudentsBySection = [];
        if (!empty($filteredAssignmentIds)) {
            $studentRowCountsBySchedule = ClassScheduleStudent::query()
                ->select('class_schedule_id', DB::raw('COUNT(DISTINCT user_student_id) as students_count'))
                ->whereIn('class_schedule_id', $filteredAssignmentIds)
                ->groupBy('class_schedule_id')
                ->pluck('students_count', 'class_schedule_id')
                ->map(fn ($count) => (int) $count)
                ->toArray();

            $evaluatedStudentsBySchedule = DB::table('evaluations as e')
                ->join($classScheduleStudentsTable . ' as css', function ($join) {
                    $join->on('e.class_schedule_id', '=', 'css.class_schedule_id')
                        ->on('e.student_id', '=', 'css.user_student_id');
                })
                ->select('e.class_schedule_id', DB::raw('COUNT(DISTINCT e.student_id) as evaluated_count'))
                ->whereIn('e.class_schedule_id', $filteredAssignmentIds)
                ->groupBy('e.class_schedule_id')
                ->pluck('evaluated_count', 'e.class_schedule_id')
                ->map(fn ($count) => (int) $count)
                ->toArray();
        }

        if (!empty($sectionIds)) {
            $sectionStudentsBySection = DB::table('db_class_schedules as cs')
                ->join($classScheduleStudentsTable . ' as css', 'css.class_schedule_id', '=', 'cs.id')
                ->select('cs.section_id', DB::raw('COUNT(DISTINCT css.user_student_id) as section_students_count'))
                ->whereIn('cs.section_id', $sectionIds)
                ->groupBy('cs.section_id')
                ->pluck('section_students_count', 'cs.section_id')
                ->map(fn ($count) => (int) $count)
                ->toArray();
        }

        $subjectProgress = $filteredAssignments->map(function ($assignment) use (
            $studentRowCountsBySchedule,
            $evaluatedStudentsBySchedule,
            $sectionStudentsBySection
        ) {
            $studentRowCount = $studentRowCountsBySchedule[$assignment->id] ?? 0;
            $evaluatedStudents = $evaluatedStudentsBySchedule[$assignment->id] ?? 0;
            $sectionStudents = $assignment->section_id
                ? ($sectionStudentsBySection[$assignment->section_id] ?? 0)
                : 0;

            $capacity = $assignment->class_size ?: $assignment->class_max_size ?: $assignment->class_size_ireg ?: 0;

            // Class size should represent the practical enrolled count (including section-wide roll if larger).
            $classSize = max($studentRowCount, $sectionStudents);

            // Determine total tracking basis for completion; use enrolled size or capacity fallback.
            $totalStudents = max($classSize, $capacity, 1);

            // Cap progress between 0 and 100%.
            $progressAmount = $totalStudents > 0
                ? round(min(100, ($evaluatedStudents / $totalStudents) * 100), 2)
                : 0;

            return [
                'assignment_id' => $assignment->id,
                'instructor_id' => $assignment->instructor_id,
                'area_code' => $assignment->area_code,
                'subject_name' => $assignment->subject?->name ?? 'Unknown',
                'section_name' => $assignment->section?->name ?? ($assignment->year_level . ($assignment->section_id ? ' - ' . $assignment->section_id : '')),
                'instructor_name' => $assignment->instructor?->name ?? 'Unknown Instructor',
                'class_size' => $classSize,
                'total_students' => $totalStudents,
                'evaluated_students' => $evaluatedStudents,
                'progress' => $progressAmount,
                'average_rating' => null, // filled below
            ];
        });

        // Batch-fetch per-schedule ratings from evaluation_results.
        if (!empty($filteredAssignmentIds)) {
            $scheduleRatingMap = DB::table('evaluation_results')
                ->whereIn('class_schedule_id', $filteredAssignmentIds)
                ->selectRaw('class_schedule_id, SUM(overall_average * total_evaluations) / NULLIF(SUM(total_evaluations), 0) as avg_rating')
                ->groupBy('class_schedule_id')
                ->get()
                ->mapWithKeys(fn ($r) => [
                    (int) $r->class_schedule_id => $r->avg_rating !== null
                        ? round(((float) $r->avg_rating / 5) * 100, 2)
                        : null,
                ]);

            $subjectProgress = $subjectProgress->map(function ($row) use ($scheduleRatingMap) {
                $row['average_rating'] = $scheduleRatingMap->get($row['assignment_id']);
                return $row;
            });
        }

        $cardDetailTitle = null;
        $cardDetailRows = collect();
        $evaluatedAssignmentSet = collect($evaluatedAssignmentIds)
            ->map(fn ($id) => (int) $id)
            ->flip();

        $facultyPerformanceRows = collect();
        if ($instructorDirectory->isNotEmpty()) {
            $ratingMap = collect();
            if (!empty($filteredAssignmentIds)) {
                $ratingMap = DB::table('db_class_schedules as cs')
                    ->leftJoin('evaluation_results as er', 'er.class_schedule_id', '=', 'cs.id')
                    ->whereIn('cs.id', $filteredAssignmentIds)
                    ->whereNotNull('cs.instructor_id')
                    ->groupBy('cs.instructor_id')
                    ->selectRaw('CAST(cs.instructor_id AS CHAR) as instructor_key')
                    ->selectRaw('SUM(CASE WHEN er.overall_average IS NOT NULL THEN er.overall_average * COALESCE(er.total_evaluations, 0) ELSE 0 END) as weighted_sum')
                    ->selectRaw('SUM(CASE WHEN er.overall_average IS NOT NULL THEN COALESCE(er.total_evaluations, 0) ELSE 0 END) as total_weight')
                    ->get()
                    ->mapWithKeys(function ($row) {
                        $percentage = null;
                        $weight = (float) ($row->total_weight ?? 0);

                        if ($weight > 0) {
                            // Aggregate all class-schedule evaluation results per instructor, then convert 1-5 to 0-100.
                            $avgOnFiveScale = ((float) $row->weighted_sum) / $weight;
                            $percentage = round(($avgOnFiveScale / 5) * 100, 2);
                        }

                        return [(string) $row->instructor_key => $percentage];
                    });
            }

            $facultyPerformanceRows = $instructorDirectory
                ->map(function ($instructor) use ($ratingMap) {
                    $resolvedName = trim((string) ($instructor->name ?? ''));
                    if ($resolvedName === '') {
                        $resolvedName = trim((string) (($instructor->fname ?? '') . ' ' . ($instructor->lname ?? '')));
                    }

                    $avgRating = $ratingMap->get((string) $instructor->id);
                    if ($avgRating === null) {
                        $empid = trim((string) ($instructor->empid ?? ''));
                        if ($empid !== '' && strtoupper($empid) !== 'N/A') {
                            $avgRating = $ratingMap->get($empid);
                        }
                    }

                    return [
                        'instructor_name' => $resolvedName !== '' ? $resolvedName : 'N/A',
                        'average_rating' => $avgRating,
                    ];
                })
                ->filter(fn ($row) => $row['instructor_name'] !== 'N/A')
                ->values();

            $rank = 1;
            $rankedRows = $facultyPerformanceRows
                ->filter(fn ($row) => $row['average_rating'] !== null)
                ->sortByDesc('average_rating')
                ->values()
                ->map(function ($row) use (&$rank) {
                    $row['rank'] = $rank;
                    $rank++;
                    return $row;
                });

            $unratedRows = $facultyPerformanceRows
                ->filter(fn ($row) => $row['average_rating'] === null)
                ->values()
                ->map(function ($row) {
                    $row['rank'] = null;
                    return $row;
                });

            $facultyPerformanceRows = $rankedRows->concat($unratedRows)->values();
        }

        if ($selectedCardView === 'total-instructors') {
            $cardDetailTitle = 'Faculty Performance Overview';
            $cardDetailRows = $facultyPerformanceRows;
        }

        if ($selectedCardView === 'faculty-evaluated') {
            $cardDetailTitle = 'Faculty Evaluated';
            $cardDetailRows = $filteredAssignments
                ->filter(function ($assignment) use ($evaluatedAssignmentSet) {
                    return $evaluatedAssignmentSet->has((int) $assignment->id) && !empty($assignment->instructor_id);
                })
                ->groupBy('instructor_id')
                ->map(function ($rows) {
                    $first = $rows->first();
                    return [
                        'instructor_name' => $first->instructor?->name ?? 'Unknown Instructor',
                        'assignment_count' => $rows->count(),
                    ];
                })
                ->values();
        }

        if ($selectedCardView === 'student-evaluations') {
            $cardDetailTitle = 'Student Evaluations';
            $cardDetailRows = $subjectProgress
                ->filter(fn ($row) => ($row['evaluated_students'] ?? 0) > 0)
                ->map(function ($row) {
                    return [
                        'subject_name' => $row['subject_name'],
                        'instructor_name' => $row['instructor_name'],
                        'section_name' => $row['section_name'],
                        'evaluated_students' => $row['evaluated_students'],
                    ];
                })
                ->values();
        }

        if ($selectedCardView === 'evaluation-completion') {
            $cardDetailTitle = 'Evaluation Completion';
            $cardDetailRows = $subjectProgress
                ->map(function ($row) {
                    return [
                        'subject_name' => $row['subject_name'],
                        'section_name' => $row['section_name'],
                        'evaluated_students' => $row['evaluated_students'],
                        'total_students' => $row['total_students'],
                        'progress' => $row['progress'],
                    ];
                })
                ->values();
        }

        if ($selectedCardView === 'assignments-evaluated') {
            $cardDetailTitle = 'Assignments Evaluated';
            $cardDetailRows = $subjectProgress
                ->map(function ($row) use ($evaluatedAssignmentSet) {
                    $assignmentId = (int) ($row['assignment_id'] ?? 0);
                    return [
                        'subject_name' => $row['subject_name'],
                        'instructor_name' => $row['instructor_name'],
                        'section_name' => $row['section_name'],
                        'status' => $evaluatedAssignmentSet->has($assignmentId) ? 'Evaluated' : 'Pending',
                    ];
                })
                ->values();
        }

        $selectedProgressAssignment = null;
        $progressStudentRows = collect();

        if ($selectedProgressAssignmentId > 0) {
            $selectedProgressAssignment = $filteredAssignments->firstWhere('id', $selectedProgressAssignmentId);

            if ($selectedProgressAssignment) {
                $progressStudentRows = DB::table($classScheduleStudentsTable . ' as css')
                    ->join('db_students as s', 's.id', '=', 'css.user_student_id')
                    ->leftJoin('evaluations as e', function ($join) use ($selectedProgressAssignmentId) {
                        $join->on('e.student_id', '=', 'css.user_student_id')
                            ->where('e.class_schedule_id', '=', $selectedProgressAssignmentId);
                    })
                    ->where('css.class_schedule_id', $selectedProgressAssignmentId)
                    ->select(
                        's.id as student_id',
                        's.sid as student_number',
                        's.lname',
                        's.fname',
                        DB::raw('MAX(e.eval_id) as eval_id'),
                        DB::raw('MAX(e.date_submitted) as date_submitted')
                    )
                    ->groupBy('s.id', 's.sid', 's.lname', 's.fname')
                    ->orderBy('s.lname')
                    ->orderBy('s.fname')
                    ->get()
                    ->map(function ($student) {
                        $name = trim((string) ($student->lname ?? ''));
                        if (!empty($student->fname)) {
                            $name = $name !== '' ? $name . ', ' . $student->fname : $student->fname;
                        }

                        return [
                            'student_number' => $student->student_number,
                            'student_name' => $name !== '' ? $name : 'Unknown Student',
                            'status' => !empty($student->eval_id) ? 'Evaluated' : 'Not Evaluated',
                            'date_submitted' => $student->date_submitted,
                        ];
                    });
            }
        }

        return view('admin.dashboard', compact(
            'totalInstructors',
            'instructorsEvaluated',
            'studentEvaluations',
            'studentEvaluators',
            'totalExpectedEvaluations',
            'studentEvaluationCompletion',
            'totalAssignments',
            'evaluatedAssignments',
            'instructorCompletionRate',
            'assignmentCompletionRate',
            'subjectProgress',
            'areaOptions',
            'areaCode',
            'collegeOptions',
            'collegeId',
            'activePeriod',
            'evaluationDeadline',
            'selectedCardView',
            'cardDetailTitle',
            'cardDetailRows',
            'selectedProgressAssignmentId',
            'selectedProgressAssignment',
            'progressStudentRows'
        ));
    }
}

