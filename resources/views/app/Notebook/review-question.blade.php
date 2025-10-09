@extends('app.layout')
@section('content')

    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card mb-3">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h5 class="mb-1">Caderno: {{ $notebook->title }}</h5>
                    <div class="dropdown">
                        <button class="btn btn-text-secondary rounded-pill text-muted border-0 p-1 waves-effect waves-light" type="button" id="salesOverview" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-more-2-line ri-20px"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="salesOverview">
                            <button type="button" class="dropdown-item waves-effect" onclick="location.reload(true)">Atualizar</button>
                        </div>
                    </div>
                </div>
                <div class="card-subtitle">
                    
                    <div class="me-2">Questão {{ $question->question_position.' de '.$notebook->questions->count() }}</div>
                    <small>
                        <b>Conteúdo/Tópico:</b> {{ $question->question->topic->content->title }} | {{ $question->question->topic->title }}<br>
                        <b>Banca:</b> {{ $question->question->board->code.' '.$question->question->board->name.' - '.$question->question->board->state .'/'.$question->question->board->city }} <br>
                    </small>
                    
                    <small><b>{{ $notebook->countQuestionsByStatus(1) }}</b> Resolvidas</small> <small class="text-success"><b>{{ $notebook->countQuestionsByStatus(1, 1) }}</b> Acertos</small> <small class="text-danger"><b>{{ $notebook->countQuestionsByStatus(1, 2) }}</b> Erros</small>
                </div>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-12 d-flex justify-content-center flex-wrap gap-4">
                        <div class="btn-toolbar demo-inline-spacing gap-2">
                            <div class="btn-group" role="group" aria-label="First group">
                                <a href="javascript:window.history.back()" class="btn btn-lg btn-outline-warning" title="Voltar"> <i class="tf-icons ri-arrow-left-line"></i></a>
                                <a href="{{ route('review-question', ['question' => $question->id, 'charts' => true]) }}" class="btn btn-lg btn-outline-info" title="Estátisticas da Questão"> <i class="tf-icons ri-pie-chart-line"></i></a>
                                <a href="{{ route('notebook', ['id' => $notebook->id]) }}" title="Editar Caderno" class="btn btn-lg btn-outline-secondary"> <i class="tf-icons ri-filter-3-line"></i> </a>
                                <button type="button" class="btn btn-lg btn-outline-danger" title="Alertar Problema" data-bs-toggle="modal" data-bs-target="#createdTicketModal"> <i class="tf-icons ri-alarm-warning-line"></i></button>
                                <button type="button" class="btn btn-lg btn-outline-info" title="Comentários do Professor" data-bs-toggle="collapse" href="#collapseTeacher" role="button" aria-expanded="false" aria-controls="collapseTeacher">
                                    <i class="tf-icons ri-chat-quote-line"></i>
                                </button>
                                <button type="button" class="btn btn-lg btn-outline-success" title="Comentários da Questão" data-bs-toggle="collapse" href="#collapseComments" role="button" aria-expanded="false" aria-controls="collapseComments">
                                    <i class="tf-icons ri-chat-1-line"></i>
                                </button>
                                <a href="{{ route('favorited-question', ['id' => $question->question->id]) }}" class="btn btn-lg btn-outline-secondary" title="Favoritar"> <i class="tf-icons {{ $question->question->isFavorited() ? 'ri-heart-fill text-danger' : 'ri-heart-line' }}"></i> </a>
                            </div>
                        </div>
                    </div>

                    <div class="collapse mt-2 mb-3" id="collapseTeacher">
                        <div class="p-4 border">
                            {!! $question->question->resolution ?? 'Nenhum comentário do Professor!' !!}
                        </div>
                    </div>

                    <div class="collapse mt-2 mb-3" id="collapseComments">
                        <div class="row p-4 border">
                            <form action="{{ route('created-comment') }}" method="POST" class="col-12 col-sm-12 col-md-6 col-lg-6">
                                @csrf
                                <input type="hidden" name="question_id" value="{{ $question->question->id }}">
                                <div class="form-floating form-floating-outline mb-2">
                                    <textarea class="form-control h-px-100 editor-simple" name="comment" id="comment" placeholder="Deixe seu comentário:" required></textarea>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-outline-dark">Comentar</button>
                                </div>
                            </form>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="divider text-start-center">
                                    <div class="divider-text">
                                        {{ $question->question->comments->count() > 0 ? 'Últimos comentários' : 'Seja o primeiro a comentar algo!' }}
                                    </div>
                                </div>
                                
                                <div class="card-body">
                                    @foreach ($question->question->comments as $comment)
                                        <small class="text-light fw-medium">{{ $comment->user->name }} <cite title="Comentário feito em {{ $comment->created_at->format('d/m/Y') }}">{{ $comment->created_at->format('d/m/Y') }}</cite></small>
                                        <figure class="mt-2">
                                            <blockquote class="blockquote">
                                                <p class="mb-0">{{ $comment->comment }}</p>
                                            </blockquote>
                                        </figure>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal fade" id="createdTicketModal" tabindex="-1" aria-hidden="true">
                        <form action="{{ route('created-ticket') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="exampleModalLabel1">Dados do Ticket</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-12 mb-2">
                                                <div class="form-floating form-floating-outline mb-2">
                                                    <textarea class="form-control h-px-100" name="description" id="description" placeholder="Notas" required></textarea>
                                                    <label for="description">Descrição</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label for="assets" class="form-label">Arquivos (Imagens, PDFs e etc)</label>
                                            <input type="file" class="form-control" name="assets[]" id="assets" multiple>
                                        </div>
                                        <input type="hidden" name="question_id" value="{{ $question->question->id }}">
                                    </div>
                                    <div class="modal-footer btn-group">
                                        <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal"> Fechar </button>
                                        <button type="submit" class="btn btn-success">Enviar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    @if (!empty($charts))
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="divider">
                                <div class="divider-text">Estátisticas da Questão</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 row">
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 text-center">
                                <small><b>{{ Auth::user()->pendingCountForQuestion($question->question->id) }}</b> Pendentes</small> <small class="text-success"><b>{{ Auth::user()->successCountForQuestion($question->question->id) }}</b> Acertos</small> <small class="text-danger"><b>{{ Auth::user()->errorCountForQuestion($question->question->id) }}</b> Erros</small>

                                <div class="table-responsive text-nowrap">
                                    <table class="table border-bottom">
                                        <thead>
                                            <tr>
                                                <th class="bg-transparent border-bottom text-center">SITUAÇÃO</th>
                                                <th class="bg-transparent border-bottom text-center">RESOLVIDA</th>
                                                <th class="bg-transparent border-bottom text-center">OPÇÕES</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            @foreach (Auth::user()->countForQuestion($question->question->id, [1, 2]) as $notebookQuestion)
                                                <tr>
                                                    <td class="text-center">
                                                        <div class="badge bg-label-{{ $notebookQuestion->labelResult()['color'] }} rounded-pill">{{ $notebookQuestion->labelResult()['message'] }}</div>
                                                    </td>
                                                    <td class="text-success fw-medium text-center">
                                                        {{ $notebookQuestion->resolved_at ? \Carbon\Carbon::parse($notebookQuestion->resolved_at)->format('d/m/Y') : '' }}
                                                    </td>
                                                    <td class="text-success fw-medium text-center">
                                                        <a href="{{ route('review-question', ['question' => $notebookQuestion->id]) }}" class="btn btn-success text-white btn-sm" title="Revisão">
                                                            <i class="ri-menu-search-line"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <div class="text-center">
                                    <h5>Geral</h5>
                                    <small>{{ $charts['general']['success'] + $charts['general']['error'] }} Resoluções</small>
                                    <p>✅ Acertos: {{ $charts['general']['success'] }} x  ❌ Erros: {{ $charts['general']['error'] }}</p>
                                </div>
                                <div style="width: 250px; height: 250px; margin: 0 auto;">
                                    <canvas id="generalChart"></canvas>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <div class="text-center">
                                    <h5>Individual</h5>
                                    <small>{{ $charts['personal']['success'] + $charts['personal']['error'] }} Resoluções</small>
                                    <p>✅ Acertos: {{ $charts['personal']['success'] }} x ❌ Erros: {{ $charts['personal']['error'] }}</p>
                                </div>
                                <div style="width: 250px; height: 250px; margin: 0 auto;">
                                    <canvas id="personalChart"></canvas>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="divider">
                                <div class="divider-text">Questão</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 row">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 bg-light p-3 rounded mt-1 mb-1">
                                
                                <h5>
                                    #{{ $question->id }} - {!! $question->question->title !!}
                                </h5>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 bg-light p-3 rounded mt-2 mb-2">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 bg-light p-3 rounded mt-2 mb-2">
                                    <div class="text-center my-3">
                                        <div class="alert alert-{{ $feedback['type'] }} fw-bold">
                                            {{ $feedback['message'] }}
                                        </div>
                                    </div>

                                    @foreach ($question->question->alternatives as $alternative)
                                        @php
                                            $isCorrect = $alternative->is_correct;
                                            $isChosen  = $question->answer_id == $alternative->id;
                                        @endphp

                                        <div class="form-check mt-3
                                            @if($isCorrect)
                                                border border-success bg-success-subtle rounded px-2
                                            @elseif($isChosen && !$isCorrect)
                                                border border-warning bg-warning-subtle rounded px-2
                                            @else
                                                border border-danger bg-danger-subtle rounded px-2
                                            @endif">

                                            <input class="form-check-input" type="radio" disabled id="answer_id{{ $alternative->id }}" {{ $isChosen ? 'checked' : '' }}>
                                            <label class="form-check-label" for="answer_id{{ $alternative->id }}">
                                                <strong>{{ $alternative->label }})</strong> {{ $alternative->text }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 bg-light p-3 rounded mt-1 mb-1 text-center">
                                <a href="{{ route('answer', ['notebook' => $question->notebook_id]) }}" class="btn btn-success">Avançar</a>
                            </div>
                        </div>
                    @endif
                </div>
            
            </div>
        </div>      
    </div>

    @if (!empty($charts))
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

            const personalCtx = document.getElementById('personalChart').getContext('2d');
            new Chart(personalCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Acertos', 'Erros'],
                    datasets: [{
                        data: [
                            {{ $charts['personal']['percent_success'] }},
                            {{ $charts['personal']['percent_error'] }}
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
    @endif
    <script src="https://cdn.tiny.cloud/1/tgezwiu6jalnw1mma8qnoanlxhumuabgmtavb8vap7357t22/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="{{ asset('assets/js/tinymce.js') }}"></script>
@endsection