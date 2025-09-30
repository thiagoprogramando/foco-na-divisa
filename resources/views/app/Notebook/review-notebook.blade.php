@extends('app.layout')
@section('content')

    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-1">Caderno: {{ $notebook->title }}</h5>
                <div class="card-subtitle">
                    <div class="me-2">Questões Resolvidas {{ $notebook->questions->whereNotNull('resolved_at')->count() }}</div>
                    <small>
                        <b>Conteúdos:</b> @foreach ($notebook->contents as $content) {{ $content->title }}<span class="text-info">;</span> @endforeach<br>
                        <b>Tópicos:</b> @foreach ($notebook->topics as $topic) {{ $topic->title }}<span class="text-info">;</span> @endforeach<br>
                    </small>
                    <small><b>{{ $notebook->countQuestionsByStatus(1) }}</b> Resolvidas</small> <small class="text-success"><b>{{ $notebook->countQuestionsByStatus(1, 1) }}</b> Acertos</small> <small class="text-danger"><b>{{ $notebook->countQuestionsByStatus(1, 2) }}</b> Erros</small>
                    <hr>
                </div>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="table-responsive text-nowrap">
                            <table class="table border-top">
                                <thead>
                                    <tr>
                                        <th class="bg-transparent border-bottom">QUESTÃO</th>
                                        <th class="bg-transparent border-bottom text-center">SITUAÇÃO</th>
                                        <th class="bg-transparent border-bottom text-center">RESOLVIDA</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @foreach ($notebook->questions as $question)
                                        <tr>
                                            <td>
                                                <a href="{{ route('review-question', ['question' => $question->id, 'charts' => true]) }}" target="_blank">
                                                    {{ \Illuminate\Support\Str::limit(preg_replace('/[^A-Za-z0-9 ]/', '', strip_tags($question->question->title)), 70) }}
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                <div class="badge bg-label-{{ $question->labelResult()['color'] }} rounded-pill">{{ $question->labelResult()['message'] }}</div>
                                            </td>
                                            <td class="text-success fw-medium text-center">
                                                {{ $question->resolved_at ? \Carbon\Carbon::parse($question->resolved_at)->format('d/m/Y') : '' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>        
            </div>
        </div>      
    </div>
@endsection