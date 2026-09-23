@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Ürünler</h1>
        <p class="text-muted">Ürün listesi ve yönetimi</p>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Yeni Ürün Ekle
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Görsel</th>
                        <th>Ürün Adı</th>
                        <th>Ürün Grubu</th>
                        <th>Kategori</th>
                        <th>Renk / RGB</th>
                        <th>Beden / Ebat</th>
                        <th>Stok</th>
                        <th>Ürün Fiyatı</th>
                        <th>Kargo Fiyatı</th>
                        <th>Toplam Fiyat</th>
                        <th class="text-end pe-4">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    @php
                        $shipping = $product->shipping_price ?? 0;
                        $totalPrice = $product->price + $shipping;
                    @endphp
                    <tr>
                        <td class="ps-4 fw-bold text-muted">{{ $loop->iteration }}</td>
                        <td>
                            @if($product->image)
                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" width="50" height="50" class="rounded object-fit-cover shadow-sm">
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="fw-semibold">{{ $product->name }}</td>
                        <td>
                            <span class="badge bg-info text-dark">
                                {{ $product->group->name ?? 'Grup Yok' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">
                                {{ $product->category->name ?? 'Kategorisiz' }}
                            </span>
                        </td>
                        <td>
                            <div>{{ $product->color ?? 'Seçilmedi' }}</div>
                            @if($product->colorRGB)
                                <small class="text-muted" style="font-size: 0.75rem;">({{ $product->colorRGB }})</small>
                            @endif
                        </td>
                        <td>
                            <span class="text-dark fw-medium">{{ $product->size ?? '-' }}</span>
                        </td>
                        <td>
                            @if(isset($product->stock))
                                <span class="badge {{ $product->stock > 0 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $product->stock }} Adet
                                </span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="fw-semibold">{{ number_format($product->price, 2) }} TL</td>
                        <td>
                            @if($shipping > 0)
                                <span class="text-muted">{{ number_format($shipping, 2) }} TL</span>
                            @else
                                <span class="badge bg-light text-dark border">Ücretsiz</span>
                            @endif
                        </td>
                        <td class="fw-bold text-success">{{ number_format($totalPrice, 2) }} TL</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-outline-primary btn-sm" title="Düzenle">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu ürünü silmek istediğinizden emin misiniz?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Sil">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center py-4 text-muted">Henüz kayıtlı ürün bulunmuyor.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection