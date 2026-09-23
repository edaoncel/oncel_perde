@extends('layouts.front')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
<style>
    :root { 
        --gold: #d4af37; 
        --off-white: #faf9f6; 
        --pure-black: #623345; 
        --charcoal: #1a1a1a; 
    }
    
    .account-wrapper { 
        background-color: var(--off-white); 
        font-family: 'Montserrat', sans-serif; 
        padding: 80px 0; 
        min-height: 80vh; 
    }

    .sidebar .list-group-item { 
        border: 1px solid #e0ded9; 
        padding: 18px 25px; 
        margin-bottom: 10px; 
        border-radius: 0; 
        background: #fff; 
        transition: all 0.4s ease; 
        color: var(--charcoal); 
        font-weight: 400; 
        text-transform: uppercase; 
        font-size: 0.75rem; 
        letter-spacing: 2px; 
    }
    .sidebar .list-group-item:hover, .sidebar .list-group-item.active { 
        background: var(--pure-black); 
        color: #fff; 
        border-color: var(--pure-black);
    }

    .main-card { 
        background: #fff; 
        border: 1px solid #e0ded9; 
        padding: 50px; 
        min-height: 400px;
    }

    .content-title { 
        font-family: 'Cinzel', serif; 
        font-size: 1.8rem; 
        color: var(--pure-black); 
        text-transform: uppercase; 
        margin-bottom: 40px; 
        letter-spacing: 2px;
        position: relative;
        padding-bottom: 20px;
    }
    .content-title::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 60px;
        height: 1px;
        background: var(--gold);
    }

    .btn-logout {
        border: 1px solid #e0ded9;
        border-radius: 0;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 2px;
        padding: 15px;
        transition: all 0.3s;
    }
    .btn-logout:hover {
        background: var(--pure-black);
        color: #fff;
    }
</style>

<div class="account-wrapper">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center text-md-start">
                <h2 style="font-family: 'Cinzel', serif; letter-spacing: 2px;">HOŞ GELDİNİZ, {{ Auth::user()->name }}</h2>
                <p class="text-muted" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">Hesap panelinizden işlemlerinizi yönetebilirsiniz.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 sidebar mb-4 mb-md-0">
                <div class="list-group">
                    <!-- ALIŞVERİŞ KISMI ŞİMDİLİK KALDIRDIM. -->
                    <a href="{{ route('orders.index') }}" class="list-group-item {{ request()->routeIs('orders.index') ? 'active' : '' }}">Siparişlerim</a>
                    <a href="{{ route('dashboard.wishlist') }}" class="list-group-item {{ request()->routeIs('dashboard.wishlist') ? 'active' : '' }}">Favorilerim</a>
                    <a href="{{ route('dashboard.cart') }}" class="list-group-item {{ request()->routeIs('dashboard.cart') ? 'active' : '' }}">Sepetim</a>
                    <a href="{{ route('dashboard.profile') }}" class="list-group-item {{ request()->routeIs('dashboard.profile') ? 'active' : '' }}">Profil</a>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-logout w-100">Oturumu Kapat</button>
                </form>
            </div>
            
            <div class="col-md-9">
                <div class="main-card">
                    @yield('account-content')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection