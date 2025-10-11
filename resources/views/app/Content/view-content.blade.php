@extends('app.layout')
@section('content')

    <div class="col-12 col-sm-12 col-md-5 col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0 me-2">Dados do Conteúdo</h5>
                <div class="dropdown">
                    <button class="btn btn-text-secondary rounded-pill text-muted border-0 p-1 waves-effect waves-light" type="button" id="upgradePlanCard" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ri-more-2-line ri-20px"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="upgradePlanCard">
                        <a class="dropdown-item waves-effect" href="javascript:void(0);">Last 28 Days</a>
                        <a class="dropdown-item waves-effect" href="javascript:void(0);">Last Month</a>
                        <a class="dropdown-item waves-effect" href="javascript:void(0);">Last Year</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('updated-content', ['id' => $content->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" name="title" id="title" class="form-control" placeholder="Título" value="{{ $content->title }}"/>
                                <label for="title">Título</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="number" name="order" id="order" class="form-control" placeholder="Posição (Ordem 1 ou 2)" value="{{ $content->order }}"/>
                                <label for="order">Posição (Ordem 1 ou 2)</label>
                            </div>
                        </div>
                        <div class="col-6 col-sm-12 col-md-6 col-lg-6 mb-2">
                            <div class="form-floating form-floating-outline">
                                <div class="select2-primary">
                                    <select name="status" id="status" class="select2 form-select">
                                        <option value="active" @selected($content->status == 'active')>Ativo</option>
                                        <option value="inactive" @selected($content->status == 'inactive')>Inativo</option>
                                    </select>
                                </div>
                                <label for="status">Status</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-floating form-floating-outline mb-2">
                            <textarea class="form-control h-px-100" name="description" id="description" placeholder="Notas">{{ $content->description }}</textarea>
                            <label for="description">Descrição</label>
                        </div>
                        <div class="form-floating form-floating-outline mb-2">
                            <input id="TagifyBasic" class="form-control h-auto" name="tags" placeholder="Escreva e tecle Enter" value="{{ $content->tags }}" tabindex="-1">
                            <label for="TagifyBasic">Tags</label>
                        </div>
                        <div class="mb-4">
                            <label for="cover_image" class="form-label">Imagem de Capa</label>
                            <input class="form-control" type="file" name="cover_image" id="cover_image" accept="image/*">
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">Atualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-12 col-md-7 col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0 me-2">Tópicos Associados</h5>
                <div class="dropdown">
                    <button class="btn btn-text-secondary rounded-pill text-muted border-0 p-1 waves-effect waves-light" type="button" id="upgradePlanCard" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ri-more-2-line ri-20px"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="upgradePlanCard">
                        <a class="dropdown-item waves-effect" data-bs-toggle="modal" data-bs-target="#createdModal">Criar Tópico</a>
                        <a class="dropdown-item waves-effect" data-bs-toggle="modal" data-bs-target="#filterModal">Filtrar Tópicos</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Detalhes</th>
                                <th class="text-center">Questões</th>
                                <th class="text-center">Ordem</th>
                                <th class="text-center">Opções</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($topics as $topic)
                                <tr>
                                    <td class="text-center">
                                        <form action="{{ route('deleted-topic', ['id' => $topic->id]) }}" method="POST" class="demo-inline-spacing delete">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $topic->id }}">
                                            <button type="submit" class="btn btn-icon btn-outline-danger waves-effect" title="Deletar">
                                                <span class="tf-icons ri-delete-bin-line ri-22px"></span>
                                            </button>
                                            <button type="button" class="btn btn-icon btn-outline-success waves-effect" data-bs-toggle="modal" data-bs-target="#updatedModal{{ $topic->id }}" title="Editar">
                                                <span class="tf-icons ri-eye-line ri-22px"></span>
                                            </button>
                                            <a href="{{ route('questions', ['topic' => $topic->id]) }}" target="_blank" class="btn btn-icon btn-outline-dark waves-effect" title="Criar Nova Questão">
                                                <span class="tf-icons ri-add-circle-line ri-22px"></span>
                                            </a>
                                        </form>
                                    </td>
                                    <td>
                                        <span class="fw-medium" title="{{ $topic->title }}">{{ Str::limit($topic->title, 50) }}...</span>
                                        <br>
                                        <span class="badge bg-label-info m-1">{{ \Illuminate\Support\Str::limit($topic->description, 100) }}</span>
                                    </td>
                                    <td class="text-center">
                                        {{ $topic->questions->count() }}
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-medium">{{ $topic->order }}</span>
                                    </td>
                                </tr>

                                <div class="modal fade" id="updatedModal{{ $topic->id }}" tabindex="-1" aria-hidden="true">
                                    <form action="{{ route('updated-topic', ['id' => $topic->id]) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="content_id" value="{{ $content->id }}">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel1">Dados do Tópico</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-12 mb-2">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" name="title" id="title" class="form-control" placeholder="Título" value="{{ $topic->title }}"/>
                                                                <label for="title">Título</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="number" name="order" id="order" class="form-control" placeholder="Posição (Ordem 1 ou 2)" value="{{ $topic->order }}"/>
                                                                <label for="order">Posição (Ordem 1 ou 2)</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-sm-12 col-md-6 col-lg-6 mb-2">
                                                            <div class="form-floating form-floating-outline">
                                                                <select name="status" id="status" class="form-select">
                                                                    <option value="active" @selected($topic->status == 'active')>Ativo</option>
                                                                    <option value="inactive" @selected($topic->status == 'inactive')>Inativo</option>
                                                                </select>
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
                                                                    <textarea class="form-control h-px-100" name="description" id="description" placeholder="Notas">{{ $topic->description }}</textarea>
                                                                    <label for="description">Descrição</label>
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
                            @endforeach
                        </tbody>
                    </table>

                    <div class="text-center">
                        {{ $topics->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createdModal" tabindex="-1" aria-hidden="true">
        <form action="{{ route('created-topic') }}" method="POST">
            @csrf
            <input type="hidden" name="content_id" value="{{ $content->id }}">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel1">Dados do Tópico</h4>
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
                                    <input type="number" name="order" id="order" class="form-control" placeholder="Posição (Ordem 1 ou 2)"/>
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

    <div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
        <form action="{{ route('content', ['id' => $content->id]) }}" method="GET">
            @csrf
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel1">Filtros</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="title" id="title" class="form-control" placeholder="Título"/>
                                    <label for="title">Título</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" name="order" id="order" class="form-control" placeholder="Posição (Ordem 1 ou 2)"/>
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
                    </div>
                    <div class="modal-footer btn-group">
                        <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal"> Fechar </button>
                        <button type="submit" class="btn btn-success">Filtrar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection