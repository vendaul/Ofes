<?php

namespace App\Http\Controllers;

use App\Models\TeachingAssignment;
use App\Models\User;
use App\Models\Subject;
use App\Models\Section;
use App\Models\ClassSchedule;
use App\Models\Course;
use App\Models\Curriculum;
use App\Models\CurriculumSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TeachingAssignmentController extends Controller
{
    private const DUPLICATE_ASSIGNMENT_MESSAGE = 'This subject is already assigned to someone in that section.';

    private function duplicateAssignmentResponse(Request $request)
    {
        return redirect()->back()
            ->withInput()
            ->withErrors(['duplicate' => self::DUPLICATE_ASSIGNMENT_MESSAGE]);
    }

    // Admin: view specific instructor workload
public function instructorWorkload(Request $request, $id)
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

    $instructorSections = $schedules
        ->pluck('section')
        ->filter()
        ->unique('id')
        ->sortBy('name')
        ->values();

    // Get unique values for filter dropdowns
    $availableTerms = ClassSchedule::where('instructor_id', $instructor->id)
        ->distinct()
        ->pluck('term')
        ->filter()
        ->sort()
        ->values();

    $availableAy = ClassSchedule::where('instructor_id', $instructor->id)
        ->distinct()
        ->pluck('ay')
        ->filter()
        ->sort()
        ->values();

    $areaOptions = DB::table('areas')
        ->orderBy('area_name')
        ->pluck('area_name', 'area_code')
        ->toArray();

    $allColleges = DB::table('db_colleges')
        ->select('id', 'name', 'area_code')
        ->orderBy('name')
        ->get();

    $activeCurriculumId = (int) session('active_curriculum_id', 0);
    $activeProgramId = (int) session('active_program_id', 0);
    $sessionAreaCode = session('selected_area');
    $sessionCollegeId = session('selected_college');

    $defaultAreaCode = $sessionAreaCode ?: ($instructor->areacode ?: null);
    $defaultCollegeId = $sessionCollegeId ?: ($instructor->college ?: null);
    $defaultProgramId = $activeProgramId > 0 ? $activeProgramId : null;
    $defaultCurriculumId = $activeCurriculumId > 0 ? $activeCurriculumId : null;

    if ($activeCurriculumId > 0) {
        $activeCurriculum = Curriculum::query()
            ->select('id', 'course_id', 'area_code', 'college_id')
            ->find($activeCurriculumId);

        if ($activeCurriculum) {
            $defaultAreaCode = $activeCurriculum->area_code ?: $defaultAreaCode;
            $defaultCollegeId = $activeCurriculum->college_id ?: $defaultCollegeId;
            $defaultProgramId = $activeCurriculum->course_id ?: $defaultProgramId;
            $defaultCurriculumId = $activeCurriculum->id;
        }
    }

    $programs = Course::query()
        ->select('id', 'course_program', 'code', 'name', 'area_code', 'college_id')
        ->when($defaultAreaCode, function ($query, $defaultAreaCode) {
            return $query->where('area_code', $defaultAreaCode);
        })
        ->when($defaultCollegeId, function ($query, $defaultCollegeId) {
            return $query->where('college_id', $defaultCollegeId);
        })
        ->orderBy('course_program')
        ->orderBy('name')
        ->get();

    $curriculums = Curriculum::query()
        ->select('id', 'course_id', 'code', 'desc', 'area_code', 'college_id')
        ->with('course')
        ->when($defaultAreaCode, function ($query, $defaultAreaCode) {
            return $query->where('area_code', $defaultAreaCode);
        })
        ->when($defaultCollegeId, function ($query, $defaultCollegeId) {
            return $query->where('college_id', $defaultCollegeId);
        })
        ->orderBy('code')
        ->get();

    $activePeriodId = DB::table('system_settings')->where('key', 'active_period_id')->value('value');
    $activePeriod = $activePeriodId ? DB::table('db_periods')->find($activePeriodId) : null;

    return view('instructors.workload', compact(
        'instructor',
        'schedules',
        'instructorSections',
        'availableTerms',
        'availableAy',
        'areaOptions',
        'allColleges',
        'defaultAreaCode',
        'defaultCollegeId',
        'defaultProgramId',
        'defaultCurriculumId',
        'programs',
        'curriculums',
        'activePeriod'
    ));
}

public function importWorkload(Request $request, $id)
{
    $instructor = User::where('user_role', '2')->findOrFail($id);

    $request->validate([
        'import_file' => 'required|file|mimes:csv,txt,xlsx,xls',
        'school_year' => 'required|string|max:20',
        'semester' => 'required|string|max:20',
        'area_code' => 'required|string|exists:areas,area_code',
        'college_id' => 'required|integer|exists:db_colleges,id',
        'program_id' => 'nullable|integer|exists:db_courses,id',
        'curriculum_id' => 'nullable|integer|exists:db_curriculums,id',
    ]);

    $schoolYear = trim((string) $request->input('school_year'));
    $semester = trim((string) $request->input('semester'));
    $areaCode = trim((string) $request->input('area_code'));
    $collegeId = (int) $request->input('college_id');
    $defaultProgramId = (int) $request->input('program_id', 0);
    $defaultCurriculumId = (int) $request->input('curriculum_id', 0);

    if ($defaultProgramId > 0) {
        $programExists = Course::query()
            ->where('id', $defaultProgramId)
            ->where('area_code', $areaCode)
            ->where('college_id', $collegeId)
            ->exists();

        if (!$programExists) {
            return back()->with('error', 'Selected Program is not aligned with Area and College.');
        }
    }

    if ($defaultCurriculumId > 0) {
        $curriculumQuery = Curriculum::query()
            ->where('id', $defaultCurriculumId)
            ->where('area_code', $areaCode)
            ->where('college_id', $collegeId);

        if ($defaultProgramId > 0) {
            $curriculumQuery->where('course_id', $defaultProgramId);
        }

        if (!$curriculumQuery->exists()) {
            return back()->with('error', 'Selected Curriculum is not aligned with Area, College, and Program.');
        }
    }

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
            'course', 'program', 'programcode' => 'program_code',
            'yearsection', 'section', 'yearandsection' => 'year_section',
            'coursecode', 'subjectcode', 'code' => 'subject_code',
            'coursetitle', 'descriptivetitle', 'subjectname', 'name' => 'subject_name',
            default => '',
        };
    };

    $firstRow = array_map(static fn ($v) => trim((string) $v), $rows[0]);
    $firstMapped = array_map($mapHeader, $firstRow);
    $hasHeader = in_array('subject_code', $firstMapped, true)
        || in_array('subject_name', $firstMapped, true)
        || in_array('year_section', $firstMapped, true);

    $header = $hasHeader ? $firstMapped : ['program_code', 'year_section', 'subject_code', 'subject_name'];
    $dataRows = $hasHeader ? array_slice($rows, 1) : $rows;

    $created = 0;
    $skipped = 0;
    $mapped = 0;

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

        $programCode = trim((string) ($row['program_code'] ?? ''));
        $yearSection = trim((string) ($row['year_section'] ?? ''));
        $subjectCode = trim((string) ($row['subject_code'] ?? ''));
        $subjectName = trim((string) ($row['subject_name'] ?? ''));

        if ($subjectCode === '' || $subjectName === '' || $yearSection === '') {
            $skipped++;
            continue;
        }

        preg_match('/^(\d+)/', $yearSection, $matches);
        $rowYearLevel = $matches[1] ?? '';
        $yearLevel = $rowYearLevel;

        if ($yearLevel === '') {
            $skipped++;
            continue;
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
        }

        $section = Section::query()->firstOrCreate(
            ['name' => $yearSection],
            ['year' => is_numeric($yearLevel) ? (int) $yearLevel : 1]
        );

        $resolvedProgramId = $defaultProgramId;
        if ($programCode !== '') {
            $resolvedProgramId = (int) (Course::query()
                ->where('area_code', $areaCode)
                ->where('college_id', $collegeId)
                ->where(function ($query) use ($programCode) {
                    $query->where('course_program', $programCode)
                        ->orWhere('code', $programCode)
                        ->orWhere('name', $programCode);
                })
                ->value('id') ?: $defaultProgramId);
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
            'course_id' => $resolvedProgramId > 0 ? $resolvedProgramId : null,
            'year_level' => (string) $yearLevel,
        ]);
        $created++;

        if ($defaultCurriculumId > 0) {
            CurriculumSubject::updateOrCreate(
                ['s_code' => $subject->id],
                [
                    'curriculum_id' => $defaultCurriculumId,
                    's_year' => (string) $yearLevel,
                    's_term' => $semester,
                    's_units' => $subject->units,
                ]
            );
            $mapped++;
        }
    }

    return redirect()->route('instructors.workload', $instructor->id)
        ->with('success', "Workload import complete: {$created} created, {$mapped} mapped, {$skipped} skipped.");
}

// Instructor: view their own workload
public function workload()
{
    $instructor = auth()->user();

    if (!$instructor || $instructor->user_role !== 'instructor') {
        return redirect()->back()->with('error', 'Instructor access required.');
    }

    $schedules = ClassSchedule::with(['subject', 'section'])
        ->where('instructor_id', $instructor->id)
        ->get();

    return view('instructor.workload', compact('instructor', 'schedules'));
}
    public function index()
    {
        // assignments are now managed solely through the instructors UI; if
        // someone navigates here, send them back with a message.
        return redirect()->route('instructors.index')
                         ->with('info', 'Teaching assignments are accessed via the Instructors menu.');
    }

    public function create(Request $request)
    {
        $sections = Section::all();

        $periods = DB::table('db_periods')
            ->select('year', 'term')
            ->distinct()
            ->orderBy('year', 'desc')
            ->orderBy('term')
            ->get();

        $schoolYears = $periods->pluck('year')->unique();
        $semesters = $periods->pluck('term')->unique();

        $activePeriodId = DB::table('system_settings')->where('key', 'active_period_id')->value('value');
        $activePeriod = $activePeriodId ? DB::table('db_periods')->find($activePeriodId) : null;

        $yearLevels = DB::table('db_curriculum_subjects')
            ->select('s_year')
            ->distinct()
            ->orderBy('s_year')
            ->pluck('s_year')
            ->filter()
            ->values();

        $selectedYearLevel = $request->query('year_level');
        $selectedProgramId = $request->query('program_id');
        $selectedCurriculumId = $request->query('curriculum_id');
        $selectedCollegeId = $request->query('college_id');
        $selectedAreaId = $request->query('area_code');

        $activeCurriculumId = (int) $request->session()->get('active_curriculum_id', 0);
        $activeProgramId = (int) $request->session()->get('active_program_id', 0);

        if ($activeCurriculumId > 0) {
            $activeCurriculum = Curriculum::query()
                ->select('id', 'course_id', 'area_code', 'college_id')
                ->find($activeCurriculumId);

            if ($activeCurriculum) {
                $selectedAreaId = $activeCurriculum->area_code ?: $selectedAreaId;
                $selectedCollegeId = $activeCurriculum->college_id ?: $selectedCollegeId;
                $selectedProgramId = $activeCurriculum->course_id ?: ($activeProgramId ?: $selectedProgramId);
                $selectedCurriculumId = $activeCurriculum->id;
            } else {
                $request->session()->forget(['active_curriculum_id', 'active_program_id']);
            }
        } elseif ($activeProgramId > 0) {
            $selectedProgramId = $activeProgramId;
        }

        // If opened from an instructor-specific Add Assignment link, inherit
        // area/college so program options remain scoped correctly.
        $selectedInstructorId = $request->query('instructor_id');
        if ((!$selectedCollegeId || !$selectedAreaId) && $selectedInstructorId) {
            $selectedInstructor = User::find($selectedInstructorId);
            if ($selectedInstructor) {
                $selectedCollegeId = $selectedCollegeId ?: $selectedInstructor->college;
                $selectedAreaId = $selectedAreaId ?: $selectedInstructor->areacode;
            }
        }

        $instructors = User::where('user_role', '2')
            ->when($selectedCollegeId, function ($query, $collegeId) {
                return $query->where('college', $collegeId);
            })
            ->get();

        $programs = Course::when($selectedAreaId, function ($query, $areaCode) {
                return $query->where('area_code', $areaCode);
            })
            ->when($selectedCollegeId, function ($query, $collegeId) {
                return $query->where('college_id', $collegeId);
            })
            ->orderBy('course_program')
            ->get();

        $allCurriculums = Curriculum::query()
            ->when($selectedAreaId, function ($query, $areaCode) {
                return $query->where('area_code', $areaCode);
            })
            ->when($selectedCollegeId, function ($query, $collegeId) {
                return $query->where('college_id', $collegeId);
            })
            ->get();

        $curriculums = $allCurriculums->when($selectedProgramId, function ($query) use ($selectedProgramId) {
            return $query->where('course_id', $selectedProgramId);
        });

        $subjectQuery = Subject::query();

        $subjectQuery->whereHas('curriculumSubjects', function ($query) use ($activePeriod, $selectedYearLevel, $selectedProgramId, $selectedCurriculumId) {
            if ($activePeriod) {
                // Match term (semester) for active period; year_level is handled by selectedYearLevel.
                $query->where('s_term', $activePeriod->term);
            }

            if ($selectedYearLevel) {
                $query->where('s_year', $selectedYearLevel);
            }

            if ($selectedCurriculumId) {
                $query->where('curriculum_id', $selectedCurriculumId);
            }

            if ($selectedProgramId) {
                $query->whereHas('curriculum', function ($q2) use ($selectedProgramId) {
                    $q2->where('course_id', $selectedProgramId);
                });
            }
        });

        $subjects = $subjectQuery->get();

        if ($subjects->isEmpty() && !$selectedYearLevel && !$selectedProgramId && !$selectedCurriculumId) {
            $subjects = Subject::all();
        }

        $subjectOptions = $subjects->map(function ($s) {
            $primaryCurriculum = $s->curriculumSubjects->first()?->curriculum;

            return [
                'value' => $s->code . ' - ' . $s->name,
                'year' => $s->subject_year ?? '',
                'program' => optional(optional($primaryCurriculum)->course)->id ?? '',
                'curriculum' => optional($primaryCurriculum)->id ?? '',
            ];
        })->values();

        $programOptions = $programs->map(function ($p) {
            return [
                'id' => $p->id,
                'label' => ($p->course_program ?? $p->name ?? $p->id) . ' (' . ($p->code ?? '') . ') - ' . ($p->name ?? ''),
            ];
        })->values();

        $curriculumOptions = $allCurriculums->map(function ($c) {
            return [
                'id' => $c->id,
                'label' => $c->code ?? $c->name ?? $c->id,
                'course_id' => $c->course_id,
            ];
        })->values();

        return view('assignments.create', compact(
            'instructors',
            'subjects',
            'sections',
            'schoolYears',
            'semesters',
            'activePeriod',
            'yearLevels',
            'selectedYearLevel',
            'programs',
            'curriculums',
            'allCurriculums',
            'selectedProgramId',
            'selectedCurriculumId',
            'selectedCollegeId',
            'selectedAreaId',
            'subjectOptions',
            'programOptions',
            'curriculumOptions'
        ));
    }

    public function store(Request $request)
    {
        $activePeriodId = DB::table('system_settings')->where('key', 'active_period_id')->value('value');
        $activePeriod = $activePeriodId ? DB::table('db_periods')->find($activePeriodId) : null;

        if ($activePeriod) {
            $request->merge([
                'school_year' => $activePeriod->year,
                'semester' => $activePeriod->term,
            ]);
        }

        // validate input to avoid orphaned assignments which can later
        // cause "attempt to read property first_name on null" errors when
        // rendering the list
        $request->validate([
            'instructor_id' => 'required|exists:users,id',
            'subject_name'  => 'required|string|max:255',
            'section_name'  => 'required|string|max:255',
            'program_id'    => 'nullable|exists:db_courses,id',
            'curriculum_id' => 'nullable|exists:db_curriculums,id',
            'college_id'    => 'nullable|exists:db_colleges,id',
            'area_code'     => 'nullable|string',
            'year_level'    => 'required|string|max:10',
            'school_year'   => 'required|string|max:20',
            'semester'      => 'required|string|max:10',
        ]);

        // Handle subject - find existing or create new
        $subjectName = trim($request->subject_name);
        $subject = Subject::whereRaw("CONCAT(code, ' - ', name) = ?", [$subjectName])
                         ->orWhere('code', $subjectName)
                         ->orWhere('name', $subjectName)
                         ->first();

        if (!$subject) {
            // Try to parse "CODE - NAME" format
            if (strpos($subjectName, ' - ') !== false) {
                [$code, $name] = explode(' - ', $subjectName, 2);
                $subject = Subject::create([
                    'code' => trim($code),
                    'name' => trim($name),
                    'course_no' => '',
                    'units' => 0,
                ]);
            } else {
                // Create with name as both code and name
                $subject = Subject::create([
                    'code' => $subjectName,
                    'name' => $subjectName,
                    'course_no' => '',
                    'units' => 0,
                ]);
            }
        }

        // Enforce active-period curriculum membership for the chosen subject
        if ($activePeriod) {
            $subjectIsActive = Subject::where('id', $subject->id)
                ->whereHas('curriculumSubjects', function ($query) use ($activePeriod) {
                    // enforce active term only; year-level gets checked separately
                    $query->where('s_term', $activePeriod->term);
                })->exists();

            if (!$subjectIsActive) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['subject_name' => 'Subject does not belong to the active curriculum year/semester.']);
            }
        }

        // Enforce selected year level for chosen subject
        $selectedYearLevel = $request->input('year_level');
        if ($selectedYearLevel) {
            $subjectMatchesYear = $subject->curriculumSubjects()
                ->where('s_year', $selectedYearLevel)
                ->exists();

            if (!$subjectMatchesYear) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['year_level' => 'Subject does not match the selected year level.']);
            }
        }

        // Handle section - find existing or create new
        $sectionName = trim($request->section_name);
        $section = Section::where('name', $sectionName)->first();

        if (!$section) {
            $section = Section::create([
                'name' => $sectionName,
                'year' => 1, // Default year level
            ]);
        }

        // Prevent duplicate assignments for the same instructor/subject/section (same term)
        $exists = ClassSchedule::where('instructor_id', $request->instructor_id)
            ->where('subject_id', $subject->id)
            ->where('section_id', $section->id)
            ->where('ay', $request->school_year)
            ->where('term', $request->semester)
            ->exists();

        if ($exists) {
            return $this->duplicateAssignmentResponse($request);
        }

        $instructorId = $request->input('instructor_id');

        // Save data into db_class_schedules as requested
        $instructor = User::find($instructorId);
        $scheduleInstructorId = $instructor ? $instructor->id : $instructorId;

        ClassSchedule::firstOrCreate(
            [
                'subject_id' => $subject->id,
                'section_id' => $section->id,
                'instructor_id' => $scheduleInstructorId,
                'ay' => $request->school_year,
                'term' => $request->semester,
            ],
            [
                'subject_id' => $subject->id,
                'section_id' => $section->id,
                'instructor_id' => $scheduleInstructorId,
                'ay' => $request->school_year,
                'term' => $request->semester,
                'college_id' => $request->input('college_id'),
                'area_code' => $request->input('area_code'),
                'year_level' => $request->year_level,
            ]
        );

        // redirect to the selected instructor's workload so user sees the
        // assignment they just added; falls back to instructors index if
        // instructor_id missing for some reason.
        if ($instructorId) {
            return redirect()->route('instructors.workload', $instructorId)
                             ->with('success', 'Workload added successfully.');
        }

        return redirect()->route('instructors.index')
                         ->with('success', 'Workload added successfully.');
    }

    public function edit(TeachingAssignment $assignment)
    {
        $instructors = User::where('user_role', '2')->get();
        $sections = Section::all();

        $periods = DB::table('db_periods')
            ->select('year', 'term')
            ->distinct()
            ->orderBy('year', 'desc')
            ->orderBy('term')
            ->get();

        $schoolYears = $periods->pluck('year')->unique();
        $semesters = $periods->pluck('term')->unique();

        $activePeriodId = DB::table('system_settings')->where('key', 'active_period_id')->value('value');
        $activePeriod = $activePeriodId ? DB::table('db_periods')->find($activePeriodId) : null;

        if ($activePeriod) {
            $subjects = Subject::whereHas('curriculumSubjects', function ($query) use ($activePeriod) {
                $query->where('s_term', $activePeriod->term);
            })->get();
        } else {
            $subjects = Subject::all();
        }

        $schoolYears = $periods->pluck('year')->unique();
        $semesters = $periods->pluck('term')->unique();

        $activePeriodId = DB::table('system_settings')->where('key', 'active_period_id')->value('value');
        $activePeriod = $activePeriodId ? DB::table('db_periods')->find($activePeriodId) : null;

        return view('assignments.edit', [
            'assignment' => $assignment,
            'instructors' => $instructors,
            'subjects' => $subjects,
            'sections' => $sections,
            'schoolYears' => $schoolYears,
            'semesters' => $semesters,
            'activePeriod' => $activePeriod,
        ]);
    }

    public function update(Request $request, TeachingAssignment $assignment)
    {
        $activePeriodId = DB::table('system_settings')->where('key', 'active_period_id')->value('value');
        $activePeriod = $activePeriodId ? DB::table('db_periods')->find($activePeriodId) : null;

        if ($activePeriod) {
            $request->merge([
                'school_year' => $activePeriod->year,
                'semester' => $activePeriod->term,
            ]);
        }

        // Validate the inputs
        $request->validate([
            'instructor_id' => 'required|exists:users,id',
            'subject_name'  => 'required|string|max:255',
            'section_name'  => 'required|string|max:255',
            'school_year'   => 'required|string|max:20',
            'semester'      => 'required|string|max:10',
        ]);

        // Handle subject - find existing or create new
        $subjectName = trim($request->subject_name);
        $subject = Subject::whereRaw("CONCAT(code, ' - ', name) = ?", [$subjectName])
                         ->orWhere('code', $subjectName)
                         ->orWhere('name', $subjectName)
                         ->first();

        if (!$subject) {
            // Try to parse "CODE - NAME" format
            if (strpos($subjectName, ' - ') !== false) {
                [$code, $name] = explode(' - ', $subjectName, 2);
                $subject = Subject::create([
                    'code' => trim($code),
                    'name' => trim($name),
                    'course_no' => '',
                    'units' => 0,
                ]);
            } else {
                // Create with name as both code and name
                $subject = Subject::create([
                    'code' => $subjectName,
                    'name' => $subjectName,
                    'course_no' => '',
                    'units' => 0,
                ]);
            }
        }

        // Handle section - find existing or create new
        $sectionName = trim($request->section_name);
        $section = Section::where('name', $sectionName)->first();

        if (!$section) {
            $section = Section::create([
                'name' => $sectionName,
                'year' => 1, // Default year level
            ]);
        }

        // Prevent duplicate assignment for same instructor/subject/section (same term)
        $exists = ClassSchedule::where('instructor_id', $request->instructor_id)
            ->where('subject_id', $subject->id)
            ->where('section_id', $section->id)
            ->where('ay', $request->school_year)
            ->where('term', $request->semester)
            ->where('id', '!=', $assignment->id)
            ->exists();

        if ($exists) {
            return $this->duplicateAssignmentResponse($request);
        }

        // Update corresponding class schedule if exists
        $originalInstructorId = $assignment->instructor_id;
        $originalInstructor = User::find($originalInstructorId);
        $originalEmpId = $originalInstructor ? $originalInstructor->empid : null;

        ClassSchedule::where('subject_id', $assignment->subject_id)
            ->where('section_id', $assignment->section_id)
            ->where('ay', $assignment->school_year)
            ->where('term', $assignment->semester)
            ->where(function ($query) use ($originalInstructorId, $originalEmpId) {
                if ($originalInstructorId) {
                    $query->where('instructor_id', $originalInstructorId);
                }
                if ($originalEmpId) {
                    $query->orWhere('instructor_id', $originalEmpId);
                }
            })
            ->update([
                'subject_id' => $subject->id,
                'section_id' => $section->id,
                'instructor_id' => $request->instructor_id,
                'ay' => $request->school_year,
                'term' => $request->semester,
            ]);

        // Update the assignment
        $assignment->update([
            'instructor_id' => $request->instructor_id,
            'subject_id' => $subject->id,
            'section_id' => $section->id,
            'school_year' => $request->school_year,
            'semester' => $request->semester,
        ]);

        return redirect()->route('instructors.index')
                         ->with('success', 'Workload updated successfully!');
    }

    public function destroy($id)
    {
        $assignment = TeachingAssignment::findOrFail($id);
        $assignment->delete();
        return redirect()->route('instructors.index')
                         ->with('success', 'Workload removed.');
    }
}
