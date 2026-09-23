@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">Satış Yönetimi ve İstatistikler</h3>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded me-3 text-primary">
                        <i class="bi bi-cart-check fs-3"></i>
                    </div>
                    <div>
                        <small class="text-muted">Bugün Satılan Ürün</small>
                        <h4 class="mb-0 fw-bold">{{ $todaySalesCount }} Adet</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded me-3 text-success">
                        <i class="bi bi-currency-lira fs-3"></i>
                    </div>
                    <div>
                        <small class="text-muted">Bugünkü Ciro</small>
                        <h4 class="mb-0 fw-bold">{{ number_format($todayRevenue, 2, ',', '.') }} TL</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-3">
                <h5 class="card-title fw-bold mb-3">Aylık Satış Grafiği ({{ date('Y') }})</h5>
                <canvas id="salesChart" height="120"></canvas>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-3">
                <h5 class="card-title fw-bold mb-3">En Çok Satan Ürünler</h5>
                <ul class="list-group list-group-flush">
                    @forelse($topProducts as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-0">{{ $item->product->name ?? 'Silinmiş Ürün' }}</h6>
                                <small class="text-muted">{{ number_format($item->total_amount, 2, ',', '.') }} TL</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ $item->total_qty }} Adet</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted px-0">Henüz satış verisi yok.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm p-3">
        <h5 class="card-title fw-bold mb-3">Son Siparişler</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Sipariş No</th>
                        <th>Müşteri</th>
                        <th>Tutar</th>
                        <th>Durum</th>
                        <th>Tarih</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestOrders as $order)
                        <tr>
                            <td><strong>#{{ $order->order_number }}</strong></td>
                            <td>{{ $order->user->name ?? 'Konuk Müşteri' }}</td>
                            <td>{{ number_format($order->total_price, 2, ',', '.') }} TL</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                @switch($order->status)
                                    @case('pending')
                                        <span class="badge bg-warning text-dark">Beklemede</span>
                                        @break
                                    @case('preparing')
                                        <span class="badge bg-info text-dark">Hazırlanıyor</span>
                                        @break
                                    @case('shipped')
                                        <span class="badge bg-primary">Kargoya Verildi</span>
                                        @break
                                    @case('delivered')
                                        <span class="badge bg-success">Teslim Edildi</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger">İptal Edildi</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                @endswitch
                            </td>
                            <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                        </tr>

                        <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Sipariş Güncelle: #{{ $order->order_number }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Sipariş Durumu</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Beklemede</option>
                                                    <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>Hazırlanıyor</option>
                                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Kargoya Verildi</option>
                                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Teslim Edildi</option>
                                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>İptal Edildi</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Kargo Firması</label>
                                                <input type="text" name="cargo_company" class="form-control" value="{{ $order->cargo_company }}" placeholder="Örn: Yurtiçi Kargo">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Kargo Takip Numarası</label>
                                                <input type="text" name="tracking_number" class="form-control" value="{{ $order->tracking_number }}" placeholder="Örn: TR123456789">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                            <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Sipariş bulunamadı.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
                label: 'Aylık Toplam Ciro (TL)',
                data: @json($chartData),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.3
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