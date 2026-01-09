@extends('app.layout')
@section('content')

    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-1">Simulado: {{ $simulated->title }}</h5>
                <div class="card-subtitle">
                    <div class="me-2">Questões Resolvidas {{ $simulated->questions->whereNotNull('resolved_at')->count() }}</div>
                    <small><b>{{ $simulated->countQuestionsByStatus(1) }}</b> Resolvidas</small> <small class="text-success"><b>{{ $simulated->countQuestionsByStatus(1, 1) }}</b> Acertos</small> <small class="text-danger"><b>{{ $simulated->countQuestionsByStatus(1, 2) }}</b> Erros</small>
                    <hr>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-5 col-lg-5 mb-5">
                        <div class="d-flex justify-content-start flex-wrap gap-4">
                            <div class="btn-toolbar demo-inline-spacing gap-2">
                                <div class="btn-group" role="group" aria-label="First group">
                                    <a href="javascript:window.history.back()" class="btn btn-sm btn-outline-warning" title="Voltar"> <i class="tf-icons ri-arrow-left-line"></i> Voltar</a>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <h5>Gráfico</h5>
                           <small>{{ $charts['general']['success'] + $charts['general']['error'] }} Resoluções</small>
                            <p>✅ Acertos: {{ $charts['general']['success'] }} x  ❌ Erros: {{ $charts['general']['error'] }}</p>
                        </div>
                        <div style="width: 250px; height: 250px; margin: 0 auto;">
                            <canvas id="generalChart"></canvas>
                        </div>
                        @if ($simulated->date_end < now())
                            <div class="text-center mt-5">
                                <h5>Ranking</h5>
                                <div class="alert alert-success" role="alert">
                                    <a href="{{ route('simulated', ['uuid' => $simulated->uuid]) }}#{{ Auth::user()->uuid }}">Veja sua posição no ranking geral</a>
                                </div>
                            </div>
                            <div class="table-responsive text-nowrap">
                                <table class="table border-bottom">
                                    <thead>
                                        <tr>
                                            <th class="bg-transparent border-bottom text-center">POSIÇÃO</th>
                                            <th class="bg-transparent border-bottom">CANDIDATO</th>
                                            <th class="bg-transparent border-bottom text-center">PONTUAÇÃO</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach ($ranking as $position)
                                            <tr id="{{ $position->user->uuid }}">
                                                <td class="text-center">
                                                    {{ $position->position }}
                                                </td>
                                                <td>
                                                    @if (Auth::user()->id == $position->user->id)
                                                        {{ $position->user->name }} @isset($position->user->address_state) / {{ $position->user->address_state }} @endisset
                                                    @else
                                                        @php
                                                            $name       = $position->user->name;
                                                            $nameParts  = explode(' ', $name);
                                                            $firstName  = $nameParts[0] ?? '';
                                                            $lastName   = $nameParts[count($nameParts) - 1] ?? '';
                                                            $maskedName = mb_substr($firstName, 0, 2) . '***' . mb_substr($lastName, -2);
                                                        @endphp
                                                        {{ $maskedName }} @isset($position->user->address_state) / {{ $position->user->address_state }} @endisset
                                                    @endif
                                                </td>
                                                <td class="text-success fw-medium text-center">
                                                    {{ $position->total_points }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div class="col-12 col-sm-12 col-md-7 col-lg-7">
                        <div class="table-responsive text-nowrap">
                            <table class="table border-bottom">
                                <thead>
                                    <tr>
                                        <th class="bg-transparent border-bottom">QUESTÃO</th>
                                        <th class="bg-transparent border-bottom text-center">SELEÇÃO</th>
                                        <th class="bg-transparent border-bottom text-center">GABARITO</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @foreach ($simulated->simulatedAnswers->where('user_id', Auth::user()->id) as $question)
                                        <tr>
                                            <td>
                                                <a data-bs-toggle="modal" data-bs-target="#detailsModal{{ $question->id }}">
                                                    {{ \Illuminate\Support\Str::limit(preg_replace('/[^A-Za-z0-9 ]/', '', strip_tags($question->question->title)), 50) }} <br>
                                                    <div class="badge bg-label-{{ $question->labelResult()['color'] }} rounded-pill">{{ $question->labelResult()['message'] }}</div>
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                {{ $question->answer->label ?? ' ' }}
                                            </td>
                                            <td class="text-success fw-medium text-center">
                                                {{ $question->question->correctAlternative()->first()->label }}
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="detailsModal{{ $question->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-fullscreen" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="exampleModalLabel1">Dados do Conteúdo</h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        {!! $question->question->title !!}
                                                        @foreach ($question->question->alternatives as $alternative)
                                                            <div class="form-check mt-4 alternative-item">
                                                                <input class="form-check-input" type="checkbox" name="answer_id" value="{{ $alternative->id }}" id="answer_id{{ $alternative->id }}" @checked($alternative->is_correct) disabled>
                                                                <div class="alt-content">
                                                                    <span class="non-break">
                                                                        <label class="alt-short" for="answer_id{{ $alternative->id }}">{{ $alternative->label }})</label>
                                                                    </span>
                                                                    <label class="alt-long" for="answer_id{{ $alternative->id }}">{{ $alternative->text }}</label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="modal-footer btn-group">
                                                        <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal"> Fechar </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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