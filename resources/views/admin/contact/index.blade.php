@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Gelen İletişim Mesajları</h3>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Tarih</th>
                            <th>Ad Soyad</th>
                            <th>Telefon</th>
                            <th>Konu</th>
                            <th>Mesaj</th>
                            <th style="width: 180px;">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contacts as $contact)
                            <tr>
                                <td>{{ $contact->created_at->format('d.m.Y H:i') }}</td>
                                <td>{{ $contact->name }}</td>
                                <td>
                                    <a href="tel:{{ $contact->phone }}" class="text-dark text-decoration-none">
                                        {{ $contact->phone }}
                                    </a>
                                </td>
                                <td>{{ $contact->subject }}</td>
                                <td>{{ Str::limit($contact->message, 50) }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#messageModal{{ $contact->id }}">
                                            <i class="bi bi-envelope-open"></i> Detay
                                        </button>

                                        <form action="{{ route('admin.contact.destroy', $contact->id) }}" method="POST" onsubmit="return confirm('Bu mesajı silmek istediğinize emin misiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <div class="modal fade" id="messageModal{{ $contact->id }}" tabindex="-1" aria-labelledby="messageModalLabel{{ $contact->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title" id="messageModalLabel{{ $contact->id }}">Mesaj Detayı</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <p><strong>Gönderen:</strong> {{ $contact->name }}</p>
                                                    <p><strong>Telefon:</strong> <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a></p>
                                                </div>
                                                <div class="col-md-6 text-md-end">
                                                    <p><strong>Tarih:</strong> {{ $contact->created_at->format('d.m.Y H:i') }}</p>
                                                    <p><strong>Konu:</strong> {{ $contact->subject }}</p>
                                                </div>
                                            </div>

                                            <div class="p-3 bg-light rounded border mb-4">
                                                <small class="text-muted d-block mb-1">Gelen Mesaj:</small>
                                                {{ $contact->message }}
                                            </div>

                                            <hr>

                                            @php
                                                $cleanPhone = preg_replace('/[^0-9]/', '', $contact->phone);
                                                if(strlen($cleanPhone) == 10) { 
                                                    $cleanPhone = '90' . $cleanPhone; 
                                                }
                                                $whatsappText = urlencode("Merhaba " . $contact->name . ", Öncel Perde'den bize ilettiğiniz mesajınızı aldık.");
                                            @endphp

                                            <div class="text-end">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Kapat</button>
                                                <a href="https://wa.me/{{ $cleanPhone }}?text={{ $whatsappText }}" target="_blank" class="btn btn-success btn-sm">
                                                    <i class="bi bi-whatsapp"></i> WhatsApp ile Yanıtla
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Henüz gelen mesaj bulunmuyor.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $contacts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection