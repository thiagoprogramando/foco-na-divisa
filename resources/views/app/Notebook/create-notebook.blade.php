@extends('app.layout')
@section('content')

    <style>
        .dual-listbox {
            display: flex;
            gap: 15px;
        }
        .listbox-panel {
            flex: 1;
        }
        .scroll-box {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card mb-3">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h5 class="mb-1">Gerar Caderno</h5>
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
                    <div class="me-2">Escolha os Filtros para gerar um Caderno de questões.</div>
                </div>
            </div>
            <div class="card-body">
                
                <div class="dual-listbox row">
                    <div class="col-12 col-sm-12 col-md-5 col-lg-5 listbox-panel">
                        <h5>Disponíveis</h5>
                        <input type="text" class="form-control mb-2" id="search-available" placeholder="Pesquisar...">
                        <div class="scroll-box">
                            <select multiple id="available-topics" class="form-control" size="15">
                                @foreach($contents as $content)
                                    <optgroup label="{{ $content->title }}">
                                        <option value="content:{{ $content->id }}" data-content-id="{{ $content->id }}" class="content-option">
                                            [Todo] {{ $content->title }}
                                        </option>
                                        @foreach($content->topics as $topic)
                                            <option value="topic:{{ $topic->id }}" data-content-id="{{ $content->id }}" data-total="{{ $topic->questions->count() }}" data-filter-resolved="{{ $topic->resolved_count ?? 0 }}" data-filter-failer="{{ $topic->failer_count ?? 0 }}">
                                                {{ $topic->title }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-12 col-sm-12 col-md-2 col-lg-2 listbox-controls text-center my-auto">
                        <div class="btn-group d-flex">
                            <button type="button" class="btn btn-sm btn-success m-1" id="add-selected">&gt;&gt;</button>
                            <button type="button" class="btn btn-sm btn-danger m-1" id="remove-selected">&lt;&lt;</button>
                        </div>
                        
                        <button type="button" class="btn btn-sm w-100 btn-secondary m-1" id="clear-all">Limpar tudo</button>
                    </div>

                    <div class="col-12 col-sm-12 col-md-5 col-lg-5 listbox-panel">
                        <h5>Selecionados</h5>
                        <input type="text" class="form-control mb-2" id="search-selected" placeholder="Pesquisar...">
                        <div class="scroll-box">
                            <select multiple id="selected-topics" name="selected_topics[]" class="form-control" size="15"></select>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('created-notebook') }}" id="create-notebook-form" class="row">
                    @csrf
                    <div class="col-12 col-sm-12 col-md-5 col-lg-5">
                        <div class="p-6">
                            <small class="text-light fw-medium">+Filtros</small>
                            <div class="form-check mt-4">
                                <input name="filter" class="form-check-input" type="radio" value="filter_resolved" id="filter_resolved">
                                <label class="form-check-label" for="filter_resolved">Eliminar questões já Resolvidas</label>
                            </div>
                            <div class="form-check">
                                <input name="filter" class="form-check-input" type="radio" value="filter_failer" id="filter_failer">
                                <label class="form-check-label" for="filter_failer">Mostrar apenas as que eu já Errei</label>
                            </div>
                            {{-- <div class="form-check">
                                <input name="filter" class="form-check-input" type="radio" value="filter_favorites" id="filter_favorites">
                                <label class="form-check-label" for="filter_favorites">Mostrar apenas as questões Favoritas</label>
                            </div> --}}
                        </div>
                    </div>

                     <div class="col-12 col-sm-12 col-md-2 col-lg-2">

                     </div>

                    <div class="col-12 col-sm-12 col-md-5 col-lg-5">
                        <div class="p-6">
                            <small id="total-questions-info" class="text-light fw-medium">Foram encontradas: 0 Questões</small>
                            <div class="form-floating form-floating-outline mt-4">
                                <input type="number" name="quanty_questions" id="quanty_questions" class="form-control" placeholder="N° de Questões" required min="1"/>
                                <label for="quanty_questions">N° de Questões</label>
                            </div>
                            <div class="form-floating form-floating-outline mt-4">
                                <input type="text" name="title" id="title" class="form-control" placeholder="Dê um nome ao Caderno:" required/>
                                <label for="title">Dê um nome ao Caderno:</label>
                            </div>
                            <input type="hidden" name="topics" id="selected-topics-hidden"/>
                            <button type="submit" class="btn btn-success w-100 mt-2">Gerar</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>      
    </div>

    <script>
        document.querySelectorAll('input[name="filter"]').forEach(radio => {
            radio.addEventListener('click', function () {
                if (this.checked && this.dataset.checked === 'true') {
                    this.checked = false;
                    this.dataset.checked = 'false';
                } else {
                    document.querySelectorAll('input[name="filter"]').forEach(r => r.dataset.checked = 'false');
                    this.dataset.checked = 'true';
                }

                updateQuestionCount();
            });
        });

        function updateQuestionCount() {
            const selectedTopics    = document.querySelectorAll('#selected-topics option');
            const activeFilter      = document.querySelector('input[name="filter"]:checked');
            const inputElement      = document.querySelector('#quanty_questions');
            const infoElement       = document.querySelector('#total-questions-info');
            let total = 0;

            selectedTopics.forEach(option => {
                if (!activeFilter) {
                    total += parseInt(option.dataset.total || 0);
                } else {
                    if (activeFilter.value === 'filter_resolved') {
                        total += (parseInt(option.dataset.total || 0) - parseInt(option.dataset.filterResolved || 0));
                    } else if (activeFilter.value === 'filter_failer') {
                        total += parseInt(option.dataset.filterFailer || 0);
                    }
                }
            });

            if (infoElement) {
                infoElement.textContent = `Foram encontradas: ${total} Questões`;
            }

            if (inputElement) {
                inputElement.max = total;
                if (parseInt(inputElement.value) > total) {
                    inputElement.value = total;
                }
            }
        }

        document.getElementById('quanty_questions').addEventListener('input', function () {
            const max = parseInt(this.max, 10);
            if (parseInt(this.value, 10) > max) {
                this.value = max;
            }
        });

        $(document).ready(function () {

            function filterOptions(inputId, selectId) {
                const search = $(inputId).val().toLowerCase();
                $(selectId + ' option').each(function () {
                    const text = $(this).text().toLowerCase();
                    $(this).toggle(text.includes(search));
                });
            }

            $('#search-available').on('input', function () {
                filterOptions('#search-available', '#available-topics');
            });

            $('#search-selected').on('input', function () {
                filterOptions('#search-selected', '#selected-topics');
            });

            $('#add-selected').on('click', function () {
                $('#available-topics option:selected').each(function () {

                    const val = $(this).val();

                    if ($('#selected-topics option[value="' + val + '"]').length === 0) {
                        $('#selected-topics').append($(this).clone());
                    }

                    if (val.startsWith('content:')) {
                        const contentId = val.split(':')[1];
                        $('#available-topics option[data-content-id="' + contentId + '"]').each(function () {
                            const tVal = $(this).val();
                            if (tVal !== val && $('#selected-topics option[value="' + tVal + '"]').length === 0) {
                                $('#selected-topics').append($(this).clone());
                            }
                        });
                    }
                });

                updateQuestionCount();
            });

            $('#remove-selected').on('click', function () {
                
                const toRemove = [];

                $('#selected-topics option:selected').each(function () {
                    const val = $(this).val();

                    if (val.startsWith('content:')) {

                        const contentId = val.split(':')[1];
                        $('#selected-topics option[data-content-id="' + contentId + '"]').each(function () {
                            toRemove.push($(this).val());
                        });
                    }

                    toRemove.push(val);
                });

                toRemove.forEach(function (val) {
                    $('#selected-topics option[value="' + val + '"]').remove();
                });

                updateQuestionCount();
            });


            $('#clear-all').on('click', function () {
                $('#selected-topics').empty();
                updateQuestionCount();
            });

            $('#available-topics').on('dblclick', 'option', function () {
                $(this).prop('selected', true);
                $('#add-selected').click();
            });
        });

        $('#create-notebook-form').on('submit', function (e) {

            let selectedTopics = [];

            $('#selected-topics option').each(function () {
                const val = $(this).val();
                if (val.startsWith('topic:')) {
                    selectedTopics.push(val.split(':')[1]);
                }
            });

            $('#selected-topics-hidden').val(JSON.stringify(selectedTopics));
        });
    </script>
@endsection