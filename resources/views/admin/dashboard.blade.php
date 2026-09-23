@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Kontrol Paneli</h1>
        <p class="text-muted">Öncel yönetim sistemine hoş geldiniz.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Toplam Ürün</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProducts }}</div>
                    </div>
                    <div class="col-auto"><i class="bi bi-collection fs-2 text-primary"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Bekleyen Randevu</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingAppointments }}</div>
                    </div>
                    <div class="col-auto"><i class="bi bi-calendar-check fs-2 text-success"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="{{ route('admin.contacts.index') }}" class="text-decoration-none">
            <div class="card border-left-danger shadow h-100 py-2" style="border-left: 5px solid #dc3545 !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">İletişim Mesajları</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $pendingMessages ?? 0 }} 
                                @if(isset($pendingMessages) && $pendingMessages > 0)
                                    <span class="badge bg-danger ms-1" style="font-size: 0.6rem;">Yeni</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-auto"><i class="bi bi-chat-left-text fs-2 text-danger"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Kategoriler</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCategories }}</div>
                    </div>
                    <div class="col-auto"><i class="bi bi-tags fs-2 text-info"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm p-3">
            <h5 class="card-title fw-bold mb-3 text-secondary">
                <i class="bi bi-graph-up-arrow text-primary me-2"></i> Aylık Satış Grafiği ({{ date('Y') }})
            </h5>
            <canvas id="salesChart" height="90"></canvas>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <h5 class="mb-3 text-secondary fw-bold" style="letter-spacing: 1px;">
            <i class="bi bi-sparkles text-warning me-2"></i> Bekleyen Yeni Randevular
        </h5>
        
        <div class="row g-3">
            @forelse($recentAppointments as $appointment)
            <div class="col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: 15px; border-left: 5px solid #ffc107;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge rounded-pill bg-warning text-dark px-3 py-2" style="font-size: 0.7rem;">Yeni Talep</span>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $appointment->created_at->diffForHumans() }}</small>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">{{ $appointment->name }}</h6>
                    <p class="text-muted small mb-2"><i class="bi bi-telephone me-1"></i> {{ $appointment->phone }}</p>
                    <div class="bg-light p-2 rounded small text-secondary mb-3" style="font-size: 0.8rem;">
                        {{ Str::limit($appointment->clothing_type, 30) }}
                    </div>
                    <a href="{{ route('admin.appointments.index') }}" class="btn btn-sm btn-outline-dark w-100 rounded-pill">
                        Detaylı İncele
                    </a>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-3">
                <div class="text-muted small"><i class="bi bi-check2-circle"></i> Bekleyen randevu bulunmuyor.</div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-12">
        <h5 class="mb-3 text-secondary fw-bold" style="letter-spacing: 1px;">
            <i class="bi bi-envelope-exclamation text-danger me-2"></i> Son Gelen İletişim Mesajları
        </h5>
        
        <div class="row g-3">
            @forelse($recentMessages ?? [] as $message)
            <div class="col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: 15px; border-left: 5px solid #dc3545;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge rounded-pill bg-danger text-white px-3 py-2" style="font-size: 0.7rem;">İletişim</span>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $message->created_at->diffForHumans() }}</small>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">{{ $message->name }}</h6>
                    <p class="text-muted small mb-1"><i class="bi bi-telephone me-1"></i> {{ $message->phone }}</p>
                    <p class="text-dark small fw-semibold mb-2">Konu: {{ $message->subject }}</p>
                    <div class="bg-light p-2 rounded small text-secondary mb-3" style="font-size: 0.8rem;">
                        {{ Str::limit($message->message, 35) }}
                    </div>
                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-sm btn-outline-danger w-100 rounded-pill">
                        Mesajlara Git & Yanıtla
                    </a>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-3">
                <div class="text-muted small"><i class="bi bi-check2-circle"></i> Henüz gelen mesaj bulunmuyor.</div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
            datasets: [{
                label: 'Aylık Ciro (TL)',
                data: @json($chartData),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.08)',
                fill: true,
                tension: 0.35,
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection