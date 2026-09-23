@extends('layouts.account') 

@section('account-content')
    <h2 class="content-title">Favorilerim</h2>
    
    @if(isset($wishlists) && $wishlists->count() > 0)
        <div class="row g-3">
            @foreach($wishlists as $item)
            @php
                    $productSize = $item->product->size ?? $item->product->variant ?? $item->product->attribute ?? $item->size ?? null;
                @endphp
                <div class="col-md-4 border p-3">
                    @if($item->product && $item->product->image)
                        <img src="{{ asset($item->product->image) }}" 
                             alt="{{ $item->product->name }}"  
                             class="img-fluid mb-2" style="max-height: 150px;">
                    @else
                        <img src="{{ asset('assets/images/default-product.jpg') }}" 
                             alt="Ürün Görseli Yok" class="img-fluid mb-2">
                    @endif

                    <h6>{{ $item->product->name ?? 'Ürün Adı Yok' }}</h6>
                    
                    {{-- Beden Bilgisi Eklendi --}}
                    @if(!empty($productSize))
                        <div class="mb-2">
                            <span class="badge bg-light text-dark border" style="font-size: 0.75rem; font-weight: 500;">
                                <i class="fa-solid fa-ruler-combined me-1 text-secondary"></i> Beden: {{ $productSize }}
                            </span>
                        </div>
                    @endif
                    
                    {{-- Fiyat + Kargo Ücreti Hesaplama --}}
                    @php
                        $productPrice = $item->product->price ?? 0;
                        $shippingPrice = $item->product->shipping_price ?? 0;
                        $totalPrice = $productPrice + $shippingPrice;
                    @endphp

                    <p class="text-muted mb-1">
                        <span class="fw-bold text-dark">{{ number_format($totalPrice, 2) }} TL</span>
                        <!-- ALIŞVERİŞ KISMI ŞİMDİLİK KALDIRDIM. -->
                        @if($shippingPrice > 0)
                            <small class="d-block text-muted" style="font-size: 0.75rem;">
                                (Ürün: {{ number_format($productPrice, 2) }} TL + Kargo: {{ number_format($shippingPrice, 2) }} TL)
                            </small>
                        @else
                            <small class="d-block text-success" style="font-size: 0.75rem;">(Ücretsiz Kargo)</small>
                        @endif
                    </p>
                    
                    <div class="d-flex align-items-center gap-3 mt-3">
                        <form action="{{ route('wishlist.remove', $item->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger">KALDIR</button>
                        </form>
                        
                        <form action="{{ route('cart.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                            <input type="hidden" name="quantity" value="1">
                            <!-- ALIŞVERİŞ KISMI ŞİMDİLİK KALDIRDIM. -->
                            <button type="submit" class="btn btn-sm" style="background: #111111; color: #ffffff; border-radius: 0; padding: 8px 20px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s;">
                                <i class="fa-solid fa-basket-shopping me-2"></i> Sepete Ekle
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">Favori listeniz boş.</div>
        <a href="{{ route('products') }}" class="btn btn-outline-dark mt-2" style="border-radius: 0; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px;">Alışverişe Devam Et</a>
    @endif
@endsection