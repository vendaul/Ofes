<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\ClassSchedule;
use App\Models\TeachingAssignment;
use App\Models\EvaluationQuestion;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;

class EvaluationApiController extends Controller
{
    public function assignments()
    {
        $student = Student::where('account_id', Auth::id())->first();

        // Since students are not directly linked to sections in the current schema,
        // return all class schedule-based assignments.
        return ClassSchedule::with(['instructor','subject'])
            ->get();
    }

    public function questions()
    {
        return EvaluationQuestion::all();
    }

    public function submit(Request $request)
    {
        $student = Student::where('account_id', Auth::id())->first();

        // PREVENT DUPLICATE
        $scheduleId = $request->input('class_schedule_id') ?: $request->input('assign_id');

        $exists = Evaluation::where('student_id', $student->id)
            ->where('class_schedule_id', $scheduleId)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Already evaluated.'], 403);
        }

        $schedule = ClassSchedule::find($scheduleId);
        if (!$schedule) {
            return response()->json(['message' => 'Schedule not found.'], 404);
        }

        $evaluation = Evaluation::create([
            'student_id'        => $student->id,
            'class_schedule_id' => $schedule->id,
            'area_code'         => $schedule->area_code,
            'college_id'        => $schedule->college_id,
            'period_id'         => $schedule->period_id,
            'evaluator_type'    => 'student',
            'submitted_by'      => null,
            'date_submitted'    => now()
        ]);

        foreach($request->ratings as $question_id => $rating){
            EvaluationAnswer::create([
                'eval_id' => $evaluation->eval_id,
                'question_id' => $question_id,
                'rating' => $rating
            ]);
        }

        EvaluationResult::updateResults($schedule->id);

        return response()->json(['message' => 'Submitted']);
    }
}
