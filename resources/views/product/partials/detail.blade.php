<style>
  .modal-backdrop.show {
    background-color: rgba(240, 240, 240, 0.4) !important;
    backdrop-filter: blur(8px) !important;
    opacity: 1 !important;
  }

  .modal-content {
    border: none !important;
    border-radius: 16px !important;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.12) !important;
    background: #ffffff !important;
  }

  .modal-container {
    padding: 28px;
    background: #ffffff;
    border-radius: 16px;
    position: relative; 
  }

  .modal-close-btn {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background-color: #f8f9fa;
    border: 1px solid #eee;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #111111;
    cursor: pointer;
    z-index: 10;
    transition: all 0.2s ease;
  }

  .modal-close-btn:hover {
    background-color: #111111;
    color: #ffffff;
    transform: scale(1.05);
  }

  .zoom-wrapper {
    position: relative;
    width: 100%;
    height: 500px; 
    overflow: hidden;
    border-radius: 12px;
    background-color: #f9f9f9;
    border: 1px solid #f0f0f0;
    cursor: zoom-in;
  }

  .zoom-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transform: scale(1);
    transform-origin: center center;
    transition: transform 0.1s ease-out; 
    display: block;
  }

  .zoom-wrapper:hover img {
    transform: scale(2.5); 
  }

  .variant-badge-shipping {
    position: absolute;
    top: 14px;
    left: 14px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    padding: 6px 12px;
    border-radius: 6px;
    z-index: 5;
    pointer-events: none;
  }

  .variant-dot {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 2px solid #ffffff;
    box-shadow: 0 0 0 1px #e0e0e0;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .variant-dot:hover, .variant-dot.active {
    transform: scale(1.15);
    box-shadow: 0 0 0 2px #111111;
  }

  .info-tag {
    font-size: 12px;
    padding: 8px 14px;
    border-radius: 8px;
    background-color: #f8f9fa;
    border: 1px solid #eee;
    display: inline-flex;
    align-items: center;
  }
</style>

<div class="row align-items-center modal-container">
   
    <button type="button" class="modal-close-btn" data-bs-dismiss="modal" aria-label="Close">
        <i class="fas fa-times"></i>
    </button>

   
    <div class="col-md-6 mb-4 mb-md-0">
        <div class="zoom-wrapper" id="zoom-container-{{ $product->id }}" onmousemove="zoomImage(event, this)" onmouseleave="resetZoom(this)">
            <!-- ALIŞVERİŞ KISMI ŞİMDİLİK KALDIRDIM. -->
            <span class="badge variant-badge-shipping {{ ($product->shipping_price ?? 0) == 0 ? 'bg-success' : 'bg-dark' }}" 
                  id="modal-shipping-badge-{{ $product->id }}">
                {{ ($product->shipping_price ?? 0) == 0 ? 'Ücretsiz Kargo' : 'Kargo: ' . number_format($product->shipping_price, 2) . ' TL' }}
            </span>

            <img id="modal-main-img-{{ $product->id }}" src="{{ asset($product->image) }}" alt="{{ $product->name }}">
        </div>
        <small class="text-muted d-block text-center mt-2" style="font-size: 11px;">
            <i class="fas fa-search-plus me-1"></i> Detaylı incelemek için fareyi resmin üzerine getirin
        </small>
    </div>

  
    <div class="col-md-6 ps-md-4">
        <h3 id="modal-title-{{ $product->id }}" class="fw-bold text-dark mb-2">{{ $product->name }}</h3>
        
        <div id="modal-price-{{ $product->id }}" class="fs-2 fw-bold text-dark mb-3">
            {{ number_format($product->price, 2) }} TL
        </div>

        <p id="modal-desc-{{ $product->id }}" class="text-secondary small mb-4" style="line-height: 1.6;">
            {{ $product->description ?? 'Bu ürün için detaylı bir açıklama bulunmamaktadır.' }}
        </p>

      
        <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
            <div class="info-tag" id="modal-stock-info-{{ $product->id }}">
                @if(($product->stock ?? 0) > 5)
                    <span class="text-success fw-semibold"><i class="fas fa-check-circle me-1"></i>Stokta Var</span>
                @elseif(($product->stock ?? 0) > 0)
                    <span class="text-warning fw-semibold"><i class="fas fa-exclamation-triangle me-1"></i>Son {{ $product->stock }} Ürün</span>
                @else
                    <span class="text-danger fw-semibold"><i class="fas fa-times-circle me-1"></i>Stok Tükendi</span>
                @endif
            </div>

            @if($product->max_order_limit)
                <div class="info-tag text-secondary" id="modal-limit-info-{{ $product->id }}">
                    <i class="fas fa-info-circle me-1"></i> Sınır: Max {{ $product->max_order_limit }} Adet
                </div>
            @endif

           
            <div class="info-tag text-dark fw-semibold" id="modal-size-tag-{{ $product->id }}" style="{{ !empty($product->size) ? '' : 'display: none;' }}">
                <i class="fa-solid fa-ruler-combined me-1 text-secondary"></i> 
                <span id="modal-size-text-{{ $product->id }}">{{ $product->size ?? '' }}</span>
            </div>
        </div>
                                            
        
        <form action="{{ route('cart.store') }}" method="POST">
            @csrf
            <input type="hidden" id="modal-product-id-input-{{ $product->id }}" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="variant_id" id="modal-variant-id-input-{{ $product->id }}" value="">
            
            <!-- ALIŞVERİŞ KISMI ŞİMDİLİK KALDIRDIM. -->
            <div class="row g-3">
                <div class="col-4 col-md-3">
                    <input type="number" 
                           name="quantity" 
                           id="modal-quantity-{{ $product->id }}" 
                           class="form-control form-control-lg text-center fs-5" 
                           value="1" 
                           min="1" 
                           max="{{ $product->max_order_limit ?? $product->stock ?? 1 }}"
                           {{ ($product->stock ?? 0) <= 0 ? 'disabled' : '' }}>
                </div>
                <div class="col-8 col-md-9">
                    <button type="submit" 
                            id="modal-cart-btn-{{ $product->id }}" 
                            class="btn btn-dark btn-lg w-100 fs-5 fw-semibold py-3" 
                            {{ ($product->stock ?? 0) <= 0 ? 'disabled' : '' }}>
                        <i class="fa-solid fa-basket-shopping me-2"></i>
                        {{ ($product->stock ?? 0) <= 0 ? 'Stokta Yok' : 'Sepete Ekle' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function zoomImage(e, container) {
    const img = container.querySelector('img');
    const rect = container.getBoundingClientRect();
    
    const x = ((e.clientX - rect.left) / rect.width) * 100;
    const y = ((e.clientY - rect.top) / rect.height) * 100;
    
    img.style.transformOrigin = `${x}% ${y}%`;
    img.style.transform = "scale(2.5)";
}

function resetZoom(container) {
    const img = container.querySelector('img');
    if (img) {
        img.style.transform = "scale(1)";
        img.style.transformOrigin = "center center";
    }
}

function updateModalDetail(mainProductId, data, btnElement) {
    const img = document.getElementById('modal-main-img-' + mainProductId);
    const title = document.getElementById('modal-title-' + mainProductId);
    const priceEl = document.getElementById('modal-price-' + mainProductId);
    const descEl = document.getElementById('modal-desc-' + mainProductId);
    const input = document.getElementById('modal-product-id-input-' + mainProductId);
    const variantInput = document.getElementById('modal-variant-id-input-' + mainProductId);
    const shippingBadge = document.getElementById('modal-shipping-badge-' + mainProductId);
    const stockInfo = document.getElementById('modal-stock-info-' + mainProductId);
    const limitInfo = document.getElementById('modal-limit-info-' + mainProductId);
    const sizeTag = document.getElementById('modal-size-tag-' + mainProductId);
    const sizeText = document.getElementById('modal-size-text-' + mainProductId);
    const cartBtn = document.getElementById('modal-cart-btn-' + mainProductId);
    const qtyInput = document.getElementById('modal-quantity-' + mainProductId);

    if (img && data.imageUrl) img.src = data.imageUrl;
    if (title && data.name) title.innerText = data.name;
    if (priceEl && data.price) priceEl.innerText = parseFloat(data.price).toFixed(2) + ' TL';
    if (descEl && data.description) descEl.innerText = data.description;
    if (input && data.productId) input.value = data.productId;
    if (variantInput && data.variantId) variantInput.value = data.variantId;

    if (sizeTag && sizeText) {
        if (data.size && data.size.trim() !== '') {
            sizeText.innerText = data.size;
            sizeTag.style.display = 'inline-flex';
        } else {
            sizeTag.style.display = 'none';
        }
    }

    if (shippingBadge) {
        if (parseFloat(data.shippingPrice) === 0) {
            shippingBadge.className = 'badge variant-badge-shipping bg-success';
            shippingBadge.innerText = 'Ücretsiz Kargo';
        } else {
            shippingBadge.className = 'badge variant-badge-shipping bg-dark';
            shippingBadge.innerText = 'Kargo: ' + parseFloat(data.shippingPrice).toFixed(2) + ' TL';
        }
    }

    const stockCount = parseInt(data.stock);
    if (stockInfo && cartBtn) {
        if (stockCount > 5) {
            stockInfo.innerHTML = '<span class="text-success fw-semibold"><i class="fas fa-check-circle me-1"></i>Stokta Var</span>';
            cartBtn.disabled = false;
            if (qtyInput) qtyInput.disabled = false;
            cartBtn.innerHTML = '<i class="fa-solid fa-basket-shopping me-2"></i> Sepete Ekle';
        } else if (stockCount > 0) {
            stockInfo.innerHTML = `<span class="text-warning fw-semibold"><i class="fas fa-exclamation-triangle me-1"></i>Son ${stockCount} Ürün</span>`;
            cartBtn.disabled = false;
            if (qtyInput) qtyInput.disabled = false;
            cartBtn.innerHTML = '<i class="fa-solid fa-basket-shopping me-2"></i> Sepete Ekle';
        } else {
            stockInfo.innerHTML = '<span class="text-danger fw-semibold"><i class="fas fa-times-circle me-1"></i>Stok Tükendi</span>';
            cartBtn.disabled = true;
            if (qtyInput) qtyInput.disabled = true;
            cartBtn.innerHTML = 'Stokta Yok';
        }
    }

    if (limitInfo) {
        if (data.maxLimit > 0) {
            limitInfo.innerHTML = `<i class="fas fa-info-circle me-1"></i> Sınır: Max ${data.maxLimit} Adet`;
            limitInfo.style.display = 'inline-flex';
            if (qtyInput) qtyInput.max = data.maxLimit;
        } else {
            limitInfo.style.display = 'none';
            if (qtyInput) qtyInput.max = stockCount;
        }
    }

    if (btnElement) {
        const container = btnElement.closest('.d-flex');
        if (container) {
            container.querySelectorAll('.variant-dot').forEach(dot => dot.classList.remove('active'));
        }
        btnElement.classList.add('active');
    }
}
</script>