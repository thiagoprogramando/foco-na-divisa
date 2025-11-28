<?php

namespace App\Http\Controllers\Simulated;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Simulated;
use App\Models\SimulatedQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnswerController extends Controller {

    public function show ($uuid, Request $request) {
        
        $simulated = Simulated::where('uuid', $uuid)->first();
        if (!$simulated) {
            return redirect()->back()->with('info', 'Simulado não encontrado!');
        }

        $allQuestions = SimulatedQuestion::where('user_id', Auth::user()->id)->where('simulated_id', $simulated->id)->orderBy('question_position')->get();
        if ($allQuestions->isEmpty()) {
            return redirect()->back()->with('info', 'Nenhuma questão foi gerada para este simulado!');
        }

        if ($allQuestions->firstWhere('answer_result', 0) === null) {
            return redirect()->route('simulated', ['uuid' => $simulated->uuid]);
        }

        $page = 1;
        if ($request->has('page')) {
            $page = (int) $request->page;
        } else {
            
            $nextPending = $allQuestions->firstWhere('answer_result', 0);
            if ($nextPending) {
                $index = $allQuestions->search(fn($q) => $q->id === $nextPending->id);
                $page = $index + 1;
            }
        }

        $questions = SimulatedQuestion::where('user_id', Auth::user()->id)->where('simulated_id', $simulated->id)->orderBy('question_position')->paginate(1, ['*'], 'page', $page);
        session(['answer' => true]);

        return view('app.Simulated.answer', [
            'simulated' => $simulated,
            'questions' => $questions,
        ]);
    }

    public function update(Request $request) {

        $simulatedQuestion = SimulatedQuestion::find($request->simulated_question_id);
        if (!$simulatedQuestion) {
            return redirect()->back()->with('infor', 'Questão não encontrada!');
        }

        $question = Question::find($simulatedQuestion->question_id);
        if (!$question) {
            return redirect()->back()->with('infor', 'Questão não encontrada!');
        }

        $answer_id = $request->input('answer_id');
        if (!$answer_id) {
            return redirect()->back()->with('infor', 'Você precisa selecionar uma alternativa.');
        }
        
        $isCorrect = $question->alternatives()->where('id', $answer_id)->where('is_correct', true)->exists();

        $simulatedQuestion->answer_id      = $answer_id;
        $simulatedQuestion->answer_result  = $isCorrect ? 1 : 2;
        // $simulatedQuestion->resolved_at = now();
        if ($simulatedQuestion->save()) {
            return redirect()->route('simulated', ['uuid' => $simulatedQuestion->simulated->uuid])->with('success', 'Resposta salva com sucesso!');
        }

        return redirect()->back()->with('infor', 'Erro ao salvar a resposta. Tente novamente!');
    }
}
