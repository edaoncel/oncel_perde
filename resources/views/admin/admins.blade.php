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
                <a href="{{ route('admins.index') }}" class="nav-link {{ request()->routeIs('admins.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock"></i> Yönetici Yönetimi
                </a>
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
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="fw-bold fs-3 text-dark">Yönetici Yönetimi</h2>
                        <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#createAdminModal">
                            <i class="bi bi-person-plus me-1"></i> Yeni Yönetici Ekle
                        </button>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">Ad Soyad</th>
                                            <th>E-posta</th>
                                            <th>Kayıt Tarihi</th>
                                            <th class="text-end pe-4">İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($admins as $admin)
                                            <tr>
                                                <td class="ps-4 fw-semibold">{{ $admin->name }}</td>
                                                <td>{{ $admin->email }}</td>
                                                <td>{{ $admin->created_at ? $admin->created_at->format('d.m.Y H:i') : '-' }}</td>
                                                <td class="text-end pe-4">
                                                    <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#editPasswordModal{{ $admin->id }}" title="Şifre Yenile">
                                                        <i class="bi bi-key"></i>
                                                    </button>
                                                    
                                                    @if(Auth::guard('admin')->id() !== $admin->id)
                                                        <form action="{{ route('admins.destroy', $admin->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu yöneticiyi silmek istediğinize emin misiniz?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Yöneticiyi Kaldır">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="editPasswordModal{{ $admin->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('admins.update-password', $admin->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title fw-bold">Şifre Yenile: {{ $admin->name }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body text-start">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Yeni Şifre</label>
                                                                    <input type="password" name="password" class="form-control" required minlength="6">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Yeni Şifre (Tekrar)</label>
                                                                    <input type="password" name="password_confirmation" class="form-control" required minlength="6">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                                <button type="submit" class="btn btn-dark">Şifreyi Güncelle</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">Kayıtlı yönetici bulunamadı.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createAdminModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admins.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Yeni Yönetici Ekle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-start">
                        
                        @if ($errors->any())
                            <div class="alert alert-danger mb-3">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Ad Soyad</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">E-posta Adresi</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Şifre</label>
                            <input type="password" name="password" class="form-control" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Şifre (Tekrar)</label>
                            <input type="password" name="password_confirmation" class="form-control" required minlength="6">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-dark">Yöneticiyi Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggleBtn').addEventListener('click', function () {
            const sidebar = document.getElementById('mobileSidebar');
            sidebar.classList.toggle('show-menu');
        });

        @if ($errors->any())
            var myModal = new bootstrap.Modal(document.getElementById('createAdminModal'));
            myModal.show();
        @endif
    </script>
</body>
</html>