@extends('layouts.account')

@section('account-content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="border-radius: 0; padding: 10px 15px;">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat" style="padding: 12px;"></button>
    </div>
@endif

<h2 class="content-title mb-4">Sepetim</h2>

@if(isset($cart) && $cart->count() > 0)
    @php 
        $subTotal = 0; 
        $shippingTotal = 0;
    @endphp

    @foreach($cart as $item)
        @php 
            $productLineTotal = $item->product->price * $item->quantity; 
            $subTotal += $productLineTotal;

            $itemShipping = ($item->product->shipping_price ?? 0);
            $shippingTotal += $itemShipping;

            $lineTotalWithShipping = $productLineTotal + $itemShipping;

            $productSize = $item->product->size ?? $item->product->variant ?? $item->product->attribute ?? $item->size ?? null;
        @endphp

        <div style="display: flex; background: #fff; border: 1px solid #e0ded9; margin-bottom: 12px;" class="product-box-container">
            
            <div style="flex: 0.8; padding: 15px 20px; display: flex; flex-direction: column; justify-content: center;">
                <div style="font-family: 'Cinzel', serif; font-size: 1rem; color: #623345; text-transform: uppercase; margin-bottom: 10px; letter-spacing: 0.5px; font-weight: 600;">
                    {{ $item->product->name }}
                </div>
                
                @if($item->product && $item->product->image)
                    <img src="{{ asset($item->product->image) }}" alt="{{ $item->product->name }}" class="img-fluid mb-3" style="width: 100%; max-height: 180px; object-fit: cover; border: 1px solid #e0ded9;">
                @else
                    <img src="{{ asset('assets/images/default-product.jpg') }}" alt="Ürün Görseli Yok" class="img-fluid mb-3" style="width: 100%; max-height: 180px; object-fit: cover; border: 1px solid #e0ded9;">
                @endif

                <button type="button" 
                        class="btn btn-sm w-100" 
                        style="color: #623345; border: 1px solid #623345; border-radius: 0; padding: 6px 10px; text-transform: uppercase; font-size: 0.65rem; letter-spacing: 0.5px;"
                        data-bs-toggle="modal" 
                        data-bs-target="#removeModal-{{ $item->id }}">
                    Kaldır
                </button>
            </div>

            <div style="flex: 1; border-left: 1px solid #e0ded9; background: #fcfcfc; display: flex; flex-direction: column; align-items: flex-start; justify-content: center; padding: 15px 20px; text-align: left;">
                <div style="font-size: 0.8rem; color: #1a1a1a; margin-bottom: 6px; text-transform: uppercase;">
                    <span style="color: #6c757d;">Renk:</span> {{ $item->product->color }}
                </div>
                
                @if(!empty($productSize))
                    <div style="font-size: 0.8rem; color: #1a1a1a; margin-bottom: 6px; text-transform: uppercase;">
                        <span style="color: #6c757d;">Beden:</span> {{ $productSize }}
                    </div>
                @endif

                <div style="font-size: 0.8rem; color: #1a1a1a; margin-bottom: 6px; text-transform: uppercase;">
                    <span style="color: #6c757d;">Birim Fiyat:</span> {{ number_format($item->product->price, 2) }} TL
                </div>

                <div style="font-size: 0.8rem; color: #1a1a1a; margin-bottom: 6px; text-transform: uppercase;">
                    <span style="color: #6c757d;">Kargo:</span> 
                    @if($itemShipping > 0)
                        {{ number_format($itemShipping, 2) }} TL
                    @else
                        <span class="badge bg-success" style="border-radius: 0; font-size: 0.6rem;">Ücretsiz Kargo</span>
                    @endif
                </div>

                <div style="font-family: 'Cinzel', serif; font-size: 1rem; color: #623345; font-weight: 600; margin-bottom: 8px;">
                    Toplam: {{ number_format($lineTotalWithShipping, 2) }} TL
                </div>

                <div style="margin-top: 4px;">
                    <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-flex align-items-center">
                        @csrf
                        <span style="font-size: 0.75rem; color: #6c757d; margin-right: 6px; text-transform: uppercase;">Adet:</span>
                        <button type="submit" name="quantity" value="{{ $item->quantity - 1 }}" 
                                class="btn btn-sm btn-outline-secondary py-0 px-2" style="border-radius: 0; font-size: 0.75rem;" {{ $item->quantity <= 1 ? 'disabled' : '' }}>-</button>
                        
                        <span class="mx-2 fw-bold" style="font-size: 0.8rem;">{{ $item->quantity }}</span>
                        
                        <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" 
                                class="btn btn-sm btn-outline-secondary py-0 px-2" style="border-radius: 0; font-size: 0.75rem;">+</button>
                    </form>
                </div>

                <div class="modal fade" id="removeModal-{{ $item->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content" style="border-radius: 0; border: 1px solid #e0ded9;">
                            <div class="modal-body text-center p-4">
                                <h5 style="font-family: 'Cinzel', serif; color: #623345; margin-bottom: 15px;">İşlem Seçin</h5>
                                <p class="mb-3" style="font-size: 0.85rem;">Bu ürünü sepetten kaldırıyorsunuz. Favorilerinize kaydetmek ister misiniz?</p>
                                
                                <div class="d-flex justify-content-center gap-2">
                                    <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn" style="background: #111111; color: #fff; border-radius: 0; padding: 8px 15px; font-size: 0.7rem; text-transform: uppercase;">Sadece Sil</button>
                                    </form>
                                    
                                    <form action="{{ route('cart.moveToWishlist', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn" style="background: #623345; color: #fff; border-radius: 0; padding: 8px 15px; font-size: 0.7rem; text-transform: uppercase;">Favorilere Taşı</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @php 
        $grandTotal = $subTotal + $shippingTotal; 
    @endphp
    
    <div style="background: #fff; border: 1px solid #e0ded9; padding: 20px; margin-top: 15px; text-align: right;">
        <div style="font-size: 0.85rem; margin-bottom: 6px; color: #1a1a1a; text-transform: uppercase;">Ara Toplam: <strong>{{ number_format($subTotal, 2) }} TL</strong></div>
        
        <div style="font-size: 0.85rem; margin-bottom: 10px; color: #1a1a1a; text-transform: uppercase;">
            Kargo Toplamı: 
            @if($shippingTotal > 0)
                <strong>{{ number_format($shippingTotal, 2) }} TL</strong>
            @else
                <span class="badge bg-success" style="border-radius: 0; font-size: 0.65rem;">Ücretsiz Kargo</span>
            @endif
        </div>
        
        <div style="font-family: 'Cinzel', serif; font-size: 1.15rem; color: #623345; margin-bottom: 15px; font-weight: bold;">
            GENEL TOPLAM: {{ number_format($grandTotal, 2) }} TL
        </div>
        
        <div style="display: flex; justify-content: flex-end; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('products') }}" class="btn btn-outline-dark px-3 py-2" style="border-radius: 0; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px;">Alışverişe Devam Et</a>
            <a href="{{ route('checkout') }}" class="btn btn-dark px-4 py-2" style="background-color: #623345; border-color: #623345; border-radius: 0; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px; color: #fff;">Sepeti Onayla</a>
        </div>
    </div>

@else
    <div class="alert alert-info py-3" style="border-radius: 0; font-size: 0.9rem;">Sepetinizde ürün bulunmuyor.</div>
    <a href="{{ route('products') }}" class="btn btn-outline-dark mt-2" style="border-radius: 0; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px;">Alışverişe Devam Et</a>
@endif
@endsection