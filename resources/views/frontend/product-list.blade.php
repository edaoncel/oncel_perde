@forelse($products as $product)
    <div class="col">
        <div class="app-product-card">
            <div class="app-img-wrapper">
                <img src="{{ asset($product->image) }}" id="main-img-{{ $product->id }}" alt="{{ $product->name }}">
                <button type="button" class="wishlist-btn" onclick="toggleWishlist({{ $product->id }}, this)">
                    <i class="far fa-heart"></i>
                </button>
                <div class="app-card-overlay">
                    <button type="button" class="btn btn-app-view" data-bs-toggle="modal" data-bs-target="#detailModal" onclick="loadDetail({{ $product->id }})">İncele</button>
                </div>
            </div>
            
            <div class="product-details">
                <div class="product-title" id="title-{{ $product->id }}">{{ $product->name }}</div>
                
                <div class="color-container">
                    @foreach($product->variants ?? [] as $variant)
                        <div class="color-option">
                            <input type="radio" class="btn-check" name="color_select_{{ $product->product_group_id }}" 
                                   value="{{ $variant->id }}" id="var_{{ $variant->id }}"
                                   onchange="updateCard({{ $product->product_group_id }}, '{{ asset($variant->image) }}', '{{ $variant->name }}', {{ $variant->price }}, {{ $variant->id }})">
                            <label class="btn btn-outline-secondary p-0" for="var_{{ $variant->id }}" 
                                   style="width: 25px; height: 25px; border-radius: 50%; background-color: {{ $variant->colorRGB }}; border: 1px solid #ccc; cursor: pointer; display: block;"></label>
                        </div>
                    @endforeach
                </div>

                <div class="product-price fw-bold mb-3" id="price-{{ $product->id }}">{{ number_format($product->price, 2) }} TL</div>
                
                <button onclick="addToCart({{ $product->id }})" class="btn-add-cart">
                    <i class="fas fa-shopping-bag"></i> Sepete Ekle
                </button>
            </div>
        </div>
    </div>
@empty
    <div class="col-12 text-center py-5">
        <div class="p-5 bg-white border rounded shadow-sm">
            <i class="fa-solid fa-box-open fa-3x text-muted mb-3"></i>
            <h4 class="text-secondary">Aradığınız kriterlere uygun filtrelenmiş ürün bulunamadı.</h4>
            <p class="text-muted mb-4">Lütfen filtreleri değiştirerek veya temizleyerek tekrar deneyin.</p>
            <a href="{{ route('products') }}" class="btn btn-dark px-4">Filtreleri Temizle</a>
        </div>
    </div>
@endforelse