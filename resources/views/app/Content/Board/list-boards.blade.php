@extends('app.layout')
@section('content')

    <div class="col-12">
        <div class="kanban-add-new-board">
            <label class="kanban-add-board-btn" for="kanban-add-board-input" data-bs-toggle="modal" data-bs-target="#createdModal">
                <i class="ri-add-line"></i>
                <span class="align-middle">Nova Banca</span>
            </label>
        </div>

        <div class="modal fade" id="createdModal" tabindex="-1" aria-hidden="true">
            <form action="{{ route('created-board') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel1">Dados da Banca</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Nome" required/>
                                        <label for="name">Nome</label>
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
                                            <input type="text" name="code" id="code" class="form-control" placeholder="Código"/>
                                            <label for="code">Código</label>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" name="city" id="city" class="form-control" placeholder="Cidade"/>
                                                    <label for="city">Cidade</label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-sm-12 col-md-6 col-lg-6 mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <div class="select2-primary">
                                                        <select name="state" id="state" class="select2 form-select">
                                                            <option value="Selecione o estado" selected disabled>Selecione o estado</option>
                                                            <option value="AC">Acre (AC)</option>
                                                            <option value="AL">Alagoas (AL)</option>
                                                            <option value="AP">Amapá (AP)</option>
                                                            <option value="AM">Amazonas (AM)</option>
                                                            <option value="BA">Bahia (BA)</option>
                                                            <option value="CE">Ceará (CE)</option>
                                                            <option value="DF">Distrito Federal (DF)</option>
                                                            <option value="ES">Espírito Santo (ES)</option>
                                                            <option value="GO">Goiás (GO)</option>
                                                            <option value="MA">Maranhão (MA)</option>
                                                            <option value="MT">Mato Grosso (MT)</option>
                                                            <option value="MS">Mato Grosso do Sul (MS)</option>
                                                            <option value="MG">Minas Gerais (MG)</option>
                                                            <option value="PA">Pará (PA)</option>
                                                            <option value="PB">Paraíba (PB)</option>
                                                            <option value="PR">Paraná (PR)</option>
                                                            <option value="PE">Pernambuco (PE)</option>
                                                            <option value="PI">Piauí (PI)</option>
                                                            <option value="RJ">Rio de Janeiro (RJ)</option>
                                                            <option value="RN">Rio Grande do Norte (RN)</option>
                                                            <option value="RS">Rio Grande do Sul (RS)</option>
                                                            <option value="RO">Rondônia (RO)</option>
                                                            <option value="RR">Roraima (RR)</option>
                                                            <option value="SC">Santa Catarina (SC)</option>
                                                            <option value="SP">São Paulo (SP)</option>
                                                            <option value="SE">Sergipe (SE)</option>
                                                            <option value="TO">Tocantins (TO)</option>
                                                        </select>
                                                    </div>
                                                    <label for="state">Estado</label>
                                                </div>
                                            </div>
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
    </div>

    <div class="col-12 col-sm-12 col-md-7 col-lg-7">
        <div class="card demo-inline-spacing">
            <div class="list-group p-0 m-0">
                @foreach ($boards as $board)
                    <div class="list-group-item list-group-item-action d-flex align-items-center cursor-pointer waves-effect waves-light">
                        <div class="w-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="user-info">
                                    <h6 class="mb-1 fw-normal">{{ $board->name }}</h6>
                                    <div class="d-flex align-items-center">
                                        <div class="user-status me-2 d-flex align-items-center">
                                            <span class="badge badge-dot bg-info me-1"></span>
                                            <small>Questões: {{ $board->questions->count() }}</small>
                                        </div>
                                        <small class="text-muted ms-1" title="{{ $board->city .'/'. $board->state  }}">{{ $board->city .'/'. $board->state  }}</small>
                                    </div>
                                </div>
                                <form action="{{ route('deleted-board', ['id' => $board->id]) }}" method="POST" class="add-btn delete">
                                    @csrf
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#updatedModal{{ $board->id }}" title="Editar"><i class="ri-edit-box-line"></i></button>
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="ri-delete-bin-line"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>  

                    <div class="modal fade" id="updatedModal{{ $board->id }}" tabindex="-1" aria-hidden="true">
                        <form action="{{ route('updated-board', ['id' => $board->id]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="exampleModalLabel1">Dados da Banca</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-12 mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" name="name" id="name" class="form-control" placeholder="Nome" value="{{ $board->name }}"/>
                                                    <label for="name">Nome</label>
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
                                                        <input type="text" name="code" id="code" class="form-control" placeholder="Código" value="{{ $board->code }}"/>
                                                        <label for="code">Código</label>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="text" name="city" id="city" class="form-control" placeholder="Cidade" value="{{ $board->city }}"/>
                                                                <label for="city">Cidade</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-sm-12 col-md-6 col-lg-6 mb-2">
                                                            <div class="form-floating form-floating-outline">
                                                                <div class="select2-primary">
                                                                    <select name="state" id="state" class="select2 form-select">
                                                                        <option value="" selected disabled>Selecione o estado</option>
                                                                        <option value="AC" @selected($board->state == 'AC')>Acre (AC)</option>
                                                                        <option value="AL" @selected($board->state == 'AL')>Alagoas (AL)</option>
                                                                        <option value="AP" @selected($board->state == 'AP')>Amapá (AP)</option>
                                                                        <option value="AM" @selected($board->state == 'AM')>Amazonas (AM)</option>
                                                                        <option value="BA" @selected($board->state == 'BA')>Bahia (BA)</option>
                                                                        <option value="CE" @selected($board->state == 'CE')>Ceará (CE)</option>
                                                                        <option value="DF" @selected($board->state == 'DF')>Distrito Federal (DF)</option>
                                                                        <option value="ES" @selected($board->state == 'ES')>Espírito Santo (ES)</option>
                                                                        <option value="GO" @selected($board->state == 'GO')>Goiás (GO)</option>
                                                                        <option value="MA" @selected($board->state == 'MA')>Maranhão (MA)</option>
                                                                        <option value="MT" @selected($board->state == 'MT')>Mato Grosso (MT)</option>
                                                                        <option value="MS" @selected($board->state == 'MS')>Mato Grosso do Sul (MS)</option>
                                                                        <option value="MG" @selected($board->state == 'MG')>Minas Gerais (MG)</option>
                                                                        <option value="PA" @selected($board->state == 'PA')>Pará (PA)</option>
                                                                        <option value="PB" @selected($board->state == 'PB')>Paraíba (PB)</option>
                                                                        <option value="PR" @selected($board->state == 'PR')>Paraná (PR)</option>
                                                                        <option value="PE" @selected($board->state == 'PE')>Pernambuco (PE)</option>
                                                                        <option value="PI" @selected($board->state == 'PI')>Piauí (PI)</option>
                                                                        <option value="RJ" @selected($board->state == 'RJ')>Rio de Janeiro (RJ)</option>
                                                                        <option value="RN" @selected($board->state == 'RN')>Rio Grande do Norte (RN)</option>
                                                                        <option value="RS" @selected($board->state == 'RS')>Rio Grande do Sul (RS)</option>
                                                                        <option value="RO" @selected($board->state == 'RO')>Rondônia (RO)</option>
                                                                        <option value="RR" @selected($board->state == 'RR')>Roraima (RR)</option>
                                                                        <option value="SC" @selected($board->state == 'SC')>Santa Catarina (SC)</option>
                                                                        <option value="SP" @selected($board->state == 'SP')>São Paulo (SP)</option>
                                                                        <option value="SE" @selected($board->state == 'SE')>Sergipe (SE)</option>
                                                                        <option value="TO" @selected($board->state == 'TO')>Tocantins (TO)</option>
                                                                    </select>
                                                                </div>
                                                                <label for="state">Estado</label>
                                                            </div>
                                                        </div>
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
                @endforeach
            </div>

            <div class="card-footer d-flex justify-content-center">
                {{ $boards->links() }}
            </div>
        </div>
    </div>

@endsection