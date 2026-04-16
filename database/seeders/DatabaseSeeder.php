<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EvaluationQuestion;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call(UsersTableSeeder::class);
        // Seed evaluation questions with categories
        $questions = [
            // Teaching Quality
            [
                'question_text' => 'The instructor explains concepts clearly and effectively.',
                'category' => 'Teaching Quality'
            ],
            [
                'question_text' => 'The instructor uses appropriate teaching methods and materials.',
                'category' => 'Teaching Quality'
            ],
            [
                'question_text' => 'The instructor provides helpful examples and illustrations.',
                'category' => 'Teaching Quality'
            ],
            [
                'question_text' => 'The instructor encourages student participation and questions.',
                'category' => 'Teaching Quality'
            ],

            // Communication Skills
            [
                'question_text' => 'The instructor communicates effectively with students.',
                'category' => 'Communication Skills'
            ],
            [
                'question_text' => 'The instructor provides clear feedback on assignments.',
                'category' => 'Communication Skills'
            ],
            [
                'question_text' => 'The instructor is approachable and available for consultation.',
                'category' => 'Communication Skills'
            ],

            // Course Organization
            [
                'question_text' => 'The course materials are well-organized and structured.',
                'category' => 'Course Organization'
            ],
            [
                'question_text' => 'The instructor follows the course syllabus and schedule.',
                'category' => 'Course Organization'
            ],
            [
                'question_text' => 'Assignments and assessments are fair and relevant.',
                'category' => 'Course Organization'
            ],

            // Overall Performance
            [
                'question_text' => 'Overall, I am satisfied with the instructor\'s performance.',
                'category' => 'Overall Performance'
            ],
            [
                'question_text' => 'I would recommend this instructor to other students.',
                'category' => 'Overall Performance'
            ]
        ];

        foreach ($questions as $question) {
            EvaluationQuestion::create($question);
        }
    }
}
