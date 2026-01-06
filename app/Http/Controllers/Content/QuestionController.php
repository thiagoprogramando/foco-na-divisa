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
    
    public function index(Request $request, $topic) {

        $topic = Topic::find($topic);
        if (!$topic) {
            return redirect()->back()->with('infor', 'Não foi possível encontrar o Tópico, verifique os dados e tente novamente!');
        }
       
        $query = Question::where('topic_id', $topic->id)->orderBy('title', 'asc');

        if (!empty($request->input('title'))) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }

        if (!empty($request->input('board_id'))) {
            $query->where('board_id', $request->input('board_id'));
        }

        return view('app.Content.Question.list-questions', [
            'questions' => $query->paginate(30),
            'topic'     => $topic,
            'boards'    => Board::orderBy('name', 'asc')->get(),
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

    public function createForm($topic) {

        $topic = Topic::find($topic);
        if (!$topic) {
            return redirect()->back()->with('infor', 'Não foi possível encontrar o Tópico, verifique os dados e tente novamente!');
        }

        $boards     = Board::orderBy('name', 'asc')->get();
        $simulateds = Simulated::orderBy('title', 'asc')->get();
        
        return view('app.Content.Question.create-question', [
            'topic'         => $topic,
            'boards'        => $boards,
            'simulateds'    => $simulateds,
        ]);
    }

    public function store(Request $request, $topic) {

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
        $question->topic_id = $topic;
        $question->board_id = $request->board_id;
        $question->title                         = $request->title;
        $question->resolution                    = $request->resolution;
        $question->simulated_id                  = $request->simulated_id ?? null;
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

            return redirect()->back()->with('success', 'Questão criada com sucesso! Você pode continuar criando novas questões.');
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
