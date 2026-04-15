<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Section;
use App\Models\Student;
use App\Models\College;
use App\Models\Course;
use App\Models\Curriculum;
use App\Models\CurriculumSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SubjectController extends Controller
{
    private function resolveAcademicTab(?string $tab, string $default = 'subjects'): string
    {
        return in_array($tab, ['curriculum', 'subjects'], true) ? $tab : $default;
    }

    public function index(Request $request)
    {
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
        $activeCurriculumId = (int) $request->session()->get('active_curriculum_id', 0);
        $activeProgramId = (int) $request->session()->get('active_program_id', 0);
        $activeTab = $this->resolveAcademicTab($request->query('tab'));

        $activeCurriculum = null;
        if ($activeCurriculumId > 0) {
            $activeCurriculum = Curriculum::query()
                ->select('id', 'course_id', 'area_code', 'college_id')
                ->find($activeCurriculumId);

            if ($activeCurriculum) {
                $activeProgramId = (int) ($activeCurriculum->course_id ?: $activeProgramId);
            } else {
                $request->session()->forget(['active_curriculum_id', 'active_program_id']);
                $activeCurriculumId = 0;
                $activeProgramId = 0;
            }
        }

        $importDefaultAreaCode = $activeCurriculum?->area_code ?: $selectedArea;
        $importDefaultCollegeId = $activeCurriculum?->college_id ?: $selectedCollege;
        $importDefaultProgramId = $activeProgramId;
        $importDefaultCurriculumId = $activeCurriculum ? (int) $activeCurriculum->id : 0;

        $areaOptions = DB::table('areas')
            ->orderBy('area_name')
            ->pluck('area_name', 'area_code')
            ->toArray();

        $collegeOptions = College::when($selectedArea, function ($query, $area) {
                return $query->where('area_code', $area);
            })
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $allColleges = College::query()
            ->select('id', 'name', 'area_code')
            ->orderBy('name')
            ->get();

        $subjects = collect();
        $programs = collect();
        $programLookup = collect();
        $curriculums = collect();
        $curriculumsByProgramId = collect();
        $sections = collect();
        $sectionStudentCounts = [];
        $students = collect();
        $searchTerm = '';

        if ($activeTab === 'subjects') {
            $searchTerm = trim($request->input('subject_search', ''));
            $subjects = Subject::with(['curriculumSubjects.curriculum.course', 'curricula.course'])
                ->when($searchTerm, function ($query) use ($searchTerm) {
                    $query->where(function ($q) use ($searchTerm) {
                        $q->where('name', 'like', "%{$searchTerm}%")
                          ->orWhere('code', 'like', "%{$searchTerm}%");
                    });
                }, function ($query) use ($selectedArea, $selectedCollege) {
                    if ($selectedArea) {
                        $query->where('area_code', $selectedArea);
                    }
                    if ($selectedCollege) {
                        $query->where('college_id', $selectedCollege);
                    }
                })
                ->get();

            $programs = Course::query()
                ->select('id', 'course_program', 'code', 'name', 'area_code', 'college_id')
                ->when($selectedArea, function ($query, $selectedArea) {
                    return $query->where('area_code', $selectedArea);
                })
                ->when($selectedCollege, function ($query, $selectedCollege) {
                    return $query->where('college_id', $selectedCollege);
                })
                ->orderBy('course_program')
                ->orderBy('name')
                ->get();

            $curriculums = Curriculum::query()
                ->select('id', 'course_id', 'code', 'desc', 'area_code', 'college_id')
                ->with('course')
                ->when($selectedArea, function ($query, $selectedArea) {
                    return $query->where('area_code', $selectedArea);
                })
                ->when($selectedCollege, function ($query, $selectedCollege) {
                    return $query->where('college_id', $selectedCollege);
                })
                ->orderBy('code')
                ->get();
        }

        if ($activeTab === 'curriculum') {
            $curriculums = Curriculum::query()
                ->select('id', 'course_id', 'code', 'desc', 'area_code', 'college_id')
                ->when($selectedArea, function ($query, $selectedArea) {
                    return $query->where('area_code', $selectedArea);
                })
                ->when($selectedCollege, function ($query, $selectedCollege) {
                    return $query->where('college_id', $selectedCollege);
                })
                ->orderBy('course_id')
                ->orderBy('code')
                ->get();

            $curriculumProgramIds = $curriculums
                ->pluck('course_id')
                ->filter(fn ($id) => !empty($id))
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            $programLookup = Course::query()
                ->select('id', 'course_program', 'code', 'name')
                ->whereIn('id', $curriculumProgramIds)
                ->orderBy('course_program')
                ->orderBy('name')
                ->get()
                ->keyBy('id');

            // Used by curriculum create/edit selects.
            $programs = Course::query()
                ->select('id', 'course_program', 'code', 'name', 'area_code', 'college_id')
                ->withCount('curriculums')
                ->when($selectedArea, function ($query, $selectedArea) {
                    return $query->where('area_code', $selectedArea);
                })
                ->when($selectedCollege, function ($query, $selectedCollege) {
                    return $query->where('college_id', $selectedCollege);
                })
                ->orderBy('course_program')
                ->orderBy('name')
                ->get();

            $curriculumsByProgramId = $curriculums
                ->groupBy(fn ($row) => (int) ($row->course_id ?? 0));
        }

        if ($activeTab === 'sections') {
            $sections = Section::query()
                ->when($selectedArea, function ($query, $selectedArea) {
                    return $query->where('area_code', $selectedArea);
                })
                ->when($selectedCollege, function ($query, $selectedCollege) {
                    return $query->where('college_id', $selectedCollege);
                })
                ->get();

            if ($sections->isNotEmpty()) {
                $sectionStudentCounts = DB::table('db_class_schedules_student as css')
                    ->join('db_class_schedules as cs', 'css.class_schedule_id', '=', 'cs.id')
                    ->whereIn('cs.section_id', $sections->pluck('id')->all())
                    ->select('cs.section_id', DB::raw('COUNT(DISTINCT css.user_student_id) as total'))
                    ->groupBy('cs.section_id')
                    ->pluck('total', 'cs.section_id')
                    ->toArray();
            }
        }

        if ($activeTab === 'students') {
            // Keep this query lean for faster tab changes.
            $students = Student::query()
                ->select('id', 'sid', 'fname', 'lname', 'area_code', 'college_code')
                ->when($selectedArea, function ($query, $selectedArea) {
                    return $query->where('area_code', $selectedArea);
                })
                ->when($selectedCollege, function ($query, $selectedCollege) {
                    return $query->where('college_code', $selectedCollege);
                })
                ->orderBy('lname')
                ->orderBy('fname')
                ->get();
        }

        return view('subjects.index', compact(
            'subjects',
            'programs',
            'programLookup',
            'curriculums',
            'curriculumsByProgramId',
            'sections',
            'sectionStudentCounts',
            'students',
            'areaOptions',
            'collegeOptions',
            'selectedArea',
            'selectedCollege',
            'activeCurriculumId',
            'activeProgramId',
            'importDefaultAreaCode',
            'importDefaultCollegeId',
            'importDefaultProgramId',
            'importDefaultCurriculumId',
            'allColleges',
            'activeTab',
            'searchTerm'
        ));
    }

    public function create(Request $request)
    {
        $selectedArea = $request->query('area_code', session('selected_area'));
        $selectedCollege = $request->query('college_id', session('selected_college'));
        $activeTab = $this->resolveAcademicTab($request->query('tab'));

        $areaOptions = DB::table('areas')
            ->orderBy('area_name')
            ->pluck('area_name', 'area_code')
            ->toArray();

        $colleges = College::query()
            ->select('id', 'name', 'area_code')
            ->orderBy('name')
            ->get();

        $activeCurriculumId = (int) session('active_curriculum_id', 0);
        $activeProgramId = (int) session('active_program_id', 0);

        if ($activeCurriculumId > 0) {
            $activeCurriculum = Curriculum::query()->select('id', 'course_id', 'area_code', 'college_id')->find($activeCurriculumId);
            if ($activeCurriculum) {
                $activeProgramId = (int) ($activeCurriculum->course_id ?: $activeProgramId);

                if (!$request->has('area_code')) {
                    $selectedArea = $activeCurriculum->area_code;
                }
                if (!$request->has('college_id')) {
                    $selectedCollege = (int) $activeCurriculum->college_id;
                }
            } else {
                session()->forget(['active_curriculum_id', 'active_program_id']);
                $activeCurriculumId = 0;
                $activeProgramId = 0;
            }
        }

        $programs = Course::query()
            ->select('id', 'course_program', 'code', 'name', 'area_code', 'college_id')
            ->when($selectedArea, function ($query, $selectedArea) {
                return $query->where('area_code', $selectedArea);
            })
            ->when($selectedCollege, function ($query, $selectedCollege) {
                return $query->where('college_id', $selectedCollege);
            })
            ->orderBy('course_program')
            ->orderBy('name')
            ->get();

        $curriculums = Curriculum::query()
            ->select('id', 'course_id', 'code', 'desc', 'area_code', 'college_id')
            ->with('course')
            ->when($selectedArea, function ($query, $selectedArea) {
                return $query->where('area_code', $selectedArea);
            })
            ->when($selectedCollege, function ($query, $selectedCollege) {
                return $query->where('college_id', $selectedCollege);
            })
            ->orderBy('code')
            ->get();

        return view('subjects.create', compact('programs', 'curriculums', 'selectedArea', 'selectedCollege', 'areaOptions', 'colleges', 'activeTab', 'activeCurriculumId', 'activeProgramId'));
    }

    public function setActiveCurriculum(Request $request, $id)
    {
        $curriculum = Curriculum::query()
            ->select('id', 'course_id', 'area_code', 'college_id')
            ->findOrFail($id);

        $request->session()->put('active_curriculum_id', (int) $curriculum->id);
        $request->session()->put('active_program_id', (int) ($curriculum->course_id ?: 0));
        $request->session()->put('selected_area', $curriculum->area_code);
        $request->session()->put('selected_college', (int) $curriculum->college_id);

        return redirect()->route('subjects.index', ['tab' => 'curriculum'])
            ->withFragment('curriculum')
            ->with('success', 'Curriculum set as active for Area, College, Program, and Curriculum defaults.');
    }

    public function store(Request $request)
    {
        $activeTab = $this->resolveAcademicTab($request->input('tab'), 'subjects');
        $selectedArea = $request->input('area_code', session('selected_area'));
        $selectedCollege = $request->input('college_id', session('selected_college'));

        $request->merge([
            'area_code' => $selectedArea,
            'college_id' => $selectedCollege,
        ]);

        $request->validate([
            'area_code' => 'required|string|exists:areas,area_code',
            'college_id' => 'required|integer|exists:db_colleges,id',
            'code' => 'required|string|max:20|unique:db_subjects,code',
            'name' => 'required|string|max:255',
            'course_no' => 'nullable|string|max:20',
            'program_id' => 'nullable|integer|exists:db_courses,id',
            'curriculum_id' => 'nullable|integer|exists:db_curriculums,id',
            'subject_year' => 'nullable|string|max:20',
            'subject_term' => 'nullable|string|max:20',
            'subject_units' => 'nullable|numeric',
            'new_program_code' => 'nullable|string|max:40',
            'new_program_name' => 'nullable|string|max:255',
            'new_curriculum_code' => 'nullable|string|max:20',
            'new_curriculum_desc' => 'nullable|string|max:255',
        ]);

        $courseId = null;

        if ($request->filled('program_id')) {
            $courseId = $request->input('program_id');
        } elseif ($request->filled('new_program_code') || $request->filled('new_program_name')) {
            return back()->withInput()->withErrors(['program_id' => 'Program must be saved via "+ New Program" form before submitting subject.']);
        }

        $curriculumId = null;

        if ($request->filled('curriculum_id')) {
            $curriculumId = $request->input('curriculum_id');
        } elseif ($request->filled('new_curriculum_code') || $request->filled('new_curriculum_desc')) {
            return back()->withInput()->withErrors(['curriculum_id' => 'Curriculum must be saved via "+ New Curriculum" form before submitting subject.']);
        }

        $selectedCollegeId = (int) $selectedCollege;

        if ($courseId) {
            $programExistsForScope = Course::query()
                ->where('id', (int) $courseId)
                ->where('area_code', $selectedArea)
                ->where('college_id', $selectedCollegeId)
                ->exists();

            if (!$programExistsForScope) {
                return back()->withInput()->withErrors([
                    'program_id' => 'No program like that exists for the selected Area and College.',
                ]);
            }
        }

        if ($curriculumId) {
            $curriculumQuery = Curriculum::query()
                ->where('id', (int) $curriculumId)
                ->where('area_code', $selectedArea)
                ->where('college_id', $selectedCollegeId);

            if ($courseId) {
                $curriculumQuery->where('course_id', (int) $courseId);
            }

            $curriculumExistsForScope = $curriculumQuery->exists();

            if (!$curriculumExistsForScope) {
                return back()->withInput()->withErrors([
                    'curriculum_id' => 'Selected curriculum is not aligned with the chosen Area, College, and Program.',
                ]);
            }
        }

        $subjectPayload = $request->only(['area_code','code','name','units','load','tf','lec','lec_sched','lab','lab_sched','lab_wt','tot_hrs','type_id','level_id','lec_subj_id','college_id','is_professional','is_exclusive','is_no_tuition','is_no_grade','is_enclose_units','is_exclude_ave_wt','is_external_source','is_teaching','is_ojt','is_special','lab_hour_multiplier','lab_credit_multiplier','is_rle']);
        $subjectPayload['course_no'] = trim((string) $request->input('code'));

        $subject = Subject::create($subjectPayload);

        if ($curriculumId) {
            \App\Models\CurriculumSubject::create([
                'curriculum_id' => $curriculumId,
                's_code' => $subject->id,
                's_year' => $request->input('subject_year'),
                's_term' => $request->input('subject_term'),
                's_units' => $request->input('subject_units') ?? $subject->units,
            ]);
        }

        return redirect()->route('subjects.index', ['tab' => $activeTab])
            ->withFragment($activeTab)
            ->with('success', 'Subject created successfully.');
    }

    public function ajaxCreateProgram(Request $request)
    {
        $data = $request->validate([
            'new_program_code' => 'required|string|max:40',
            'new_program_name' => 'required|string|max:255',
        ]);

        $course = \App\Models\Course::create([
            'course_program' => $data['new_program_code'],
            'code' => $data['new_program_code'],
            'name' => $data['new_program_name'],
            'status' => 'active',
        ]);

        return response()->json([
            'id' => $course->id,
            'course_program' => $course->course_program,
            'code' => $course->code,
            'name' => $course->name,
        ], 201);
    }

    public function ajaxCreateCurriculum(Request $request)
    {
        $data = $request->validate([
            'new_curriculum_code' => 'required|string|max:20',
            'new_curriculum_desc' => 'required|string|max:255',
            'program_id' => 'required|integer|exists:db_courses,id',
        ]);

        $curriculum = \App\Models\Curriculum::create([
            'code' => $data['new_curriculum_code'],
            'desc' => $data['new_curriculum_desc'],
            'course_id' => $data['program_id'],
        ]);

        return response()->json([
            'id' => $curriculum->id,
            'code' => $curriculum->code,
            'desc' => $curriculum->desc,
            'course_id' => $curriculum->course_id,
        ], 201);
    }

    public function storeProgram(Request $request)
    {
        $request->validate([
            'course_program' => 'required|string|max:40|unique:db_courses,course_program',
            'name' => 'required|string|max:255',
            'area_code' => 'required|string|exists:areas,area_code',
            'college_id' => 'required|integer|exists:db_colleges,id',
        ]);

        $programCode = strtoupper(trim((string) $request->input('course_program')));

        Course::create([
            'area_code' => $request->input('area_code'),
            'college_id' => (int) $request->input('college_id'),
            'course_program' => $programCode,
            'code' => $programCode,
            'name' => trim((string) $request->input('name')),
            'status' => 'active',
        ]);

        return redirect()->route('subjects.index', ['tab' => 'curriculum'])
            ->withFragment('curriculum')
            ->with('success', 'Program created successfully.');
    }

    public function updateProgram(Request $request, $id)
    {
        $program = Course::findOrFail($id);

        $request->validate([
            'course_program' => 'required|string|max:40|unique:db_courses,course_program,' . $program->id,
            'name' => 'required|string|max:255',
            'area_code' => 'required|string|exists:areas,area_code',
            'college_id' => 'required|integer|exists:db_colleges,id',
        ]);

        $programCode = strtoupper(trim((string) $request->input('course_program')));

        $program->update([
            'area_code' => $request->input('area_code'),
            'college_id' => (int) $request->input('college_id'),
            'course_program' => $programCode,
            'code' => $programCode,
            'name' => trim((string) $request->input('name')),
        ]);

        return redirect()->route('subjects.index', ['tab' => 'curriculum'])
            ->withFragment('curriculum')
            ->with('success', 'Program updated successfully.');
    }

    public function destroyProgram($id)
    {
        $program = Course::withCount('curriculums')->findOrFail($id);

        if ((int) $program->curriculums_count > 0) {
            return redirect()->route('subjects.index', ['tab' => 'curriculum'])
                ->withFragment('curriculum')
                ->with('error', 'Cannot delete this program because it has curriculum records.');
        }

        $program->delete();

        return redirect()->route('subjects.index', ['tab' => 'curriculum'])
            ->withFragment('curriculum')
            ->with('success', 'Program deleted successfully.');
    }

    public function storeCurriculum(Request $request)
    {
        $selectedArea = $request->filled('area_code')
            ? $request->input('area_code')
            : $request->session()->get('selected_area');

        $selectedCollege = $request->filled('college_id')
            ? $request->input('college_id')
            : $request->session()->get('selected_college');

        $request->merge([
            'area_code' => $selectedArea ?: null,
            'college_id' => $selectedCollege ?: null,
        ]);

        $request->validate([
            'course_id' => 'required|integer|exists:db_courses,id',
            'code' => 'required|string|max:20|unique:db_curriculums,code',
            'area_code' => 'nullable|string|exists:areas,area_code',
            'college_id' => 'nullable|integer|exists:db_colleges,id',
        ]);

        $curriculumCode = strtoupper(trim((string) $request->input('code')));

        Curriculum::create([
            'course_id' => (int) $request->input('course_id'),
            'code' => $curriculumCode,
            'desc' => $curriculumCode,
            'area_code' => $request->input('area_code') ?: null,
            'college_id' => $request->input('college_id') ?: null,
        ]);

        return redirect()->route('subjects.index', ['tab' => 'curriculum'])
            ->withFragment('curriculum')
            ->with('success', 'Curriculum created successfully.');
    }

    public function updateCurriculum(Request $request, $id)
    {
        $curriculum = Curriculum::findOrFail($id);

        $selectedArea = $request->filled('area_code')
            ? $request->input('area_code')
            : ($request->session()->get('selected_area') ?: $curriculum->area_code);

        $selectedCollege = $request->filled('college_id')
            ? $request->input('college_id')
            : ($request->session()->get('selected_college') ?: $curriculum->college_id);

        $request->merge([
            'area_code' => $selectedArea ?: null,
            'college_id' => $selectedCollege ?: null,
        ]);

        $request->validate([
            'course_id' => 'required|integer|exists:db_courses,id',
            'code' => 'required|string|max:20|unique:db_curriculums,code,' . $curriculum->id,
            'area_code' => 'nullable|string|exists:areas,area_code',
            'college_id' => 'nullable|integer|exists:db_colleges,id',
        ]);

        $curriculumCode = strtoupper(trim((string) $request->input('code')));

        $curriculum->update([
            'course_id' => (int) $request->input('course_id'),
            'code' => $curriculumCode,
            'desc' => $curriculumCode,
            'area_code' => $request->input('area_code') ?: null,
            'college_id' => $request->input('college_id') ?: null,
        ]);

        return redirect()->route('subjects.index', ['tab' => 'curriculum'])
            ->withFragment('curriculum')
            ->with('success', 'Curriculum updated successfully.');
    }

    public function destroyCurriculum($id)
    {
        $curriculum = Curriculum::withCount('subjects')->findOrFail($id);

        if ((int) $curriculum->subjects_count > 0) {
            return redirect()->route('subjects.index', ['tab' => 'curriculum'])
                ->withFragment('curriculum')
                ->with('error', 'Cannot delete this curriculum because it is linked to subjects.');
        }

        if ((int) session('active_curriculum_id', 0) === (int) $curriculum->id) {
            session()->forget(['active_curriculum_id', 'active_program_id']);
        }

        $curriculum->delete();

        return redirect()->route('subjects.index', ['tab' => 'curriculum'])
            ->withFragment('curriculum')
            ->with('success', 'Curriculum deleted successfully.');
    }

    public function edit(Request $request, $id)
    {
        $subject = Subject::with(['curriculumSubjects'])->findOrFail($id);
        $selectedArea = $request->query('area_code', session('selected_area') ?: $subject->area_code);
        $selectedCollege = $request->query('college_id', session('selected_college') ?: $subject->college_id);
        $activeTab = $this->resolveAcademicTab($request->query('tab'));

        $areaOptions = DB::table('areas')
            ->orderBy('area_name')
            ->pluck('area_name', 'area_code')
            ->toArray();

        $collegeOptions = College::when($selectedArea, function ($query, $area) {
                return $query->where('area_code', $area);
            })
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $colleges = College::query()
            ->select('id', 'name', 'area_code')
            ->orderBy('name')
            ->get();

        $programs = Course::query()
            ->select('id', 'course_program', 'code', 'name', 'area_code', 'college_id')
            ->orderBy('course_program')
            ->orderBy('name')
            ->get();

        $curriculums = Curriculum::query()
            ->select('id', 'course_id', 'code', 'desc', 'area_code', 'college_id')
            ->with('course')
            ->orderBy('code')
            ->get();

        $currentMap = $subject->curriculumSubjects->first();
        $mappedCurriculumId = (int) ($currentMap->curriculum_id ?? 0);
        $mappedProgramId = 0;

        if ($mappedCurriculumId > 0) {
            $mappedProgramId = (int) (Curriculum::query()->where('id', $mappedCurriculumId)->value('course_id') ?: 0);
        }

        $activeCurriculumId = (int) session('active_curriculum_id', 0);
        $activeProgramId = (int) session('active_program_id', 0);

        if ($activeCurriculumId > 0) {
            $activeCurriculum = Curriculum::query()->select('id', 'course_id', 'area_code', 'college_id')->find($activeCurriculumId);
            if ($activeCurriculum) {
                if (!$request->has('area_code')) {
                    $selectedArea = $activeCurriculum->area_code;
                }
                if (!$request->has('college_id')) {
                    $selectedCollege = (int) $activeCurriculum->college_id;
                }
            }
        }

        $currentCurriculumId = $mappedCurriculumId;
        $currentProgramId = $mappedProgramId;

        if ($activeCurriculumId > 0) {
            $activeCurriculumProgramId = (int) (Curriculum::query()->where('id', $activeCurriculumId)->value('course_id') ?: 0);

            if ($activeCurriculumProgramId > 0 || $activeProgramId > 0) {
                $currentCurriculumId = $activeCurriculumId;
                $currentProgramId = $activeCurriculumProgramId ?: $activeProgramId;
            }
        }

        return view('subjects.edit', compact(
            'subject',
            'selectedArea',
            'selectedCollege',
            'activeTab',
            'areaOptions',
            'collegeOptions',
            'colleges',
            'programs',
            'curriculums',
            'currentProgramId',
            'currentCurriculumId'
        ));
    }

    public function update(Request $request, $id)
    {
        $activeTab = $this->resolveAcademicTab($request->input('tab'), 'subjects');
        $selectedArea = $request->input('area_code', session('selected_area'));
        $selectedCollege = $request->input('college_id', session('selected_college'));

        $request->merge([
            'area_code' => $selectedArea,
            'college_id' => $selectedCollege,
        ]);

        $subject = Subject::findOrFail($id);

        $request->validate([
            'area_code' => 'required|string|exists:areas,area_code',
            'college_id' => 'required|integer|exists:db_colleges,id',
            'code' => 'required|string|max:20|unique:db_subjects,code,' . $subject->id,
            'name' => 'required|string|max:255',
            'program_id' => 'nullable|integer|exists:db_courses,id',
            'curriculum_id' => 'nullable|integer|exists:db_curriculums,id',
            'subject_year' => 'nullable|string|max:20',
            'subject_term' => 'nullable|string|max:20',
            'subject_units' => 'nullable|numeric',
        ]);

        $courseId = $request->input('program_id');
        $curriculumId = $request->input('curriculum_id');
        $selectedCollegeId = (int) $selectedCollege;

        if ($courseId) {
            $programExistsForScope = Course::query()
                ->where('id', (int) $courseId)
                ->where('area_code', $selectedArea)
                ->where('college_id', $selectedCollegeId)
                ->exists();

            if (!$programExistsForScope) {
                return back()->withInput()->withErrors([
                    'program_id' => 'No program like that exists for the selected Area and College.',
                ]);
            }
        }

        if ($curriculumId) {
            $curriculumQuery = Curriculum::query()
                ->where('id', (int) $curriculumId)
                ->where('area_code', $selectedArea)
                ->where('college_id', $selectedCollegeId);

            if ($courseId) {
                $curriculumQuery->where('course_id', (int) $courseId);
            }

            $curriculumExistsForScope = $curriculumQuery->exists();

            if (!$curriculumExistsForScope) {
                return back()->withInput()->withErrors([
                    'curriculum_id' => 'Selected curriculum is not aligned with the chosen Area, College, and Program.',
                ]);
            }
        }

        $subjectPayload = $request->only(['area_code','code','name','units','load','tf','lec','lec_sched','lab','lab_sched','lab_wt','tot_hrs','type_id','level_id','lec_subj_id','college_id','is_professional','is_exclusive','is_no_tuition','is_no_grade','is_enclose_units','is_exclude_ave_wt','is_external_source','is_teaching','is_ojt','is_special','lab_hour_multiplier','lab_credit_multiplier','is_rle']);
        $subjectPayload['course_no'] = trim((string) $request->input('code'));

        $subject->update($subjectPayload);

        if ($curriculumId) {
            CurriculumSubject::updateOrCreate(
                ['s_code' => $subject->id],
                [
                    'curriculum_id' => $curriculumId,
                    's_year' => $request->input('subject_year'),
                    's_term' => $request->input('subject_term'),
                    's_units' => $request->input('subject_units') ?? $subject->units,
                ]
            );
        } else {
            CurriculumSubject::where('s_code', $subject->id)->delete();
        }

        return redirect()->route('subjects.index', ['tab' => $activeTab])->withFragment($activeTab);
    }

    public function import(Request $request)
    {
        $activeTab = 'subjects';
        $selectedArea = $request->input('area_code', session('selected_area'));
        $selectedCollege = $request->input('college_id', session('selected_college'));

        $request->merge([
            'area_code' => $selectedArea,
            'college_id' => $selectedCollege,
        ]);

        $request->validate([
            'area_code' => 'required|string|exists:areas,area_code',
            'college_id' => 'required|integer|exists:db_colleges,id',
            'import_file' => 'required|file|mimes:csv,txt,xlsx,xls',
            'import_program_id' => 'nullable|integer|exists:db_courses,id',
            'import_curriculum_id' => 'nullable|integer|exists:db_curriculums,id',
            'import_subject_year' => 'nullable|string|max:20',
            'import_subject_term' => 'nullable|string|max:20',
        ]);

        $file = $request->file('import_file');
        $defaultProgramId = (int) $request->input('import_program_id', 0);
        $defaultCurriculumId = (int) $request->input('import_curriculum_id', 0);
        $defaultSubjectYear = trim((string) $request->input('import_subject_year', ''));
        $defaultSubjectTerm = trim((string) $request->input('import_subject_term', ''));
        $selectedCollegeId = (int) $selectedCollege;

        if ($defaultProgramId > 0) {
            $programExistsForScope = Course::query()
                ->where('id', $defaultProgramId)
                ->where('area_code', $selectedArea)
                ->where('college_id', $selectedCollegeId)
                ->exists();

            if (!$programExistsForScope) {
                return back()->with('error', 'Selected default program is not aligned with the current Area and College.');
            }
        }

        if ($defaultCurriculumId > 0) {
            $defaultCurriculumQuery = Curriculum::query()
                ->where('id', $defaultCurriculumId)
                ->where('area_code', $selectedArea)
                ->where('college_id', $selectedCollegeId);

            if ($defaultProgramId > 0) {
                $defaultCurriculumQuery->where('course_id', $defaultProgramId);
            }

            if (!$defaultCurriculumQuery->exists()) {
                return back()->with('error', 'Selected default curriculum is not aligned with the current Area, College, and Program.');
            }
        }

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
                return back()->with('error', 'Unable to read the import file.');
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
                'code', 'subjectcode', 'coursecode' => 'code',
                'name', 'subjectname', 'descriptivetitle', 'coursetitle' => 'name',
                'subjectyear', 'year', 'yearlevel' => 'subject_year',
                'subjectterm', 'term', 'semester', 'termsemester', 'semesterterm' => 'subject_term',
                'programcode', 'program' => 'program_code',
                'curriculumcode', 'curriculum' => 'curriculum_code',
                'subjectunits', 'units' => 'subject_units',
                default => '',
            };
        };

        $firstRow = array_map(static fn ($v) => trim((string) $v), $rows[0]);
        $firstMapped = array_map($mapHeader, $firstRow);
        $hasHeader = in_array('code', $firstMapped, true) || in_array('name', $firstMapped, true);

        $header = $hasHeader ? $firstMapped : ['code', 'name', 'subject_year', 'subject_term', 'program_code', 'curriculum_code', 'subject_units'];
        $dataRows = $hasHeader ? array_slice($rows, 1) : $rows;

        $created = 0;
        $skipped = 0;
        $mapped = 0;
        $reused = 0;

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

            $code = (string) ($row['code'] ?? '');
            $name = (string) ($row['name'] ?? '');

            if ($code === '') {
                $skipped++;
                continue;
            }

            $subject = Subject::query()->where('code', $code)->first();

            if (!$subject) {
                if ($name === '') {
                    $skipped++;
                    continue;
                }

                $subject = Subject::create([
                    'area_code' => $selectedArea,
                    'college_id' => (int) $selectedCollege,
                    'code' => $code,
                    'course_no' => $code,
                    'name' => $name,
                ]);

                $created++;
            } else {
                $reused++;
            }

            $programCode = (string) ($row['program_code'] ?? '');
            $curriculumCode = (string) ($row['curriculum_code'] ?? '');
            $subjectYear = (string) (($row['subject_year'] ?? '') ?: $defaultSubjectYear);
            $subjectTerm = (string) (($row['subject_term'] ?? '') ?: $defaultSubjectTerm);
            $subjectUnits = $row['subject_units'] ?? null;

            $programId = null;
            if ($programCode !== '') {
                $programId = Course::query()
                    ->where('area_code', $selectedArea)
                    ->where('college_id', $selectedCollegeId)
                    ->where(function ($query) use ($programCode) {
                        $query->where('course_program', $programCode)
                            ->orWhere('code', $programCode);
                    })
                    ->value('id');
            }

            if (!$programId && $defaultProgramId > 0) {
                $programId = $defaultProgramId;
            }

            $curriculumId = null;
            if ($curriculumCode !== '') {
                $curriculumQuery = Curriculum::query()
                    ->where('area_code', $selectedArea)
                    ->where('college_id', $selectedCollegeId)
                    ->where('code', $curriculumCode);

                if ($programId) {
                    $curriculumQuery->where('course_id', (int) $programId);
                }

                $curriculumId = $curriculumQuery->value('id');
            }

            if (!$curriculumId && $defaultCurriculumId > 0) {
                $curriculumId = $defaultCurriculumId;
            }

            if (!$subject->wasRecentlyCreated) {
                $incomingName = trim($name);
                $existingName = trim((string) ($subject->name ?? ''));
                $nameMatches = $incomingName === '' || strcasecmp($existingName, $incomingName) === 0;

                if ($curriculumId) {
                    $existingMap = CurriculumSubject::query()
                        ->where('s_code', $subject->id)
                        ->first();

                    $incomingYear = $subjectYear !== '' ? $subjectYear : null;
                    $incomingTerm = $subjectTerm !== '' ? $subjectTerm : null;
                    $incomingUnits = $subjectUnits !== '' ? $subjectUnits : null;

                    $mapMatches = $existingMap
                        && (int) $existingMap->curriculum_id === (int) $curriculumId
                        && (string) ($existingMap->s_year ?? '') === (string) ($incomingYear ?? '')
                        && (string) ($existingMap->s_term ?? '') === (string) ($incomingTerm ?? '')
                        && (string) ($existingMap->s_units ?? '') === (string) ($incomingUnits ?? '');

                    if ($nameMatches && $mapMatches) {
                        $skipped++;
                        continue;
                    }
                } elseif ($nameMatches) {
                    $skipped++;
                    continue;
                }
            }

            if ($curriculumId) {
                CurriculumSubject::updateOrCreate(
                    ['s_code' => $subject->id],
                    [
                        'curriculum_id' => $curriculumId,
                        's_year' => $subjectYear !== '' ? $subjectYear : null,
                        's_term' => $subjectTerm !== '' ? $subjectTerm : null,
                        's_units' => $subjectUnits,
                    ]
                );
                $mapped++;
            }
        }

        return redirect()->route('subjects.index', ['tab' => $activeTab])
            ->withFragment($activeTab)
            ->with('success', "Import complete: {$created} created, {$reused} existing reused, {$mapped} mapped, {$skipped} skipped.");
    }

    public function destroy(Request $request, $id)
    {
        $activeTab = $this->resolveAcademicTab($request->query('tab'), 'subjects');
        $subject = Subject::findOrFail($id);
        $subject->delete();
        return redirect()->route('subjects.index', ['tab' => $activeTab])->withFragment($activeTab);
    }

    public function destroyMany(Request $request)
    {
        $activeTab = $this->resolveAcademicTab($request->input('tab'), 'subjects');

        $validated = $request->validate([
            'subject_ids' => 'required|array|min:1',
            'subject_ids.*' => 'integer|exists:db_subjects,id',
        ]);

        $subjectIds = collect($validated['subject_ids'])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($subjectIds->isEmpty()) {
            return redirect()->route('subjects.index', ['tab' => $activeTab])
                ->withFragment($activeTab)
                ->with('error', 'Please select at least one subject to delete.');
        }

        $deleted = Subject::query()->whereIn('id', $subjectIds->all())->delete();

        return redirect()->route('subjects.index', ['tab' => $activeTab])
            ->withFragment($activeTab)
            ->with('success', "{$deleted} subject(s) deleted successfully.");
    }
}
