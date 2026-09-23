@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Yeni Kategori Ekle</h1>
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

<form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Kategori Adı</label>
                <input type="text" name="name" class="form-control" required autofocus>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Özel Slug (URL)</label>
                <input type="text" name="slug" class="form-control" placeholder="Boş bırakırsanız kategori adından otomatik oluşturulur.">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Kategori Görseli</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary px-4 mt-2">Kaydet</button>
        </form>
    </div>
</div>
@endsection