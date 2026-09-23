@extends('layouts.account')

@section('account-content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@php
    $shippingTotal = 0;
    if(isset($cart) && $cart->count() > 0) {
        foreach($cart as $item) {
            $shippingTotal += ($item->product->shipping_price ?? 0);
        }
    }
    $finalTotal = $grandTotal + $shippingTotal;
@endphp

<form id="checkoutForm" action="{{ route('order.store') }}" method="POST">
    @csrf
    
    <div class="row g-4">
        <div class="col-lg-8">
            <h5 class="mb-3">Teslimat Adresi</h5>
            <textarea name="address" class="form-control mb-4" rows="3" placeholder="Açık adresinizi yazınız..." required></textarea>

            <h5 class="mb-3">Ödeme Seçenekleri</h5>
            <div class="d-flex mb-3">
                <div class="form-check me-4">
                    <input class="form-check-input" type="radio" name="payment_method" value="card" checked>
                    <label class="form-check-label">Kredi Kartı</label>
                </div>
            </div>

            <div class="card p-3 bg-light mb-4">
                <input type="text" name="card_name" class="form-control mb-2" placeholder="Örn: AHMED YILMAZ" required style="text-transform: uppercase;">
                
                <input type="text" id="card_number" name="card_number" class="form-control mb-2" placeholder="**** **** **** ****" maxlength="19" required autocomplete="off">
                
                <div class="row">
                    <div class="col-6">
                        <input type="text" id="expiry" name="expiry" class="form-control" placeholder="AA/YY (Örn: 08/28)" maxlength="5" required autocomplete="off">
                    </div>
                    <div class="col-6">
                        <input type="password" id="cvv" name="cvv" class="form-control" placeholder="CVV (Örn: 123)" maxlength="3" required autocomplete="off">
                    </div>
                </div>
            </div>

            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="agreement" id="agreement" required>
                <label class="form-check-label" for="agreement">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#sozlesmeModal">Mesafeli Satış Sözleşmesi</a>'ni okudum ve onaylıyorum.
                </label>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="kvkk" id="kvkk" required>
                <label class="form-check-label" for="kvkk">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#kvkkModal">KVKK Aydınlatma Metni</a>'ni okudum ve kabul ediyorum.
                </label>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="main-card p-4 bg-light rounded position-sticky" style="top: 20px;">
                <h5 class="mb-3">Sipariş Özeti</h5>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span>Ürünler Toplamı:</span>
                    <span>{{ number_format($grandTotal, 2) }} TL</span>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span>Kargo Toplamı:</span>
                    <span>
                        @if($shippingTotal > 0)
                            <strong>{{ number_format($shippingTotal, 2) }} TL</strong>
                        @else
                            <span class="badge bg-success" style="border-radius: 0; font-size: 0.65rem;">Ücretsiz Kargo</span>
                        @endif
                    </span>
                </div>
                
                <hr>
                <div class="d-flex justify-content-between mb-4">
                    <strong>Genel Toplam:</strong>
                    <strong class="text-danger fs-4">{{ number_format($finalTotal, 2) }} TL</strong>
                </div>

                <button type="submit" class="btn btn-dark w-100 py-3" id="btnSubmit">Siparişi Onayla</button>
            </div>
        </div>
    </div>
</form>

<div class="modal fade" id="sozlesmeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mesafeli Satış Sözleşmesi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @include('legal.sozlesme')
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="kvkkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">KVKK Aydınlatma Metni</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @include('legal.kvkk')
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const cardNumberInput = document.getElementById('card_number');
    const expiryInput = document.getElementById('expiry');
    const cvvInput = document.getElementById('cvv');

    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.substring(0, 16);
            let formatted = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formatted;
        });
    }

    if (expiryInput) {
        expiryInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                let month = parseInt(value.substring(0, 2), 10);
                if (month < 1) month = '01';
                if (month > 12) month = '12';
                let monthStr = month < 10 ? '0' + month : '' + month;
                value = monthStr + value.substring(2, 4);
            }
            if (value.length > 2) {
                e.target.value = value.substring(0, 2) + '/' + value.substring(2, 4);
            } else {
                e.target.value = value;
            }
        });
    }

    if (cvvInput) {
        cvvInput.addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/\D/g, '').substring(0, 3);
        });
    }
});

@if(session('success'))
    let timerInterval;
    Swal.fire({
        title: 'Siparişiniz Alındı!',
        html: 'Ödemeniz başarıyla alındı. <b>5</b> saniye içinde siparişleriniz sayfasına yönlendiriliyorsunuz...',
        icon: 'success',
        timer: 5000,
        timerProgressBar: true,
        confirmButtonText: 'Hemen Siparişlerime Git',
        didOpen: () => {
            const content = Swal.getHtmlContainer();
            const b = content.querySelector('b');
            timerInterval = setInterval(() => {
                b.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
            }, 100);
        },
        willClose: () => {
            clearInterval(timerInterval);
        }
    }).then((result) => {
        window.location.href = "{{ url('dashboard/hesabim/siparislerim') }}";
    });
@endif
</script>
@endsection