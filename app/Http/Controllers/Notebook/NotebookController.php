<?php

namespace App\Http\Controllers\Notebook;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Notebook;
use App\Models\NotebookQuestion;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotebookController extends Controller {
    
    public function index(Request $request) {

        $notebooks = Notebook::where('created_by', Auth::user()->id)->orderBy('created_at', 'desc')->paginate(10);
        return view('app.Notebook.list-notebooks', [
            'notebooks' => $notebooks
        ]);
    }

    public function show($id) {

        $notebook = Notebook::find($id);
        if (!$notebook) {
            return redirect()->back()->with('infor', 'Caderno não encontrado, verique os dados e tente novamente!');
        }

        $userId = Auth::id();

        $contents = Content::with([
            'topics' => function ($query) use ($userId) {
                $query->withCount([
                    'questions',
                    'questions as resolved_count' => function ($q) use ($userId) {
                        $q->whereHas('notebookQuestions', function ($nq) use ($userId) {
                            $nq->where('user_id', $userId)
                            ->where('answer_result', 1);
                        });
                    },
                    'questions as failer_count' => function ($q) use ($userId) {
                        $q->whereHas('notebookQuestions', function ($nq) use ($userId) {
                            $nq->where('user_id', $userId)
                            ->where('answer_result', 2);
                        });
                    }
                ]);
            }
        ])->orderBy('order', 'asc')->get();

        $filters = is_array($notebook->filters) ? $notebook->filters : json_decode($notebook->filters, true);

        return view('app.Notebook.view-notebook', [
            'notebook'  => $notebook,
            'contents'  => $contents,
            'filters'   => $filters,
        ]);
    }

    public function create() {

        $userId = Auth::id();

        $contents = Content::with([
            'topics' => function ($query) use ($userId) {
                $query->withCount([
                    'questions',
                    'questions as resolved_count' => function ($q) use ($userId) {
                        $q->whereHas('notebookQuestions', function ($nq) use ($userId) {
                            $nq->where('user_id', $userId)
                            ->where('answer_result', 1);
                        });
                    },
                    'questions as failer_count' => function ($q) use ($userId) {
                        $q->whereHas('notebookQuestions', function ($nq) use ($userId) {
                            $nq->where('user_id', $userId)
                            ->where('answer_result', 2);
                        });
                    }
                ]);
            }
        ])->orderBy('order', 'asc')->get();

        return view('app.Notebook.create-notebook', compact('contents'));
    }

    public function review(Request $request, $id) {
        
        $notebook = Notebook::find($id);
        if (!$notebook) {
            return redirect()->back()->with('infor', 'Caderno não encontrado, verique os dados e tente novamente!');
        }

        return view('app.Notebook.review-notebook', [
            'notebook'  => $notebook,
            'charts'    => true
        ]);
    }

    public function store(Request $request) {

        $request->validate([
            'title'             => 'required|string|max:255',
            'topics'            => 'required|string',
            'quanty_questions'  => 'required|integer|min:1',
        ]);

        $topicIds        = json_decode($request->input('topics'), true);
        $totalQuestions  = (int) $request->input('quanty_questions');
        $user_id         = Auth::id();

        $topicsCount = count($topicIds);
        if ($topicsCount === 0) {
            return redirect()->back()->with('infor', 'Nenhum tópico selecionado. Por favor, selecione pelo menos um tópico!');
        }

        $questionsPerTopic  = floor($totalQuestions / $topicsCount);
        $remaining          = $totalQuestions % $topicsCount;
        $filters            = $request->except(['title']);

        DB::beginTransaction();

        try {
            $notebook = Notebook::create([
                'title'         => $request->title,
                'created_by'    => $user_id,
                'filters'       => json_encode($filters),
                'status'        => 'draft',
            ]);

            $allSelectedQuestions   = [];
            $position               = 1;
            $remainingQuestions     = $totalQuestions;

            foreach ($topicIds as $index => $topicId) {
                
                $limit = min($questionsPerTopic + ($remaining-- > 0 ? 1 : 0), $remainingQuestions);

                $query = Question::where('topic_id', $topicId);

                if ($request->has('filter_resolved')) {
                    $query->whereDoesntHave('notebookQuestions', function ($q) use ($user_id) {
                        $q->where('user_id', $user_id)->where('answer_result', 1);
                    });
                }

                if ($request->has('filter_failer')) {
                    $query->whereHas('notebookQuestions', function ($q) use ($user_id) {
                        $q->where('user_id', $user_id)->where('answer_result', 2);
                    });
                }

                // if ($request->has('filter_favorites')) {
                //     $query->whereHas('favorites', function ($q) use ($user_id) {
                //         $q->where('user_id', $user_id);
                //     });
                // }

                $questions = $query->inRandomOrder()->limit($limit)->get();

                foreach ($questions as $question) {
                    $allSelectedQuestions[] = [
                        'notebook_id'       => $notebook->id,
                        'user_id'           => $user_id,
                        'question_id'       => $question->id,
                        'question_position' => $position++,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ];
                }

                $remainingQuestions -= $questions->count();
            }

            if ($remainingQuestions > 0) {
                $extraQuestions = Question::whereIn('topic_id', $topicIds)
                    ->whereNotIn('id', collect($allSelectedQuestions)->pluck('question_id'))
                    ->inRandomOrder()
                    ->limit($remainingQuestions)
                    ->get();

                foreach ($extraQuestions as $question) {
                    $allSelectedQuestions[] = [
                        'notebook_id'       => $notebook->id,
                        'user_id'           => $user_id,
                        'question_id'       => $question->id,
                        'question_position' => $position++,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ];
                }
            }

            NotebookQuestion::insert($allSelectedQuestions);
            DB::commit();

            return redirect()->route('answer', ['notebook' => $notebook->id])->with('success', count($allSelectedQuestions) . ' questões adicionadas ao caderno com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors('Erro ao criar caderno: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id) {

        $request->validate([
            'title'             => 'required|string|max:255',
            'topics'            => 'required|string',
            'quanty_questions'  => 'required|integer|min:1',
        ]);

        $topicIds        = json_decode($request->input('topics'), true);
        $totalQuestions  = (int) $request->input('quanty_questions');
        $user_id         = Auth::id();

        $topicsCount = count($topicIds);
        if ($topicsCount === 0) {
            return redirect()->back()->with('infor', 'Nenhum tópico selecionado. Por favor, selecione pelo menos um tópico!');
        }

        $questionsPerTopic  = floor($totalQuestions / $topicsCount);
        $remaining          = $totalQuestions % $topicsCount;
        $filters            = $request->except(['title']);

        DB::beginTransaction();

        try {
            $notebook = Notebook::findOrFail($id);

            $notebook->update([
                'title'   => $request->title,
                'filters' => json_encode($filters),
                'status'  => 'draft',
            ]);

            NotebookQuestion::where('notebook_id', $notebook->id)->delete();

            $allSelectedQuestions = [];
            $position             = 1;
            $remainingQuestions   = $totalQuestions;

            foreach ($topicIds as $index => $topicId) {
                $limit = min($questionsPerTopic + ($remaining-- > 0 ? 1 : 0), $remainingQuestions);

                $query = Question::where('topic_id', $topicId);

                if ($request->has('filter_resolved')) {
                    $query->whereDoesntHave('notebookQuestions', function ($q) use ($user_id) {
                        $q->where('user_id', $user_id)->where('answer_result', 1);
                    });
                }

                if ($request->has('filter_failer')) {
                    $query->whereHas('notebookQuestions', function ($q) use ($user_id) {
                        $q->where('user_id', $user_id)->where('answer_result', 2);
                    });
                }

                $questions = $query->inRandomOrder()->limit($limit)->get();

                foreach ($questions as $question) {
                    $allSelectedQuestions[] = [
                        'notebook_id'       => $notebook->id,
                        'user_id'           => $user_id,
                        'question_id'       => $question->id,
                        'question_position' => $position++,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ];
                }

                $remainingQuestions -= $questions->count();
            }

            if ($remainingQuestions > 0) {
                $extraQuestions = Question::whereIn('topic_id', $topicIds)
                    ->whereNotIn('id', collect($allSelectedQuestions)->pluck('question_id'))
                    ->inRandomOrder()
                    ->limit($remainingQuestions)
                    ->get();

                foreach ($extraQuestions as $question) {
                    $allSelectedQuestions[] = [
                        'notebook_id'       => $notebook->id,
                        'user_id'           => $user_id,
                        'question_id'       => $question->id,
                        'question_position' => $position++,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ];
                }
            }

            NotebookQuestion::insert($allSelectedQuestions);
            DB::commit();

            return redirect()
                ->route('answer', ['notebook' => $notebook->id])
                ->with('success', count($allSelectedQuestions) . ' questões adicionadas ao caderno com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors('Erro ao atualizar caderno: ' . $e->getMessage());
        }
    }

    public function destroy($id) {
        
        $notebook = Notebook::find($id);
        if ($notebook && $notebook->delete()) {
            return redirect()->back()->with('success', 'Caderno excluído com sucesso!');
        }

        return redirect()->back()->with('infor', 'Não foi possível Excluir/ou Encontrar o Caderno, verifique os dados e tente novamente!');
    }
}
