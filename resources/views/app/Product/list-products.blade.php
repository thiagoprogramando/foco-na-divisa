@extends('app.layout')
@section('content')

    <div class="col-12 col-sm-12 col-md-7 col-lg-7">
        <div class="kanban-add-new-board mb-5">
            <a href="{{ route('create-product') }}" class="kanban-add-board-btn" for="kanban-add-board-input">
                <i class="ri-add-line"></i>
                <span class="align-middle">Novo Produto</span>
            </a>
            <label class="kanban-add-board-btn" for="kanban-add-board-input" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="ri-filter-line"></i>
                <span class="align-middle">Filtrar</span>
            </label>
        </div>

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
                            <i class="ri-eye-line ri-24px"></i>
                        </div>
                    </div>
                    <div class="card-info">
                        <h5 class="mb-0"></h5>
                        <p class="mb-0">Visitas</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar">
                        <div class="avatar-initial bg-label-success rounded">
                        <i class="ri-shopping-cart-line ri-24px"></i>
                        </div>
                    </div>
                    <div class="card-info">
                        <h5 class="mb-0"></h5>
                        <p class="mb-0">Compras</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar">
                        <div class="avatar-initial bg-label-info rounded">
                        <i class="ri-store-2-line ri-24px"></i>
                        </div>
                    </div>
                    <div class="card-info">
                        <h5 class="mb-0"></h5>
                        <p class="mb-0">Produtos</p>
                    </div>
                </div>
            </div>
        </div>  
        
        <div class="card demo-inline-spacing">
            <div class="list-group p-0 m-0">
                @foreach ($products as $product)
                    <div class="list-group-item list-group-item-action d-flex align-items-center cursor-pointer waves-effect waves-light">
                        <img src="{{ $product->image ? asset('storage/'.$product->image) : asset('assets/img/avatars/man.png') }}" onclick="window.location.href='{{ route('product', ['uuid' => $product->uuid]) }}'" alt="Produto Imagem" class="rounded-circle me-3" width="40">
                        <div class="w-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="user-info">
                                    <h6 class="mb-1 fw-normal" onclick="window.location.href='{{ route('product', ['uuid' => $product->uuid]) }}'">{{ $product->name }}</h6>
                                    <div class="d-flex align-items-center">
                                        <div class="user-status me-2 d-flex align-items-center">
                                            <span class="badge badge-dot bg-dark me-1"></span>
                                            <small>Vendas: X</small>
                                        </div>
                                        <div class="user-status me-2 d-flex align-items-center">
                                            <span class="badge badge-dot bg-info me-1"></span>
                                            <small>Visitas: Y</small>
                                        </div>
                                    </div>
                                </div>
                                <form action="{{ route('deleted-product', ['uuid' => $product->uuid]) }}" method="POST" class="add-btn delete">
                                    @csrf
                                    <a href="{{ route('product', ['uuid' => $product->uuid]) }}" class="btn btn-success text-white btn-sm" title="Acessar Conteúdo"><i class="ri-menu-search-line"></i></a>
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