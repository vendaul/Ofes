<?php

namespace App\Http\Controllers;

use App\Models\EvaluationAnswer;
use Illuminate\Http\Request;

class EvaluationAnswerController extends Controller
{
    public function index()
    {
        $evaluationAnswers = EvaluationAnswer::all();
        return view('evaluation-answers.index', compact('evaluationAnswers'));
    }

    public function create()
    {
        return view('evaluation-answers.create');
    }

    public function store(Request $request)
    {
        EvaluationAnswer::create($request->all());
        return redirect()->route('evaluation-answers.index');
    }

    public function show($id)
    {
        $evaluationAnswer = EvaluationAnswer::findOrFail($id);
        return view('evaluation-answers.show', compact('evaluationAnswer'));
    }

    public function edit($id)
    {
        $evaluationAnswer = EvaluationAnswer::findOrFail($id);
        return view('evaluation-answers.edit', compact('evaluationAnswer'));
    }

    public function update(Request $request, $id)
    {
        $evaluationAnswer = EvaluationAnswer::findOrFail($id);
        $evaluationAnswer->update($request->all());
        return redirect()->route('evaluation-answers.index');
    }

    public function destroy($id)
    {
        $evaluationAnswer = EvaluationAnswer::findOrFail($id);
        $evaluationAnswer->delete();
        return redirect()->route('evaluation-answers.index');
    }
}