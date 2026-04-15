<?php
namespace App\Http\Controllers;

use App\Models\QuestionTemplate;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class QuestionTemplateController extends Controller
{
    protected function isTemplateLocked(QuestionTemplate $questionTemplate): bool
    {
        $activeTemplateId = DB::table('system_settings')->where('key', 'active_evaluation_template_id')->value('value');
        $evaluationStartDate = DB::table('system_settings')->where('key', 'evaluation_start_date')->value('value');

        if ((int) $questionTemplate->id !== (int) $activeTemplateId) {
            return false;
        }

        if (empty($evaluationStartDate)) {
            return false;
        }

        if (Carbon::now()->lt(Carbon::parse($evaluationStartDate)->startOfDay())) {
            return false;
        }

        return Evaluation::query()->exists();
    }

    protected function templateLockedMessage(): string
    {
        return 'Evaluation has started, cant edit/delete.';
    }

    // List all templates for the current user
    public function index()
    {
        $templates = QuestionTemplate::where('user_id', Auth::id())->get();
        return view('question_templates.index', compact('templates'));
    }

    // Show form to create a new template
    public function create()
    {
        $activePeriodId = DB::table('system_settings')->where('key', 'active_period_id')->value('value');
        $activePeriod = $activePeriodId ? DB::table('db_periods')->find($activePeriodId) : null;

        return view('question_templates.create', compact('activePeriod'));
    }

    // Store a new template
    public function store(Request $request)
    {
        $activePeriodId = DB::table('system_settings')->where('key', 'active_period_id')->value('value');
        $activePeriod = $activePeriodId ? DB::table('db_periods')->find($activePeriodId) : null;

        if ($activePeriod) {
            $request->merge([
                'semester' => $activePeriod->term,
                'school_year' => $activePeriod->year,
            ]);
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array',
            'template_date' => 'nullable|date',
            'semester' => 'nullable|string|max:10',
            'school_year' => 'nullable|string|max:20',
        ]);

        $name = $request->name;
        if ($name) {
            $exists = QuestionTemplate::where('user_id', auth()->id())
                        ->where('name', $name)
                        ->exists();
            if ($exists) {
                return redirect()->route('question_templates.index')->with('error', 'A template with that name already exists.');
            }
        }
        if (empty($name)) {
            if ($request->semester && $request->school_year) {
                $name = trim($request->semester.' '.$request->school_year);
            } else {
                $name = 'Untitled Template';
            }
        }

        QuestionTemplate::create([
            'name' => $name,
            'description' => $request->description,
            'questions' => json_encode($request->questions),
            'user_id' => Auth::id(),
            'template_date' => $request->template_date,
            'semester' => $request->semester,
            'school_year' => $request->school_year,
        ]);

        return redirect()->route('question_templates.index')->with('success', 'Template created successfully.');
    }

    // Show form to edit a template
    public function edit(QuestionTemplate $questionTemplate)
    {
        // ensure user owns template
        if ($questionTemplate->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $activePeriodId = DB::table('system_settings')->where('key', 'active_period_id')->value('value');
        $activePeriod = $activePeriodId ? DB::table('db_periods')->find($activePeriodId) : null;

        return view('question_templates.edit', compact('questionTemplate', 'activePeriod'));
    }

    // Update a template
    public function update(Request $request, QuestionTemplate $questionTemplate)
    {
        // ensure user owns template
        if ($questionTemplate->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($this->isTemplateLocked($questionTemplate)) {
            return redirect()->route('questions.index')->with('error', $this->templateLockedMessage());
        }

        $activePeriodId = DB::table('system_settings')->where('key', 'active_period_id')->value('value');
        $activePeriod = $activePeriodId ? DB::table('db_periods')->find($activePeriodId) : null;

        if ($activePeriod) {
            $request->merge([
                'semester' => $activePeriod->term,
                'school_year' => $activePeriod->year,
            ]);
        }

        $request->validate([
            // name is no longer editable once created
            'description' => 'nullable|string',
            'questions' => 'sometimes|array',
            'template_date' => 'nullable|date',
            'semester' => 'nullable|string|max:10',
            'school_year' => 'nullable|string|max:20',
        ]);

        // do not modify name on update; keep original value
        $updateData = [
            'description' => $request->description,
            'template_date' => $request->template_date,
            'semester' => $request->semester,
            'school_year' => $request->school_year,
        ];

        if ($request->has('questions')) {
            $rawQuestions = $request->input('questions', []);
            $normalized = [];

            foreach ((array) $rawQuestions as $entry) {
                if (is_array($entry) && array_key_exists('question_text', $entry)) {
                    $questionText = trim((string) ($entry['question_text'] ?? ''));
                    if ($questionText === '') {
                        continue;
                    }

                    $normalized[] = [
                        'category' => trim((string) ($entry['category'] ?? 'General')) ?: 'General',
                        'question_text' => $questionText,
                    ];
                    continue;
                }

                if (is_array($entry)) {
                    $category = trim((string) ($entry['category'] ?? 'General')) ?: 'General';
                    $texts = $entry['question_text'] ?? null;

                    if (is_array($texts)) {
                        foreach ($texts as $text) {
                            $questionText = trim((string) $text);
                            if ($questionText !== '') {
                                $normalized[] = [
                                    'category' => $category,
                                    'question_text' => $questionText,
                                ];
                            }
                        }
                        continue;
                    }
                }

                if (is_string($entry)) {
                    $questionText = trim($entry);
                    if ($questionText !== '') {
                        $normalized[] = [
                            'category' => 'General',
                            'question_text' => $questionText,
                        ];
                    }
                }
            }

            $updateData['questions'] = json_encode($normalized);
        }

        $questionTemplate->update($updateData);

        $redirect = $request->input('return_url') ?: route('question_templates.index');
        return redirect($redirect)->with('success', 'Template updated successfully.');
    }

    // Delete a template
    public function destroy(QuestionTemplate $questionTemplate)
    {
        // Remove authorization check to allow deletion for the owner
        if ($questionTemplate->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($this->isTemplateLocked($questionTemplate)) {
            return redirect()->route('questions.index')->with('error', $this->templateLockedMessage());
        }

        $questionTemplate->delete();
        // return user to questions list rather than template index
        return redirect()->route('questions.index')->with('success', 'Template deleted successfully.');
    }
    
    // Show template details
    public function show($id)
    {
        $template = QuestionTemplate::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        $questions = json_decode($template->questions, true);
        return view('question_templates.show', compact('template', 'questions'));
    }
        // Store template from evaluation questions
        public function storeFromQuestions(Request $request)
        {
            if (!$request->has('questions') && $request->has('question_text')) {
                $category = trim((string) $request->input('category', 'General'));
                $texts = $request->input('question_text', []);
                $mappedQuestions = [];
                foreach ((array) $texts as $text) {
                    $text = trim((string) $text);
                    if ($text === '') {
                        continue;
                    }
                    $mappedQuestions[] = [
                        'category' => $category !== '' ? $category : 'General',
                        'question_text' => $text,
                    ];
                }
                $request->merge(['questions' => $mappedQuestions]);
            }

            // validation has been relaxed for "name" since we can infer or auto-generate it
            $request->validate([
                'name' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'questions' => 'required|array',
                'template_date' => 'nullable|date',
                'semester' => 'nullable|string|max:10',
                'school_year' => 'nullable|string|max:20',
            ]);

            // Normalize questions to always have category and question_text.
            // Supports both flat arrays and nested category blocks.
            $normalizedQuestions = [];
            $collectQuestions = null;
            $collectQuestions = function ($items) use (&$collectQuestions, &$normalizedQuestions) {
                foreach ((array) $items as $item) {
                    if (is_array($item) && array_key_exists('category', $item) && array_key_exists('question_text', $item)) {
                        $category = trim((string) ($item['category'] ?? 'General'));
                        $questionText = trim((string) ($item['question_text'] ?? ''));
                        if ($questionText === '') {
                            continue;
                        }
                        $normalizedQuestions[] = [
                            'category' => $category !== '' ? $category : 'General',
                            'question_text' => $questionText,
                        ];
                        continue;
                    }

                    if (is_array($item)) {
                        $collectQuestions($item);
                        continue;
                    }

                    if (is_string($item)) {
                        $text = trim($item);
                        if ($text === '') {
                            continue;
                        }
                        $normalizedQuestions[] = [
                            'category' => 'General',
                            'question_text' => $text,
                        ];
                    }
                }
            };

            $collectQuestions($request->questions);

            // fallback name generation if not provided
            $name = $request->name;
            if (empty($name)) {
                if ($request->semester && $request->school_year) {
                    $name = trim($request->semester.' '.$request->school_year);
                } else {
                    $name = 'Untitled Template';
                }
            }

            // avoid creating duplicate template with identical question set
            $jsonQuestions = json_encode($normalizedQuestions);
            $existing = QuestionTemplate::where('user_id', auth()->id())
                ->where('questions', $jsonQuestions)
                ->first();
            if ($existing) {
                return redirect()->route('questions.index')->with('error', 'A template with the same questions already exists.');
            }
            // also prevent name collision if name provided
            if ($request->name) {
                $nameExists = QuestionTemplate::where('user_id', auth()->id())
                    ->where('name', $name)
                    ->exists();
                if ($nameExists) {
                    return redirect()->route('questions.index')->with('error', 'A template with that name already exists.');
                }
            }
        $existing = QuestionTemplate::where('user_id', auth()->id())
            ->where('questions', $jsonQuestions)
            ->first();
        if ($existing) {
            return redirect()->route('questions.index')->with('error', 'A template with the same questions already exists.');
        }

        QuestionTemplate::create([
                'name' => $name,
                'description' => $request->description,
                'questions' => $jsonQuestions,
                'user_id' => auth()->id(),
                'template_date' => $request->template_date,
                'semester' => $request->semester,
                'school_year' => $request->school_year,
            ]);

            // after creating template, return to questions page with confirmation
            return redirect()->route('questions.index')->with('success', 'Evaluation form saved as template.');
        }
}
