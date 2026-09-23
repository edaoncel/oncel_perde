@extends('layouts.front')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600&family=Montserrat:wght@300;400&display=swap" rel="stylesheet">
<style>
    :root {
        --gold: #d4af37;
        --off-white: #faf9f6;
        --pure-black: #0f0f0f;
        --charcoal: #1a1a1a;
    }

    .admin-auth-wrapper {
        min-height: 80vh;
        display: flex;
        align-items: center;
        padding: 60px 0;
        background-color: var(--off-white);
    }

    .admin-card {
        background: #ffffff;
        border: 1px solid #e0ded9;
        border-radius: 0;
        padding: 50px; 
        box-shadow: 0 15px 35px rgba(0,0,0,0.05);
    }

    .admin-title {
        font-family: 'Cinzel', serif;
        font-size: 1.5rem;
        font-weight: 500;
        color: var(--pure-black);
        letter-spacing: 4px;
        text-align: center;
        margin-bottom: 40px;
        text-transform: uppercase;
        line-height: 1.4;
    }

    .admin-title span {
        font-family: 'Montserrat', sans-serif;
        font-size: 0.75rem !important;
        color: #8a8a8a;
        letter-spacing: 3px;
        display: block;
        margin-top: 5px;
    }

    .form-control {
        border-radius: 0;
        border: 1px solid #e0ded9;
        padding: 15px;
        background-color: #fdfdfd;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--pure-black);
        box-shadow: none;
        background-color: #fff;
    }

    .form-label {
        font-family: 'Montserrat', sans-serif;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: #666;
        margin-bottom: 8px;
    }

    .btn-luxe-admin {
        background: var(--pure-black);
        color: #ffffff;
        border-radius: 0;
        text-transform: uppercase;
        letter-spacing: 3px;
        font-size: 0.8rem;
        padding: 16px;
        border: none;
        transition: all 0.5s ease;
        margin-top: 20px;
    }

    .btn-luxe-admin:hover {
        background: var(--gold);
        color: #fff;
    }

    .alert-danger {
        background-color: #fcf2f2;
        color: #9a2b2b;
        border: 1px solid #f5e0e0;
        font-family: 'Montserrat', sans-serif;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }
</style>

<div class="admin-auth-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="admin-card">
                    <h3 class="admin-title">
                        ÖNCEL
                        <span>ADMIN PANEL</span>
                    </h3>
                    
                    @if($errors->any())
                        <div class="alert alert-danger rounded-0 py-2 mb-4 text-center">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('admin.login') }}" method="POST" target="_blank">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">E-Posta Adresi</label>
                            <input type="email" name="email" class="form-control shadow-none" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Şifre</label>
                            <input type="password" name="password" class="form-control shadow-none" required>
                        </div>
                        <button type="submit" class="btn btn-luxe-admin w-100">Oturum Aç</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection