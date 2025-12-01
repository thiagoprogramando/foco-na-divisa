@extends('app.layout')
@section('content')

    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        @if (Auth::user()->role === 'admin')
            <div class="kanban-add-new-board mb-3">
                <label class="kanban-add-board-btn" for="kanban-add-board-input" data-bs-toggle="modal" data-bs-target="#createdModal">
                    <i class="ri-add-line"></i>
                    <span class="align-middle">Novo Simulado</span>
                </label>
            </div>

            <div class="modal fade" id="createdModal" tabindex="-1" aria-hidden="true">
                <form action="{{ route('created-simulated') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="exampleModalLabel1">Dados do Simulado</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-floating form-floating-outline mb-2">
                                            <input type="text" name="title" id="title" class="form-control" placeholder="Título" required/>
                                            <label for="title">Título</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                        <div class="form-floating form-floating-outline">
                                            <input type="date" name="date_start" id="date_start" class="form-control" placeholder="Data de início"/>
                                            <label for="date_start">Data de início</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                        <div class="form-floating form-floating-outline">
                                            <input type="date" name="date_end" id="date_end" class="form-control" placeholder="Data de término"/>
                                            <label for="date_end">Data de término</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                        <div class="form-floating form-floating-outline">
                                            <div class="select2-primary">
                                                <select name="status" id="status" class="select2 form-select">
                                                    <option value="active" selected>Ativo</option>
                                                    <option value="draft">Rascunho</option>
                                                    <option value="completed">Concluído</option>
                                                </select>
                                            </div>
                                            <label for="status">Status</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" name="value" id="value" class="form-control money" oninput="maskValue(this)" placeholder="Valor (Mín R$ 5,00)"/>
                                            <label for="value">Valor (Mín R$ 5,00)</label>
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
                                                <textarea class="form-control h-px-100" name="description" id="description" placeholder="Descrição"></textarea>
                                                <label for="description">Descrição</label>
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
        @endif

        <div class="card bg-dark mb-3">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h3 class="mb-1 text-white">Simulados</h3>
                    <button class="btn btn-success" id="shepherd-example" onclick="startShepherdTour()"><i class="ri-graduation-cap-line"></i></button>
                </div>
                <div class="d-flex align-items-center card-subtitle">
                    <div class="me-2 text-muted">Escolha o simulado que deseja realizar!</div>
                </div>
            </div>
        </div>   
        
        
        <div class="row">
            @foreach ($simulateds as $simulated)
                <div class="col-12 col-xxl-4 col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="bg-label-primary text-center mb-6 pt-2 rounded-3">
                                <img class="img-fluid" src="{{ $simulated->image ? asset('storage/'.$simulated->image) : asset('assets/img/illustrations/faq-illustration.png') }}" alt="Boy card image">
                            </div>
                            <h5 class="mb-1">{{ $simulated->title }}</h5>
                            <p class="mb-6">
                                {{ $simulated->description }}
                            </p>
                            <h4>
                                <span class="badge bg-label-warning">
                                    R$ {{ number_format($simulated->value, 2, ',', '.') }}
                                </span>
                            </h4>
                            <div class="row mb-6 g-4">
                                <div class="col-6">
                                    <div class="d-flex">
                                        <div class="avatar flex-shrink-0 me-4">
                                            <span class="avatar-initial rounded-3 bg-label-primary"><i class="ri-calendar-line ri-24px"></i></span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-nowrap fw-normal">{{ \Carbon\Carbon::parse($simulated->date_start)->format('d M Y') }}</h6>
                                            <small>Início</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex">
                                        <div class="avatar flex-shrink-0 me-4">
                                            <span class="avatar-initial rounded-3 bg-label-primary"><i class="ri-time-line ri-24px"></i></span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-nowrap fw-normal">{{ \Carbon\Carbon::parse($simulated->date_end)->format('d M Y') }}</h6>
                                            <small>Término</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if ($simulated->hasInvoice(Auth::id(), 1))
                                <a href="{{ route('review-simulated', ['uuid' => $simulated->uuid]) }}" class="btn btn-primary w-100 mb-2">ACESSAR SIMULADO</a>
                            @elseif ($simulated->date_end < now())
                                <button type="button" class="btn btn-dark w-100 mb-2">INDISPONÍVEL</button>
                            @else
                                <button data-bs-toggle="modal" data-bs-target="#buyModal{{ $simulated->uuid }}" class="btn btn-success w-100 mb-2">COMPRAR</button>
                            @endif
                            @if (Auth::user()->role === 'admin')
                                <a href="{{ route('simulated', ['uuid' => $simulated->uuid]) }}" class="btn btn-warning w-100">EDITAR</a>
                            @endif

                            <div class="modal fade" id="buyModal{{ $simulated->uuid }}" data-simulated-uuid="{{ $simulated->uuid }}" tabindex="-1" aria-hidden="true">
                                <form action="{{ route('buy-simulated', ['uuid' => $simulated->uuid]) }}" method="POST">
                                    @csrf
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="exampleModalLabel1">Dados da Compra</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-12 col-sm-12 col-md-8 col-lg-8 mb-2">
                                                        <div class="form-floating form-floating-outline mb-2">
                                                            <select name="payment_method" id="payment_method_{{ $simulated->uuid }}" class="form-select" required>
                                                                <option value="PIX">Pix</option>
                                                                <option value="CREDIT_CARD">Cartão de Crédito</option>
                                                            </select>
                                                            <label for="payment_method_{{ $simulated->uuid }}">Forma de Pagamento</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-sm-12 col-md-4 col-lg-4 mb-2">
                                                        <div class="form-floating form-floating-outline">
                                                            <select name="payment_installments" id="payment_installments_{{ $simulated->uuid }}" class="form-select" required></select>
                                                            <label for="payment_installments_{{ $simulated->uuid }}">Parcelas</label>
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
                    </div>
                </div>
            @endforeach
        </div>
            
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('div.modal[id^="buyModal"]').forEach(function(modalEl) {
                modalEl.addEventListener('show.bs.modal', function (event) {

                    const triggerButton         = event.relatedTarget;
                    const simulatedUuid           = triggerButton?.dataset?.simulatedUuid ?? modalEl.dataset.simulatedUuid;
                    const methodSelect          = modalEl.querySelector('#payment_method_' + simulatedUuid);
                    const installmentsSelect    = modalEl.querySelector('#payment_installments_' + simulatedUuid);

                    if (!methodSelect || !installmentsSelect) return;

                    function populateInstallments(method) {
                        if (method === 'PIX') {
                            installmentsSelect.innerHTML = '<option value="1">1x</option>';
                            installmentsSelect.disabled = true;
                        } else if (method === 'CREDIT_CARD') {
                            let max     = 2;
                            let options = '';
                            for (let i = 1; i <= max; i++) {
                                options += `<option value="${i}">${i}x</option>`;
                            }
                            installmentsSelect.innerHTML = options;
                            installmentsSelect.disabled = false;
                        } else {
                            installmentsSelect.innerHTML = '<option value="1">1x</option>';
                            installmentsSelect.disabled = true;
                        }
                    }

                    populateInstallments(methodSelect.value);
                    const onMethodChange = function () {
                        populateInstallments(this.value);
                    };

                    methodSelect.addEventListener('change', onMethodChange);
                    const onHidden = function () {
                        methodSelect.removeEventListener('change', onMethodChange);
                        modalEl.removeEventListener('hidden.bs.modal', onHidden);
                    };

                    modalEl.addEventListener('hidden.bs.modal', onHidden);
                });
            });
        });
    </script>
@endsection