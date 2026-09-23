@extends('layouts.account')

@section('account-content')
    <h2 class="content-title">Siparişlerim</h2>

    @if(isset($orders) && $orders->count() > 0)
        <div class="table-responsive mb-4">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Sipariş No</th>
                        <th>Tarih</th>
                        <th>Tutar</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td><strong>{{ $order->order_number }}</strong></td>
                            <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                            <td>{{ number_format($order->total_price, 2, ',', '.') }} TL</td>
                            <td>
                                @switch($order->status)
                                    @case('pending')
                                        <span class="badge bg-warning text-dark">Beklemede</span>
                                        @break
                                    @case('preparing')
                                        <span class="badge bg-info text-dark">Hazırlanıyor</span>
                                        @break
                                    @case('shipped')
                                        <span class="badge bg-primary">Kargoya Verildi</span>
                                        @break
                                    @case('delivered')
                                        <span class="badge bg-success">Teslim Edildi</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger">İptal Edildi</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                @endswitch
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


    @else
        <div class="empty-state p-4 text-center text-muted">
            <p class="mb-0">Henüz bir siparişiniz bulunmuyor.</p>
            <a href="{{ route('products') }}" class="btn btn-outline-dark mt-2" style="border-radius: 0; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px;">Alışverişe Devam Et</a>
        </div>
    @endif
@endsection