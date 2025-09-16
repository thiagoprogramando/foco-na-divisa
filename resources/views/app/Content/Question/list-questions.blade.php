@extends('app.layout')
@section('content')

    <div class="col-12">
        <div class="kanban-add-new-board">
            <a href="{{ route('create-question', ['topic' => $topic->id]) }}" class="kanban-add-board-btn" for="kanban-add-board-input">
                <i class="ri-add-line"></i>
                <span class="align-middle">Nova Questão</span>
            </a>
            <label class="kanban-add-board-btn" for="kanban-add-board-input" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="ri-filter-line"></i>
                <span class="align-middle">Filtrar</span>
            </label>
        </div>

        <div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
            <form action="{{ route('questions', ['topic' => $topic]) }}" method="GET">
                @csrf
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel1">Dados da Pesquisa</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" name="title" id="title" class="form-control" placeholder="Trecho da Questão:"/>
                                        <label for="title">Trecho da Questão:</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
                                <div class="form-floating form-floating-outline mb-2">
                                    <div class="select2-primary">
                                        <select name="board_id" id="board_id" class="select2 form-select" required>
                                            <option value="  " selected>Opções Disponíveis</option>
                                            @foreach ($boards as $board)
                                                <option value="{{ $board->id }}">{{ $board->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label for="board_id">Bancas</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer btn-group">
                            <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal"> Fechar </button>
                            <button type="submit" class="btn btn-success">Filtrar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="col-12 col-sm-12 col-md-12 col-lg-12">  
        <div class="card demo-inline-spacing">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h5 class="mb-1">Questões</h5>
                    <div class="dropdown">
                        <button class="btn btn-text-secondary rounded-pill text-muted border-0 p-1 waves-effect waves-light" type="button" id="salesOverview" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-more-2-line ri-20px"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="salesOverview">
                            <button type="button" class="dropdown-item waves-effect" onclick="location.reload(true)">Atualizar</button>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center card-subtitle">
                    <div class="me-2">Associadas ao Tópico {{ $topic->title }}.</div>
                </div>
            </div>
            <div class="card-body">
                <div class="list-group p-0 m-0">
                    @foreach ($questions as $question)
                        <div class="list-group-item list-group-item-action d-flex align-items-center cursor-pointer waves-effect waves-light">
                            <div class="w-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="user-info">
                                        <h6 class="mb-1 fw-normal">{{ $question->title }}</h6>
                                        <div class="d-flex align-items-center">
                                            <div class="user-status me-2 d-flex align-items-center">
                                                <span class="badge badge-dot bg-info me-1"></span>
                                                <small>Respostas: 0</small>
                                            </div>
                                            <div class="user-status me-2 d-flex align-items-center">
                                                <span class="badge badge-dot bg-success me-1"></span>
                                                <small>Acertos: 0</small>
                                            </div>
                                            <div class="user-status me-2 d-flex align-items-center">
                                                <span class="badge badge-dot bg-danger me-1"></span>
                                                <small>Erros: 0</small>
                                            </div>
                                            <small class="text-muted ms-1" title="{{ $question->resolution }}">{{ Str::limit($question->resolution, 50) }}</small>
                                        </div>
                                    </div>
                                    <form action="{{ route('deleted-question', ['id' => $question->id]) }}" method="POST" class="add-btn delete">
                                        @csrf
                                        <a href="{{ route('question', ['id' => $question->id]) }}" class="btn btn-success text-white btn-sm"><i class="ri-menu-search-line"></i></a>
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="ri-delete-bin-line"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>  
                    @endforeach
                </div>
            </div>
        </div>
    </div>

@endsection