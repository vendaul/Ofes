<?php

namespace App\Http\Controllers;

use App\Models\EvaluationQuestion;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class EvaluationQuestionController extends Controller
{
    /**
     * Lock question bank edits once submissions exist to preserve data integrity.
     */
    protected function isQuestionBankLocked(): bool
    {
        return Evaluation::query()->exists();
    }

    protected function questionBankLockedMessage(): string
    {
        return 'Question changes are locked because students have already submitted evaluations. Create a new evaluation version for future periods instead.';
    }

        public function index(Request $request)
    {
           // show earliest questions first (first created on top)
           $questions = EvaluationQuestion::orderBy('created_at', 'asc')->get();
           $templates = \App\Models\QuestionTemplate::where('user_id', auth()->id())->get();
            $isNewEvaluationMode = $request->boolean('new_evaluation');

           $evaluationStartDate = DB::table('system_settings')->where('key', 'evaluation_start_date')->value('value');
           $evaluationEndDate = DB::table('system_settings')->where('key', 'evaluation_end_date')->value('value');
           $activeTemplateId = DB::table('system_settings')->where('key', 'active_evaluation_template_id')->value('value');

           $lockedTemplateIds = [];
           if (!empty($activeTemplateId) && !empty($evaluationStartDate)) {
               $hasStarted = Carbon::now()->gte(Carbon::parse($evaluationStartDate)->startOfDay());
               $hasSubmissions = Evaluation::query()->exists();
               if ($hasStarted && $hasSubmissions) {
                   $lockedTemplateIds[] = (int) $activeTemplateId;
               }
           }

            return view('questions.index', compact('questions', 'templates', 'evaluationStartDate', 'evaluationEndDate', 'activeTemplateId', 'lockedTemplateIds', 'isNewEvaluationMode'));
    }

    // Use a template as the default evaluation form
    public function useTemplate(Request $request, $templateId)
    {
        $hasActiveSchedule = DB::table('system_settings')->where('key', 'evaluation_start_date')->exists()
            && DB::table('system_settings')->where('key', 'evaluation_end_date')->exists();

        if ($hasActiveSchedule) {
            return redirect()->route('questions.index')->with('error', 'Cannot change template while evaluation is active. Stop evaluation first.');
        }

        $template = \App\Models\QuestionTemplate::where('id', $templateId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $templateQuestions = json_decode($template->questions, true);

        // Select the template first; scheduling is handled via Start Evaluation.
        DB::table('system_settings')->updateOrInsert(
            ['key' => 'active_evaluation_template_id'],
            ['value' => (string)$template->id, 'updated_at' => now()]
        );
        DB::table('system_settings')
            ->whereIn('key', ['evaluation_start_date', 'evaluation_end_date'])
            ->delete();

        // Normalize template questions to array of ['category' => ..., 'question_text' => ...]
        $normalizedTemplateQuestions = [];
        foreach ($templateQuestions as $item) {
            if (is_array($item) && isset($item['category']) && isset($item['question_text'])) {
                $normalizedTemplateQuestions[] = [
                    'category' => $item['category'],
                    'question_text' => $item['question_text'],
                ];
            } elseif (is_string($item)) {
                $normalizedTemplateQuestions[] = [
                    'category' => 'General',
                    'question_text' => $item,
                ];
            }
        }

        if (empty($normalizedTemplateQuestions)) {
            return redirect()->route('questions.index')->with('error', 'Selected template has no questions.');
        }

        $snapshotRows = [];
        foreach ($normalizedTemplateQuestions as $item) {
            $question = \App\Models\EvaluationQuestion::firstOrCreate([
                'category' => $item['category'],
                'question_text' => $item['question_text'],
            ]);

            $snapshotRows[] = [
                'question_id' => (int) $question->question_id,
                'category' => (string) ($question->category ?? 'General'),
                'question_text' => (string) $question->question_text,
            ];
        }

        $activeQuestionSnapshot = collect($snapshotRows)->values()->toJson();

        DB::table('system_settings')->updateOrInsert(
            ['key' => 'active_evaluation_questions'],
            ['value' => $activeQuestionSnapshot, 'updated_at' => now()]
        );

        return redirect()->route('questions.index')->with('success', 'Template selected. Click Start Evaluation to set the schedule.');
    }

    public function reschedulePeriod(Request $request)
    {
        $request->validate([
            'template_id' => 'required|integer',
            'evaluation_start_date' => 'required|date',
            'evaluation_end_date' => 'required|date|after_or_equal:evaluation_start_date',
        ]);

        $template = \App\Models\QuestionTemplate::where('id', $request->template_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $startDate = Carbon::parse($request->evaluation_start_date)->startOfDay();
        $endDate = Carbon::parse($request->evaluation_end_date)->endOfDay();

        DB::table('system_settings')->updateOrInsert(
            ['key' => 'active_evaluation_template_id'],
            ['value' => (string) $template->id, 'updated_at' => now()]
        );
        DB::table('system_settings')->updateOrInsert(
            ['key' => 'evaluation_start_date'],
            ['value' => $startDate->toDateString(), 'updated_at' => now()]
        );
        DB::table('system_settings')->updateOrInsert(
            ['key' => 'evaluation_end_date'],
            ['value' => $endDate->toDateString(), 'updated_at' => now()]
        );

        return redirect()->route('questions.index')->with('success', 'Evaluation period rescheduled successfully.');
    }

    public function stopEvaluation(Request $request)
    {
        DB::table('system_settings')
            ->whereIn('key', ['evaluation_start_date', 'evaluation_end_date'])
            ->delete();

        return redirect()->route('questions.index')->with('success', 'Evaluation stopped. You can start it again anytime using Start Evaluation.');
    }

    public function create()
    {
        return view('questions.create');
    }

    public function store(Request $request)
    {
        if ($this->isQuestionBankLocked()) {
            return redirect()->route('questions.index')->with('error', $this->questionBankLockedMessage());
        }

        $category = $request->input('category');

        // if an array of inputs was submitted, use those directly
        if ($request->has('question_text') && is_array($request->input('question_text'))) {
            foreach ($request->input('question_text') as $text) {
                $text = trim($text);
                if ($text === '') {
                    continue;
                }
                EvaluationQuestion::create([
                    'category' => $category,
                    'question_text' => $text,
                ]);
            }
        } else {
            // fallback: single textarea where questions separated by newline
            $text = trim($request->input('question_text', ''));
            if (strpos($text, "\n") !== false) {
                $lines = preg_split('/\r?\n/', $text);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '') {
                        continue;
                    }
                    EvaluationQuestion::create([
                        'category' => $category,
                        'question_text' => $line,
                    ]);
                }
            } elseif ($text !== '') {
                EvaluationQuestion::create([
                    'category' => $category,
                    'question_text' => $text,
                ]);
            }
        }
        return redirect()->route('questions.index');
    }

    public function edit($id)
    {
        $question = EvaluationQuestion::findOrFail($id);
        return view('questions.edit', compact('question'));
    }

    public function update(Request $request, $id)
    {
        if ($this->isQuestionBankLocked()) {
            return redirect()->route('questions.index')->with('error', $this->questionBankLockedMessage());
        }

        $question = EvaluationQuestion::findOrFail($id);

        $category = $request->input('category');

        if ($request->has('question_text') && is_array($request->input('question_text'))) {
            // trim and remove empty entries
            $texts = array_filter(array_map('trim', $request->input('question_text')), function ($t) {
                return $t !== '';
            });

            if (count($texts) > 0) {
                // update the original question with the first text
                $question->update([
                    'question_text' => array_shift($texts),
                    'category' => $category,
                ]);

                // any remaining texts become new questions in same category
                foreach ($texts as $txt) {
                    EvaluationQuestion::create([
                        'category' => $category,
                        'question_text' => $txt,
                    ]);
                }
            }
        } else {
            $question->update($request->all());
        }

        return redirect()->route('questions.index');
    }

    public function destroy($id)
    {
        if ($this->isQuestionBankLocked()) {
            return redirect()->route('questions.index')->with('error', $this->questionBankLockedMessage());
        }

        $question = EvaluationQuestion::findOrFail($id);
        $question->delete();
        return redirect()->route('questions.index');
    }
}
