<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Group;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GroupController extends Controller {
    
    public function store(Request $request) {

        $content = Content::find($request->input('content_id'));
        if (!$content) {
            return redirect()->back()->with('infor', 'Não foi possível encontrar o Conteúdo, verifique os dados e tente novamente!');
        }

        $topicIds        = $request->input('topic_id', []);
        $alreadyGrouped  = Topic::whereIn('id', $topicIds)->whereNotNull('group_id')->pluck('id')->toArray();
        $availableTopics = array_diff($topicIds, $alreadyGrouped);

        if (empty($availableTopics) && !empty($topicIds)) {
            return redirect()->back()->with('infor', 'Todos os tópicos selecionados já pertencem a outros grupos!');
        }
        
        $group = New Group();
        $group->uuid       = Str::uuid();
        $group->title      = $request->input('title');
        $group->order      = $request->input('order');
        $group->topics     = collect($availableTopics)->mapWithKeys(fn($id) => ['topic_id_' . $id => $id])->toJson();
        $group->content_id = $request->input('content_id');

        if ($group->save()) {
            
            Topic::whereIn('id', $availableTopics)->update(['group_id' => $group->id]);

            $message = 'Grupo criado com sucesso!';
            if (count($alreadyGrouped) > 0) {
                $message .= ' Porém, os seguintes tópicos já pertenciam a outros grupos: ' . implode(', ', $alreadyGrouped);
            }

            return redirect()->back()->with('success', $message);
        } else {
            return redirect()->back()->with('error', 'Não foi possível criar o Grupo, verifique os dados e tente novamente!');
        }
    }

    public function update(Request $request, $uuid) {
        
        $group = Group::where('uuid', $uuid)->first();
        if (!$group) {
            return redirect()->back()->with('error', 'Grupo não encontrado!');
        }

        $group->title = $request->input('title');
        $group->order = $request->input('order');
        if ($group->save()) {
            return redirect()->back()->with('success', 'Grupo atualizado com sucesso!');
        } else {
            return redirect()->back()->with('error', 'Não foi possível atualizar o Grupo, verifique os dados e tente novamente!');
        }
    }

    public function destroy($uuid) {
        
        $group = Group::where('uuid', $uuid)->first();
        if (!$group) {
            return redirect()->back()->with('error', 'Grupo não encontrado!');
        }

        try {

            Topic::where('group_id', $group->id)->update(['group_id' => null]);
            $group->delete();

            return redirect()->back()->with('success', 'Grupo e seus vínculos foram removidos com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro ao excluir o grupo: ' . $e->getMessage());
        }
    }
}
