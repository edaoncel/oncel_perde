@extends('layouts.account')

@section('account-content')
    <h2 class="content-title">Profil Bilgilerim</h2>

    <form action="{{ route('dashboard.profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Ad Soyad</label>
                <input type="text" name="name" class="form-control rounded-0" value="{{ Auth::user()->name }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">E-posta</label>
                <input type="email" name="email" class="form-control rounded-0" value="{{ Auth::user()->email }}">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Yeni Şifre</label>
                <input type="password" name="password" class="form-control rounded-0" placeholder="Değiştirmek istemiyorsanız boş bırakın">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Şifre Tekrar</label>
                <input type="password" name="password_confirmation" class="form-control rounded-0">
            </div>
        </div>

        <button type="submit" class="btn btn-dark rounded-0 px-4">Güncelle</button>
    </form>
@endsection