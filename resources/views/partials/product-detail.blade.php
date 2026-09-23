<div class="modal-header">
    <h5 class="modal-title">{{ $product->name }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <p>Fiyat: {{ $product->price }} TL</p>
    <p>Açıklama: {{ $product->description }}</p>
</div>