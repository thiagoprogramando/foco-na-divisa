<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopicController extends Controller {

    public function store(Request $request) {

        $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'status'       => 'required|string',
            'order'        => 'nullable|integer',
            'tags'         => 'nullable|string',
            'content_id'   => 'required|integer|exists:contents,id',
            'group_id'     => 'nullable|integer|exists:topic_groups,id',
        ]);

        $topic = new Topic();
        $topic->created_by    = Auth::user()->id;
        $topic->content_id    = $request->input('content_id');
        $topic->group_id      = $request->input('group_id');
        $topic->title         = $request->input('title');
        $topic->description   = $request->input('description');
        $topic->status        = $request->input('status');
        $topic->order         = $request->input('order') == 1 ? 1 : 2;
        $topic->tags          = $request->input('tags');

        if ($topic->save()) {
            return redirect()->back()->with('success', 'Conteúdo criado com sucesso!');
        } 

        return redirect()->back()->with('error', 'Falha ao criar Conteúdo, verifique os dados e tente novamente!');
    }

    public function update(Request $request, $id) {
        
        $topic = Topic::find($id);
        if (!$topic) {
            return redirect()->back()->with('infor', 'Não foi possível excluir o Tópico, verifique os dados e tente novamente!');
        }

        if (!empty($request->input('title'))) {
            $topic->title = $request->input('title');
        }

        if (!empty($request->input('description'))) {
            $topic->description = $request->input('description');
        }

        if (!empty($request->input('order'))) {
            $topic->order = $request->input('order');
        }

        if (!empty($request->input('status'))) {
            $topic->status = $request->input('status');
        }

        if (!empty($request->input('group_id'))) {
            $topic->group_id = $request->input('group_id');
        }
        
        if ($topic->save()) {
            return redirect()->back()->with('success', 'Tópico atualizado com sucesso!');
        }

        return redirect()->back()->with('error', 'Falha ao atualizar Tópico, verifique os dados e tente novamente!');
    }

    public function destroy($id) {
        
        $topic = Topic::find($id);
        if ($topic && $topic->delete()) {
            return redirect()->back()->with('success', 'Tópico excluído com sucesso!');
        }

        return redirect()->back()->with('infor', 'Não foi possível excluir o Tópico, verifique os dados e tente novamente!');
    }
}
