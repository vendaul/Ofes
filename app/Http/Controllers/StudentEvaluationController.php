<?php

namespace App\Http\Controllers;

use App\Models\EvaluationQuestion;
use App\Models\Student;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;
use App\Models\EvaluationResult;
use App\Models\ClassSchedule;
use App\Models\College;
use App\Models\TeachingAssignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StudentEvaluationController extends Controller
{
    protected function getActiveEvaluationQuestions()
    {
        $raw = DB::table('system_settings')->where('key', 'active_evaluation_questions')->value('value');
        $decoded = json_decode((string) $raw, true);

        if (is_array($decoded) && count($decoded) > 0) {
            return collect($decoded)
                ->map(function ($q) {
                    return [
                        'question_id' => (int) ($q['question_id'] ?? 0),
                        'category' => (string) ($q['category'] ?? 'General'),
                        'question_text' => (string) ($q['question_text'] ?? ''),
                    ];
                })
                ->filter(function ($q) {
                    return $q['question_id'] > 0 && $q['question_text'] !== '';
                })
                ->values();
        }

        return EvaluationQuestion::orderBy('question_id', 'asc')
            ->get(['question_id', 'category', 'question_text'])
            ->map(function ($q) {
                return [
                    'question_id' => (int) $q->question_id,
                    'category' => (string) ($q->category ?? 'General'),
                    'question_text' => (string) $q->question_text,
                ];
            })
            ->values();
    }

    protected function resolveStudentIdForAuthUser()
    {
        // Evaluations reference db_students.id.
        $dbStudent = DB::table('db_students')->where('account_id', Auth::id())->first();
        if ($dbStudent) {
            return $dbStudent->id;
        }

        // Legacy fallback: create or find mapping in db_students from existing Student model.
        $student = Student::where('account_id', Auth::id())->first();
        if (!$student) {
            return null;
        }

        $newDbStudentId = DB::table('db_students')->insertGetId([
            'account_id' => Auth::id(),
            'fname' => $student->fname,
            'lname' => $student->lname,
            'section_id' => $student->section_id ?? 1,
            'sid' => $student->sid,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $newDbStudentId;
    }

    public function show($class_schedule_id)
    {
        $questions = $this->getActiveEvaluationQuestions()->map(function ($q) {
            return (object) $q;
        });
        $classSchedule = ClassSchedule::with(['instructor', 'subject', 'section'])->find($class_schedule_id);
        $collegeName = null;

        if ($classSchedule && $classSchedule->college_id) {
            $collegeName = College::where('id', $classSchedule->college_id)->value('name');
        }

        return view('students.evaluate', compact('questions', 'class_schedule_id', 'classSchedule', 'collegeName'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_schedule_id' => 'required|integer',
            'ratings' => 'required|array',
            'ratings.*' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $activeQuestions = $this->getActiveEvaluationQuestions();
        if ($activeQuestions->isEmpty()) {
            return back()->with('error', 'Active evaluation template has no questions configured.');
        }

        $expectedQuestionIds = $activeQuestions->pluck('question_id')->map(function ($id) {
            return (string) $id;
        })->all();

        $submittedQuestionIds = collect(array_keys($request->input('ratings', [])))->map(function ($id) {
            return (string) $id;
        })->all();

        $missingQuestionIds = array_diff($expectedQuestionIds, $submittedQuestionIds);
        if (!empty($missingQuestionIds)) {
            return back()->with('error', 'Please answer all questions from the active evaluation template.');
        }

        $scheduleId = $request->input('class_schedule_id');

        $studentId = $this->resolveStudentIdForAuthUser();

        if (!$studentId) {
            return back()->with('error', 'Student record not found.');
        }

        $evaluationStartDate = DB::table('system_settings')->where('key', 'evaluation_start_date')->value('value');
        $evaluationEndDate = DB::table('system_settings')->where('key', 'evaluation_end_date')->value('value');

        if (!$evaluationStartDate || !$evaluationEndDate) {
            return back()->with('error', 'Evaluation period is not configured.');
        }

        $now = Carbon::now();
        $start = Carbon::parse($evaluationStartDate)->startOfDay();
        $end = Carbon::parse($evaluationEndDate)->endOfDay();

        if ($now->lt($start)) {
            return back()->with('error', 'Evaluation is not yet open.');
        }

        if ($now->gt($end)) {
            return back()->with('error', 'Evaluation period has ended.');
        }

        $schedule = ClassSchedule::find($scheduleId);
        if (!$schedule) {
            return back()->with('error', 'Class schedule not found.');
        }

        // Validate enrollment through the canonical class schedule student pivot table.
        $enrolled = \App\Models\ClassScheduleStudent::where('class_schedule_id', $scheduleId)
            ->where('user_student_id', $studentId)
            ->exists();

        if (!$enrolled) {
            return back()->with('error', 'You are not enrolled in this class schedule.');
        }

        // One evaluation per student per class schedule.
        if (Evaluation::where('student_id', $studentId)
            ->where('class_schedule_id', $scheduleId)
            ->exists()) {
            return back()->with('error', 'You already submitted evaluation for this class schedule.');
        }

        $evaluation = Evaluation::create([
            'student_id'        => $studentId,
            'class_schedule_id' => $schedule->id,
            'area_code'         => $schedule->area_code,
            'college_id'        => $schedule->college_id,
            'period_id'         => $schedule->period_id,
            'evaluator_type'    => 'student',
            'submitted_by'      => null,
            'date_submitted'    => now(),
            'comment'           => $request->comment
        ]);

        $activeQuestionsById = $activeQuestions->keyBy('question_id');

        foreach ($request->ratings as $question_id => $rating) {
            $questionIdInt = (int) $question_id;
            $questionFromSnapshot = $activeQuestionsById->get($questionIdInt);
            if (!$questionFromSnapshot) {
                continue;
            }

            $questionRecord = EvaluationQuestion::find($questionIdInt);
            if (!$questionRecord) {
                $questionRecord = EvaluationQuestion::create([
                    'category' => $questionFromSnapshot['category'],
                    'question_text' => $questionFromSnapshot['question_text'],
                ]);
            }

            EvaluationAnswer::create([
                'eval_id' => $evaluation->eval_id,
                'question_id' => $questionRecord->question_id,
                'rating' => $rating
            ]);
        }

        EvaluationResult::updateResults($schedule->id);

        return redirect()->route('student.dashboard')
            ->with('success', 'Evaluation submitted successfully.');
    }
}