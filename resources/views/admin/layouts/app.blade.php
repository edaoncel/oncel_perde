<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öncel - Yönetim Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fc;
            overflow-x: hidden;
        }
        .sidebar {
            background-color: #212529;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.3s ease;
        }
        .sidebar .nav-link {
            color: #adb5bd;
            padding: 0.75rem 1rem;
            border-radius: 0.25rem;
            margin-bottom: 0.25rem;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }

        @media (max-width: 767.98px) {
            .sidebar {
                display: none !important;
            }
            .sidebar.show-menu {
                display: flex !important;
            }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-dark d-md-none px-3">
        <a class="navbar-brand fw-bold fs-4" href="{{ route('admin.dashboard') }}">Öncel</a>
        <button class="navbar-toggler border-0 shadow-none" type="button" id="sidebarToggleBtn">
            <span class="navbar-toggler-icon"></span>
        </button>
    </nav>

    <div class="d-flex flex-column flex-md-row h-100 min-vh-100">
        <div class="sidebar p-3 flex-shrink-0 col-12 col-md-3 col-lg-2 d-flex flex-column" id="mobileSidebar">
            <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-light text-decoration-none d-none d-md-flex">
                <span class="fs-4 fw-bold">Öncel</span>
            </a>
            <hr class="text-white d-none d-md-block">
            <ul class="nav nav-pills flex-column mb-auto mt-2">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Anasayfa Yönetimi
                    </a>
                </li>
                <li>
                    <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i> Kategori Yönetimi
                    </a>
                </li>
                <li>
                    <a href="{{ route('product-groups.index') }}" class="nav-link {{ request()->routeIs('product-groups.*') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i> Kategori Grup Yönetimi
                    </a>
                </li>
                <li>
                    <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <i class="bi bi-collection"></i> Ürün & Stok Yönetimi
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <i class="bi bi-cart3"></i> Satış Yönetimi
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.appointments.index') }}" class="nav-link {{ request()->routeIs('admin.appointments.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check"></i> Randevu Yönetimi
                    </a>
                </li>
                <li>
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Müşteri Yönetimi
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.contacts.index') }}" class="nav-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                        <i class="bi bi-chat-dots"></i> İletişim Yönetimi
                    </a>
                </li>
                <li>
                    <a href="{{ route('admins.index') }}" class="nav-link {{ request()->routeIs('admins.*') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock"></i> Yönetici Yönetimi
                    </a>
                </li>
            </ul>
            <hr class="text-white">
            
            <form action="{{ route('admin.logout') }}" method="POST" class="mt-auto">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100">
                    <i class="bi bi-box-arrow-right"></i> Çıkış Yap
                </button>
            </form>
        </div>

        <div class="flex-grow-1 overflow-auto">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4 py-3 d-none d-md-block">
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <span class="text-muted">Yönetim Paneli</span>
                    <div>
                        <span class="fw-semibold me-3">{{ Auth::guard('admin')->user()->name ?? 'Yönetici' }}</span>
                    </div>
                </div>
            </nav>

            <div class="p-4">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggleBtn').addEventListener('click', function () {
            const sidebar = document.getElementById('mobileSidebar');
            sidebar.classList.toggle('show-menu');
        });
    </script>
</body>
</html>