<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Evaluation;
use App\Models\TeachingAssignment;
use App\Models\ClassSchedule; // <-- correct import
use App\Models\College;
use App\Models\Course;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;

class InstructorController extends Controller
{
    // your methods...

    public function dashboard()
{
    $instructor = Auth::user();
    
    if (!$instructor || $instructor->user_role != '2') {
        return redirect()->back()->with('error', 'Instructor profile not found.');
    }

    $activePeriodId = DB::table('system_settings')->where('key', 'active_period_id')->value('value');
    $activePeriod = $activePeriodId ? DB::table('db_periods')->find($activePeriodId) : null;

    // Get all class schedules for this instructor (main workload source)
    $schedules = ClassSchedule::where('instructor_id', $instructor->id)
        ->with(['subject', 'section']);

    if ($activePeriod) {
        $schedules->where('ay', $activePeriod->year)->where('term', $activePeriod->term);
    }

    $schedules = $schedules->get();

    // Map schedules to assignment objects (now based on class schedule ids)
    $assignments = $schedules->map(function ($schedule) {
        return TeachingAssignment::forClassSchedule($schedule);
    });

    // Count assignments and workload for dashboard stats
    $assignmentCount = $assignments->count();
    $workloadCount = $schedules->count();

    // Get all evaluations
    $evaluations = collect();
    if ($assignments->count() > 0) {
        $assignmentIds = $assignments->pluck('id')->toArray();

        $evaluationQuery = DB::table('evaluations')
            ->whereIn('class_schedule_id', $assignmentIds);

        $evaluations = $evaluationQuery
            ->join('evaluation_answers', 'evaluations.eval_id', '=', 'evaluation_answers.eval_id')
            ->join('db_students', 'evaluations.student_id', '=', 'db_students.id')
            ->select(
                'evaluations.class_schedule_id',
                'evaluations.eval_id',
                DB::raw('AVG(evaluation_answers.rating) as average_rating'),
                'db_students.fname',
                'db_students.lname',
                'evaluations.date_submitted'
            )
            ->groupBy(
                'evaluations.eval_id',
                'evaluations.class_schedule_id',
                'db_students.fname',
                'db_students.lname',
                'evaluations.date_submitted'
            )
            ->get();
    }

    $assignmentStats = collect();

    foreach ($assignments as $assignment) {
        $assignmentEvals = $evaluations->where('class_schedule_id', $assignment->id);
        $count = $assignmentEvals->count();
        $avgRating = $count > 0 ? round($assignmentEvals->avg('average_rating'), 2) : null; // null for no evaluation

        $assignmentStats->push([
            'assignment' => $assignment,
            'evaluation_count' => $count,
            'average_rating' => $avgRating,
            'evaluations' => $assignmentEvals
        ]);
    }

    return view('instructor.dashboard', compact(
        'instructor',
        'assignmentStats',
        'assignmentCount',
        'workloadCount',
        'activePeriod'
    ));
}

    public function index(Request $request)
    {
        $areaOptions = DB::table('areas')
            ->orderBy('area_name')
            ->pluck('area_name', 'area_code')
            ->toArray();

        // Persist selected area/college across pages using session.
        if ($request->has('area_code')) {
            if ($request->input('area_code')) {
                $request->session()->put('selected_area', $request->input('area_code'));
            } else {
                $request->session()->forget('selected_area');
            }
        }

        if ($request->has('college_id')) {
            if ($request->input('college_id')) {
                $request->session()->put('selected_college', $request->input('college_id'));
            } else {
                $request->session()->forget('selected_college');
            }
        }

        $selectedArea = $request->session()->get('selected_area');
        $selectedCollege = $request->session()->get('selected_college');

        $collegeOptions = College::when($selectedArea, function ($query, $area) {
                return $query->where('area_code', $area);
            })
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $instructors = User::where('user_role', '2')
            ->with('collegeRelation')
            ->when($selectedArea, function ($query, $selectedArea) {
                return $query->where('areacode', $selectedArea);
            })
            ->when($selectedCollege, function ($query, $selectedCollege) {
                return $query->where('college', $selectedCollege);
            })
            ->get();

        $activePeriodId = DB::table('system_settings')->where('key', 'active_period_id')->value('value');
        $activePeriod = $activePeriodId ? DB::table('db_periods')->find($activePeriodId) : null;

        $selectedCollegeName = null;
        if ($selectedCollege) {
            $selectedCollegeName = College::query()->where('id', (int) $selectedCollege)->value('name');
        }

        return view('instructors.index', compact(
            'instructors',
            'areaOptions',
            'collegeOptions',
            'selectedArea',
            'selectedCollege',
            'activePeriod',
            'selectedCollegeName'
        ));
    }

    public function importWithWorkload(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt,xlsx,xls',
            'school_year' => 'required|string|max:20',
            'semester' => 'required|string|max:20',
            'area_code' => 'required|string|exists:areas,area_code',
            'college_id' => 'required|integer|exists:db_colleges,id',
        ]);

        $schoolYear = trim((string) $request->input('school_year'));
        $semester = trim((string) $request->input('semester'));
        $areaCode = trim((string) $request->input('area_code'));
        $collegeId = (int) $request->input('college_id');

        $file = $request->file('import_file');
        $extension = strtolower((string) $file->getClientOriginalExtension());
        $rows = [];

        if (in_array($extension, ['xlsx', 'xls'], true)) {
            try {
                $spreadsheet = IOFactory::load($file->getRealPath());
                $rows = $spreadsheet->getActiveSheet()->toArray(null, false, false, false);
            } catch (\Throwable $e) {
                return back()->with('error', 'Unable to read Excel file. Please check the file format.');
            }
        } else {
            $handle = fopen($file->getRealPath(), 'r');
            if ($handle === false) {
                return back()->with('error', 'Unable to read import file.');
            }
            while (($data = fgetcsv($handle)) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }

        if (count($rows) === 0) {
            return back()->with('error', 'Import file is empty.');
        }

        $mapHeader = static function ($value) {
            $normalized = strtolower(trim((string) $value));
            $normalized = preg_replace('/[^a-z0-9]+/', '', $normalized);

            return match ($normalized) {
                'instructorid', 'employeeid', 'empid' => 'empid',
                'name', 'fullname', 'instructorname' => 'full_name',
                'course', 'program', 'programcode' => 'program_code',
                'yearsection', 'yearandsection', 'section' => 'year_section',
                'coursecode', 'subjectcode', 'code' => 'subject_code',
                'coursetitle', 'descriptivetitle', 'subjectname', 'nameofthecourse' => 'subject_name',
                'academicrank', 'rank' => 'academic_rank',
                'position', 'designation' => 'position',
                default => '',
            };
        };

        $firstRow = array_map(static fn ($v) => trim((string) $v), $rows[0]);
        $firstMapped = array_map($mapHeader, $firstRow);
        $hasHeader = in_array('empid', $firstMapped, true)
            || in_array('full_name', $firstMapped, true)
            || in_array('subject_code', $firstMapped, true);

        $header = $hasHeader
            ? $firstMapped
            : ['empid', 'full_name', 'program_code', 'year_section', 'subject_code', 'subject_name'];
        $dataRows = $hasHeader ? array_slice($rows, 1) : $rows;

        $createdInstructors = 0;
        $createdSchedules = 0;
        $createdSubjects = 0;
        $createdSections = 0;
        $skipped = 0;

        foreach ($dataRows as $rawRow) {
            $values = array_map(static fn ($v) => trim((string) $v), $rawRow);
            if (count(array_filter($values, static fn ($v) => $v !== '')) === 0) {
                continue;
            }

            $row = [];
            foreach ($header as $index => $key) {
                if ($key !== '') {
                    $row[$key] = $values[$index] ?? null;
                }
            }

            $empid = trim((string) ($row['empid'] ?? ''));
            $fullName = trim((string) ($row['full_name'] ?? ''));
            $programCode = trim((string) ($row['program_code'] ?? ''));
            $yearSection = trim((string) ($row['year_section'] ?? ''));
            $subjectCode = trim((string) ($row['subject_code'] ?? ''));
            $subjectName = trim((string) ($row['subject_name'] ?? ''));
            $academicRank = trim((string) ($row['academic_rank'] ?? ''));
            $position = trim((string) ($row['position'] ?? ''));

            if ($empid === '' || $fullName === '' || $yearSection === '' || $subjectCode === '' || $subjectName === '') {
                $skipped++;
                continue;
            }

            $existingUserWithEmpid = User::query()->where('empid', $empid)->first();
            if ($existingUserWithEmpid && (string) $existingUserWithEmpid->user_role !== '2') {
                $skipped++;
                continue;
            }

            $nameParts = $this->splitInstructorFullName($fullName);

            $instructor = User::query()
                ->where('empid', $empid)
                ->where('user_role', '2')
                ->first();

            if (!$instructor) {
                $instructor = User::create([
                    'empid' => $empid,
                    'fname' => $nameParts['fname'],
                    'mname' => $nameParts['mname'],
                    'lname' => $nameParts['lname'],
                    'fullname' => $nameParts['fullname'],
                    'academic_rank' => $academicRank !== '' ? $academicRank : null,
                    'position' => $position !== '' ? $position : null,
                    'user_role' => '2',
                    'college' => $collegeId,
                    'areacode' => $areaCode,
                ]);
                $createdInstructors++;
            } else {
                $instructor->update([
                    'fname' => $instructor->fname ?: $nameParts['fname'],
                    'mname' => $instructor->mname ?: $nameParts['mname'],
                    'lname' => $instructor->lname ?: $nameParts['lname'],
                    'fullname' => $instructor->fullname ?: $nameParts['fullname'],
                    'academic_rank' => $academicRank !== '' ? $academicRank : $instructor->academic_rank,
                    'position' => $position !== '' ? $position : $instructor->position,
                    'college' => $instructor->college ?: $collegeId,
                    'areacode' => $instructor->areacode ?: $areaCode,
                ]);
            }

            $subject = Subject::query()->where('code', $subjectCode)->first();
            if (!$subject) {
                $subject = Subject::create([
                    'area_code' => $areaCode,
                    'college_id' => $collegeId,
                    'code' => $subjectCode,
                    'course_no' => $subjectCode,
                    'name' => $subjectName,
                ]);
                $createdSubjects++;
            }

            preg_match('/^(\d+)/', $yearSection, $matches);
            $yearLevelDigits = $matches[1] ?? '';
            $yearLevel = $yearLevelDigits !== '' ? $yearLevelDigits : '1';

            $section = Section::query()->where('name', $yearSection)->first();
            if (!$section) {
                $section = Section::create([
                    'name' => $yearSection,
                    'year' => is_numeric($yearLevel) ? (int) $yearLevel : 1,
                    'area_code' => $areaCode,
                    'college_id' => $collegeId,
                ]);
                $createdSections++;
            }

            $resolvedProgramId = null;
            if ($programCode !== '') {
                $resolvedProgramId = Course::query()
                    ->where('area_code', $areaCode)
                    ->where('college_id', $collegeId)
                    ->where(function ($query) use ($programCode) {
                        $query->where('course_program', $programCode)
                            ->orWhere('code', $programCode)
                            ->orWhere('name', $programCode);
                    })
                    ->value('id');
            }

            $duplicate = ClassSchedule::query()
                ->where('instructor_id', $instructor->id)
                ->where('subject_id', $subject->id)
                ->where('section_id', $section->id)
                ->where('ay', $schoolYear)
                ->where('term', $semester)
                ->exists();

            if ($duplicate) {
                $skipped++;
                continue;
            }

            ClassSchedule::create([
                'instructor_id' => $instructor->id,
                'subject_id' => $subject->id,
                'section_id' => $section->id,
                'ay' => $schoolYear,
                'term' => $semester,
                'area_code' => $areaCode,
                'college_id' => $collegeId,
                'course_id' => $resolvedProgramId ?: null,
                'year_level' => (string) $yearLevel,
            ]);

            $createdSchedules++;
        }

        return redirect()->route('instructors.index', [
            'area_code' => $areaCode,
            'college_id' => $collegeId,
        ])->with('success', "Import complete: {$createdInstructors} instructor(s), {$createdSubjects} subject(s), {$createdSections} section(s), {$createdSchedules} workload row(s) created; {$skipped} skipped.");
    }

    private function splitInstructorFullName(string $rawName): array
    {
        $name = trim(preg_replace('/\s+/', ' ', $rawName));
        $name = trim($name);

        $lname = '';
        $fname = '';
        $mname = '';

        if (str_contains($name, ',')) {
            [$lastPart, $restPart] = array_map('trim', explode(',', $name, 2));
            $lname = $lastPart;

            $tokens = preg_split('/\s+/', $restPart);
            $tokens = array_values(array_filter($tokens, static fn ($t) => $t !== ''));

            if (count($tokens) > 0) {
                $fname = array_shift($tokens);
            }

            if (count($tokens) > 0) {
                $mname = implode(' ', $tokens);
            }
        } else {
            $tokens = preg_split('/\s+/', $name);
            $tokens = array_values(array_filter($tokens, static fn ($t) => $t !== ''));

            if (count($tokens) === 1) {
                $fname = $tokens[0];
            } elseif (count($tokens) > 1) {
                $lname = array_pop($tokens);
                $fname = array_shift($tokens);
                if (count($tokens) > 0) {
                    $mname = implode(' ', $tokens);
                }
            }
        }

        $lname = trim($lname, " \t\n\r\0\x0B,");
        $fname = trim($fname, " \t\n\r\0\x0B,");
        $mname = trim($mname, " \t\n\r\0\x0B,");

        $fullname = trim($fname . ' ' . ($mname !== '' ? $mname . ' ' : '') . $lname);

        return [
            'lname' => $lname,
            'fname' => $fname,
            'mname' => $mname,
            'fullname' => $fullname,
        ];
    }

    /**
     * Show workload (assignments) for the current authenticated instructor.
     */
    public function instructorWorkload($id)
{
    $instructor = User::where('user_role', '2')->findOrFail($id);

    $schedules = ClassSchedule::with(['subject', 'section'])
        ->where('instructor_id', $instructor->id)
        ->get();

    $assignments = $schedules->map(function ($schedule) {
        return TeachingAssignment::forClassSchedule($schedule);
    });

    return view('instructors.workload', compact('instructor', 'assignments'));
}
public function workload(Request $request)
{
    $instructor = Auth::user();

    if (!$instructor || $instructor->user_role !== '2') {
        return redirect()->back()->with('error', 'Instructor access required.');
    }

    $query = ClassSchedule::with(['subject', 'section', 'instructor'])
        ->where('instructor_id', $instructor->id);

    // Combined filter: term + ay
    $selectedFilter = $request->query('filter');
    if ($selectedFilter) {
        [$term, $ay] = explode(' ', $selectedFilter, 2);
        $query->where('term', $term)->where('ay', $ay);
    }

    $schedules = $query->get();

    // Attach evaluation result data for each schedule (legacy + new id compatibility)
    $schedules = $schedules->map(function ($schedule) {
        $schedule->evaluation_result = \App\Models\EvaluationResult::findByClassScheduleId($schedule->id);
        return $schedule;
    });

    // Get combined filter options for select dropdown
    $availableFilters = ClassSchedule::where('instructor_id', $instructor->id)
        ->selectRaw("TRIM(CONCAT(term, ' ', ay)) AS combined")
        ->whereNotNull('term')
        ->whereNotNull('ay')
        ->distinct()
        ->pluck('combined')
        ->filter()
        ->sort()
        ->values();

    return view('instructor.workload', compact(
        'instructor',
        'schedules',
        'availableFilters',
        'selectedFilter'
    ));
}
    /**
     * Show evaluation reports for the current authenticated instructor.
     */
    public function reports()
    {
        $instructor = Auth::user();
        
        if (!$instructor || $instructor->user_role !== '2') {
            return redirect()->back()->with('error', 'Instructor access required.');
        }

        $activePeriodId = DB::table('system_settings')->where('key', 'active_period_id')->value('value');
        $activePeriod = $activePeriodId ? DB::table('db_periods')->find($activePeriodId) : null;

        // Get all class schedules for this instructor (workload source)
        $schedules = ClassSchedule::where('instructor_id', $instructor->id)
            ->with(['subject', 'section'])
            ->withCount('students');

        if ($activePeriod) {
            $schedules->where('ay', $activePeriod->year)->where('term', $activePeriod->term);
        }

        $schedules = $schedules->get();

        // Ensure teaching assignment entries exist for reporting and evaluations
        $assignments = $schedules->map(function ($schedule) {
            $assignment = TeachingAssignment::forClassSchedule($schedule);
            // Preserve the student count from eager load
            $assignment->students_count = $schedule->students_count;
            return $assignment;
        });

        // Get all evaluations for this instructor's assignments with answers
        $evaluations = collect();
        if ($assignments->count() > 0) {
            $assignmentIds = $assignments->pluck('id')->toArray();

            $evaluationQuery = DB::table('evaluations')
                ->join('db_class_schedules', 'evaluations.class_schedule_id', '=', 'db_class_schedules.id')
                ->whereIn('evaluations.class_schedule_id', $assignmentIds);

            // Filter by active period (year and semester)
            if ($activePeriod) {
                $evaluationQuery->where('db_class_schedules.ay', $activePeriod->year)
                    ->where('db_class_schedules.term', $activePeriod->term);
            }

            $evaluations = $evaluationQuery
                ->join('evaluation_answers', 'evaluations.eval_id', '=', 'evaluation_answers.eval_id')
                ->join('db_students', 'evaluations.student_id', '=', 'db_students.id')
                ->select(
                    'evaluations.class_schedule_id',
                    'evaluations.eval_id',
                    DB::raw('AVG(evaluation_answers.rating) as average_rating'),
                    'db_students.fname',
                    'db_students.lname',
                    'evaluations.date_submitted'
                )
                ->groupBy('evaluations.eval_id', 'evaluations.class_schedule_id', 'db_students.fname', 'db_students.lname', 'evaluations.date_submitted')
                ->get();
        }

        // Calculate summary statistics per assignment
        $assignmentStats = [];

foreach ($assignments as $assignment) {
    $evaluationResult = \App\Models\EvaluationResult::findByClassScheduleId($assignment->id);

    // fallback to raw eval rows if no precomputed result exists
    $assignmentEvals = $evaluations->where('class_schedule_id', $assignment->id);
    $count = $evaluationResult ? $evaluationResult->total_evaluations : $assignmentEvals->count();
    $avgRating = $evaluationResult ? (float) $evaluationResult->overall_average : ($count > 0 ? round($assignmentEvals->avg('average_rating'), 2) : 0);

    $assignmentStats[] = [
        'assignment' => $assignment,
        'evaluation_count' => $count,
        'average_rating' => $avgRating,
        'evaluations' => $assignmentEvals,
        'evaluation_result' => $evaluationResult,
    ];
}

/* Convert array → collection */
$assignmentStats = collect($assignmentStats);

// Build class summary for the report table.
$classes = collect();
foreach ($assignmentStats as $stats) {
    $assignment = $stats['assignment'];

    // Use actual enrollment count, fallback to database fields, then evaluation count.
    $enrolledCount = $assignment->students_count ?? 0;
    $classSize = $enrolledCount ?: ($assignment->class_size ?: $assignment->class_max_size ?: $assignment->class_size_ireg ?: $stats['evaluation_count']);

    $average100 = round($stats['average_rating'] * 20, 1);
    $weighted = round($classSize * $average100, 1);

    $classes->push((object)[
        'course_code' => $assignment->subject->code ?? $assignment->subject->name ?? 'Unknown',
        'section' => $assignment->section->name ?? 'Unknown Section',
        'students' => $classSize,
        'evaluated_students' => $stats['evaluation_count'],
        'average' => $average100,
        'weighted' => $weighted,
    ]);
}

$semester = $assignments->count() > 0 ? ($assignments->first()->term . ' / ' . $assignments->first()->ay) : 'N/A';

return view('instructor.reports', compact('instructor', 'assignmentStats', 'classes', 'semester'));
    }

    /**
     * Show workload (assignments) for a given instructor (admin view).
     */
    public function workloadAdmin(Request $request, $id)
    {
        $instructor = User::where('user_role', '2')->findOrFail($id);

        $query = ClassSchedule::with(['subject', 'section', 'instructor'])
            ->where('instructor_id', $instructor->id);

        // Apply filters
        if ($request->filled('term')) {
            $query->where('term', $request->term);
        }

        if ($request->filled('ay')) {
            $query->where('ay', $request->ay);
        }

        $schedules = $query->get();

        // Get unique values for filter dropdowns
        $availableTerms = ClassSchedule::where('instructor_id', $id)
            ->distinct()
            ->pluck('term')
            ->filter()
            ->sort()
            ->values();

        $availableAY = ClassSchedule::where('instructor_id', $id)
            ->distinct()
            ->pluck('ay')
            ->filter()
            ->sort()
            ->values();

        return view('instructors.workload', compact(
            'instructor',
            'schedules',
            'availableTerms',
            'availableAY'
        ));
    }

    public function create(Request $request)
    {
        $selectedArea = $request->query('area_code');
        $selectedCollege = $request->query('college_id');

        if (!$selectedArea || !$selectedCollege) {
            return redirect()->route('instructors.index')->with('error', 'Please set area and college first.');
        }

        $collegeOptions = College::when($selectedArea, function ($query, $area) {
                return $query->where('area_code', $area);
            })
            ->orderBy('name')
            ->get();

        $academicRankOptions = User::query()
            ->where('user_role', '2')
            ->whereNotNull('academic_rank')
            ->where('academic_rank', '!=', '')
            ->distinct()
            ->orderBy('academic_rank')
            ->pluck('academic_rank')
            ->values();

        $positionOptions = User::query()
            ->where('user_role', '2')
            ->whereNotNull('position')
            ->where('position', '!=', '')
            ->distinct()
            ->orderBy('position')
            ->pluck('position')
            ->values();

        $selectedAreaName = DB::table('areas')
            ->where('area_code', $selectedArea)
            ->value('area_name') ?: $selectedArea;

        return view('instructors.create', compact(
            'selectedArea',
            'selectedAreaName',
            'selectedCollege',
            'collegeOptions',
            'academicRankOptions',
            'positionOptions'
        ));
    }

    public function store(Request $request)
    {
        // Validate fields
        $request->validate([
            'empid' => 'required|string|max:255|unique:users,empid',
            'fname' => 'required|string|max:255',
            'mname' => 'nullable|string|max:255',
            'lname' => 'required|string|max:255',
            'academic_rank' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'area_code' => 'required|string',
            'college' => 'required|exists:db_colleges,id',
        ]);

        User::create([
            'empid' => $request->empid,
            'fname' => $request->fname,
            'mname' => $request->mname,
            'lname' => $request->lname,
            'fullname' => trim($request->fname . ' ' . ($request->mname ? $request->mname . ' ' : '') . $request->lname),
            'user_role' => '2',
            'academic_rank' => $request->academic_rank,
            'position' => $request->position,
            'college' => $request->college,
            'areacode' => $request->area_code,
        ]);

        return redirect()->route('instructors.index')->with('success', 'Instructor created successfully.');
    }

    public function edit($id)
    {
        $instructor = User::where('user_role', '2')->with('collegeRelation')->findOrFail($id);

        $academicRankOptions = User::query()
            ->where('user_role', '2')
            ->whereNotNull('academic_rank')
            ->where('academic_rank', '!=', '')
            ->distinct()
            ->orderBy('academic_rank')
            ->pluck('academic_rank')
            ->values();

        $positionOptions = User::query()
            ->where('user_role', '2')
            ->whereNotNull('position')
            ->where('position', '!=', '')
            ->distinct()
            ->orderBy('position')
            ->pluck('position')
            ->values();

        return view('instructors.edit', compact('instructor', 'academicRankOptions', 'positionOptions'));
    }

    public function update(Request $request, $id)
    {
        $instructor = User::where('user_role', '2')->findOrFail($id);

        $request->validate([
            'empid' => 'required|string|max:255|unique:users,empid,' . $id,
            'fname' => 'required|string|max:255',
            'mname' => 'nullable|string|max:255',
            'lname' => 'required|string|max:255',
            'academic_rank' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'college' => 'nullable|string|max:255',
        ]);

        $instructor->update([
            'empid' => $request->empid,
            'fname' => $request->fname,
            'mname' => $request->mname,
            'lname' => $request->lname,
            'fullname' => trim($request->fname . ' ' . ($request->mname ? $request->mname . ' ' : '') . $request->lname),
            'academic_rank' => $request->academic_rank,
            'position' => $request->position,
            'college' => $request->college,
        ]);

        return redirect()->route('instructors.index')->with('success', 'Instructor updated successfully.');
    }

    public function destroy($id)
    {
        $instructor = User::where('user_role', '2')->findOrFail($id);
        $instructor->delete();
        return redirect()->route('instructors.index');
    }
}
