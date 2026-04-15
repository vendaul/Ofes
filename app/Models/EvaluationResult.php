<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_schedule_id',
        'area_code',
        'college_id',
        'period_id',
        'total_evaluations',
        'overall_average',
        'overall_rating',
        'category_averages',
        'question_averages',
        'last_updated'
    ];

    protected $casts = [
        'category_averages' => 'array',
        'question_averages' => 'array',
        'overall_average' => 'decimal:2',
        'last_updated' => 'datetime'
    ];

    public function classSchedule()
    {
        return $this->belongsTo(ClassSchedule::class, 'class_schedule_id', 'id');
    }

    /**
     * Find an evaluation result by class_schedule_id.
     */
    public static function findByClassScheduleId($scheduleId)
    {
        return self::where('class_schedule_id', $scheduleId)->first();
    }

    /**
     * Compute and update evaluation results for a class schedule assignment.
     */
    public static function updateResults($classScheduleId)
    {
        $schedule = ClassSchedule::find($classScheduleId);

        $evaluations = Evaluation::where('class_schedule_id', $classScheduleId)
            ->with(['answers.question'])
            ->get();

        if ($evaluations->isEmpty()) {
            self::where('class_schedule_id', $classScheduleId)->delete();
            return;
        }

        // Group answers by category and calculate averages
        $categoryData = [];
        $questionData = [];
        $allRatings = [];

        foreach ($evaluations as $evaluation) {
            foreach ($evaluation->answers as $answer) {
                $category = $answer->question->category;
                $questionText = $answer->question->question_text;
                $rating = $answer->rating;

                // Category data
                if (!isset($categoryData[$category])) {
                    $categoryData[$category] = [
                        'total_ratings' => 0,
                        'count' => 0
                    ];
                }
                $categoryData[$category]['total_ratings'] += $rating;
                $categoryData[$category]['count'] += 1;

                // Question data
                if (!isset($questionData[$questionText])) {
                    $questionData[$questionText] = [
                        'ratings' => [],
                        'category' => $category
                    ];
                }
                $questionData[$questionText]['ratings'][] = $rating;

                $allRatings[] = $rating;
            }
        }

        // Calculate category averages
        $categoryAverages = [];
        foreach ($categoryData as $category => $data) {
            $categoryAverages[$category] = round($data['total_ratings'] / $data['count'], 2);
        }

        // Calculate question averages
        $questionAverages = [];
        foreach ($questionData as $question => $data) {
            $questionAverages[$question] = [
                'average' => round(array_sum($data['ratings']) / count($data['ratings']), 2),
                'category' => $data['category'],
                'count' => count($data['ratings']),
                'ratings' => $data['ratings'],
            ];
        }

        // Calculate overall average and rating
        $overallAverage = round(array_sum($allRatings) / count($allRatings), 2);
        $overallRating = self::getOverallRating($overallAverage);

        // Update or create result
        self::updateOrCreate(
            ['class_schedule_id' => $classScheduleId],
            [
                'class_schedule_id' => $classScheduleId,
                'area_code'         => $schedule->area_code ?? null,
                'college_id'        => $schedule->college_id ?? null,
                'period_id'         => $schedule->period_id ?? null,
                'total_evaluations' => $evaluations->count(),
                'overall_average'   => $overallAverage,
                'overall_rating'    => $overallRating,
                'category_averages' => $categoryAverages,
                'question_averages' => $questionAverages,
                'last_updated'      => now()
            ]
        );
    }

    /**
     * Get overall rating based on average
     */
    private static function getOverallRating($average)
    {
        if ($average >= 4.5) {
            return 'Excellent';
        } elseif ($average >= 3.5) {
            return 'Good';
        } elseif ($average >= 2.5) {
            return 'Fair';
        } else {
            return 'Poor';
        }
    }
}
