@extends('app.layout')
@section('content')

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/front-page-pricing.css') }}"/>

    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        
        <section class="section-py first-section-pt">
            <div class="container">
                <h2 class="text-center mb-2">Planos</h2>
                <p class="text-center px-sm-12 mb-5">
                    Todos os planos disponíveis para você escolher o melhor para sua necessidade!
                </p>

                <div class="pricing-plans row mx-0 gap-6">
                    
                    @foreach ($products as $product)
                        <div class="col-lg px-0">
                            <div class="card border shadow-none">
                                <div class="card-body pt-12">
                                    <div class="mt-3 mb-5 text-center">
                                        <img src="../../assets/img/illustrations/pricing-basic.png" alt="Basic Image" height="120" />
                                    </div>
                                    <h4 class="card-title text-center text-capitalize mb-2">{{ $product->name }}</h4>
                                    <div class="text-center">
                                        <div class="d-flex justify-content-center">
                                            <sup class="h6 pricing-currency mt-2 mb-0 me-1 text-body fw-normal">R$</sup>
                                            <h1 class="mb-0 text-primary">{{ $product->value }}</h1>
                                            <sub class="h6 pricing-duration mt-auto mb-1 text-body fw-normal">/{{ $product->timeLabel() }}</sub>
                                        </div>
                                    </div>

                                    <div class="ps-6 my-5 pt-4">
                                        {!! $product->description !!}
                                    </div>
                                    <form action="{{ route('buy-product', ['product' => $product->uuid]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success d-grid w-100" @disabled($product->hasInvoice())>Escolher Plano</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>
    </div>

@endsection