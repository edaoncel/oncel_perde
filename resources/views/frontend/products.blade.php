@extends('layouts.front')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
  body {
    font-family: 'Inter', sans-serif;
    background-color: #eae4e6;
    color: #111111;
    overflow-x: hidden;
    margin: 0;
    padding: 0;
  }

  .modal-backdrop.fade.show {
    background-color: #000 !important;
    opacity: 0.85 !important;
    backdrop-filter: blur(5px) !important;
  }

  .app-header {
    padding: 150px 0;
    background-image: url("{{ asset('images/4.png') }}"); 
    background-size: cover; 
    background-position: center; 
    background-repeat: no-repeat;
    color: #f2eeef; 
    text-align: center; 
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 50vh;
  }

  .app-showroom { padding: 80px 0; }

  .app-product-card {
    background: #ffffff;
    border: 1px solid #e0dcce;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 30px;
    transition: box-shadow 0.3s ease, transform 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
  }

  .app-product-card:hover {
    box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    transform: translateY(-3px);
  }

  .app-img-wrapper {
    position: relative;
    overflow: hidden;
    background: #f0ede6;
    width: 100%;
    padding-top: 100%;
  }

  .app-img-wrapper img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .badge-shipping {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 5;
    font-size: 10px;
    text-transform: uppercase;
    padding: 5px 8px;
    border-radius: 2px;
  }

  .wishlist-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 5;
  }

  .wishlist-btn.active i.fas { color: #ff0000 !important; }

  .app-card-overlay {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(26, 26, 26, 0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.5s ease;
  }

  .app-product-card:hover .app-card-overlay { opacity: 1; }

  .btn-app-view {
    background: #ffffff;
    color: #111111;
    text-transform: uppercase;
    letter-spacing: 2px;
    font-size: 11px;
    padding: 16px 45px;
    border: none;
    font-weight: 600;
  }

  .product-details {
    padding: 15px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    justify-content: space-between;
  }
  
  .product-title {
    font-size: 15px;
    line-height: 1.3;
    color: #1a1a1a;
    font-weight: 400;
    margin-bottom: 8px;
    min-height: 40px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
  }

  .product-size-badge {
    font-size: 12px;
    color: #555555;
    background-color: #f7f5f0;
    border: 1px solid #e0dcce;
    display: inline-block;
    padding: 2px 8px;
    border-radius: 2px;
    margin-bottom: 10px;
  }

  .color-container {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 12px;
  }

  .btn-check:checked + label {
    outline: 2px solid #111111;
    outline-offset: 2px;
  }

  @media (min-width: 992px) {
    #filterOffcanvas {
      visibility: visible !important;
      transform: none !important;
      position: static !important;
      background: transparent !important;
      width: 100% !important;
      height: auto !important;
      box-shadow: none !important;
    }
    #filterOffcanvas .offcanvas-header {
      display: none !important;
    }
  }
</style>

<section class="app-header">
  <div class="container col-lg-8">
    <span class="text-uppercase d-block mb-3" style="letter-spacing: 4px; font-size: 12px; font-weight: 600; color: #c2a66c;">Koleksiyon Seçkisi</span>
    <section class="splash">
      <h1>Yaşam Alanınız için<br>Zamansız Dokunuşlar</h1>
    </section>
  </div>
</section>

<section class="app-showroom py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <button class="btn btn-dark d-lg-none w-100 mb-4" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                    <i class="fas fa-sliders-h"></i> Filtreleri Düzenle
                </button>

                <div class="offcanvas offcanvas-start" tabindex="-1" id="filterOffcanvas">
                    <div class="offcanvas-header d-lg-none">
                        <h5 class="offcanvas-title">Filtreler</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <form id="filterForm" class="w-100">
                            <div id="filterAccordion">
                                <div class="filter-group mb-4">
                                    <button class="btn btn-outline-dark w-100" type="button" data-bs-toggle="collapse" data-bs-target="#catCollapse">Kategoriler</button>
                                    <div class="collapse show" id="catCollapse">
                                        <div class="p-2">
                                            @foreach($categories as $category)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="categories[]" value="{{ $category->id }}" id="cat{{ $category->id }}" {{ (isset($selectedCategories) && in_array($category->id, $selectedCategories)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="cat{{ $category->id }}">{{ $category->name }}</label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="filter-group mb-4">
                                    <button class="btn btn-outline-dark w-100" type="button" data-bs-toggle="collapse" data-bs-target="#colorCollapse">Renkler</button>
                                    <div class="collapse" id="colorCollapse">
                                        <div class="d-flex flex-wrap gap-2 p-2">
                                            @foreach($allColors as $color)
                                                @if(!empty($color))
                                                <div class="form-check">
                                                    <input class="btn-check" type="checkbox" name="colors[]" value="{{ $color }}" id="color{{ $loop->index }}">
                                                    <label class="btn btn-outline-secondary p-0" for="color{{ $loop->index }}" style="width: 25px; height: 25px; border-radius: 50%; background-color: {{ $color }};"></label>
                                                </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="filter-group mb-4">
                                    <button class="btn btn-outline-dark w-100" type="button" data-bs-toggle="collapse" data-bs-target="#priceCollapse">Fiyat Aralığı</button>
                                    <div class="collapse" id="priceCollapse">
                                        <div class="row g-2 p-2">
                                            <div class="col-6"><input type="number" name="min_price" value="{{ request('min_price') }}" class="form-control" placeholder="Min"></div>
                                            <div class="col-6"><input type="number" name="max_price" value="{{ request('max_price') }}" class="form-control" placeholder="Max"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" onclick="filterProducts()" class="btn btn-dark w-100">Filtrele</button>
                            <a href="{{ route('products') }}" class="btn btn-outline-secondary w-100 mt-2">Temizle</a>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="row row-cols-1 row-cols-md-3 g-4" id="product-container">
                    @if(isset($products) && $products->count() > 0)
                        @foreach($products as $product)
                            @php
                                $isMainWishlisted = \App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->exists();
                            @endphp
                            <div class="col">
                                <div class="app-product-card">
                                    <div class="app-img-wrapper">
                                        <!-- ALIŞVERİŞ KISMI ŞİMDİLİK KALDIRDIM. -->
                                        <span class="badge badge-shipping {{ ($product->shipping_price ?? 0) == 0 ? 'bg-success' : 'bg-secondary' }}" id="shipping-badge-{{ $product->id }}">
                                            {{ ($product->shipping_price ?? 0) == 0 ? 'Ücretsiz Kargo' : 'Kargo: ' . number_format($product->shipping_price, 2) . ' TL' }}
                                        </span>
                                        

                                        <img id="main-img-{{ $product->id }}" src="{{ asset($product->image) }}" alt="{{ $product->name }}">
                                        
                                        <button type="button" class="wishlist-btn {{ $isMainWishlisted ? 'active' : '' }}" onclick="toggleWishlist({{ $product->id }}, this)">
                                            <i class="{{ $isMainWishlisted ? 'fas' : 'far' }} fa-heart"></i>
                                        </button>

                                        <div class="app-card-overlay">
                                            <button type="button" class="btn btn-app-view" data-bs-toggle="modal" data-bs-target="#detailModal" onclick="loadDetailWithVariant('{{ $product->id }}', this)">İncele</button>
                                        </div>
                                    </div>

                                    <div class="product-details">
                                        <div>
                                            <div class="product-title">{{ $product->name }}</div>
                                            
                                            @if(!empty($product->size))
                                                <div id="size-badge-{{ $product->id }}" class="product-size-badge">
                                                    <i class="fa-solid fa-ruler-combined me-1"></i> {{ $product->size }}
                                                </div>
                                            @else
                                                <div id="size-badge-{{ $product->id }}" class="product-size-badge" style="display: none;"></div>
                                            @endif

                                            <div class="color-container">
                                                @if(isset($product->variants))
                                                    @foreach($product->variants as $variant)
                                                        @php
                                                            $isVarWishlisted = \App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $variant->id)->exists();
                                                        @endphp
                                                        <div class="color-option">
                                                            <input type="radio" 
                                                                   class="btn-check variant-radio" 
                                                                   name="color_select_{{ $product->id }}" 
                                                                   value="{{ $variant->id }}" 
                                                                   id="var_{{ $variant->id }}"
                                                                   {{ $loop->first ? 'checked' : '' }}
                                                                   data-product-id="{{ $product->id }}"
                                                                   data-image="{{ asset($variant->image) }}"
                                                                   data-name="{{ $variant->name }}"
                                                                   data-price="{{ $variant->price }}"
                                                                   data-size="{{ $variant->size ?? '' }}"
                                                                   data-variant-id="{{ $variant->id }}"
                                                                   data-stock="{{ $variant->stock ?? 0 }}"
                                                                   data-shipping="{{ $variant->shipping_price ?? 0 }}"
                                                                   data-limit="{{ $variant->max_order_limit ?? 5 }}"
                                                                   data-wishlisted="{{ $isVarWishlisted ? 'true' : 'false' }}">
                                                            <label for="var_{{ $variant->id }}" style="width: 22px; height: 22px; border-radius: 50%; background-color: {{ $variant->colorRGB }}; border: 1px solid #ccc; cursor: pointer; display: block;"></label>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>

                                        <div>
                                            <div class="info-tag" id="modal-stock-info-{{ $product->id }}">
                                                    @if(($product->stock ?? 0) > 5)
                                                        <span class="text-success fw-semibold"><i class="fas fa-check-circle me-1"></i>Stokta Var</span>
                                                    @elseif(($product->stock ?? 0) > 0)
                                                        <span class="text-warning fw-semibold"><i class="fas fa-exclamation-triangle me-1"></i>Son {{ $product->stock }} Ürün</span>
                                                    @else
                                                        <span class="text-danger fw-semibold"><i class="fas fa-times-circle me-1"></i>Stok Tükendi</span>
                                                    @endif
                                                </div>
                                            <div class="product-price fw-bold fs-5 mb-3" id="price-{{ $product->id }}">
                                                {{ number_format($product->price, 2) }} TL
                                            </div>
                                            

                                            <form action="{{ route('cart.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" id="product-id-input-{{ $product->id }}" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                
                                                <!-- ALIŞVERİŞ KISMI ŞİMDİLİK KALDIRDIM. -->
                                                <button type="submit" class="btn btn-dark w-100" id="cart-btn-{{ $product->id }}" {{ ($product->stock ?? 0) <= 0 ? 'disabled' : '' }}>
                                                    <i class="fa-solid fa-basket-shopping"></i> {{ ($product->stock ?? 0) <= 0 ? 'Stokta Yok' : 'Sepete Ekle' }}
                                                </button>
                                                
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12 text-center py-5">
                            <div class="p-5 bg-white border rounded shadow-sm">
                                <i class="fa-solid fa-box-open fa-3x text-muted mb-3"></i>
                                <h4 class="text-secondary">Aradığınız kriterlere uygun filtrelenmiş ürün bulunamadı.</h4>
                                <p class="text-muted mb-4">Lütfen filtreleri değiştirerek veya temizleyerek tekrar deneyin.</p>
                                <a href="{{ route('products') }}" class="btn btn-dark px-4">Filtreleri Temizle</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="modal-content-area"></div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    initVariantListeners();
});

function loadDetailWithVariant(productId, btn) {
    const modalContentArea = document.getElementById('modal-content-area');
    modalContentArea.innerHTML = '<div class="text-center p-5"><div class="spinner-border" role="status"></div></div>';
    
    const selectedCard = btn.closest('.app-product-card');
    const selectedVariantInput = selectedCard.querySelector('input[name="color_select_' + productId + '"]:checked');
    const variantId = selectedVariantInput ? selectedVariantInput.value : productId;

    let url = "{{ url('/product/detail') }}/" + variantId;

    fetch(url)
        .then(res => res.text())
        .then(html => {
            modalContentArea.innerHTML = html;
        })
        .catch(err => {
            modalContentArea.innerHTML = '<div class="p-4 text-center text-danger">Detaylar yüklenirken bir hata oluştu.</div>';
        });
}

function updateCard(mainProductId, imageUrl, name, price, size, variantId, stock, shippingPrice, maxLimit, isWishlisted) {
    const img = document.getElementById('main-img-' + mainProductId);
    if (img && imageUrl) img.src = imageUrl;

    const priceEl = document.getElementById('price-' + mainProductId);
    if (priceEl) priceEl.innerText = parseFloat(price).toFixed(2) + ' TL';

    const sizeBadge = document.getElementById('size-badge-' + mainProductId);
    if (sizeBadge) {
        if (size && size.trim() !== '') {
            sizeBadge.innerHTML = '<i class="fa-solid fa-ruler-combined me-1"></i> ' + size;
            sizeBadge.style.display = 'inline-block';
        } else {
            sizeBadge.style.display = 'none';
        }
    }

    const input = document.getElementById('product-id-input-' + mainProductId);
    if (input) input.value = variantId;

    const shippingBadge = document.getElementById('shipping-badge-' + mainProductId);
    if (shippingBadge) {
        if (parseFloat(shippingPrice) === 0) {
            shippingBadge.className = 'badge badge-shipping bg-success';
            shippingBadge.innerText = 'Ücretsiz Kargo';
        } else {
            shippingBadge.className = 'badge badge-shipping bg-secondary';
            shippingBadge.innerText = 'Kargo: ' + parseFloat(shippingPrice).toFixed(2) + ' TL';
        }
    }

    const cartBtn = document.getElementById('cart-btn-'  + mainProductId);
    if (cartBtn) {
        if (parseInt(stock) > 0) {
            cartBtn.disabled = false;
            cartBtn.innerHTML = '<i class="fa-solid fa-basket-shopping"></i> Sepete Ekle';
        } else {
            cartBtn.disabled = true;
            cartBtn.innerHTML = 'Stokta Yok';
        }
    }

    // Varyant değiştikçe favori butonunun durumunu da senkronize et
    const card = img.closest('.app-product-card');
    const wishlistBtn = card.querySelector('.wishlist-btn');
    if (wishlistBtn) {
        const icon = wishlistBtn.querySelector('i');
        if (isWishlisted) {
            wishlistBtn.classList.add('active');
            if (icon) {
                icon.classList.remove('far');
                icon.classList.add('fas', 'text-danger');
            }
        } else {
            wishlistBtn.classList.remove('active');
            if (icon) {
                icon.classList.remove('fas', 'text-danger');
                icon.classList.add('far');
            }
        }
    }
}

function filterProducts() {
    const offcanvasEl = document.getElementById('filterOffcanvas');
    const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasEl);
    if (offcanvasInstance) {
        offcanvasInstance.hide();
    }

    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);

    let url = "{{ route('products') }}" + "?" + params.toString();

    const productContainer = document.getElementById('product-container');
    productContainer.innerHTML = '<div class="text-center col-12 p-5"><div class="spinner-border" role="status"></div></div>';

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newProducts = doc.getElementById('product-container');
        
        if (newProducts) {
            productContainer.innerHTML = newProducts.innerHTML;
        } else {
            productContainer.innerHTML = html;
        }
        
        initVariantListeners();
    })
    .catch(error => {
        console.error('Filtreleme hatası:', error);
        productContainer.innerHTML = '<div class="text-center col-12 text-danger p-4">Ürünler filtrelenirken bir hata oluştu.</div>';
    });
}

function initVariantListeners() {
    document.querySelectorAll('.variant-radio').forEach(radio => {
        radio.removeEventListener('change', variantChangeHandler);
        radio.addEventListener('change', variantChangeHandler);
    });
}

function variantChangeHandler(e) {
    updateCard(
        this.dataset.productId,
        this.dataset.image,
        this.dataset.name,
        this.dataset.price,
        this.dataset.size,
        this.dataset.variantId,
        this.dataset.stock,
        this.dataset.shipping,
        this.dataset.limit,
        this.dataset.wishlisted === 'true'
    );
}

function toggleWishlist(productId, buttonElement) {
    const card = buttonElement.closest('.app-product-card');
    
    const selectedVariantInput = card.querySelector('input.variant-radio:checked');
    let targetProductId = productId;
    let colorName = null;

    if (selectedVariantInput) {
        targetProductId = selectedVariantInput.value;
        colorName = selectedVariantInput.dataset.name; 
    }

    fetch("{{ route('wishlist.toggle') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
            product_id: targetProductId,
            color: colorName 
        })
    })
    .then(async response => {
        // Eğer kullanıcı girişi yoksa (401 Unauthorized)
        if (response.status === 401) {
            alert("Favorilere ürün eklemek için lütfen giriş yapın.");
            window.location.href = "{{ route('login') }}"; // Giriş sayfasına yönlendir
            return;
        }
        
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            return;
        }
        return response.json();
    })
    .then(data => {
        if (!data) return;
        
        if (data.status === 'added') {
            if (buttonElement) {
                buttonElement.classList.add('active');
                const icon = buttonElement.querySelector('i');
                if (icon) {
                    icon.classList.remove('far');
                    icon.classList.add('fas', 'text-danger');
                }
            }
            if (selectedVariantInput) {
                selectedVariantInput.dataset.wishlisted = 'true';
            }
        } else if (data.status === 'removed') {
            if (buttonElement) {
                buttonElement.classList.remove('active');
                const icon = buttonElement.querySelector('i');
                if (icon) {
                    icon.classList.remove('fas', 'text-danger');
                    icon.classList.add('far');
                }
            }
            if (selectedVariantInput) {
                selectedVariantInput.dataset.wishlisted = 'false';
            }
        }
    })
    .catch(error => console.error('Hata:', error));
}
</script>
@endsection