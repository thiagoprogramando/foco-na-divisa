@extends('app.layout')
@section('content')

    <div class="col-12 col-sm-12 col-md-7 col-lg-7">   

        <div class="kanban-add-new-board">
            <label class="kanban-add-board-btn mb-5" for="kanban-add-board-input" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="ri-filter-line"></i>
                <span class="align-middle">Filtrar</span>
            </label>
        </div>

        <div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
            <form action="{{ route('invoices') }}" method="GET">
                @csrf
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel1">Dados da Pesquisa</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body row">
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-floating form-floating-outline mb-2">
                                    <div class="select2-primary">
                                        <select name="payment_status" id="payment_status" class="select2 form-select" required>
                                            <option value="Opções Disponíveis" selected>Opções Disponíveis</option>
                                            <option value="1">Aprovado</option>
                                            <option value="0">Pendente</option>
                                            <option value="2">Cancelado</option>
                                        </select>
                                    </div>
                                    <label for="payment_status">Status</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-floating form-floating-outline mb-2">
                                    <div class="select2-primary">
                                        <select name="product_id" id="product_id" class="select2 form-select" required>
                                            <option value="Opções Disponíveis" selected>Opções Disponíveis</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label for="product_id">Produto/Plano</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-floating form-floating-outline mb-2">
                                    <input type="date" name="start_date" id="start_date" class="form-control" placeholder="Data Inicial"/>
                                    <label for="start_date">Data Inicial</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-floating form-floating-outline mb-2">
                                    <input type="date" name="end_date" id="end_date" class="form-control" placeholder="Data Final"/>
                                    <label for="end_date">Data Final</label>
                                </div>
                            </div>
                            @if (Auth::user()->role === 'admin')
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <div class="select2-primary">
                                            <select name="user_id" id="user_id" class="select2 form-select" required>
                                                <option value="Opções Disponíveis" selected>Opções Disponíveis</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label for="user_id">Usuário/Cliente</label>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer btn-group">
                            <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal"> Fechar </button>
                            <button type="submit" class="btn btn-success">Enviar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="card demo-inline-spacing">
            <div class="card-header align-items-center">
                <h5 class="card-action-title mb-0">Faturas</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @foreach ($invoices as $invoice)
                        <li class="mb-4">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="me-2">
                                        <h6 class="mb-1">{{ $invoice->product->name ?? $invoice->simulated->title }} - R$ {{ number_format($invoice->value, 2, ',', '.') }}</h6>
                                        <small>{{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}</small> | <small><a href="{{ $invoice->payment_status !== 2 ? $invoice->payment_url : '#invoices' }}" target="_blank" rel="noopener noreferrer">Acessar</a></small>
                                    </div>
                                </div>
                                <div class="ms-auto">
                                    <a href="javascript:;">{!! $invoice->statusLabel() !!}</a>
                                </div>
                            </div>
                        </li>
                    @endforeach
                    
                    <li class="text-center">
                        @if ($invoices->hasPages())
                            {{ $invoices->links() }}
                        @else
                            <a href="javascript:;">Não há mais dados.</a>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>

@endsection