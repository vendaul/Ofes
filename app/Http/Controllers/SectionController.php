<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Student;
use App\Models\College;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SectionController extends Controller
{
    private function getEquivalentSectionIds(Section $section): array
    {
        $ids = Section::query()
            ->whereRaw('TRIM(UPPER(name)) = TRIM(UPPER(?))', [$section->name])
            ->when(!empty($section->area_code), function ($query) use ($section) {
                $query->where(function ($q) use ($section) {
                    $q->where('area_code', $section->area_code)
                        ->orWhereNull('area_code');
                });
            })
            ->when(!empty($section->college_id), function ($query) use ($section) {
                $query->where(function ($q) use ($section) {
                    $q->where('college_id', $section->college_id)
                        ->orWhereNull('college_id');
                });
            })
            ->pluck('id')
            ->map(static fn ($value) => (int) $value)
            ->unique()
            ->values()
            ->all();

        if (!in_array((int) $section->id, $ids, true)) {
            $ids[] = (int) $section->id;
        }

        return $ids;
    }

    private function resolveAcademicTab(?string $tab, string $default = 'sections'): string
    {
        return in_array($tab, ['sections', 'students'], true) ? $tab : $default;
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
        $activeTab = $this->resolveAcademicTab($request->query('tab'), 'sections');
        $sectionSearchTerm = trim($request->input('section_search', ''));
        $studentSearchTerm = trim($request->input('student_search', ''));

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

        $sections = Section::query()
            ->when($sectionSearchTerm, function ($query) use ($sectionSearchTerm) {
                $query->where(function ($q) use ($sectionSearchTerm) {
                    $q->where('name', 'like', "%{$sectionSearchTerm}%")
                      ->orWhere('year', 'like', "%{$sectionSearchTerm}%");
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

        $sectionStudentCounts = [];

        if ($sections->isNotEmpty()) {
            foreach ($sections as $section) {
                $equivalentIds = $this->getEquivalentSectionIds($section);

                $sectionStudentCounts[$section->id] = (int) DB::table('db_class_schedules_student as css')
                    ->join('db_class_schedules as cs', 'css.class_schedule_id', '=', 'cs.id')
                    ->whereIn('cs.section_id', $equivalentIds)
                    ->distinct('css.user_student_id')
                    ->count('css.user_student_id');
            }
        }

        $students = Student::query()
            ->select('id', 'sid', 'fname', 'lname', 'area_code', 'college_code')
            ->when($studentSearchTerm, function ($query) use ($studentSearchTerm) {
                $query->where(function ($q) use ($studentSearchTerm) {
                    $q->where('sid', 'like', "%{$studentSearchTerm}%")
                        ->orWhere('fname', 'like', "%{$studentSearchTerm}%")
                        ->orWhere('lname', 'like', "%{$studentSearchTerm}%");
                });
            }, function ($query) use ($selectedArea, $selectedCollege) {
                if ($selectedArea) {
                    $query->where('area_code', $selectedArea);
                }
                if ($selectedCollege) {
                    $query->where('college_code', $selectedCollege);
                }
            })
            ->orderBy('lname')
            ->orderBy('fname')
            ->get();

        return view('sections.index', compact(
            'sections',
            'sectionStudentCounts',
            'students',
            'areaOptions',
            'collegeOptions',
            'selectedArea',
            'selectedCollege',
            'activeTab',
            'sectionSearchTerm',
            'studentSearchTerm'
        ));
    }

    public function create(Request $request)
    {
        $activeTab = $this->resolveAcademicTab($request->query('tab'), 'sections');
        $selectedArea = session('selected_area');
        $selectedCollege = session('selected_college');
        $areaOptions = DB::table('areas')
            ->orderBy('area_name')
            ->pluck('area_name', 'area_code')
            ->toArray();
        $colleges = College::orderBy('name')->get();

        return view('sections.create', compact('selectedArea', 'selectedCollege', 'areaOptions', 'colleges', 'activeTab'));
    }

    public function store(Request $request)
    {
        $activeTab = $this->resolveAcademicTab($request->input('tab'), 'sections');
        $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|string|max:255',
            'area_code' => 'nullable|string|max:15',
            'college_id' => 'nullable|exists:db_colleges,id',
        ]);

        $request->merge([
            'area_code' => $request->input('area_code', session('selected_area')),
            'college_id' => $request->input('college_id', session('selected_college')),
        ]);

        Section::create($request->all());
    return redirect()->route('sections.index');
    }

    public function show($id)
    {
        $section = Section::findOrFail($id);
        $section->students_count = \DB::table('db_class_schedules_student')
            ->join('db_class_schedules', 'db_class_schedules_student.class_schedule_id', '=', 'db_class_schedules.id')
            ->where('db_class_schedules.section_id', $id)
            ->distinct('db_class_schedules_student.user_student_id')
            ->count('db_class_schedules_student.user_student_id');

        $students = Student::query()
            ->join('db_class_schedules_student', 'db_students.id', '=', 'db_class_schedules_student.user_student_id')
            ->join('db_class_schedules', 'db_class_schedules_student.class_schedule_id', '=', 'db_class_schedules.id')
            ->where('db_class_schedules.section_id', $id)
            ->select('db_students.*')
            ->distinct()
            ->with('user')
            ->orderBy('db_students.lname')
            ->orderBy('db_students.fname')
            ->get();

        return view('sections.show', compact('section', 'students'));
    }

    public function edit(Request $request, $id)
    {
        $activeTab = $this->resolveAcademicTab($request->query('tab'), 'sections');
        $section = Section::findOrFail($id);
        $selectedArea = $section->area_code ?: session('selected_area');
        $selectedCollege = $section->college_id ?: session('selected_college');
        $areaOptions = DB::table('areas')
            ->orderBy('area_name')
            ->pluck('area_name', 'area_code')
            ->toArray();
        $colleges = College::orderBy('name')->get();

        return view('sections.edit', compact('section', 'selectedArea', 'selectedCollege', 'areaOptions', 'colleges', 'activeTab'));
    }

    public function update(Request $request, $id)
    {
        $activeTab = $this->resolveAcademicTab($request->input('tab'), 'sections');
        $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|string|max:255',
            'area_code' => 'nullable|string|max:15',
            'college_id' => 'nullable|exists:db_colleges,id',
        ]);

        $request->merge([
            'area_code' => $request->input('area_code', session('selected_area')),
            'college_id' => $request->input('college_id', session('selected_college')),
        ]);

        $section = Section::findOrFail($id);
        $section->update($request->all());
        return redirect()->route('sections.index');
    }

    public function destroy(Request $request, $id)
    {
        $activeTab = $this->resolveAcademicTab($request->query('tab'), 'sections');
        $section = Section::findOrFail($id);
        $section->delete();
        return redirect()->route('sections.index');
    }

    public function showStudents($id)
    {
        $section = Section::findOrFail($id);

        $hasStudentSectionColumn = Schema::hasColumn('db_students', 'section_id');
        $equivalentSectionIds = $this->getEquivalentSectionIds($section);

        $students = Student::query()
            ->leftJoin('db_class_schedules_student', 'db_students.id', '=', 'db_class_schedules_student.user_student_id')
            ->leftJoin('db_class_schedules', 'db_class_schedules_student.class_schedule_id', '=', 'db_class_schedules.id')
            ->where(function ($query) use ($id, $hasStudentSectionColumn, $equivalentSectionIds) {
                $query->whereIn('db_class_schedules.section_id', $equivalentSectionIds);

                if ($hasStudentSectionColumn) {
                    $query->orWhere('db_students.section_id', $id);
                }
            })
            ->select('db_students.*')
            ->distinct()
            ->with('user')
            ->orderBy('db_students.lname')
            ->orderBy('db_students.fname')
            ->get();

        return view('sections.students', compact('section', 'students'));
    }
}
