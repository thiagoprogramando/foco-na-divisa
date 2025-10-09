@extends('app.layout')
@section('content')

    <div class="col-12">
        <div class="kanban-add-new-board">
            <a href="{{ route('create-notebook') }}" class="btn btn-sm btn-primary waves-effect waves-light">
                <i class="ri-add-line"></i>
                <span class="align-middle">CRIAR CADERNO DE QUESTÕES</span>
            </a>
        </div>
    </div>

    <div class="col-12 col-sm-12 col-md-12 col-lg-7">    
        <div class="card demo-inline-spacing">
            <div class="list-group p-0 m-0">
                @foreach ($notebooks as $notebook)
                    <div class="list-group-item list-group-item-action d-flex align-items-center cursor-pointer waves-effect waves-light mb-2">
                        <img onclick="window.location.href='{{ route('answer', ['notebook' => $notebook->id]) }}'" src="{{ $notebook->cover_image ? asset($notebook->cover_image) : asset('assets/img/avatars/man.png') }}" alt="Conteúdo Imagem" class="rounded-circle me-3" width="40">
                        <div class="w-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="user-info" onclick="window.location.href='{{ route('answer', ['notebook' => $notebook->id]) }}'">
                                    <h6 class="mb-1 fw-normal">{{ $notebook->title }}</h6>
                                    <div class="d-flex align-items-center">
                                        <div class="user-status me-2 d-flex align-items-center">
                                            <span class="badge badge-dot bg-dark me-1"></span>
                                            <small>Tópicos: {{ $notebook->topics_count }}</small>
                                        </div>
                                        <div class="user-status me-2 d-flex align-items-center">
                                            <span class="badge badge-dot bg-info me-1"></span>
                                            <small>Questões: {{ $notebook->questions->count() }}</small>
                                        </div>
                                    </div>
                                </div>
                                <form action="{{ route('deleted-notebook', ['id' => $notebook->id]) }}" method="POST" class="add-btn delete">
                                    @csrf
                                    <a href="{{ route('answer', ['notebook' => $notebook->id]) }}" title="Responder Caderno" class="btn btn-success text-white btn-sm"><i class="ri-menu-search-line"></i></a>
                                    <a href="{{ route('review-notebook', ['id' => $notebook->id]) }}" title="Estatísticas do Caderno" class="btn btn-info text-white btn-sm"><i class="tf-icons ri-pie-chart-line"></i></a>
                                    <a href="{{ route('notebook', ['id' => $notebook->id]) }}" title="Editar Caderno" class="btn btn-warning text-white btn-sm"><i class="ri-edit-box-line"></i></a>
                                    <button type="submit" class="btn btn-danger btn-sm" title="Excluir Caderno"><i class="ri-delete-bin-line"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>  
                @endforeach

                <div class="mt-2 mb-5 text-center">
                    {{ $notebooks->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection