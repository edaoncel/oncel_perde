@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Kategori Düzenle</h1>
    </div>
    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Geri Dön
    </a>
</div>

<div class="card shadow-sm border-0 col-md-6">
    <div class="card-body p-4">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

<form action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-semibold">Kategori Adı</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Özel Slug (URL)</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug', $category->slug) }}">
                <small class="text-muted">Boş bırakırsanız kategori adına göre güncellenir.</small>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Mevcut Görsel</label>
                @if($category->image)
                    <div class="mb-2">
                        <img src="{{ asset($category->image) }}" alt="Kategori Görseli" width="80" class="rounded shadow-sm">
                    </div>
                @else
                    <p class="text-muted small mb-2">Yüklü görsel yok</p>
                @endif
                <input type="file" name="image" class="form-control" accept="image/*">
                <small class="text-muted">Değiştirmek istemiyorsanız boş bırakın.</small>
            </div>

            <button type="submit" class="btn btn-success px-4 mt-2">Güncelle</button>
        </form>
    </div>
</div>
@endsection