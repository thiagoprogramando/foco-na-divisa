@extends('app.layout')
@section('content')

    <div class="col-12">
        <div class="kanban-add-new-board">
            <label class="kanban-add-board-btn" for="kanban-add-board-input" data-bs-toggle="modal" data-bs-target="#createdModal">
                <i class="ri-add-line"></i>
                <span class="align-middle">Novo Ticket</span>
            </label>
            @if (Auth::user()->role == 'admin')
                <label class="kanban-add-board-btn" for="kanban-add-board-input" data-bs-toggle="modal" data-bs-target="#filterModal">
                    <i class="ri-filter-line"></i>
                    <span class="align-middle">Filtrar Tickets</span>
                </label>
            @endif
        </div>

        <div class="modal fade" id="createdModal" tabindex="-1" aria-hidden="true">
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
                        </div>
                        <div class="modal-footer btn-group">
                            <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal"> Fechar </button>
                            <button type="submit" class="btn btn-success">Enviar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
            <form action="{{ route('tickets') }}" method="GET">
                @csrf
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel1">Dados da Pesquisa</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <input type="number" name="question_id" id="question_id" class="form-control">
                                        <label for="question_id">ID da questão</label>
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <input type="number" name="user_id" id="user_id" class="form-control">
                                        <label for="user_id">ID do Usuário</label>
                                    </div>
                                </div>
                                <div class="col-12 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <div class="select2-primary">
                                            <select name="status" id="status" class="select2 form-select">
                                                <option value="open">Aberto</option>
                                                <option value="pending">Pendente</option>
                                                <option value="closed">Fechado</option>
                                            </select>
                                        </div>
                                        <label for="status">Status</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer btn-group">
                            <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal"> Fechar </button>
                            <button type="submit" class="btn btn-success">Enviar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="col-12 col-sm-12 col-md-12 col-lg-7">
        <div class="card demo-inline-spacing">
            <div class="list-group p-0 m-0">
                <div class="accordion mt-4" id="accordionExample">
                    @foreach ($tickets as $key => $ticket)
                        <div class="accordion-item @if($key == 0) active @endif">
                            <h2 class="accordion-header" id="heading{{ $ticket->id }}">
                                <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#accordion{{ $ticket->id }}" aria-expanded="true" aria-controls="accordion{{ $ticket->id }}">
                                    @if ($ticket->status == 'open')
                                        <span class="badge bg-label-success me-2">Aberto</span>
                                    @elseif ($ticket->status == 'pending')
                                        <span class="badge bg-label-warning me-2">Pendente</span>
                                    @else
                                        <span class="badge bg-label-danger me-2">Fechado</span>
                                    @endif
                                    #{{ $ticket->id }} - {{ $ticket->user->name }} - {{ $ticket->created_at->format('d/m/Y H:i') }}
                                </button>
                            </h2>
                            <div id="accordion{{ $ticket->id }}" class="accordion-collapse collapse @if($key == 0) show @endif" data-bs-parent="#accordi{{ $ticket->id }}example">
                                <div class="accordion-body">
                                    {{ $ticket->description }}

                                    @if ($ticket->question)
                                        <div class="mt-3">
                                            <h6>Questão Associada:</h6>
                                            <a href="@if (Auth::user()->role == 'admin') {{ route('question', ['id' => $ticket->question_id]) }} @else # @endif" target="_blank">{!! $ticket->question->title !!}</a>
                                        </div>
                                    @endif

                                    @php $assets = json_decode($ticket->assets, true); @endphp
                                    @if (!empty($assets))
                                        <div class="mt-3">
                                            <h6>Arquivos anexados:</h6>
                                            <ul>
                                                @foreach ($assets as $file)
                                                    <li>
                                                        <a href="{{ asset($file['url']) }}" target="_blank">
                                                            {{ basename($file['original_name']) }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if (Auth::user()->role == 'admin')
                                        <form action="{{ route('updated-ticket', ['id' => $ticket->id]) }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="divider">
                                                        <div class="divider-text">Área para Suporte/administração</div>
                                                    </div>
                                                </div>
                                                <div class="col-12 mb-2">
                                                    <div class="form-floating form-floating-outline mb-2">
                                                        <textarea class="form-control h-px-100" name="answer" id="answer" placeholder="Resposta">{{ $ticket->answer }}</textarea>
                                                        <label for="answer">Resposta</label>
                                                    </div>
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <div class="form-floating form-floating-outline">
                                                        <div class="select2-primary">
                                                            <select name="status" id="status" class="select2 form-select">
                                                                <option value="open" @selected($ticket->status == 'open')>Aberto</option>
                                                                <option value="pending" @selected($ticket->status == 'pending')>Pendente</option>
                                                                <option value="closed" @selected($ticket->status == 'closed')>Fechado</option>
                                                            </select>
                                                        </div>
                                                        <label for="status">Status</label>
                                                    </div>
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <button type="submit" class="btn btn-success mt-2">Salvar</button>
                                                </div>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

@endsection