@extends('admin.layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Ürünü Düzenle: {{ $product->name }}</h1>
    </div>
    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Geri Dön
    </a>
</div>

{{-- Genel Hata Bildirimi --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4 col-md-8" role="alert">
        <h5 class="fw-bold mb-2">Lütfen Formdaki Hataları Düzeltin:</h5>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm border-0 col-md-8">
    <div class="card-body p-4">
        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="category_id" class="form-label fw-semibold">Kategori Seçin <span class="text-danger">*</span></label>
                <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                    <option value="" disabled>Kategori Seçiniz</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="groupSelect" class="form-label fw-semibold">Grup Seçimi (Koleksiyon)</label>
                <select name="product_group_id" class="form-select @error('product_group_id') is-invalid @enderror" id="groupSelect">
                    <option value="">Grup Seçiniz (Opsiyonel)</option>
                    @foreach($productGroups as $group)
                        <option value="{{ $group->id }}" {{ old('product_group_id', $product->product_group_id) == $group->id ? 'selected' : '' }}>
                            {{ $group->name }}
                        </option>
                    @endforeach
                </select>
                @error('product_group_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Ürün Adı <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="slug" class="form-label fw-semibold">Özel Slug (URL)</label>
                <input type="text" id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $product->slug) }}">
                <small class="text-muted">Boş bırakırsanız ürün adına göre otomatik güncellenir.</small>
                @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Beden / Ölçü / Ebat Alanı Eklendi --}}
            <div class="mb-3">
                <label for="size" class="form-label fw-semibold">Beden / Ölçü / Ebat <span class="text-muted">(İsteğe Bağlı)</span></label>
                <input type="text" id="size" name="size" class="form-control @error('size') is-invalid @enderror" value="{{ old('size', $product->size) }}" placeholder="Örn: 200x260 cm, XL, Çift Kişilik vb.">
                <small class="text-muted">Perde ebadı, nevresim/yorgan ölçüsü veya kıyafet bedeni yazabilirsiniz.</small>
                @error('size')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="color" class="form-label fw-semibold">Ürün Rengi (Adı)</label>
                    <input type="text" id="color" name="color" class="form-control @error('color') is-invalid @enderror" value="{{ old('color', $product->color) }}" placeholder="Örn: Krem, Vizon">
                    @error('color')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="colorRGB" class="form-label fw-semibold">Renk Kodu (RGB / Picker)</label>
                    <input type="color" id="colorRGB" name="colorRGB" class="form-control form-control-color w-100 @error('colorRGB') is-invalid @enderror" value="{{ old('colorRGB', $product->colorRGB ?? '#000000') }}">
                    @error('colorRGB')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="price" class="form-label fw-semibold">Fiyat (TL) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" id="price" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $product->price) }}" min="0" required>
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="shipping_price" class="form-label fw-semibold">Kargo Fiyatı (TL)</label>
                    <input type="number" step="0.01" id="shipping_price" name="shipping_price" class="form-control @error('shipping_price') is-invalid @enderror" value="{{ old('shipping_price', $product->shipping_price) }}" min="0">
                    @error('shipping_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="stock" class="form-label fw-semibold">Stok Miktarı <span class="text-danger">*</span></label>
                    <input type="number" id="stock" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', $product->stock) }}" min="0" required>
                    @error('stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_main" value="1" id="isMainSwitch" {{ old('is_main', $product->is_main) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="isMainSwitch">Grup İçi Ana Ürün Olsun mu?</label>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label fw-semibold">Açıklama / Detay</label>
                <textarea id="description" name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="image" class="form-label fw-semibold">Ürün Görseli</label>
                @if($product->image)
                    <div class="mb-2">
                        <img src="{{ asset($product->image) }}" alt="Ürün Görseli" width="100" class="rounded shadow-sm border">
                    </div>
                @else
                    <p class="text-muted small mb-2">Yüklü görsel yok</p>
                @endif
                <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                <small class="text-muted">Görseli değiştirmek istemiyorsanız boş bırakın.</small>
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-success px-4 mt-2">Güncelle</button>
        </form>
    </div>
</div>

@endsection