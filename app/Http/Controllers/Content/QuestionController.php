<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Favorite;
use App\Models\Question;
use App\Models\Simulated;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller {
    
    public function index(Request $request) {

        $query = Question::query();

        $topic = null;
        if ($request->filled('topic')) {
            
            $topic = Topic::find($request->topic);
            if (!$topic) {
                return back()->with('info', 'Não foi possível encontrar o Tópico, verifique os dados e tente novamente!');
            }

            $query->where('topic_id', $topic->id);
        }

        $simulated = null;
        if ($request->filled('simulated')) {
            
            $simulated = Simulated::find($request->simulated);
            if (!$simulated) {
                return back()->with('info', 'Não foi possível encontrar o Simulado, verifique os dados e tente novamente!');
            }

            $query->where('simulated_id', $simulated->id);
        }

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->filled('board_id')) {
            $query->where('board_id', $request->board_id);
        }

        return view('app.Content.Question.list-questions', [
            'questions'  => $query->orderBy('title')->paginate(30),
            'topic'      => $topic,
            'simulated'  => $simulated,
            'boards'     => Board::orderBy('name')->get(),
        ]);
    }

    public function show($id) {

        $question = Question::find($id);
        if (!$question) {
            return redirect()->back()->with('infor', 'Não foi possível encontrar a Questão, verifique os dados e tente novamente!');
        }
        
        $boards     = Board::orderBy('name', 'asc')->get();
        $topics     = Topic::orderBy('title', 'asc')->get();
        $simulateds = Simulated::orderBy('title', 'asc')->get();

        return view('app.Content.Question.view-question', [
            'question'      => $question,
            'boards'        => $boards,
            'topics'        => $topics,
            'simulateds'    => $simulateds,
        ]);
    }

    public function createForm(Request $request) {

        $topic = null;
        if ($request->filled('topic')) {
            
            $topic = Topic::find($request->topic);
            if (!$topic) {
                return back()->with('info', 'Não foi possível encontrar o Tópico, verifique os dados e tente novamente!');
            }
        }

        $simulated = null;
        $nextOrder = 0;
        if ($request->filled('simulated')) {
            
            $simulated = Simulated::find($request->simulated);
            if (!$simulated) {
                return back()->with('info', 'Não foi possível encontrar o Simulado, verifique os dados e tente novamente!');
            }

            $lastOrder = Question::where('simulated_id', $simulated->id)->max('simulated_question_position');
            $nextOrder = $lastOrder + 1;
        }

        return view('app.Content.Question.create-question', [
            'topic'             => $topic,
            'boards'            => Board::orderBy('name')->get(),
            'simulateds'        => Simulated::orderBy('title')->get(),
            'simulatedSelect'   => $simulated,
            'nextOrder'         => $nextOrder,
        ]);
    }

    public function store(Request $request) {

        $request->validate([
            'title'         => 'required|string',
            'board_id'      => 'required|exists:boards,id',
            'alternative'   => 'required|array|min:2',
            'correct'       => 'required',
        ], [
            'title.required'    => 'É necessário informar um texto para a questão.',
            'alternative.min'   => 'Informe no mínimo duas alternativas.',
            'correct.required'  => 'Selecione uma alternativa como correta.',
        ]);

        if (is_array($request->correct) && count($request->correct) !== 1) {
            return back()->withErrors(['correct' => 'Apenas uma alternativa pode ser marcada como correta!'])->withInput();
        }

        $question           = new Question();
        $question->topic_id = $request->topic_id;
        $question->board_id = $request->board_id;
        $question->title                         = $request->title;
        $question->resolution                    = $request->resolution;
        $question->simulated_id                  = $request->simulated_id;
        $question->simulated_question_position   = $request->simulated_question_position ?? 0;
        if ($question->save()) {

            foreach ($request->alternative as $index => $text) {
                $label      = chr(65 + $index);
                $isCorrect  = in_array($index, $request->correct) ? 1 : 0;

                $question->alternatives()->create([
                    'label'         => $label,
                    'text'          => $text,
                    'is_correct'    => $isCorrect,
                ]);
            }

            return redirect()->route('create-question', ['topic' => $request->topic_id, 'simulated' => $request->simulated_id])->with('success', 'Questão criada com sucesso! Você pode continuar criando novas questões.');
        }

        return redirect()->back()->with('error', 'Falha ao criar a questão, tente novamente!');
    }

    public function update(Request $request, $id) {
        
        $request->validate([
            'title'             => 'required|string',
            'alternative'       => 'required|array|min:2',
            'correct'           => 'required|array|size:1',
            'alternative_id'    => 'nullable|array',
        ], [
            'title.required'      => 'É necessário informar um texto para a questão.',
            'alternative.required'  => 'Informe pelo menos duas alternativas.',
            'correct.required'      => 'Selecione uma alternativa correta.',
            'correct.size'          => 'Selecione exatamente uma alternativa correta.',
        ]);

        $question = Question::find($id);
        if (!$question) {
            return redirect()->back()->with('error', 'Questão não encontrada!');
        }

        $question->simulated_id = $request->simulated_id;
        $question->board_id     = $request->board_id;
        $question->topic_id     = $request->topic_id;
        $question->title        = $request->title;
        $question->resolution   = $request->resolution;
        $question->simulated_question_position   = $request->simulated_question_position;
        if ($question->save()) {

            $alternatives = $request->alternative;
            $alternativeIds = $request->alternative_id;
            $correctIndex = intval($request->correct[0]);

            $existingIds = $question->alternatives()->pluck('id')->toArray();
            $receivedIds = [];

            $labelIndex = 0;
            foreach ($alternatives as $index => $text) {
                $text = trim($text);
                $altId = $alternativeIds[$index] ?? null;
                $isCorrect = $index === $correctIndex;

                if ($text === '') {
                    if ($altId && in_array($altId, $existingIds)) {
                        $question->alternatives()->where('id', $altId)->delete();
                    }
                    continue;
                }

                $label = chr(65 + $labelIndex);

                if ($altId && in_array($altId, $existingIds)) {
                    $alt = $question->alternatives()->find($altId);
                    $alt->update([
                        'label' => $label,
                        'text' => $text,
                        'is_correct' => $isCorrect,
                    ]);
                    $receivedIds[] = $altId;
                } else {
                    $new = $question->alternatives()->create([
                        'label' => $label,
                        'text' => $text,
                        'is_correct' => $isCorrect,
                    ]);
                    $receivedIds[] = $new->id;
                }

                $labelIndex++;
            }

            $toDelete = array_diff($existingIds, $receivedIds);
            if (!empty($toDelete)) {
                $question->alternatives()->whereIn('id', $toDelete)->delete();
            }

            return redirect()->back()->with('success', 'Questão criada com sucesso! Você pode continuar criando novas questões.');
        }

        return redirect()->back()->with('error', 'Falha ao criar a questão, tente novamente!');
    }

    public function destroy($id) {

        $question = Question::find($id);
        if ($question && $question->delete()) {
            return redirect()->back()->with('success', 'Questão excluída com sucesso!');
        }

        return redirect()->back()->with('error', 'Falha ao excluir a questão, tente novamente!');
    }

    public function favorited($id) {

        $question = Question::find($id);
        if (!$question) {
            return redirect()->back()->with('infor', 'Falha ao favoritar a questão, tente novamente!'); 
        }

        $existing = Favorite::where('user_id', Auth::user()->id)->where('question_id', $id)->first();
        if ($existing &&  $existing->delete()) {
            return redirect()->back()->with('success', 'Questão removida dos favoritos!');
        } 

        $favorite               = new Favorite();
        $favorite->user_id      = Auth::user()->id;
        $favorite->question_id  = $id;
        if ($favorite->save()) {
            return redirect()->back()->with('success', 'Questão adicionada aos favoritos!');
        }

        return redirect()->back()->with('error', 'Falha ao favoritar a questão, tente novamente!');
    }
}
