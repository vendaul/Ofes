<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function instructorResult($assign_id)
    {
        // Get assignment with relationships
        $assignment = \App\Models\ClassSchedule::with(['instructor', 'subject', 'section'])
            ->findOrFail($assign_id);

        // Ensure current user is the instructor
        if (auth()->id() !== $assignment->instructor_id) {
            abort(403, 'Unauthorized access to evaluation results.');
        }

        // Get pre-computed evaluation results (legacy and new key compatibility)
        $result = \App\Models\EvaluationResult::findByClassScheduleId($assign_id);

        if (!$result) {
            return view('reports.instructor_result', compact('assignment'))
                ->with('message', 'No evaluations found for this assignment.');
        }

        $evaluations = \App\Models\Evaluation::where('class_schedule_id', $assign_id)
            ->with(['answers.question'])->get();

        // Build per-question response count as fallback when evaluation_results has no count data
        $questionResponseCounts = [];
        foreach ($evaluations as $evaluation) {
            foreach ($evaluation->answers as $answer) {
                $questionText = $answer->question->question_text ?? null;
                if (!$questionText) {
                    continue;
                }
                if (!isset($questionResponseCounts[$questionText])) {
                    $questionResponseCounts[$questionText] = 0;
                }
                $questionResponseCounts[$questionText]++;
            }
        }

        // Prepare data for the view using pre-computed results
        $categoryAverages = $result->category_averages ?? [];
        $questionAverages = $result->question_averages ?? [];
        $overallAverage = $result->overall_average;
        $overallRating = $result->overall_rating;
        $totalEvaluations = $result->total_evaluations;

        // Reorganize question data for the view
        $categoryData = [];
        foreach ($questionAverages as $questionText => $data) {
            $category = $data['category'];
            if (!isset($categoryData[$category])) {
                $categoryData[$category] = [
                    'questions' => [],
                    'average' => $categoryAverages[$category] ?? 0
                ];
            }
            $categoryData[$category]['questions'][$questionText] = [
                'average' => $data['average'],
                'response_count' => $data['count'] ?? ($questionResponseCounts[$questionText] ?? 0),
                'ratings' => $data['ratings'] ?? []
            ];
        }

        return view('reports.instructor_result', compact(
            'assignment',
            'evaluations',
            'categoryData',
            'categoryAverages',
            'overallAverage',
            'overallRating',
            'totalEvaluations'
        ));
    }
}
