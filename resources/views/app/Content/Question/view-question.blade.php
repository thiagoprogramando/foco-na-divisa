@extends('app.layout')
@section('content')

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}"/>

    <div class="col-12 col-sm-12 col-md-12 col-lg-12">  
        <div class="card demo-inline-spacing">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h5 class="mb-1">Questão #{{ $question->id }}</h5>
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
                    <div class="me-2">Mantenha os dados atualizados</div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('updated-question', ['id' => $question->id]) }}" method="POST" class="row" id="question-form">
                    @csrf
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="form-floating form-floating-outline mb-2">
                            <div class="select2-primary">
                                <select name="topic_id" id="topic_id" class="select2 form-select" required>
                                    <option value="Opções Disponíveis" selected>Opções Disponíveis</option>
                                    @foreach ($topics as $topic)
                                        <option value="{{ $topic->id }}" @selected($topic->id == $question->topic_id)>{{ $topic->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label for="topic_id">Tópico</label>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="form-floating form-floating-outline mb-2">
                            <div class="select2-primary">
                                <select name="board_id" id="board_id" class="select2 form-select" required>
                                    <option value="Opções Disponíveis" selected>Opções Disponíveis</option>
                                    @foreach ($boards as $board)
                                        <option value="{{ $board->id }}" @selected($question->board_id == $board->id)>{{ $board->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label for="board_id">Bancas</label>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="full-editor">
                            {!! $question->title !!}
                        </div>
                        <textarea name="title" id="title" hidden></textarea>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 mt-3 mb-3">
                        <small class="text-light fw-medium">Alternativas</small>
                        <div id="alternatives-wrapper">
                            @foreach ($question->alternatives as $index => $alternative)
                                <div class="row mt-2 alternative-row">
                                    <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                        <div class="form-floating form-floating-outline mb-2">
                                            <input type="text" class="form-control" name="alternative[]" placeholder="Ex: dois é o único Primo Par" value="{{ $alternative->text }}">
                                            <label>{{ $alternative->label }})</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-4 d-flex align-items-center">
                                        <div class="form-check form-switch mb-2">
                                            <input 
                                                class="form-check-input correct-switch" 
                                                type="checkbox" 
                                                name="correct[]" 
                                                value="{{ $index }}" 
                                                {{ $alternative->is_correct ? 'checked' : '' }}
                                            >
                                            <label class="form-check-label ms-2">Correta</label>
                                        </div>
                                    </div> 
                                </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-2 mb-2">
                            <button type="button" class="btn rounded-pill btn-icon btn-success" id="add-alternative">
                                <span class="tf-icons ri-add-line ri-22px"></span>
                            </button>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="resolution">
                            {!! $question->resolution !!}
                        </div>
                        <textarea name="resolution" id="resolution" hidden></textarea>
                    </div>
                    <div class="col-12 btn-group">
                        <a href="{{ route('questions', ['topic' => $question->topic_id]) }}" class="btn btn-outline-danger"> Cancelar </a>
                        <button type="submit" class="btn btn-success">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/js/question.js') }}"></script>
    <script>
        const fullToolbar = [
            [
            { font: [] },
            { size: [] }
            ],
            ['bold', 'italic', 'underline', 'strike'],
            [
            { color: [] },
            { background: [] }
            ],
            [
            { script: 'super' },
            { script: 'sub' }
            ],
            [
            { header: '1' },
            { header: '2' },
            'blockquote',
            'code-block'
            ],
            [
            { list: 'ordered' },
            { list: 'bullet' },
            { indent: '-1' },
            { indent: '+1' }
            ],
            [{ direction: 'rtl' }],
            ['link', 'image', 'video', 'formula'],
            ['clean']
        ];

        const editor = new Quill('.full-editor', {
            bounds: '.full-editor',
            placeholder: 'Digite o conteúdo do contrato...',
            modules: {
            formula: true,
            toolbar: fullToolbar
            },
            theme: 'snow'
        });

        const resolution = new Quill('.resolution', {
            bounds: '.resolution',
            placeholder: 'Digite o conteúdo do contrato...',
            modules: {
            formula: true,
            toolbar: fullToolbar
            },
            theme: 'snow'
        });

        const create = document.getElementById('question-form');
        create.addEventListener('submit', function (event) {
            event.preventDefault();
            const title = editor.root.innerHTML.trim();
            document.getElementById('title').value = title;
            const resolutionContent = resolution.root.innerHTML.trim();
            document.getElementById('resolution').value = resolutionContent;
            create.submit();
        });
    </script>
@endsection