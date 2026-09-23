@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Kategoriler</h1>
        <p class="text-muted">Kategori listesi ve yönetimi</p>
    </div>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Yeni Kategori Ekle
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
                        <th>Kategori Adı</th>
                        <th>Slug</th>
                        <th class="text-end pe-4">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td class="ps-4 fw-bold text-muted">{{ $loop->iteration }}</td>
                        <td>
                            @if($category->image)
                                <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" width="50" height="50" class="rounded object-fit-cover shadow-sm">
                            @else
                                <span class="text-muted small">Resim Yok</span>
                            @endif
                        </td>
                        <td class="fw-semibold">{{ $category->name }}</td>
                        <td class="text-muted small">{{ $category->slug }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-outline-primary btn-sm" title="Düzenle">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu kategoriyi silmek istediğinizden emin misiniz?');">
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
                        <td colspan="5" class="text-center py-4 text-muted">Henüz kayıtlı kategori bulunmuyor.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection