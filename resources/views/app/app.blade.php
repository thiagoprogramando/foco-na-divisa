@extends('app.layout')
@section('content')

    <div class="col-12 col-sm-12 col-md-8 col-lg-6">
        <div class="card mb-3">
            <div class="card-body text-nowrap">
                <h5 class="card-title mb-1">Olá, <span class="fw-bold">{{ Auth::user()->maskName() }}!</span> 🎉</h5>
                <p class="card-subtitle mb-3">Bem-vindo(a) ao {{ env('APP_NAME') }}</p>
                <h4 class="text-primary mb-0">Plano Atual</h4>
                <p class="mb-3">Aproveite os benefícios da sua conta! 🚀</p>
                <a href="{{ route('create-notebook') }}" class="btn btn-sm btn-primary waves-effect waves-light">Gerar novo Caderno</a>
            </div>
            <img src="{{ asset('assets/img/illustrations/trophy.png') }}" class="position-absolute bottom-0 end-0 me-4" height="140" alt="view sales">
        </div>

        <div class="card mb-3" id="dashboard">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h5 class="mb-1">Evolução</h5>
                    <div class="dropdown">
                        <button class="btn btn-text-secondary rounded-pill text-muted border-0 p-1 waves-effect waves-light" type="button" id="salesOverview" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ri-more-2-line ri-20px"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="salesOverview">
                        <a class="dropdown-item waves-effect" href="{{ route('app') }}/#dashboard">Atualizar</a>
                        <a class="dropdown-item waves-effect" href="javascript:void(0);">Compartilhar</a>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center card-subtitle">
                    <div class="me-2">Você alcançou X questões resolvidas!</div>
                    <div class="d-flex align-items-center text-success">
                        <p class="mb-0 fw-medium">+18%</p>
                        <i class="ri-arrow-up-s-line ri-20px"></i>
                    </div>
                </div>
            </div>
            <div class="card-body d-flex justify-content-between flex-wrap gap-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar">
                        <div class="avatar-initial bg-label-primary rounded">
                        <i class="ri-question-line ri-24px"></i>
                        </div>
                    </div>
                    <div class="card-info">
                        <h5 class="mb-0">{{ Auth::user()->questions->count() }}</h5>
                        <p class="mb-0">Questões</p>
                    </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                    <div class="avatar">
                        <div class="avatar-initial bg-label-info rounded">
                        <i class="ri-book-read-line ri-24px"></i>
                        </div>
                    </div>
                    <div class="card-info">
                        <h5 class="mb-0">{{ Auth::user()->notebooks->count() }}</h5>
                        <p class="mb-0">Cadernos</p>
                    </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                    <div class="avatar">
                        <div class="avatar-initial bg-label-success rounded">
                        <i class="ri-bar-chart-2-line ri-24px"></i>
                        </div>
                    </div>
                    <div class="card-info">
                        <h5 class="mb-0">95%</h5>
                        <p class="mb-0">Progresso</p>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
@endsection