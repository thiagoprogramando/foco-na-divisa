@extends('app.layout')
@section('content')

    <div class="col-12">
        <div class="kanban-add-new-board">
            <label class="kanban-add-board-btn" for="kanban-add-board-input" data-bs-toggle="modal" data-bs-target="#createdModal">
                <i class="ri-add-line"></i>
                <span class="align-middle">Novo Perfil</span>
            </label>
            <label class="kanban-add-board-btn" for="kanban-add-board-input" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="ri-filter-line"></i>
                <span class="align-middle">Filtrar Perfis</span>
            </label>
        </div>

        <div class="modal fade" id="createdModal" tabindex="-1" aria-hidden="true">
            <form action="{{ route('created-user', ['role' => $role]) }}" method="POST">
                @csrf
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel1">Dados do Perfil</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <b class="text-danger">Atenção</b>
                                    <ul>
                                        <li>Todos os campos são obrigatórios!</li>
                                        <li>A senha inicial do Perfil será <b>CPF/CNPJ</b> sem pontos e traços.</li>
                                    </ul>
                                </div>
                                <div class="col-12 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Nome:" required/>
                                        <label for="name">Nome:</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="email" name="email" id="email" class="form-control" placeholder="E-mail:" required/>
                                        <label for="email">E-mail:</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" name="cpfcnpj" id="cpfcnpj" class="form-control cpfcnpj" placeholder="CPF/CNPJ:" oninput="maskCpfCnpj(this)" required/>
                                        <label for="cpfcnpj">CPF/CNPJ:</label>
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
            <form action="{{ route('users', ['role' => $role]) }}" method="GET">
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
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Nome:"/>
                                        <label for="name">Nome:</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="email" name="email" id="email" class="form-control" placeholder="E-mail:"/>
                                        <label for="email">E-mail:</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" name="cpfcnpj" id="cpfcnpj" class="form-control cpfcnpj" placeholder="CPF/CNPJ:" oninput="maskCpfCnpj(this)"/>
                                        <label for="cpfcnpj">CPF/CNPJ:</label>
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
    </div>

    <div class="col-12 col-sm-12 col-md-7 col-lg-7">
        <div class="card demo-inline-spacing">
            <div class="list-group p-0 m-0">
                @foreach ($users as $user)
                    <div class="list-group-item list-group-item-action d-flex align-items-center cursor-pointer waves-effect waves-light">
                        <img src="{{ $user->photo ? asset('storage/'.$user->photo) : asset('assets/img/avatars/man.png') }}" alt="Perfil {{ $user->name }}" class="rounded-circle me-3" width="40">
                        <div class="w-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="user-info">
                                    <h6 class="mb-1 fw-normal">{{ $user->name }}</h6>
                                    <div class="d-flex align-items-center">
                                        <div class="user-status me-2 d-flex align-items-center">
                                            <span class="badge badge-dot bg-dark me-1"></span>
                                            <small>{{ $user->maskCpfCnpj() }}</small>
                                        </div>
                                        <div class="user-status me-2 d-flex align-items-center">
                                            <span class="badge badge-dot bg-info me-1"></span>
                                            <small>{{ $user->email }}</small>
                                        </div>
                                        <small class="text-muted ms-1" title="">Plano Ativo do User</small>
                                    </div>
                                </div>
                                <form action="{{ route('deleted-user', ['uuid' => $user->uuid]) }}" method="POST" class="add-btn delete">
                                    @csrf
                                    <a href="{{ route('user', ['uuid' => $user->uuid]) }}" class="btn btn-success text-white btn-sm"><i class="ri-menu-search-line"></i></a>
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="ri-delete-bin-line"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>  
                @endforeach
            </div>
        </div>
    </div>

@endsection