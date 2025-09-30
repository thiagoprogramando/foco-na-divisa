@extends('app.layout')
@section('content')

    <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
        <div class="card mb-3">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h5 class="mb-1">Questões</h5>
                </div>
                <form action="{{ route('search') }}" method="GET" class="col-12 col-sm-12 col-md-8 col-lg-8 mb-3 g-3">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="search" placeholder="Pesquisar..." value="{{ request('search') }}">
                        <select name="topic_id" id="" class="select2 form-select">
                            <option value="all">Tópicos</option>
                            @foreach ($topics as $topic)
                                <option value="{{ $topic->id }}" @selected(request('topic_id') == $topic->id)>{{ $topic->title }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-dark" type="submit">Pesquisar</button>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="list-group p-0 m-0">
                    @foreach ($questions as $question)
                        <div class="list-group-item list-group-item-action d-flex align-items-center cursor-pointer waves-effect waves-light">
                            <div class="w-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="user-info">
                                        <h6 class="mb-1 fw-normal">{{ html_entity_decode(strip_tags($question->title)) }}</h6>
                                    </div>
                        
                                    <button type="button" class="btn btn-icon btn-outline-success waves-effect" title="Enviar Questão Para Caderno" data-bs-toggle="modal" data-bs-target="#createdModal{{ $question->id }}"><i class="tf-icons ri-add-circle-line ri-22px"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="createdModal{{ $question->id }}" tabindex="-1" aria-hidden="true">
                            <form action="{{ route('created-question-search') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="question_id" value="{{ $question->id }}">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="exampleModalLabel1">Dados do Caderno</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-12 mb-2">
                                                    <div class="form-floating form-floating-outline">
                                                        <teaxtarea class="form-control h-px-100" placeholder="Descrição" id="description">{{ strip_tags($question->title) }}</textarea>
                                                        <label for="description">Descrição</label>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-sm-12 col-md-12 col-lg-12 mb-2">
                                                    <div class="form-floating form-floating-outline">
                                                        <div class="select2-primary">
                                                            <select name="notebook_id" id="notebook_id" class="select2 form-select" required>
                                                                @foreach ($notebooks as $notebook)
                                                                    <option value="{{ $notebook->id }}">{{ $notebook->title }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <label for="notebook_id">Cadernos</label>
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
                </div>
            </div>
        </div>

        
    </div>
@endsection