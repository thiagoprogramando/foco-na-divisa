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

                    <div class="col-12 d-flex justify-content-center flex-wrap gap-4 mb-5">
                        <div class="btn-toolbar demo-inline-spacing gap-2">
                            <div class="btn-group" role="group" aria-label="First group">
                                <a href="javascript:window.history.back()" class="btn btn-lg btn-outline-warning" title="Voltar"> <i class="tf-icons ri-arrow-left-line"></i></a>
                                <a href="{{ route('notebook', ['id' => $notebook->id]) }}" title="Editar Caderno" class="btn btn-lg btn-outline-info"><i class="ri-edit-box-line"></i></a>
                                <a href="{{ route('answer', ['notebook' => $notebook->id]) }}" title="Responder Caderno" class="btn btn-lg btn-outline-success"><i class="ri-menu-search-line"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-12 col-md-5 col-lg-5">
                        <div class="text-center">
                            <h5>Gráfico</h5>
                            <small>{{ $charts['general']['success'] + $charts['general']['error'] }} Resoluções</small>
                            <p>✅ Acertos: {{ $charts['general']['success'] }} x  ❌ Erros: {{ $charts['general']['error'] }}</p>
                        </div>
                        <div style="width: 250px; height: 250px; margin: 0 auto;">
                            <canvas id="generalChart"></canvas>
                        </div>
                    </div>

                    <div class="col-12 col-sm-12 col-md-7 col-lg-7">
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
                                                    {{ \Illuminate\Support\Str::limit(preg_replace('/[^A-Za-z0-9 ]/', '', strip_tags($question->question->title)), 50) }}
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            
            const generalCtx = document.getElementById('generalChart').getContext('2d');
            new Chart(generalCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Acertos', 'Erros'],
                    datasets: [{
                        data: [
                            {{ $charts['general']['percent_success'] }},
                            {{ $charts['general']['percent_error'] }}
                        ],
                        backgroundColor: ['#28a745', '#dc3545'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed + '%';
                                }
                            }
                        }
                    }
                }
            });
        </script>
@endsection