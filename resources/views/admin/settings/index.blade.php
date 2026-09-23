@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Genel Ayarlar</h1>
        <p class="text-muted">Site genel ayarlarını yönetin</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm border-0 col-md-8">
    <div class="card-body p-4">
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label fw-semibold">Site Başlığı (Title)</label>
                <input type="text" name="site_title" class="form-control" value="{{ $settings['site_title'] ?? '' }}">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">İletişim Telefon</label>
                <input type="text" name="contact_phone" class="form-control" value="{{ $settings['contact_phone'] ?? '' }}">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">İletişim E-posta</label>
                <input type="email" name="contact_email" class="form-control" value="{{ $settings['contact_email'] ?? '' }}">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Adres</label>
                <textarea name="contact_address" rows="3" class="form-control">{{ $settings['contact_address'] ?? '' }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Logo</label>
                @if(isset($settings['site_logo']) && $settings['site_logo'])
                    <div class="mb-2">
                        <img src="{{ asset($settings['site_logo']) }}" alt="Logo Görseli" width="120" class="bg-light p-1 border rounded">
                    </div>
                @endif
                <input type="file" name="site_logo" class="form-control" accept="image/*">
            </div>

            <button type="submit" class="btn btn-success px-4 mt-2">Ayarları Kaydet</button>
        </form>
    </div>
</div>
@endsection