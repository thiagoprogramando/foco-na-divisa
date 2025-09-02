@extends('app.layout')
@section('content')

    <div class="col-12 col-sm-12 col-md-7 col-lg-7">
        <div class="card p-3">
            <form action="{{ route('updated-product', ['uuid' => $product->uuid]) }}" method="POST" enctype="multipart/form-data" class="row">
                @csrf
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-3">
                    <div class="form-floating form-floating-outline mb-2">
                        <input type="text" class="form-control" name="name" placeholder="Ex: Semestral, Ebook: Mais um Ebook" value="{{ $product->name }}">
                        <label>Título:</label>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-3">
                    <div class="form-floating form-floating-outline mb-2">
                        <input type="file" class="form-control" name="image" id="image" placeholder="Capa">
                        <label for="image" class="form-label">Capa</label>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-3">
                    <div class="form-floating form-floating-outline mb-2">
                        <input type="text" class="form-control money" name="value" oninput="maskValue(this)" value="{{ $product->value }}">
                        <label>Valor:</label>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                    <div class="form-floating form-floating-outline mb-2">
                        <div class="select2-primary">
                            <select name="type" id="type" class="select2 form-select" required>
                                <option value="Opções Disponíveis" selected>Opções Disponíveis</option>
                                <option value="plan" @selected($product->type == 'plan')>Assinatura (Planos)</option>
                                <option value="midia" @selected($product->type == 'midia')>Mídias</option>
                                <option value="community" @selected($product->type == 'community')>Comunidade</option>
                            </select>
                        </div>
                        <label for="type">Tipo</label>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                    <div class="form-floating form-floating-outline mb-2">
                        <div class="select2-primary">
                            <select name="time" id="time" class="select2 form-select" required>
                                <option value="Opções Disponíveis" selected>Opções Disponíveis</option>
                                <option value="free" @selected($product->time == 'free')>Gratuito</option>
                                <option value="monthly" @selected($product->time == 'monthly')>Mensal</option>
                                <option value="semi-annual" @selected($product->time == 'semi-annual')>Semestral</option>
                                <option value="yearly" @selected($product->time == 'yearly')>Anual</option>
                                <option value="lifetime" @selected($product->time == 'lifetime')>vitalício</option>
                            </select>
                        </div>
                        <label for="time">Prazo</label>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="form-floating form-floating-outline mb-2">
                        <textarea class="form-control h-px-100 editor" name="description" id="description" placeholder="Descrição do Produto:">{{ $product->description }}</textarea>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                    <div class="form-floating form-floating-outline mb-2">
                        <div class="select2-primary">
                            <select name="status" id="status" class="select2 form-select" required>
                                <option value="Opções Disponíveis" selected>Opções Disponíveis</option>
                                <option value="1" @selected($product->status == 1)>Ativo</option>
                                <option value="2" @selected($product->status == 2)>Inativo</option>
                            </select>
                        </div>
                        <label for="status">Status</label>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                    <button type="submit" class="btn btn-primary mt-1">Salvar</button>
                </div>
            </form>
        </div>  
    </div>

    <div class="col-12 col-sm-12 col-md-5 col-lg-5">
        <div class="card p-3">
    
            <div class="card-header mb-0">
                <div class="d-flex justify-content-between">
                    <h5 class="mb-1">Itens Associados</h5>
                    <div class="dropdown">
                        <button class="btn btn-text-secondary rounded-pill text-muted border-0 p-1 waves-effect waves-light" type="button" id="ItensOverview" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-more-2-line ri-20px"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="ItensOverview">
                            @switch($product->type)
                                @case('plan')
                                    <button type="button" class="dropdown-item waves-effect" data-bs-toggle="collapse" href="#collapsePlan" role="button" aria-expanded="false" aria-controls="collapsePlan">Adicionar</button>
                                    @break
                                @case('midia')
                                    <button type="button" class="dropdown-item waves-effect" data-bs-toggle="collapse" href="#collapseMidia" role="button" aria-expanded="false" aria-controls="collapseMidia">Adicionar</button>
                                    @break
                                @case('community')
                                    <button type="button" class="dropdown-item waves-effect" data-bs-toggle="collapse" href="#collapseCommunity" role="button" aria-expanded="false" aria-controls="collapseCommunity">Adicionar</button>
                                    @break
                                @default
                                    <button type="button" class="dropdown-item waves-effect">Sem opções disponíveis</button>
                                    @break
                            @endswitch
                            <button type="button" class="dropdown-item waves-effect" onclick="location.reload(true)">Atualizar</button>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center card-subtitle">
                    <div class="me-2">Publicações, Mensagens e Mídias</div>
                </div>
            </div>

            <div class="card-body mt-0">

                <div class="collapse" id="collapsePlan">
                    <div class="d-grid d-sm-flex p-4 border">
                          Assinaturas (Tipo Plano) não possuem itens associados.
                    </div>
                </div>

                <div class="collapse" id="collapseMidia">
                    <div class="d-grid d-sm-flex p-4 border">
                        <form action="{{ route('created-product-item-file', ['uuid' => $product->uuid]) }}" method="POST" enctype="multipart/form-data" class="row">
                            @csrf
                            <div class="col-12">
                                <div class="form-floating form-floating-outline mb-2">
                                    <input type="text" class="form-control" name="title" placeholder="Ex: Semestral, Ebook: Mais um Ebook, " required>
                                    <label>Título:</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-4">
                                    <label for="file" class="form-label">Arquivo (Imagens, PDFs e etc)</label>
                                    <input type="file" class="form-control" name="file" id="file" multiple>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success w-100">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="collapse" id="collapseCommunity">
                    <div class="d-grid d-sm-flex p-4 border">
                        <form action="{{ route('created-product-item-post', ['uuid' => $product->uuid]) }}" method="POST" enctype="multipart/form-data" class="row">
                            @csrf
                            <div class="col-12">
                                <div class="form-floating form-floating-outline mb-2">
                                    <input type="text" class="form-control" name="title" placeholder="Ex: Semestral, Ebook: Mais um Ebook, " required>
                                    <label>Título:</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating form-floating-outline mb-2">
                                    <textarea class="form-control h-px-100" name="message" id="message" placeholder="Mensagem..."></textarea>
                                    <label for="message">Mensagem</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-2">
                                    <label for="file" class="form-label">Arquivo (Imagens, PDFs e etc)</label>
                                    <input type="file" class="form-control" name="file" id="file" multiple>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success w-100">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="demo-inline-spacing mt-4">
                    <div class="list-group">
                        @switch($product->type)
                            @case('plan')
                                Assinaturas (Tipo Plano) não possuem itens associados.
                                @break
                            @case('midia')
                                @foreach ($product->getFilesList() as $file)
                                    <a href="javascript:void(0);" class="list-group-item list-group-item-action flex-column align-items-start waves-effect">
                                        <div class="d-flex justify-content-between w-100">
                                            <h5 class="mb-1">{{ $file['title'] }}</h5>
                                            <small>{{ \Carbon\Carbon::parse($file['date'])->format('d/m/Y') }}</small>
                                        </div>

                                        <form action="{{ route('deleted-product-item-file', ['uuid' => $product->uuid, 'id' => $file['id']]) }}" method="POST" class="btn-group mt-2 mb-2" role="group">
                                            @csrf
                                            <button type="button" onclick="window.open('{{ $file['url'] }}', '_blank')" class="btn btn-sm btn-outline-secondary waves-effect">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            <button type="submit" class="btn btn-sm btn-outline-secondary waves-effect">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    </a>
                                @endforeach
                                @break
                            @case('community')
                                @foreach ($product->getPostsList() as $post)
                                    <a href="javascript:void(0);" class="list-group-item list-group-item-action flex-column align-items-start waves-effect">
                                        <div class="d-flex justify-content-between w-100">
                                            <h5 class="mb-1">{{ $post['title'] }}</h5>
                                            <small>{{ \Carbon\Carbon::parse($post['date'])->format('d/m/Y') }}</small>
                                        </div>

                                        <p class="mb-1">
                                            {{ $post['message'] }}
                                        </p>

                                        <form action="{{ route('deleted-product-item-post', ['uuid' => $product->uuid, 'id' => $post['id']]) }}" method="POST" class="btn-group mt-2 mb-2" role="group">
                                            @csrf
                                            <button type="button" onclick="window.open('{{ $post['url'] }}', '_blank')" class="btn btn-sm btn-outline-secondary waves-effect">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            <button type="submit" class="btn btn-sm btn-outline-secondary waves-effect">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    </a>
                                @endforeach
                                @break
                            @default
                                <button type="button" class="dropdown-item waves-effect">Sem opções disponíveis</button>
                                @break
                        @endswitch
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <script src="https://cdn.tiny.cloud/1/tgezwiu6jalnw1mma8qnoanlxhumuabgmtavb8vap7357t22/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="{{ asset('assets/js/product.js') }}"></script>
    <script src="{{ asset('assets/js/tinymce.js') }}"></script>
@endsection