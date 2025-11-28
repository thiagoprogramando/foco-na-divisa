@extends('app.layout')
@section('content')

<div class="col-12 col-sm-12 col-md-12 col-lg-12">
    <div class="card mb-3">

        {{-- HEADER --}}
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h5 class="mb-1">Simulado: {{ $simulated->title }}</h5>

                <div class="dropdown">
                    <button class="btn btn-text-secondary rounded-pill text-muted border-0 p-1 waves-effect waves-light"
                            type="button" data-bs-toggle="dropdown">
                        <i class="ri-more-2-line ri-20px"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <button type="button" class="dropdown-item waves-effect" onclick="location.reload(true)">
                            Atualizar
                        </button>
                    </div>
                </div>
            </div>

            {{-- SUBTÍTULO --}}
            <div class="card-subtitle">
                 @foreach($questions as $question)
                    <div class="me-2">Questão {{ $question->question_position.' de '.$simulated->questions->count() }}</div>
                    <small>
                        <b>Conteúdo/Tópico:</b> {{ $question->question->topic->content->title }} | {{ $question->question->topic->title }}<br>
                        <b>Banca:</b> {{ $question->question->board->code.' '.$question->question->board->name.' - '.$question->question->board->state .'/'.$question->question->board->city }} <br>
                    </small>
                @endforeach
            </div>
        </div>

        {{-- BODY --}}
        <div class="card-body">
            <div class="row">

                {{-- QUESTÃO --}}
                <div class="col-12">
                    <div class="divider">
                        <div class="divider-text">Questão</div>
                    </div>
                </div>

                {{-- FORM DE RESPOSTA --}}
                <form id="answerForm" method="POST" class="col-12 col-sm-12 col-md-12 col-lg-12 row">
                    @csrf
                    @foreach($questions as $question)
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 bg-light p-3 rounded mt-1 mb-1">
                            <h5>
                                #{{ $question->question->id }} - {!! $question->question->title !!}
                            </h5>
                        </div>

                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 bg-light p-3 rounded mt-2 mb-2">
                            @foreach ($question->question->alternatives as $alternative)
                                <div class="form-check mt-4 alternative-item" data-alternative-id="{{ $alternative->id }}">
                                    <input class="form-check-input" type="radio" name="answer_id" value="{{ $alternative->id }}" id="answer_id{{ $alternative->id }}" @checked($question->answer_id == $alternative->id)>
                                    <div class="alt-content">
                                        <span class="non-break">
                                            <i class="ri-scissors-line scissors-icon" title="Eliminar alternativa" role="button" aria-label="Eliminar alternativa"></i>
                                            <label class="alt-short" for="answer_id{{ $alternative->id }}">{{ $alternative->label }})</label>
                                        </span>
                                        <label class="alt-long" for="answer_id{{ $alternative->id }}">{{ $alternative->text }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <input type="hidden" name="simulated_question_id" value="{{ $question->id }}">
                    @endforeach

                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 bg-light p-3 rounded mt-1 mb-1 text-center">
                        {{ $questions->links() }}
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function submitAnswer() {
        const form = document.getElementById('answerForm');
        form.action = "{{ route('answer-simulated-question') }}";
        form.submit();
    }

    function submitDelete() {
        const questionId = document.querySelector('[name="notebook_question_id"]')?.value;
        console.log(questionId);
        if (!questionId) {
            return;
        }

        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `/deleted-question/${questionId}`;
        deleteForm.submit();
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.scissors-icon').forEach(icon => {
            icon.addEventListener('click', function () {
                const item = this.closest('.alternative-item');
                const short = item.querySelector('.alt-short');
                const long = item.querySelector('.alt-long');
                const input = item.querySelector('.form-check-input');

                short.classList.toggle('eliminado');
                long.classList.toggle('eliminado');
                input.disabled = !input.disabled;
            });
        });

        document.querySelectorAll('.alt-long').forEach(el => {
            el.addEventListener('dblclick', function () {
                const item = this.closest('.alternative-item');
                const short = item.querySelector('.alt-short');
                const long = item.querySelector('.alt-long');
                const input = item.querySelector('.form-check-input');

                short.classList.toggle('eliminado');
                long.classList.toggle('eliminado');
                input.disabled = !input.disabled;
            });
        });
    });
</script>

@endsection
