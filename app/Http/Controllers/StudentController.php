<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Section;
use App\Models\College;
use App\Models\Course;
use App\Models\User;
use App\Models\ClassSchedule;
use App\Models\ClassScheduleStudent;
use App\Models\EvaluationQuestion;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;
use App\Models\EvaluationResult;
use App\Http\Controllers\StudentEvaluationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StudentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | STUDENT SIDE - EVALUATION
    |--------------------------------------------------------------------------
    */


    public function storeEvaluation(Request $request)
    {
        // Delegate to StudentEvaluationController for unified behavior.
        return app(StudentEvaluationController::class)->store($request);
    }
public function dashboard()
{
    $student = Student::where('account_id', Auth::id())->first();

    if (!$student) {
        return redirect()->back()->with('error', 'Student record not found.');
    }

    $assignments = TeachingAssignment::with(['instructor', 'subject'])
        ->get();

    return view('students.dashboard', compact('assignments'));
}

// Show evaluation form
public function showEvaluation($class_schedule_id)
{
    $questions = EvaluationQuestion::all();
    $classSchedule = ClassSchedule::with(['instructor', 'subject', 'section'])->find($class_schedule_id);
    $collegeName = null;

    if ($classSchedule && $classSchedule->college_id) {
        $collegeName = College::where('id', $classSchedule->college_id)->value('name');
    }

    return view('students.evaluate', compact('questions', 'class_schedule_id', 'classSchedule', 'collegeName'));
}

// Submit evaluation
public function submitEvaluation(Request $request)
    {
        return app(StudentEvaluationController::class)->store($request);
    }


    public function index()
    {
        return redirect()->route('sections.index', ['tab' => 'students'])->withFragment('students');
    }

    public function create(Request $request)
    {
        $preselectedSectionId = $request->get('section_id');
        $selectedArea = session('selected_area');
        $selectedCollege = session('selected_college');

        if ($preselectedSectionId && (!$selectedArea || !$selectedCollege)) {
            $preselectedSection = Section::find($preselectedSectionId);
            if ($preselectedSection) {
                $selectedArea = $selectedArea ?: $preselectedSection->area_code;
                $selectedCollege = $selectedCollege ?: $preselectedSection->college_id;
            }
        }

        $sections = Section::query()
            ->when($selectedArea, function ($query, $selectedArea) {
                return $query->where('area_code', $selectedArea);
            })
            ->when($selectedCollege, function ($query, $selectedCollege) {
                return $query->where('college_id', $selectedCollege);
            })
            ->orderBy('name')
            ->get();
        
        // Get existing students not yet assigned to this section
        $existingStudents = collect();
        if ($preselectedSectionId) {
            // Get all students not in the selected section
            $studentsInSection = Student::whereHas('classScheduleEnrollments', function ($query) {
                $query->whereHas('classSchedule', function ($subQuery) {
                    $subQuery->where('section_id', request('section_id'));
                });
            })->pluck('id');
            
            $existingStudents = Student::whereNotIn('id', $studentsInSection)
                ->orderBy('fname')
                ->get();
        }
        
        return view('students.create', compact('sections', 'preselectedSectionId', 'existingStudents', 'selectedArea', 'selectedCollege'));
    }

    public function show($id)
    {
        // Try to find by database ID first
        $student = Student::with('user', 'classScheduleEnrollments.classSchedule.section')->find($id);

        // If not found by ID, try to find by SID
        if (!$student) {
            $student = Student::with('user', 'classScheduleEnrollments.classSchedule.section')->where('sid', $id)->first();
        }

        // If still not found, throw 404
        if (!$student) {
            abort(404, 'Student not found');
        }

        return view('students.show', compact('student'));
    }

    public function store(Request $request)
    {
        $selectedArea = $request->input('area_code', session('selected_area'));
        $selectedCollege = $request->input('college_code', session('selected_college'));

        $request->validate([
            'sid' => 'required|unique:db_students,sid',
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'nullable|email|max:255',
            'section_id' => 'nullable|integer|exists:db_sections,id',
        ]);

        $data = [
            'sid' => $request->sid,
            'fname' => $request->fname,
            'lname' => $request->lname,
            'area_code' => $selectedArea,
            'college_code' => $selectedCollege,
            'email' => $request->input('email'),
            'has_account' => 'N',
            'student_status' => 'New',
        ];

        $student = Student::create($data);

        // create user account only if email and password are provided
        $email = $request->input('email');
        $password = $request->input('password');

        if ($email && $password) {
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'fname' => $student->fname,
                    'lname' => $student->lname,
                    'fullname' => trim($student->fname . ' ' . $student->lname),
                    'user_role' => '3',
                    'password' => Hash::make($password),
                ]
            );

            $student->account_id = $user->id;
            $student->has_account = 'Y';
            $student->email = $email;
        } else {
            // No account creation via admin panel.
            $student->account_id = null;
            $student->has_account = 'N';
            $student->email = $email ?: $student->email;
        }
        $student->save();

        $sectionId = $request->input('section_id');
        if (!empty($sectionId)) {
            $classSchedules = ClassSchedule::where('section_id', $sectionId)->get();

            foreach ($classSchedules as $schedule) {
                ClassScheduleStudent::firstOrCreate([
                    'class_schedule_id' => $schedule->id,
                    'user_student_id' => $student->id,
                ], [
                    'area_code' => $schedule->area_code ?? null,
                    'year_level' => $schedule->year_level ?? null,
                    'period_id' => $schedule->period_id ?? null,
                    'term' => $schedule->term ?? null,
                    'ay' => $schedule->ay ?? null,
                    'class_type' => $schedule->class_type ?? 'Regular',
                    'class_status' => 'P',
                    'subject_code' => $schedule->subject_code ?? null,
                    'remark' => 'SECTION IMPORT',
                ]);
            }

            return redirect()->route('sections.students', $sectionId)
                ->with('success', 'Student created and added to the section class schedules.');
        }

        return redirect()->route('students.index')
            ->with('success', 'Student created successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'nullable|file|mimes:csv,txt,xlsx,xls',
            'csv_file' => 'nullable|file|mimes:csv,txt,xlsx,xls',
            'instructor_id' => 'nullable|integer|exists:users,id',
            'section_name' => 'nullable|string|max:50',
        ]);

        $file = $request->file('file') ?: $request->file('csv_file');
        if (!$file) {
            return redirect()->back()->with('error', 'Please upload a CSV or Excel file.');
        }
        $extension = strtolower((string) $file->getClientOriginalExtension());

        $data = [];
        if ($extension === 'csv' || $extension === 'txt') {
            $data = $this->parseCsv($file);
        } elseif ($extension === 'xlsx' || $extension === 'xls') {
            $data = $this->parseExcel($file);
        }

        $imported = 0;
        $enrolled = 0;
        $matchedExisting = 0;
        $errors = [];
        $carryCourse = '';
        $carrySection = '';

        $instructorId = (int) $request->input('instructor_id', 0);
        $allowedSectionIds = null;
        if ($instructorId > 0) {
            $allowedSectionIds = ClassSchedule::query()
                ->where('instructor_id', $instructorId)
                ->pluck('section_id')
                ->filter()
                ->map(static fn ($v) => (int) $v)
                ->unique()
                ->values();
        }

        $fallbackSectionName = trim((string) $request->input('section_name', ''));

        foreach ($data as $index => $row) {
            try {
                // Accept either: sid/fname/lname OR id/fullname OR class-list format.
                $studentId = trim((string) ($row['student_id'] ?? $row['sid'] ?? $row['id_number'] ?? $row['id'] ?? $row['id_no'] ?? $row['idno'] ?? ''));
                $studentId = ltrim($studentId, "' ");
                $fullName = trim((string) ($row['fullname'] ?? $row['full_name'] ?? $row['name'] ?? ''));
                $firstName = trim((string) ($row['fname'] ?? ''));
                $lastName = trim((string) ($row['lname'] ?? ''));
                $middleName = trim((string) ($row['mname'] ?? ''));

                $rowCourse = trim((string) ($row['course'] ?? ''));
                $rowSection = trim((string) ($row['year_section'] ?? $row['section'] ?? $row['section_name'] ?? ''));

                if ($rowCourse !== '') {
                    $carryCourse = $rowCourse;
                } else {
                    $rowCourse = $carryCourse;
                }

                if ($rowSection !== '') {
                    $carrySection = $rowSection;
                } else {
                    $rowSection = $carrySection;
                }

                if ($rowSection === '' && $fallbackSectionName !== '') {
                    $rowSection = $fallbackSectionName;
                }

                if (($firstName === '' || $lastName === '') && $fullName !== '') {
                    if (str_contains($fullName, ',')) {
                        [$parsedLast, $parsedFirst] = array_map('trim', explode(',', $fullName, 2));
                        $lastName = $parsedLast;

                        $nameTokens = preg_split('/\s+/', $parsedFirst);
                        $nameTokens = array_values(array_filter($nameTokens, static fn ($token) => $token !== ''));
                        if (!empty($nameTokens)) {
                            $firstName = array_shift($nameTokens);
                            if (empty($middleName) && !empty($nameTokens)) {
                                $middleName = implode(' ', $nameTokens);
                            }
                        }
                    } else {
                        $nameParts = preg_split('/\s+/', $fullName);
                        if (count($nameParts) > 1) {
                            $lastName = array_pop($nameParts);
                            $firstName = array_shift($nameParts);
                            if (empty($middleName) && !empty($nameParts)) {
                                $middleName = implode(' ', $nameParts);
                            }
                        } else {
                            $firstName = $fullName;
                        }
                    }
                }

                if ($studentId === '' || $firstName === '' || $lastName === '') {
                    $errors[] = "Row " . ($index + 1) . ": Missing required fields (Student ID, First Name, Last Name)";
                    continue;
                }

                $isSectionEnrollment = $rowSection !== '';

                $student = Student::where('sid', $studentId)->first();

                if ($student && !$isSectionEnrollment) {
                    $matchedExisting++;
                    continue;
                }

                $email = isset($row['email']) ? trim((string) $row['email']) : null;
                if ($email === '') {
                    $email = null;
                }

                if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Row " . ($index + 1) . ": Invalid email format ({$email})";
                    continue;
                }

                $normalizedCourseCode = $this->normalizeStudentCourseCode($rowCourse);

                if (!$student) {
                    $studentData = [
                        'sid' => $studentId,
                        'fname' => $firstName,
                        'mname' => $middleName !== '' ? $middleName : null,
                        'lname' => $lastName,
                        'fullname' => trim($firstName . ' ' . ($middleName !== '' ? $middleName . ' ' : '') . $lastName),
                        'course_code' => $normalizedCourseCode,
                        'email' => $email,
                        'has_account' => 'N',
                        'student_status' => 'New',
                    ];

                    $student = Student::create($studentData);
                    $imported++;
                } else {
                    $matchedExisting++;
                    $student->update([
                        'fname' => $student->fname ?: $firstName,
                        'mname' => $student->mname ?: ($middleName !== '' ? $middleName : null),
                        'lname' => $student->lname ?: $lastName,
                        'fullname' => $student->fullname ?: trim($firstName . ' ' . ($middleName !== '' ? $middleName . ' ' : '') . $lastName),
                        'course_code' => $student->course_code ?: $normalizedCourseCode,
                        'email' => $student->email ?: $email,
                    ]);
                }

                // Create user account if email and password are provided
                if (!empty($email) && !empty($row['password'])) {
                    $user = User::firstOrCreate(
                        ['email' => $email],
                        [
                            'fname' => $student->fname,
                            'lname' => $student->lname,
                            'fullname' => trim($student->fname . ' ' . $student->lname),
                            'user_role' => '3',
                            'password' => Hash::make(trim($row['password'])),
                        ]
                    );

                    $student->account_id = $user->id;
                    $student->has_account = 'Y';
                    $student->email = $email;
                    $student->save();
                }

                if ($rowSection !== '') {
                    $section = Section::query()
                        ->whereRaw('LOWER(name) = ?', [strtolower($rowSection)])
                        ->first();

                    if (!$section) {
                        $errors[] = "Row " . ($index + 1) . ": Section {$rowSection} not found";
                        continue;
                    }

                    if ($allowedSectionIds !== null && !$allowedSectionIds->contains((int) $section->id)) {
                        $errors[] = "Row " . ($index + 1) . ": Section {$rowSection} is not assigned to this instructor";
                        continue;
                    }

                    $classSchedules = ClassSchedule::query()->where('section_id', $section->id)->get();
                    if ($classSchedules->isEmpty()) {
                        $errors[] = "Row " . ($index + 1) . ": No class schedules found for section {$rowSection}";
                        continue;
                    }

                    preg_match('/^(\d+)/', $rowSection, $sectionYearMatch);
                    $derivedYearLevel = $sectionYearMatch[1] ?? null;

                    foreach ($classSchedules as $schedule) {
                        $enrollment = ClassScheduleStudent::firstOrCreate([
                            'class_schedule_id' => $schedule->id,
                            'user_student_id' => $student->id,
                        ], [
                            'area_code' => $schedule->area_code ?? null,
                            'year_level' => $schedule->year_level ?? $derivedYearLevel,
                            'period_id' => $schedule->period_id ?? null,
                            'term' => $schedule->term ?? null,
                            'ay' => $schedule->ay ?? null,
                            'class_type' => $schedule->class_type ?? 'Regular',
                            'class_status' => 'P',
                            'subject_code' => $schedule->subject_code ?? null,
                            'remark' => 'CLASSLIST',
                        ]);

                        if ($enrollment->wasRecentlyCreated) {
                            $enrolled++;
                        }
                    }
                }

            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        $message = "Import completed. {$imported} student(s) created.";
        if ($matchedExisting > 0) {
            $message .= " {$matchedExisting} existing student(s) matched.";
        }
        if ($enrolled > 0) {
            $message .= " {$enrolled} enrollment(s) added to class schedule(s).";
        }
        if (!empty($errors)) {
            $message .= " Errors: " . implode('; ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $message .= " (and " . (count($errors) - 5) . " more errors)";
            }
        }

        return redirect()->back()->with('success', $message);
    }

    private function parseCsv($file)
    {
        $data = [];
        $handle = fopen($file->getPathname(), 'r');

        // Read header
        $header = fgetcsv($handle);
        if (!$header) {
            return $data;
        }

        // Normalize header names
        $header = array_map('strtolower', array_map('trim', $header));

        while (($row = fgetcsv($handle)) !== false) {
            $studentData = [];
            foreach ($header as $index => $column) {
                $value = $row[$index] ?? '';
                switch ($column) {
                    case 'course':
                    case 'program':
                        $studentData['course'] = $value;
                        break;
                    case 'year and section':
                    case 'year & section':
                    case 'year_section':
                    case 'yearsection':
                        $studentData['year_section'] = $value;
                        break;
                    case 'id number':
                    case 'id_number':
                    case 'id':
                    case 'id no':
                    case 'id_no':
                    case 'idno':
                    case 'student id':
                    case 'student_id':
                    case 'sid':
                        $studentData['sid'] = $value;
                        break;
                    case 'fullname':
                    case 'full name':
                    case 'student name':
                    case 'name':
                        $studentData['fullname'] = $value;
                        break;
                    case 'first name':
                    case 'first_name':
                    case 'fname':
                        $studentData['fname'] = $value;
                        break;
                    case 'last name':
                    case 'last_name':
                    case 'lname':
                        $studentData['lname'] = $value;
                        break;
                    case 'section':
                    case 'section_name':
                        $studentData['section'] = $value;
                        break;
                    case 'email':
                    case 'e-mail':
                        $studentData['email'] = $value;
                        break;
                    case 'password':
                        $studentData['password'] = $value;
                        break;
                }
            }
            $data[] = $studentData;
        }

        fclose($handle);
        return $data;
    }

    private function parseExcel($file)
    {
        $data = [];

        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            $rows = $spreadsheet->getActiveSheet()->toArray(null, false, false, false);
        } catch (\Throwable $e) {
            return [];
        }

        if (count($rows) === 0) {
            return [];
        }

        $header = array_map(static fn ($value) => strtolower(trim((string) $value)), $rows[0]);

        foreach (array_slice($rows, 1) as $row) {
            $studentData = [];

            foreach ($header as $index => $column) {
                $value = trim((string) ($row[$index] ?? ''));
                switch ($column) {
                    case 'course':
                    case 'program':
                        $studentData['course'] = $value;
                        break;
                    case 'year and section':
                    case 'year & section':
                    case 'year_section':
                    case 'yearsection':
                        $studentData['year_section'] = $value;
                        break;
                    case 'id number':
                    case 'id_number':
                    case 'id':
                    case 'id no':
                    case 'id_no':
                    case 'idno':
                    case 'student id':
                    case 'student_id':
                    case 'sid':
                        $studentData['sid'] = $value;
                        break;
                    case 'fullname':
                    case 'full name':
                    case 'student name':
                    case 'name':
                        $studentData['fullname'] = $value;
                        break;
                    case 'first name':
                    case 'first_name':
                    case 'fname':
                        $studentData['fname'] = $value;
                        break;
                    case 'last name':
                    case 'last_name':
                    case 'lname':
                        $studentData['lname'] = $value;
                        break;
                    case 'email':
                    case 'e-mail':
                        $studentData['email'] = $value;
                        break;
                    case 'password':
                        $studentData['password'] = $value;
                        break;
                    case 'section':
                    case 'section_name':
                        $studentData['section'] = $value;
                        break;
                }
            }

            if (count(array_filter($studentData, static fn ($v) => trim((string) $v) !== '')) > 0) {
                $data[] = $studentData;
            }
        }

        return $data;
    }

    private function normalizeStudentCourseCode(string $rowCourse): ?string
    {
        $value = trim($rowCourse);
        if ($value === '') {
            return null;
        }

        $normalizedInput = $this->normalizeComparableText($value);
        $matchedCourse = null;

        $courses = Course::query()->get(['course_program', 'code', 'name']);
        foreach ($courses as $course) {
            $nameNormalized = $this->normalizeComparableText((string) ($course->name ?? ''));
            $programNormalized = $this->normalizeComparableText((string) ($course->course_program ?? ''));
            $codeNormalized = $this->normalizeComparableText((string) ($course->code ?? ''));

            if (
                ($nameNormalized !== '' && $nameNormalized === $normalizedInput)
                || ($programNormalized !== '' && $programNormalized === $normalizedInput)
                || ($codeNormalized !== '' && $codeNormalized === $normalizedInput)
            ) {
                $matchedCourse = $course;
                break;
            }
        }

        if ($matchedCourse) {
            $candidates = [
                trim((string) ($matchedCourse->course_program ?? '')),
                trim((string) ($matchedCourse->code ?? '')),
                trim((string) ($matchedCourse->name ?? '')),
            ];
            foreach ($candidates as $candidate) {
                if ($candidate !== '' && strlen($candidate) <= 11) {
                    return $candidate;
                }
            }
        }

        if (strlen($value) <= 11) {
            return $value;
        }

        $stopWords = ['OF', 'IN', 'AND', 'THE'];
        $tokens = preg_split('/\s+/', strtoupper($value));
        $acronym = '';
        foreach ($tokens as $token) {
            $clean = preg_replace('/[^A-Z0-9]/', '', $token);
            if ($clean === '' || in_array($clean, $stopWords, true)) {
                continue;
            }
            $acronym .= $clean[0];
        }

        if ($acronym !== '') {
            return substr($acronym, 0, 11);
        }

        $safe = preg_replace('/[^A-Za-z0-9-]/', '', strtoupper($value));
        if ($safe === '') {
            return null;
        }

        return substr($safe, 0, 11);
    }

    private function normalizeComparableText(string $value): string
    {
        $normalized = strtoupper(trim($value));
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        return preg_replace('/[^A-Z0-9 ]/', '', $normalized);
    }

    public function edit($id)
    {
        $activeTab = request()->query('tab', 'students');
        if (!in_array($activeTab, ['subjects', 'sections', 'students'], true)) {
            $activeTab = 'students';
        }

        // Try to find by database ID first
        $student = Student::find($id);

        // If not found by ID, try to find by SID
        if (!$student) {
            $student = Student::where('sid', $id)->first();
        }

        // If still not found, throw 404
        if (!$student) {
            abort(404, 'Student not found');
        }

        $selectedArea = $student->area_code ?: session('selected_area');
        $selectedCollege = $student->college_code ?: session('selected_college');

        if ((!$selectedArea || !$selectedCollege) && $student->classScheduleEnrollments->isNotEmpty()) {
            $classSchedule = optional($student->classScheduleEnrollments->first())->classSchedule;
            if ($classSchedule) {
                $selectedArea = $selectedArea ?: $classSchedule->area_code;
                $selectedCollege = $selectedCollege ?: $classSchedule->college_id;
            }
        }

        $sections = Section::query()
            ->when($selectedArea, function ($query, $selectedArea) {
                return $query->where('area_code', $selectedArea);
            })
            ->when($selectedCollege, function ($query, $selectedCollege) {
                return $query->where('college_id', $selectedCollege);
            })
            ->orderBy('name')
            ->get();

        $areaOptions = DB::table('areas')
            ->orderBy('area_name')
            ->pluck('area_name', 'area_code')
            ->toArray();

        $colleges = College::orderBy('name')->get();

        $currentSectionName = optional(
            optional(optional($student->classScheduleEnrollments->first())->classSchedule)->section
        )->name;

        if (!$currentSectionName && $student->course_code && $student->year_level) {
            $currentSectionName = $student->course_code . '-' . $student->year_level;
        }

        return view('students.edit', compact('student', 'sections', 'selectedArea', 'selectedCollege', 'areaOptions', 'colleges', 'currentSectionName', 'activeTab'));
    }

    public function update(Request $request, $id)
    {
        $selectedArea = $request->input('area_code', session('selected_area'));
        $selectedCollege = $request->input('college_code', session('selected_college'));

        // Try to find by database ID first
        $student = Student::find($id);

        // If not found by ID, try to find by SID
        if (!$student) {
            $student = Student::where('sid', $id)->first();
        }

        // If still not found, throw 404
        if (!$student) {
            abort(404, 'Student not found');
        }

        $request->validate([
            'sid' => 'required|unique:db_students,sid,' . $student->id,
            'fname' => 'required',
            'lname' => 'required',
            'area_code' => 'nullable|string|max:15',
            'college_code' => 'nullable|exists:db_colleges,id',
        ]);

        $data = $request->all();

        $data['area_code'] = $selectedArea;
        $data['college_code'] = $selectedCollege;

        // Remove section_name since it's not stored on the student table
        unset($data['section_name']);

        $student->update($data);

        $sectionId = $request->input('section_id');
        if (!empty($sectionId)) {
            return redirect()->route('sections.students', $sectionId)
                ->with('success', 'Student updated successfully.');
        }

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully.');
    }

    /**
     * Add an existing student to a section
     */
    public function addExistingStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:db_students,id',
            'section_id' => 'required|exists:db_sections,id'
        ]);
        
        $student = Student::find($request->student_id);
        $section = Section::find($request->section_id);
        
        if (!$student || !$section) {
            return redirect()->back()->with('error', 'Student or section not found.');
        }
        
        // Check if already enrolled
        $alreadyEnrolled = $student->classScheduleEnrollments()
            ->whereHas('classSchedule', function ($query) use ($section) {
                $query->where('section_id', $section->id);
            })
            ->exists();
        
        if ($alreadyEnrolled) {
            return redirect()->back()->with('warning', $student->fname . ' is already enrolled in this section.');
        }
        
        // Create ClassScheduleStudent entries for this student in the selected section
        $classSchedules = ClassSchedule::where('section_id', $section->id)->get();
        
        if ($classSchedules->isEmpty()) {
            return redirect()->back()->with('warning', 'No class schedules found for this section.');
        }
        
        foreach ($classSchedules as $schedule) {
            ClassScheduleStudent::create([
                'area_code' => $schedule->area_code ?? null,
                'user_student_id' => $student->id,
                'year_level' => $schedule->year_level ?? $student->year_level ?? null,
                'period_id' => $schedule->period_id ?? null,
                'term' => $schedule->term ?? null,
                'ay' => $schedule->ay ?? null,
                'class_type' => $schedule->class_type ?? 'Regular',
                'class_status' => 'P',
                'class_schedule_id' => $schedule->id,
                'subject_code' => $schedule->subject_code ?? null,
                'remark' => 'Added to section'
            ]);
        }
        
        return redirect()->back()->with('success', $student->fname . ' ' . $student->lname . ' has been added to the section successfully.');
    }

    public function destroy(Request $request, $id)
    {
        // Try to find by database ID first
        $student = Student::find($id);

        // If not found by ID, try to find by SID
        if (!$student) {
            $student = Student::where('sid', $id)->first();
        }

        // If still not found, throw 404
        if (!$student) {
            abort(404, 'Student not found');
        }

        $student->delete();

        $sectionId = $request->input('section_id');
        if (!empty($sectionId)) {
            return redirect()->route('sections.students', $sectionId)
                ->with('success', 'Student deleted successfully.');
        }

        return redirect()->route('students.index')
            ->with('success', 'Student deleted successfully.');
    }
}
