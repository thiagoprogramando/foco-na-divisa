@extends('app.layout')
@section('content')

    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card p-5">
            <form action="{{ route('created-product') }}" method="POST" class="row" enctype="multipart/form-data">
                @csrf
                <div class="col-12 col-sm-12 col-md-9 col-lg-9 mb-3">
                    <div class="form-floating form-floating-outline mb-2">
                        <input type="text" class="form-control" name="name" placeholder="Ex: Semestral, Ebook: Mais um Ebook, " required>
                        <label>Título:</label>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-3 col-lg-3 mb-3">
                    <div class="form-floating form-floating-outline mb-2">
                        <input type="file" class="form-control" name="image" id="image" placeholder="Capa">
                        <label for="image" class="form-label">Capa</label>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-3 col-lg-3 mb-3">
                    <div class="form-floating form-floating-outline mb-2">
                        <input type="text" class="form-control money" name="value" oninput="maskValue(this)" value="0">
                        <label>Valor:</label>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="form-floating form-floating-outline mb-2">
                        <div class="select2-primary">
                            <select name="type" id="type" class="select2 form-select" required>
                                <option value="Opções Disponíveis" selected>Opções Disponíveis</option>
                                <option value="plan">Assinatura (Planos)</option>
                                <option value="midia">Mídias</option>
                                <option value="community">Comunidade</option>
                            </select>
                        </div>
                        <label for="type">Tipo</label>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="form-floating form-floating-outline mb-2">
                        <div class="select2-primary">
                            <select name="time" id="time" class="select2 form-select" required>
                                <option value="Opções Disponíveis" selected>Opções Disponíveis</option>
                                <option value="free">Gratuito</option>
                                <option value="monthly">Mensal</option>
                                <option value="semi-annual">Semestral</option>
                                <option value="yearly">Anual</option>
                                <option value="lifetime">vitalício</option>
                            </select>
                        </div>
                        <label for="time">Prazo</label>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="form-floating form-floating-outline mb-2">
                        <div class="select2-primary">
                            <select name="status" id="status" class="select2 form-select" required>
                                <option value="Opções Disponíveis" selected>Opções Disponíveis</option>
                                <option value="true">Ativo</option>
                                <option value="false">Inativo</option>
                            </select>
                        </div>
                        <label for="status">Status</label>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="form-floating form-floating-outline mb-2">
                        <textarea class="form-control h-px-100 editor" name="description" id="description" placeholder="Descrição do Produto:"></textarea>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6 offset-md-6 col-lg-6 offset-lg-6">
                    <button type="submit" class="btn btn-primary w-100 mt-1">Salvar</button>
                </div>
            </form>
        </div>  
    </div>

    <script src="https://cdn.tiny.cloud/1/tgezwiu6jalnw1mma8qnoanlxhumuabgmtavb8vap7357t22/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="{{ asset('assets/js/product.js') }}"></script>
    <script src="{{ asset('assets/js/tinymce.js') }}"></script>
@endsection