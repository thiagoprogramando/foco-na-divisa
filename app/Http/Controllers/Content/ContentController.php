<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ContentController extends Controller {
    
    public function index(Request $request) {

        $query = Content::withCount(['topics', 'questions'])->orderBy('title', 'asc');

        if (!empty($request->input('title'))) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }

        if (!empty($request->input('description'))) {
            $query->where('description', 'like', '%' . $request->input('description') . '%');
        }

        if (!empty($request->input('status'))) {
            $query->where('status', $request->input('status'));
        }

        $topicsCount    = (clone $query)->get()->sum('topics_count');
        $questionsCount = (clone $query)->get()->sum('questions_count');

        return view('app.Content.list-contents', [
            'contents'        => $query->paginate(30),
            'topicsCount'     => $topicsCount,
            'questionsCount'  => $questionsCount,
        ]);
    }

    public function show(Request $request, $id) {

        $content = Content::find($id);
        if (!$content) {
            return redirect()->back()->with('infor', 'Não foi possível encontrar o Conteúdo, verifique os dados e tente novamente!');
        }

        $query = Topic::where('content_id', $content->id);
        if (!empty($request->input('title'))) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }

        if (!empty($request->input('order'))) {
            $query->where('order', $request->input('order'));
        }

        if (!empty($request->input('status'))) {
            $query->where('status', $request->input('status'));
        }

        return view('app.Content.view-content', [
            'content' => $content,
            'topics'  => $query->orderBy('order', 'asc')->paginate(30),
        ]);
    }

    public function store(Request $request) {

        $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'status'       => 'required|string',
            'order'        => 'nullable|integer',
            'tags'         => 'nullable|string',
            'cover_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
        
        $content = new Content();
        $content->created_by    = Auth::user()->id;
        $content->title         = $request->input('title');
        $content->description   = $request->input('description');
        $content->status        = $request->input('status');
        $content->order         = $request->input('order') == 1 ? 1 : 2;
        $content->tags          = $request->input('tags');

        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('content-images', $filename, 'public');
            $content->cover_image = 'storage/content-images/' . $filename;
        }

        if ($content->save()) {
            return redirect()->back()->with('success', 'Conteúdo criado com sucesso!');
        } 

        return redirect()->back()->with('error', 'Falha ao criar Conteúdo, verifique os dados e tente novamente!');
    }

    public function update(Request $request, $id) {

        $content = Content::find($id);
        if (!$content) {
            return redirect()->back()->with('infor', 'Não foi possível encontrar o Conteúdo, verifique os dados e tente novamente!');
        }

        if (!empty($request->input('title'))) {
            $content->title = $request->input('title');
        }

        if (!empty($request->input('description'))) {
            $content->description = $request->input('description');
        }

        if (!empty($request->input('status'))) {
            $content->status = $request->input('status');
        }

        if (!empty($request->input('order'))) {
            $content->order = $request->input('order');
        }

        if (!empty($request->input('tags'))) {
            $content->tags = $request->input('tags');
        }

        if ($request->hasFile('cover_image')) {

            if ($content->cover_image && Storage::exists($content->cover_image)) {
                Storage::disk('public')->delete($content->cover_image);
            }

            $file     = $request->file('cover_image');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

            $file->storeAs('content-images', $filename, 'public');

            $content->cover_image = 'content-images/' . $filename;
        }

        if ($content->save()) {
            return redirect()->back()->with('success', 'Conteúdo atualizado com sucesso!');
        } 

        return redirect()->back()->with('error', 'Falha ao atualizar Conteúdo, verifique os dados e tente novamente!');
    }

    public function destroy($id) {
        
        $content = Content::find($id);
        if ($content && $content->delete()) {
            return redirect()->back()->with('success', 'Conteúdo excluído com sucesso!');
        }

        return redirect()->back()->with('infor', 'Não foi possível Excluir/ou Encontrar o Conteúdo, verifique os dados e tente novamente!');
    }
}
