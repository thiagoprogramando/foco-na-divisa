@extends('app.layout')
@section('content')

    <div class="col-12">
        @if (Auth::user()->role === 'admin')
            <div class="kanban-add-new-board">
                <label class="kanban-add-board-btn" for="kanban-add-board-input" data-bs-toggle="modal" data-bs-target="#createdModal">
                    <i class="ri-add-line"></i>
                    <span class="align-middle">Novo Conteúdo</span>
                </label>
            </div>
        @endif
        

        <div class="modal fade" id="createdModal" tabindex="-1" aria-hidden="true">
            <form action="{{ route('created-content') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel1">Dados do Conteúdo</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" name="title" id="title" class="form-control" placeholder="Título" required/>
                                        <label for="title">Título</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="number" name="order" id="order" class="form-control money" placeholder="Posição (Ordem 1 ou 2)"/>
                                        <label for="order">Posição (Ordem 1 ou 2)</label>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-12 col-md-6 col-lg-6 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <div class="select2-primary">
                                            <select name="status" id="status" class="select2 form-select">
                                                <option value="active" selected>Ativo</option>
                                                <option value="inactive">Inativo</option>
                                            </select>
                                        </div>
                                        <label for="status">Status</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <a class="me-1" data-bs-toggle="collapse" href="#collapseNotes" role="button" aria-expanded="false" aria-controls="collapseNotes"> Extras </a>
                                </div>
                                <div class="col-12">
                                    <div class="collapse" id="collapseNotes">
                                        <div class="form-floating form-floating-outline mb-2">
                                            <textarea class="form-control h-px-100" name="description" id="description" placeholder="Notas"></textarea>
                                            <label for="description">Descrição</label>
                                        </div>
                                        <div class="form-floating form-floating-outline">
                                            <input id="TagifyBasic" class="form-control h-auto" name="tags" placeholder="Escreva e tecle Enter" tabindex="-1">
                                            <label for="TagifyBasic">Tags</label>
                                        </div>
                                        <div class="mb-4">
                                            <label for="cover_image" class="form-label">Imagem de Capa</label>
                                            <input class="form-control" type="file" name="cover_image" id="cover_image" accept="image/*">
                                        </div>
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

    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card mb-3">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h5 class="mb-1">Visão Geral</h5>
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
                    <div class="me-2">Os dados são atualizados automáticamente.</div>
                </div>
            </div>
            <div class="card-body d-flex justify-content-between flex-wrap gap-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar">
                        <div class="avatar-initial bg-label-primary rounded">
                            <i class="ri-file-edit-line ri-24px"></i>
                        </div>
                    </div>
                    <div class="card-info">
                        <h5 class="mb-0">{{ $contents->count() }}</h5>
                        <p class="mb-0">Conteúdos</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar">
                        <div class="avatar-initial bg-label-warning rounded">
                        <i class="ri-pie-chart-2-line ri-24px"></i>
                        </div>
                    </div>
                    <div class="card-info">
                        <h5 class="mb-0">{{ $topicsCount }}</h5>
                        <p class="mb-0">Tópicos</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar">
                        <div class="avatar-initial bg-label-info rounded">
                        <i class="ri-question-line ri-24px"></i>
                        </div>
                    </div>
                    <div class="card-info">
                        <h5 class="mb-0">{{ $questionsCount }}</h5>
                        <p class="mb-0">Questões</p>
                    </div>
                </div>
            </div>
        </div>      

        <div class="card demo-inline-spacing">
            <div class="list-group p-0 m-0">
                @foreach ($contents as $content)
                    <div onclick="window.location.href='{{ route('content', ['id' => $content->id]) }}'" class="list-group-item list-group-item-action d-flex align-items-center cursor-pointer waves-effect waves-light">
                        <img src="{{ $content->cover_image ? asset('storage/'.$content->cover_image) : asset('assets/img/avatars/man.png') }}" alt="Conteúdo Imagem" class="rounded-circle me-3" width="40">
                        <div class="w-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="user-info">
                                    <h6 class="mb-1 fw-normal">{{ $content->title }}</h6>
                                    <div class="d-flex align-items-center">
                                        <div class="user-status me-2 d-flex align-items-center">
                                            <span class="badge badge-dot bg-dark me-1"></span>
                                            <small>Tópicos: {{ $content->topics->count() }}</small>
                                        </div>
                                        <div class="user-status me-2 d-flex align-items-center">
                                            <span class="badge badge-dot bg-info me-1"></span>
                                            <small>Questões: {{ $content->questions->count() }}</small>
                                        </div>
                                        <small class="text-muted ms-1" title="{{ $content->description }}">{{ Str::limit($content->description, 50) }}</small>
                                    </div>
                                </div>
                                <form action="{{ route('deleted-content', ['id' => $content->id]) }}" method="POST" class="add-btn delete">
                                    @csrf
                                    <a href="{{ route('content', ['id' => $content->id]) }}" class="btn btn-success text-white btn-sm" title="Acessar Conteúdo"><i class="ri-menu-search-line"></i></a>
                                    <button type="submit" class="btn btn-danger btn-sm" title="Excluir Conteúdo"><i class="ri-delete-bin-line"></i></button>
                                </form>
                            </div>
                        </div>
                    </div> 
                @endforeach
            </div>
        </div>
    </div>

@endsection