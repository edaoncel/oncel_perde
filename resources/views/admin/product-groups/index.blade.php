@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1>Grup Yönetimi</h1>
    
    <form action="{{ route('product-groups.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <input type="text" name="name" class="form-control" placeholder="Yeni Grup Adı" required>
        </div>
        <button type="submit" class="btn btn-primary">Kaydet</button>
    </form>

    <ul class="mt-4">
        @foreach($groups as $group)
            <li>{{ $group->name }}</li>
        @endforeach
    </ul>
</div>
<ul class="list-group mt-4">
    @foreach($groups as $group)
        <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ $group->name }}
            
            <form action="{{ route('product-groups.destroy', $group->id) }}" method="POST" onsubmit="return confirm('Bu grubu silmek istediğinize emin misiniz? Gruba bağlı ürünler boşa çıkacaktır.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="bi bi-trash"></i> Sil
                </button>
            </form>
        </li>
    @endforeach
</ul>
@endsection