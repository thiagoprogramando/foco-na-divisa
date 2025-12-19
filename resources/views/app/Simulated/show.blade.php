@extends('app.layout')
@section('content')

    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-1">Simulado: {{ $simulated->title }}</h5>
                <div class="card-subtitle">
                    <div class="me-2">Compras: {{ $simulated->invoices->where('payment_status', 1)->count() }}</div>
                    <hr>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-5 col-lg-5 mb-5">
                        <h5 class="text-center">Dados</h5>
                        <form action="{{ route('updated-simulated', ['uuid' => $simulated->uuid]) }}" method="POST" class="row border g-3 p-3">
                            @csrf
                            <div class="col-12">
                                <div class="form-floating form-floating-outline mb-2">
                                    <input type="text" name="title" id="title" class="form-control" placeholder="Título" value="{{ $simulated->title }}"/>
                                    <label for="title">Título</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="date" name="date_start" id="date_start" class="form-control" placeholder="Data de início" value="{{ $simulated->date_start }}"/>
                                    <label for="date_start">Data de início</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="date" name="date_end" id="date_end" class="form-control" placeholder="Data de término" value="{{ $simulated->date_end }}"/>
                                    <label for="date_end">Data de término</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                <div class="form-floating form-floating-outline">
                                    <div class="select2-primary">
                                        <select name="status" id="status" class="select2 form-select">
                                            <option value="active" @selected($simulated->status == 'active')>Ativo</option>
                                            <option value="draft" @selected($simulated->status == 'draft')>Rascunho</option>
                                            <option value="completed" @selected($simulated->status == 'completed')>Concluído</option>
                                        </select>
                                    </div>
                                    <label for="status">Status</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="value" id="value" class="form-control money" oninput="maskValue(this)" placeholder="Valor (Mín R$ 5,00)" value="{{ $simulated->value }}"/>
                                    <label for="value">Valor (Mín R$ 5,00)</label>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <div class="form-floating form-floating-outline mb-2">
                                    <textarea class="form-control h-px-100" name="description" id="description" placeholder="Descrição">{{ $simulated->description }}</textarea>
                                    <label for="description">Descrição</label>
                                </div>
                                <div class="mb-4">
                                    <label for="cover_image" class="form-label">Imagem de Capa</label>
                                    <input class="form-control" type="file" name="cover_image" id="cover_image" accept="image/*">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 offset-md-3 col-lg-6 offset-lg-3 mb-2">
                                <div class="btn-group">
                                    <a href="{{ route('simulateds') }}" class="btn btn-outline-danger">Fechar</a>
                                    <button type="submit" class="btn btn-success">Atualizar</button>
                                </div>
                            </div>
                        </form>

                        <div class="text-center">
                            <h5>Gráfico</h5>
                           <small>{{ $charts['general']['success'] + $charts['general']['error'] }} Resoluções</small>
                            <p>✅ Acertos: {{ $charts['general']['success'] }} x  ❌ Erros: {{ $charts['general']['error'] }}</p>
                        </div>
                        <div style="width: 250px; height: 250px; margin: 0 auto;">
                            <canvas id="generalChart"></canvas>
                        </div>

                        <div class="text-center mt-5">
                            <h5>Ranking</h5>
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
                                                {{ $position->user->name }} @isset($position->user->address_state) / {{ $position->user->address_state }} @endisset
                                            </td>
                                            <td class="text-success fw-medium text-center">
                                                {{ $position->total_points }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-12 col-sm-12 col-md-7 col-lg-7">
                        <div class="table-responsive text-nowrap">
                            <table class="table border-bottom">
                                <thead>
                                    <tr>
                                        <th class="bg-transparent border-bottom">QUESTÃO</th>
                                        <th class="bg-transparent border-bottom">ORDEM</th>
                                        <th class="bg-transparent border-bottom text-center">RESPOSTAS</th>
                                        <th class="bg-transparent border-bottom text-center">ACERTOS X ERROS</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @foreach ($simulated->questions->sortBy('simulated_question_position') as $question)
                                        <tr>
                                            <td>
                                                <a data-bs-toggle="modal" data-bs-target="#detailsModal{{ $question->id }}">
                                                    {{ \Illuminate\Support\Str::limit(preg_replace('/[^A-Za-z0-9 ]/', '', strip_tags($question->title)), 40) }} <br>
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                {{ $question->simulated_question_position }}
                                            </td>
                                            <td class="text-center">
                                                {{ $question->simulatedQuestions->count() ?? 0 }}
                                            </td>
                                            <td class="fw-medium text-center">
                                                <span class="text-success">{{ $question->simulatedQuestions->where('answer_result', 1)->count() ?? 0 }}</span> X <span class="text-danger">{{ $question->simulatedQuestions->where('answer_result', 2)->count() ?? 0 }}</span>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="detailsModal{{ $question->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-fullscreen" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="exampleModalLabel1">Dados da Questão</h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        {!! $question->title !!}
                                                        @foreach ($question->alternatives as $alternative)
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
                                                        <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Fechar</button>
                                                        <a href="{{ route('question', ['id' => $question->id]) }}" target="_blank" class="btn btn-outline-warning">Editar Questão</a>
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