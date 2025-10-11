@extends('app.layout')
@section('content')

    <div class="col-12 col-sm-12 col-md-12 col-lg-12">  
        <div class="card demo-inline-spacing">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h5 class="mb-1">Nova Questão</h5>
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
                    <div class="me-2">Será associada ao Tópico {{ $topic->title }}.</div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('created-question', ['topic' => $topic->id]) }}" method="POST" class="row" id="question-form">
                    @csrf
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="form-floating form-floating-outline mb-2">
                            <div class="select2-primary">
                                <select name="board_id" id="board_id" class="select2 form-select" required>
                                    <option value="Opções Disponíveis" selected>Opções Disponíveis</option>
                                    @foreach ($boards as $board)
                                        <option value="{{ $board->id }}" @selected($board->id == 1)>{{ $board->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label for="board_id">Bancas</label>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="form-floating form-floating-outline mb-2">
                            <textarea class="form-control h-px-100 editor" name="title" id="question" placeholder="Questão:"></textarea>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 mt-3 mb-3">
                        <small class="text-light fw-medium">Alternativas</small>
                        <div id="alternatives-wrapper">
                            <div class="row mt-2 alternative-row">
                                <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <input type="text" class="form-control" name="alternative[]" placeholder="Ex: dois é o único Primo Par">
                                        <label>A)</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-4 col-lg-4 d-flex align-items-center">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input correct-switch" type="checkbox" name="correct[]" value="0">
                                        <label class="form-check-label ms-2">Correta</label>
                                    </div>
                                </div> 
                            </div>
                        </div>
                        <div class="text-center mt-2 mb-2">
                            <button type="button" class="btn rounded-pill btn-icon btn-success" id="add-alternative">
                                <span class="tf-icons ri-add-line ri-22px"></span>
                            </button>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating form-floating-outline mb-2">
                            <textarea class="form-control h-px-100 editor" name="resolution" id="resolution" placeholder="Resolução / Comentário do Professor"></textarea>
                        </div>
                    </div>
                    <div class="col-12 btn-group">
                        <a href="{{ route('questions', ['topic' => $topic->id]) }}" class="btn btn-outline-danger"> Cancelar </a>
                        <button type="submit" class="btn btn-success">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.tiny.cloud/1/tgezwiu6jalnw1mma8qnoanlxhumuabgmtavb8vap7357t22/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="{{ asset('assets/js/question.js') }}"></script>
    <script src="{{ asset('assets/js/tinymce.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('add-alternative');
            for (let i = 0; i < 4; i++) {
                btn.click();
            }
        });
    </script>
@endsection