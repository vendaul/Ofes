<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ClassSchedule;
use App\Models\Subject;
use App\Models\Section;
use App\Models\User;

class ClassScheduleController extends Controller
{
    public function index()
    {
        $schedules = ClassSchedule::with(['subject', 'section', 'instructor'])->paginate(15);
        return view('class_schedules.index', compact('schedules'));
    }

    public function create(Request $request)
    {
        $subjects = Subject::all();
        $sections = Section::all();
        $instructors = User::where('user_role', 2)->get(); // instructors

        $areaOptions = DB::table('areas')
            ->orderBy('area_name')
            ->pluck('area_name', 'area_code')
            ->toArray();

        $colleges = DB::table('db_colleges')
            ->orderBy('name')
            ->get();

        $selectedArea = $request->query('area_code', session('selected_area'));
        $selectedCollege = $request->query('college_id', session('selected_college'));

        if ((!$selectedArea || !$selectedCollege) && $request->filled('instructor_id')) {
            $instructor = User::find($request->input('instructor_id'));
            if ($instructor) {
                $selectedArea = $selectedArea ?: $instructor->areacode;
                $selectedCollege = $selectedCollege ?: $instructor->college;
            }
        }

        $activePeriodId = DB::table('system_settings')->where('key', 'active_period_id')->value('value');
        $activePeriod = $activePeriodId ? DB::table('db_periods')->find($activePeriodId) : null;

        return view('class_schedules.create', compact('subjects', 'sections', 'instructors', 'activePeriod', 'areaOptions', 'colleges', 'selectedArea', 'selectedCollege'));
    }

    public function store(Request $request)
    {
        $activePeriodId = DB::table('system_settings')->where('key', 'active_period_id')->value('value');
        $activePeriod = $activePeriodId ? DB::table('db_periods')->find($activePeriodId) : null;

        $request->merge([
            'area_code' => $request->input('area_code', session('selected_area')),
            'college_id' => $request->input('college_id', session('selected_college')),
        ]);

        if ($activePeriod) {
            $request->merge([
                'period_id' => $activePeriod->id,
                'ay' => $activePeriod->year,
                'term' => $activePeriod->term,
            ]);
        }

        $request->validate([
            'subject_name' => 'required|string|max:255',
            'section_name' => 'required|string|max:255',
            'instructor_id' => 'required|exists:users,id',
            'area_code' => 'nullable|string|max:15',
            'college_id' => 'nullable|exists:db_colleges,id',
            'period_id' => 'nullable|exists:db_periods,id',
            'schedule_code' => 'required|string|max:255',
            'ay' => 'nullable|string|max:255',
            'term' => 'nullable|string|max:255',
            'lec_week_day' => 'nullable|string',
            'lec_start_time' => 'nullable|date_format:H:i',
            'lec_end_time' => 'nullable|date_format:H:i',
            'lec_room_id' => 'nullable|string',
            'lab_week_day' => 'nullable|string',
            'lab_start_time' => 'nullable|date_format:H:i',
            'lab_end_time' => 'nullable|date_format:H:i',
            'lab_room_id' => 'nullable|string',
            'class_size' => 'nullable|integer',
            'year_level' => 'nullable|string',
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
                'area_code' => $request->area_code,
                'college_id' => $request->college_id,
                'name' => $sectionName,
                'year' => 1, // Default year level
            ]);
        }

        ClassSchedule::create([
            'area_code' => $request->area_code,
            'college_id' => $request->college_id,
            'period_id' => $request->period_id,
            'subject_id' => $subject->id,
            'section_id' => $section->id,
            'instructor_id' => $request->instructor_id,
            'schedule_code' => $request->schedule_code,
            'lec_week_day' => $request->lec_week_day,
            'lec_start_time' => $request->lec_start_time,
            'lec_end_time' => $request->lec_end_time,
            'lec_room_id' => $request->lec_room_id,
            'lab_week_day' => $request->lab_week_day,
            'lab_start_time' => $request->lab_start_time,
            'lab_end_time' => $request->lab_end_time,
            'lab_room_id' => $request->lab_room_id,
            'class_size' => $request->class_size,
            'year_level' => $request->year_level,
        ]);

        return redirect()->route('class-schedules.index')->with('success', 'Class schedule created successfully.');
    }

    public function show(ClassSchedule $classSchedule)
    {
        $classSchedule->load(['subject', 'section', 'instructor', 'students']);
        return view('class_schedules.show', compact('classSchedule'));
    }

    public function edit(ClassSchedule $classSchedule)
    {
        $subjects = Subject::all();
        $sections = Section::all();
        $instructors = User::where('user_role', 2)->get(); // instructors

        $areaOptions = DB::table('areas')
            ->orderBy('area_name')
            ->pluck('area_name', 'area_code')
            ->toArray();

        $colleges = DB::table('db_colleges')
            ->orderBy('name')
            ->get();

        $selectedArea = $classSchedule->area_code ?: request()->query('area_code', session('selected_area'));
        $selectedCollege = $classSchedule->college_id ?: request()->query('college_id', session('selected_college'));

        if ((!$selectedArea || !$selectedCollege) && $classSchedule->instructor) {
            $selectedArea = $selectedArea ?: $classSchedule->instructor->areacode;
            $selectedCollege = $selectedCollege ?: $classSchedule->instructor->college;
        }

        $activePeriodId = DB::table('system_settings')->where('key', 'active_period_id')->value('value');
        $activePeriod = $activePeriodId ? DB::table('db_periods')->find($activePeriodId) : null;

        return view('class_schedules.edit', compact('classSchedule', 'subjects', 'sections', 'instructors', 'activePeriod', 'areaOptions', 'colleges', 'selectedArea', 'selectedCollege'));
    }

    public function update(Request $request, ClassSchedule $classSchedule)
    {
        $activePeriodId = DB::table('system_settings')->where('key', 'active_period_id')->value('value');
        $activePeriod = $activePeriodId ? DB::table('db_periods')->find($activePeriodId) : null;

        if (!$activePeriod) {
            return back()->withInput()->with('error', 'No active period is configured. Please set an active period in Settings first.');
        }

        $request->merge([
            'period_id' => $activePeriod->id,
            'ay' => $activePeriod->year,
            'term' => $activePeriod->term,
        ]);

        $request->validate([
            'subject_name' => 'required|string|max:255',
            'section_name' => 'required|string|max:255',
            'area_code' => 'nullable|string|max:15',
            'college_id' => 'nullable|exists:db_colleges,id',
            'period_id' => 'required|exists:db_periods,id',
            'ay' => 'required|string|max:255',
            'term' => 'required|string|max:255',
        ]);

        // Trim all inputs to handle whitespace
        $data = $request->all();
        array_walk_recursive($data, function(&$value) {
            if (is_string($value)) {
                $value = trim($value);
            }
        });
        $request->merge($data);

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

        $classSchedule->update([
            'subject_id' => $subject->id,
            'section_id' => $section->id,
            'area_code' => $request->area_code,
            'college_id' => $request->college_id,
            'period_id' => $request->period_id,
            'ay' => $request->ay,
            'term' => $request->term,
        ]);

        return redirect()->route('instructors.workload', $classSchedule->instructor_id)->with('success', 'Class schedule updated successfully.');
    }

    public function destroy(ClassSchedule $classSchedule)
    {
        $instructorId = $classSchedule->instructor_id;
        $classSchedule->delete();

        if ($instructorId) {
            return redirect()->route('instructors.workload', $instructorId)
                ->with('success', 'Class schedule deleted successfully.');
        }

        return redirect()->route('class-schedules.index')
            ->with('success', 'Class schedule deleted successfully.');
    }
}
