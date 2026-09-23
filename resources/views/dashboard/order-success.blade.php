@extends('layouts.account')

@section('account-content')
    <div class="text-center py-5">
        <h2 class="text-success">Siparişiniz Başarıyla Alındı!</h2>
        <p>Sipariş Numaranız: <strong>{{ $order->order_number }}</strong></p>
        <p>Ödemeniz onaylandı. Siparişiniz hazırlanıyor.</p>
        <a href="{{ url('/') }}" class="btn btn-primary">Ana Sayfaya Dön</a>
    </div>
@endsection