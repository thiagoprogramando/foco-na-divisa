<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Content;
use App\Models\Notebook;
use App\Models\NotebookQuestion;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller {
    
    public function index(Request $request) {
        
        $userId     = Auth::id();
        $questions  = Question::query();

       if (!empty($request->search)) {
            $search = trim(strip_tags($request->search));
            $words = preg_split('/\s+/', $search);

            $questions->where(function ($query) use ($words) {
                foreach ($words as $word) {
                    $query->whereRaw("LOWER(REGEXP_REPLACE(title, '<[^>]*>', '')) LIKE ?", ['%' . strtolower($word) . '%']);
                }
            });
        }

        if (!empty($request->topic_id) && $request->topic_id !== 'all') {
            $questions->where('topic_id', $request->topic_id);
        }

         if (!empty($request->filter) && $request->filter !== 'all') {
            switch ($request->filter) {
                case 'filter_favorited':
                    $questions->whereHas('favorites', function ($q) use ($userId) {
                        $q->where('user_id', $userId);
                    });
                    break;

                case 'filter_eliminated':
                    $questions->whereHas('notebookQuestions', function ($q) use ($userId) {
                        $q->where('user_id', $userId)
                        ->whereIn('answer_result', [1, 2]);
                    });
                    break;

                case 'filter_failer':
                    $questions->whereHas('notebookQuestions', function ($q) use ($userId) {
                        $q->where('user_id', $userId)
                        ->where('answer_result', 2);
                    });
                    break;

                case 'filter_success':
                    $questions->whereHas('notebookQuestions', function ($q) use ($userId) {
                        $q->where('user_id', $userId)
                        ->where('answer_result', 1);
                    });
                    break;
            }
        }

        return view('app.search', [
            'questions' => $questions->paginate(30),
            'search'    => $request->search,
            'topic_id'  => $request->topic_id,
            'topics'    => Topic::where('status', 'active')->orderBy('title')->get(),
            'notebooks' => Notebook::where('created_by', Auth::user()->id)->orderBy('title')->get(),
        ]);
    }

    public function store(Request $request) {
        
        $notebook = Notebook::where('id', $request->notebook_id)->where('created_by', Auth::user()->id)->first();
        if (!$notebook) {
            return redirect()->back()->with('error', 'Caderno não encontrado!');
        }

        $question = Question::find($request->question_id);
        if (!$question) {
            return redirect()->back()->with('error', 'Questão não encontrada!');
        }

        $lastPosition = NotebookQuestion::where('notebook_id', $notebook->id)->max('question_position');
        $nextPosition = $lastPosition ? $lastPosition + 1 : 1;

        $notebookQuestion                       = new NotebookQuestion();
        $notebookQuestion->notebook_id          = $notebook->id;
        $notebookQuestion->question_id          = $question->id;
        $notebookQuestion->user_id              = Auth::user()->id;
        $notebookQuestion->question_position    = $nextPosition;
        $notebookQuestion->created_at           = now();
        $notebookQuestion->updated_at           = now();
        if ($notebookQuestion->save()) {
            return redirect()->back()->with('success', 'Adicionado no caderno com sucesso!');
        }

        return redirect()->back()->with('error', 'Falha ao adicionar no caderno!');
    }
}
