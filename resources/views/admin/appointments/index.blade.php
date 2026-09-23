@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Randevular & Özel Tasarımlar</h1>
        <p class="text-muted mb-0">Gelen talep listesi</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4" style="width: 50px;">#</th>
                        <th>İsim</th>
                        <th>Numara</th>
                        <th>Kategori</th>
                        <th>Açıklama</th>
                        <th class="text-end pe-4" style="width: 250px;">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="text-dark">
                    @forelse($appointments as $appointment)
                    <tr>
                        <td class="ps-4 fw-bold text-secondary">{{ $loop->iteration }}</td>
                        <td class="fw-semibold">{{ $appointment->name }}</td>
                        <td>
                            <span class="text-muted small"><i class="bi bi-telephone me-1"></i>{{ $appointment->phone }}</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $appointment->clothing_type ?? 'Belirtilmedi' }}</span>
                        </td>
                        <td>
                            <span class="text-muted small" title="{{ $appointment->message }}">
                                {{ Str::limit($appointment->message ?? '-', 40) }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-1 align-items-center">
                                <button type="button" class="btn btn-sm btn-light border fw-medium" data-bs-toggle="modal" data-bs-target="#detailModal{{ $appointment->id }}">
                                    <i class="bi bi-eye text-primary me-1"></i> Detay Gör
                                </button>

                                @php
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $appointment->phone);
                                    if (strpos($cleanPhone, '90') !== 0) { 
                                        $cleanPhone = '90' . $cleanPhone; 
                                    }
                                    
                                    $clothingType = $appointment->clothing_type ?? 'Özel Tasarım';
                                    $statusText = $appointment->status ?? 'Bekliyor';
                                    $messageTemplate = "Merhaba {$appointment->name}, Öncel Yönetim Panelinden ulaşıyoruz. Göndermiş olduğunuz *{$clothingType}* siparişi/randevu talebi incelenmiştir. Güncel durumunuz: *{$statusText}* olarak güncellenmiştir. Detayları görüşmek üzere randevunuzu netleştirmek isteriz.";
                                    $whatsappUrl = "https://api.whatsapp.com/send?phone=" . $cleanPhone . "&text=" . rawurlencode($messageTemplate);
                                @endphp

                                <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-sm btn-success" title="WhatsApp İletişim">
                                    <i class="bi bi-whatsapp"></i>
                                </a>

                                <form action="{{ route('admin.appointments.destroy', $appointment->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Silmek istediğinizden emin misiniz?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border" title="Sil">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox h2 d-block text-secondary mb-2"></i>
                            Henüz herhangi bir randevu veya özel tasarım talebi bulunmuyor.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@foreach($appointments as $appointment)
<div class="modal fade" id="detailModal{{ $appointment->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="bi bi-person-badge me-2"></i> Talep Detayı: {{ $appointment->name }} (#{{ $appointment->id }})
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body bg-light">
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0 fw-bold text-dark">Durum Güncelle</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.appointments.updateStatus', $appointment->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="input-group">
                                        <select name="status" class="form-select fw-semibold 
                                            @if($appointment->status == 'Bekliyor') text-warning
                                            @elseif($appointment->status == 'Onaylandı') text-success
                                            @elseif($appointment->status == 'Tamamlandı') text-primary
                                            @else text-danger @endif">
                                            <option value="Bekliyor" {{ $appointment->status == 'Bekliyor' ? 'selected' : '' }}>⏳ Bekliyor</option>
                                            <option value="Onaylandı" {{ $appointment->status == 'Onaylandı' ? 'selected' : '' }}>✅ Onaylandı</option>
                                            <option value="Tamamlandı" {{ $appointment->status == 'Tamamlandı' ? 'selected' : '' }}>📦 Tamamlandı</option>
                                            <option value="İptal" {{ $appointment->status == 'İptal' ? 'selected' : '' }}>❌ İptal</option>
                                        </select>
                                        <button type="submit" class="btn btn-outline-primary">Kaydet</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-white border-0 py-3">
                                <h6 class="mb-0 text-primary fw-bold"><i class="bi bi-info-circle-fill me-2"></i> İletişim & Talep Özetleri</h6>
                            </div>
                            <div class="card-body pt-0">
                                <table class="table table-sm table-borderless mb-0 text-dark">
                                    <tr><td class="text-muted w-25">Müşteri:</td><td class="fw-semibold">{{ $appointment->name }}</td></tr>
                                    <tr><td class="text-muted">Telefon:</td><td>{{ $appointment->phone }}</td></tr>
                                    <tr><td class="text-muted">Kategori:</td><td><span class="badge bg-secondary">{{ $appointment->clothing_type ?? 'Girilmedi' }}</span></td></tr>
                                    <tr><td class="text-muted">Detaylar:</td><td><p class="mb-0 text-secondary bg-white p-2 rounded border small">{{ $appointment->message ?? 'Ekstra detay belirtilmemiş.' }}</p></td></tr>
                                </table>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-white border-0 py-3">
                                <h6 class="mb-0 text-success fw-bold"><i class="bi bi-rulers me-2"></i> Girilen Anatomik Ölçüler</h6>
                            </div>
                            <div class="card-body pt-0">
                                <div class="row text-center g-2">
                                    <div class="col-4 bg-white border rounded p-2"><small class="text-muted d-block">Boy</small><span class="fw-bold">{{ $appointment->height ?? '-' }} cm</span></div>
                                    <div class="col-4 bg-white border rounded p-2"><small class="text-muted d-block">Kilo Türü</small><span class="fw-bold">{{ ucfirst($appointment->manken_kilo ?? '-') }}</span></div>
                                    <div class="col-4 bg-white border rounded p-2"><small class="text-muted d-block">Göğüs</small><span class="fw-bold">{{ $appointment->chest ?? '-' }} cm</span></div>
                                    <div class="col-4 bg-white border rounded p-2"><small class="text-muted d-block">Bel</small><span class="fw-bold">{{ $appointment->waist ?? '-' }} cm</span></div>
                                    <div class="col-4 bg-white border rounded p-2"><small class="text-muted d-block">Kalça</small><span class="fw-bold">{{ $appointment->hip ?? '-' }} cm</span></div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted fw-bold d-block mb-1">Müşteri Ekstra Ölçü Notu:</small>
                                    <div class="p-2 bg-white rounded border small text-secondary">{{ $appointment->ekstra_olculer ?? 'Ekstra ölçü notu bırakılmamış.' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0 py-3">
                                <h6 class="mb-0 text-info fw-bold"><i class="bi bi-images me-2"></i> Yüklenen İlham / Referans Resimleri</h6>
                            </div>
                            <div class="card-body pt-0">
                                <div class="row g-2">
                                    @if($appointment->referans_resimler)
                                        @php 
                                            $resimler = is_array($appointment->referans_resimler) ? $appointment->referans_resimler : json_decode($appointment->referans_resimler, true);
                                        @endphp
                                        @if($resimler && count($resimler) > 0)
                                            @foreach($resimler as $imgSrc)
                                                <div class="col-4">
                                                    <a href="{{ asset('storage/' . $imgSrc) }}" target="_blank">
                                                        <img src="{{ asset('storage/' . $imgSrc) }}" class="img-fluid rounded border img-thumbnail" style="height: 90px; width: 100%; object-fit: cover;" alt="Referans Görseli">
                                                    </a>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted small ps-2 mb-0">Kullanıcı referans resim eklemedi.</p>
                                        @endif
                                    @else
                                        <p class="text-muted small ps-2 mb-0">Kullanıcı referans resim eklemedi.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 text-center">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-0 py-3 text-start">
                                <h6 class="mb-0 text-danger fw-bold"><i class="bi bi-palette-fill me-2"></i> Kullanıcının Çizdiği Canlı Tasarım</h6>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center bg-white border m-3 rounded p-4 position-relative" style="min-height: 380px;">
                                @if($appointment->cizim_katmani)
                                    <div class="canvas-preview-container" style="position: relative; display: inline-block; width: 250px; height: 400px; max-width: 100%;">
                                        <img src="{{ asset('models/' . strtolower($appointment->gender ?? 'kadin') . '_' . strtolower($appointment->manken_kilo ?? 'orta') . '.png') }}" 
                                             style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain; opacity: 0.85;" 
                                             onerror="this.src='http://127.0.0.1:8000/models/kadin_orta.png';" 
                                             alt="Manken Arkaplan Görseli">
                                        
                                        <img src="{{ $appointment->cizim_katmani }}" 
                                             style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain; pointer-events: none;" 
                                             alt="Müşteri Çizimi Görseli">
                                    </div>
                                @else
                                    <div class="text-muted small">
                                        <i class="bi bi-pencil-eraser d-block h1 text-secondary"></i>
                                        Kullanıcı manken üzerinde herhangi bir çizim yapmadan formu gönderdi.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Pencereyi Kapat</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection