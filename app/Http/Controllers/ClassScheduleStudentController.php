<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use App\Models\ClassScheduleStudent;
use App\Models\Student;
use Illuminate\Http\Request;

class ClassScheduleStudentController extends Controller
{
    /**
     * Return true if student is already enrolled in a different section for the same AY/term.
     */
    private function hasSectionConflict(int $studentId, ClassSchedule $targetSchedule): bool
    {
        return ClassScheduleStudent::where('user_student_id', $studentId)
            ->where('ay', $targetSchedule->ay)
            ->where('term', $targetSchedule->term)
            ->whereHas('classSchedule', function ($query) use ($targetSchedule) {
                $query->whereNotNull('section_id')
                    ->where('section_id', '!=', $targetSchedule->section_id);
            })
            ->exists();
    }

    /**
     * Show students enrolled in a class schedule
     */
    public function index($classScheduleId)
    {
        $classSchedule = ClassSchedule::with(['subject', 'section', 'instructor'])->findOrFail($classScheduleId);

        $students = ClassScheduleStudent::where('class_schedule_id', $classScheduleId)
            ->with('student')
            ->get();

        return view('class_schedules.students.index', compact('classSchedule', 'students'));
    }

    /**
     * Show form to add students to class schedule
     */
    public function create($classScheduleId)
    {
        $classSchedule = ClassSchedule::with(['subject', 'section', 'instructor'])->findOrFail($classScheduleId);
        
        // Get students currently IN the section but NOT in this class schedule
        $enrolledStudentIds = ClassScheduleStudent::where('class_schedule_id', $classScheduleId)
            ->pluck('user_student_id')
            ->toArray();

        // If section_id exists, get students from that section
        $availableStudents = [];
        if ($classSchedule->section_id) {
            $availableStudents = Student::whereNotIn('id', $enrolledStudentIds)
                ->get();
        }

        $sectionSubjectSchedules = ClassSchedule::with('subject')
            ->where('section_id', $classSchedule->section_id)
            ->where('ay', $classSchedule->ay)
            ->where('term', $classSchedule->term)
            ->where('year_level', $classSchedule->year_level)
            ->orderBy('subject_id')
            ->get();

        return view('class_schedules.students.create', compact('classSchedule', 'availableStudents', 'sectionSubjectSchedules'));
    }

    /**
     * Store student in class schedule
     */
    public function store(Request $request, $classScheduleId)
    {
        $classSchedule = ClassSchedule::findOrFail($classScheduleId);
        $mode = $request->input('mode', 'existing');
        $enrollAllSubjects = $request->boolean('enroll_all_subjects');
        $enrollSpecificSubjects = $request->boolean('enroll_specific_subjects');

        // If both are set, prioritize specific-subject selection.
        if ($enrollAllSubjects && $enrollSpecificSubjects) {
            $enrollAllSubjects = false;
        }

        if ($mode === 'new') {
            $request->validate([
                'sid'        => 'required|string|max:50|unique:db_students,sid',
                'lname'      => 'required|string|max:100',
                'fname'      => 'required|string|max:100',
                'mname'      => 'nullable|string|max:100',
                'email'      => 'nullable|email|max:255',
            ]);

            $student = Student::create([
                'sid'        => $request->sid,
                'lname'      => $request->lname,
                'fname'      => $request->fname,
                'mname'      => $request->mname,
                'email'      => $request->email,
                'area_code'  => $classSchedule->area_code ?? null,
                'has_account' => 'N',
            ]);

            $classStatus = 'P';
            $remark = 'ENROLLED';
        } else {
            $request->validate([
                'user_student_id' => 'required|exists:db_students,id',
                'class_status'    => 'required|in:P,A',
            ]);

            $student = Student::findOrFail($request->user_student_id);

            $classStatus = $request->class_status;
            $remark = $request->remark ?? 'ENROLLED';
        }

        if ($this->hasSectionConflict($student->id, $classSchedule)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Student is already enrolled in a different section for this subject.');
        }

        $targetScheduleIds = collect([$classSchedule->id]);
        if ($enrollAllSubjects || $enrollSpecificSubjects) {
            $allowedScheduleIds = ClassSchedule::where('section_id', $classSchedule->section_id)
                ->where('ay', $classSchedule->ay)
                ->where('term', $classSchedule->term)
                ->where('year_level', $classSchedule->year_level)
                ->pluck('id');

            if ($enrollAllSubjects) {
                $targetScheduleIds = $allowedScheduleIds->values();
            } elseif ($enrollSpecificSubjects) {
                $selectedScheduleIds = collect((array) $request->input('selected_schedule_ids', []))
                    ->map(fn ($id) => (int) $id)
                    ->filter(fn ($id) => $id > 0)
                    ->unique()
                    ->values();

                if ($selectedScheduleIds->isEmpty()) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Please select at least one subject to enroll.');
                }

                $targetScheduleIds = $allowedScheduleIds
                    ->intersect($selectedScheduleIds)
                    ->values();

                if ($targetScheduleIds->isEmpty()) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Selected subjects are not valid for this section/year level/term.');
                }
            }
        }

        $targetSchedules = ClassSchedule::with('subject')
            ->whereIn('id', $targetScheduleIds)
            ->get();

        $created = 0;
        foreach ($targetSchedules as $schedule) {
            $record = ClassScheduleStudent::firstOrCreate(
                [
                    'class_schedule_id' => $schedule->id,
                    'user_student_id' => $student->id,
                ],
                [
                    'area_code' => $student->area_code ?? $schedule->area_code,
                    'year_level' => $schedule->year_level,
                    'period_id' => $schedule->period_id,
                    'term' => $schedule->term,
                    'ay' => $schedule->ay,
                    'class_type' => $request->class_type ?? 'Regular',
                    'class_status' => $classStatus,
                    'subject_code' => $schedule->subject?->code ?? null,
                    'remark' => $remark,
                    'created_user_id' => auth()->id(),
                ]
            );

            if ($record->wasRecentlyCreated) {
                $created++;
            }
        }

        if ($created === 0) {
            return redirect()->back()->with('error', 'Student is already enrolled in the selected subject/s.');
        }

        return redirect()->route('class-schedule-students.index', $classScheduleId)
            ->with('success', $created > 1
                ? 'Student enrolled in selected subjects for this section.'
                : 'Student added to class schedule successfully.');
    }

    /**
     * Show edit form for a student's details
     */
    public function edit($classScheduleId, $classScheduleStudentId)
    {
        $classSchedule = ClassSchedule::with(['subject', 'section'])->findOrFail($classScheduleId);
        $enrollment = ClassScheduleStudent::findOrFail($classScheduleStudentId);

        if ($enrollment->class_schedule_id != $classScheduleId) {
            abort(403, 'Unauthorized');
        }

        $student = Student::findOrFail($enrollment->user_student_id);

        $sectionSubjectSchedules = ClassSchedule::with('subject')
            ->where('section_id', $classSchedule->section_id)
            ->where('ay', $classSchedule->ay)
            ->where('term', $classSchedule->term)
            ->where('year_level', $classSchedule->year_level)
            ->orderBy('subject_id')
            ->get();

        $selectedScheduleIds = ClassScheduleStudent::where('user_student_id', $student->id)
            ->whereIn('class_schedule_id', $sectionSubjectSchedules->pluck('id'))
            ->pluck('class_schedule_id')
            ->map(fn ($id) => (string) $id)
            ->toArray();

        return view('class_schedules.students.edit', compact('classSchedule', 'enrollment', 'student', 'sectionSubjectSchedules', 'selectedScheduleIds'));
    }

    /**
     * Update a student's details
     */
    public function update(Request $request, $classScheduleId, $classScheduleStudentId)
    {
        $classSchedule = ClassSchedule::with('subject')->findOrFail($classScheduleId);
        $enrollment = ClassScheduleStudent::findOrFail($classScheduleStudentId);

        if ($enrollment->class_schedule_id != $classScheduleId) {
            abort(403, 'Unauthorized');
        }

        $student = Student::findOrFail($enrollment->user_student_id);
        $applyAllSubjects = $request->boolean('apply_all_subjects');
        $applySpecificSubjects = $request->boolean('apply_specific_subjects');

        // If both are set, prioritize specific-subject selection.
        if ($applyAllSubjects && $applySpecificSubjects) {
            $applyAllSubjects = false;
        }

        $request->validate([
            'sid'   => 'required|string|max:50|unique:db_students,sid,' . $student->id,
            'lname' => 'required|string|max:100',
            'fname' => 'required|string|max:100',
            'mname' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
        ]);

        $student->update([
            'sid'   => $request->sid,
            'lname' => $request->lname,
            'fname' => $request->fname,
            'mname' => $request->mname,
            'email' => $request->email,
        ]);

        if ($applyAllSubjects || $applySpecificSubjects) {
            if ($this->hasSectionConflict($student->id, $classSchedule)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Student is already enrolled in a different section for this subject.');
            }

            $allowedScheduleIds = ClassSchedule::where('section_id', $classSchedule->section_id)
                ->where('ay', $classSchedule->ay)
                ->where('term', $classSchedule->term)
                ->where('year_level', $classSchedule->year_level)
                ->pluck('id');

            $targetScheduleIds = collect();
            if ($applyAllSubjects) {
                $targetScheduleIds = $allowedScheduleIds->values();
            } elseif ($applySpecificSubjects) {
                $selectedScheduleIds = collect((array) $request->input('selected_schedule_ids', []))
                    ->map(fn ($id) => (int) $id)
                    ->filter(fn ($id) => $id > 0)
                    ->unique()
                    ->values();

                if ($selectedScheduleIds->isEmpty()) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Please select at least one subject.');
                }

                $targetScheduleIds = $allowedScheduleIds
                    ->intersect($selectedScheduleIds)
                    ->values();

                if ($targetScheduleIds->isEmpty()) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Selected subjects are not valid for this section/year level/term.');
                }
            }

            $targetSchedules = ClassSchedule::with('subject')
                ->whereIn('id', $targetScheduleIds)
                ->get();

            foreach ($targetSchedules as $schedule) {
                ClassScheduleStudent::firstOrCreate(
                    [
                        'class_schedule_id' => $schedule->id,
                        'user_student_id' => $student->id,
                    ],
                    [
                        'area_code' => $student->area_code ?? $schedule->area_code,
                        'year_level' => $schedule->year_level,
                        'period_id' => $schedule->period_id,
                        'term' => $schedule->term,
                        'ay' => $schedule->ay,
                        'class_type' => $enrollment->class_type ?? 'Regular',
                        'class_status' => $enrollment->class_status ?? 'P',
                        'subject_code' => $schedule->subject?->code ?? null,
                        'remark' => $enrollment->remark ?? 'ENROLLED',
                        'created_user_id' => auth()->id(),
                    ]
                );
            }

            ClassScheduleStudent::where('user_student_id', $student->id)
                ->whereIn('class_schedule_id', $allowedScheduleIds)
                ->whereNotIn('class_schedule_id', $targetScheduleIds)
                ->delete();
        }

        return redirect()->route('class-schedule-students.index', $classScheduleId)
            ->with('success', ($applyAllSubjects || $applySpecificSubjects)
                ? 'Student details and subject enrollments updated successfully.'
                : 'Student updated successfully.');
    }

    /**
     * Remove student from class schedule
     */
    public function destroy($classScheduleId, $classScheduleStudentId)
    {
        $classScheduleStudent = ClassScheduleStudent::findOrFail($classScheduleStudentId);
        
        // Verify the student record belongs to this class schedule
        if ($classScheduleStudent->class_schedule_id != $classScheduleId) {
            abort(403, 'Unauthorized');
        }

        $classScheduleStudent->update(['deleted_user_id' => auth()->id()]);
        $classScheduleStudent->delete();

        return redirect()->route('class-schedule-students.index', $classScheduleId)
            ->with('success', 'Student removed from class schedule.');
    }

    /**
     * Bulk import students to class schedule
     */
    public function bulkStore(Request $request, $classScheduleId)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'required|exists:db_students,id',
            'class_type' => 'required|in:Regular,Irregular',
            'remark' => 'nullable|string|max:20'
        ]);

        $classSchedule = ClassSchedule::findOrFail($classScheduleId);
        $created = 0;
        $skipped = 0;

        foreach ($request->student_ids as $studentId) {
            if ($this->hasSectionConflict((int) $studentId, $classSchedule)) {
                $skipped++;
                continue;
            }

            // Check if already enrolled
            $exists = ClassScheduleStudent::where('class_schedule_id', $classScheduleId)
                ->where('user_student_id', $studentId)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            $student = Student::find($studentId);
            if (!$student) continue;

            ClassScheduleStudent::create([
                'area_code' => $student->area_code ?? null,
                'user_student_id' => $studentId,
                'year_level' => $classSchedule->year_level,
                'period_id' => $classSchedule->period_id,
                'term' => $classSchedule->term,
                'ay' => $classSchedule->ay,
                'class_type' => $request->class_type,
                'class_status' => 'P',
                'class_schedule_id' => $classScheduleId,
                'subject_code' => $classSchedule->subject?->code ?? null,
                'remark' => $request->remark ?? 'ENROLLED',
                'created_user_id' => auth()->id(),
            ]);

            $created++;
        }

        $message = "Added $created students";
        if ($skipped > 0) {
            $message .= " ($skipped skipped: already enrolled or section conflict)";
        }

        return redirect()->route('class-schedule-students.index', $classScheduleId)
            ->with('success', $message . '.');
    }
}
