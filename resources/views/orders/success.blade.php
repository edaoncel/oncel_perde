@extends('layouts.app') {{-- Kendi ana layout adınız neyse onu yazın --}}

@section('content')
<div class="container py-5 text-center">
    <div class="card shadow-sm border-0 p-5 max-w-600 mx-auto style="max-width: 600px; margin: 0 auto;">
        <div class="mb-4 text-success">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l5-5.25a.75.75 0 0 0-.019-1.06z"/>
            </svg>
        </div>
        
        <h1 class="fw-bold mb-3">Bizi Tercih Ettiğiniz İçin Teşekkür Ederiz!</h1>
        <p class="fs-5 text-muted mb-4">Siparişiniz başarıyla alındı ve hazırlanmaya başladı.</p>
        
        @if(session('order_number'))
            <div class="alert alert-light border d-inline-block px-4 py-2 mb-4">
                Sipariş Numarası: <strong>#{{ session('order_number') }}</strong>
            </div>
        @endif

        <div>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-dark me-2">Siparişlerimi Görüntüle</a>
            <a href="{{ url('/') }}" class="btn btn-dark">Alışverişe Devam Et</a>
        </div>
    </div>
</div>
@endsection