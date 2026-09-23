@extends('layouts.front')

@section('content')
<style>
    .auth-wrapper {
        min-height: 80vh;
        display: flex;
        align-items: center;
        background-color: #faf9f6; 
        font-family: 'Montserrat', sans-serif;
        padding: 60px 0;
    }
    .auth-card {
        background: #ffffff;
        border: 1px solid #e0ded9;
        border-radius: 0; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        padding: 40px;
        width: 100%;
        max-width: 450px;
        margin: 0 auto;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .auth-card:hover {
        box-shadow: 0 20px 40px rgba(0,0,0,0.06);
    }
    .auth-title {
        font-family: 'Cinzel', serif;
        font-size: 1.75rem;
        font-weight: 400;
        color: #0f0f0f;
        letter-spacing: 2px;
        text-align: center;
        margin-bottom: 30px;
        text-transform: uppercase;
    }
    .form-control {
        border-radius: 0 !important;
        border-top: 0 !important;
        border-right: 0 !important;
        border-left: 0 !important;
        box-shadow: none !important;
        padding-left: 0 !important;
    }
    .form-control:focus {
        border-color: #d4af37 !important;
    }
    .btn-luxe {
        background: #0f0f0f;
        color: #ffffff;
        border-radius: 0;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 0.9rem;
        padding: 15px;
        transition: all 0.4s ease;
        border: none;
        width: 100%;
    }
    .btn-luxe:hover {
        background: #b8903b;
        color: #fff;
    }
    .toggle-link {
        text-align: center;
        margin-top: 20px;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #8a8a8a;
    }
    .toggle-link a {
        color: #0f0f0f;
        text-decoration: underline;
        font-weight: 500;
    }
    .alert-danger {
        background-color: #fdf2f2 !important;
        color: #623345 !important;
        border: 1px solid #e0ded9 !important;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border-radius: 0 !important;
    }
    .form-section {
        transition: opacity 0.4s ease-in-out;
    }
</style>

<div class="auth-wrapper">
    <div class="container">
        
        @if($errors->any())
            <div class="row justify-content-center mb-4">
                <div class="col-md-6">
                    <div class="alert alert-danger py-3 px-4">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

       
        <div id="loginSection" class="form-section">
            <div class="auth-card">
                <h2 class="auth-title">Oturum Aç</h2>
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="form-floating mb-3">
                        <input type="email" name="email" class="form-control" id="loginEmail" placeholder="E-posta" required value="{{ old('email') }}">
                        <label for="loginEmail" class="px-0">E-posta Adresi</label>
                    </div>
                    <div class="form-floating mb-4">
                        <input type="password" name="password" class="form-control" id="loginPassword" placeholder="Şifre" required>
                        <label for="loginPassword" class="px-0">Şifre</label>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check"></div>
                        <a href="javascript:void(0);" onclick="openForgotPasswordModal()" class="text-muted small">Şifremi Unuttum?</a>
                    </div>
                    <button type="submit" class="btn btn-luxe">Giriş Yap</button>
                </form>
                <div class="toggle-link">
                    Hesabınız yok mu? <a href="javascript:void(0);" onclick="toggleForms()">Kayıt Ol</a>
                </div>
            </div>
        </div>

    
        <div id="registerSection" class="form-section" style="display: none; opacity: 0;">
            <div class="auth-card">
                <h2 class="auth-title">Hesap Oluştur</h2>
                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="form-floating mb-3">
                        <input type="text" name="name" class="form-control" id="registerName" placeholder="Ad Soyad" required>
                        <label for="registerName" class="px-0">Ad Soyad</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" name="email" class="form-control" id="registerEmail" placeholder="E-posta" required>
                        <label for="registerEmail" class="px-0">E-posta Adresi</label>
                    </div>
                    <div class="form-floating mb-4">
                        <input type="password" name="password" class="form-control" id="registerPassword" placeholder="Şifre" required>
                        <label for="registerPassword" class="px-0">Şifre</label>
                    </div>
                    <button type="submit" class="btn btn-luxe">Kayıt Ol</button>
                </form>
                <div class="toggle-link">
                    Zaten hesabınız var mı? <a href="javascript:void(0);" onclick="toggleForms()">Giriş Yap</a>
                </div>
            </div>
        </div>

    </div>
</div>


<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-0 p-4" style="background: #ffffff; border: 1px solid #e0ded9 !important;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title auth-title fs-5" id="modalTitle">Şifremi Yenile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                
                <div id="step1Form">
                    <p class="text-muted small mb-3">Hesabınıza kayıtlı e-posta adresinizi girin, tarafınıza 6 haneli doğrulama kodu gönderilecektir.</p>
                    <div class="form-floating mb-3">
                        <input type="email" id="resetEmail" class="form-control" placeholder="E-posta" required>
                        <label for="resetEmail" class="px-0">E-posta Adresi</label>
                    </div>
                    <button type="button" class="btn btn-luxe" onclick="sendCode()">Kod Gönder</button>
                </div>

                
                <div id="step2Form" style="display: none;">
                    <p class="text-muted small mb-3">E-postanıza gönderilen 6 haneli kodu ve yeni şifrenizi girin.</p>
                    <input type="hidden" id="verifiedEmail">
                    
                    <div class="form-floating mb-3">
                        <input type="text" id="resetCode" class="form-control" placeholder="6 Haneli Kod" maxlength="6" required>
                        <label for="resetCode" class="px-0">6 Haneli Doğrulama Kodu</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" id="newPassword" class="form-control" placeholder="Yeni Şifre" required>
                        <label for="newPassword" class="px-0">Yeni Şifre</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" id="newPasswordConfirmation" class="form-control" placeholder="Yeni Şifre Tekrar" required>
                        <label for="newPasswordConfirmation" class="px-0">Yeni Şifre Tekrar</label>
                    </div>
                    <button type="button" class="btn btn-luxe" onclick="resetPassword()">Şifreyi Güncelle</button>
                </div>

                <div id="modalAlert" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleForms() {
        const loginSection = document.getElementById('loginSection');
        const registerSection = document.getElementById('registerSection');
        
        if (loginSection.style.display !== 'none') {
            loginSection.style.opacity = '0';
            setTimeout(() => {
                loginSection.style.display = 'none';
                registerSection.style.display = 'block';
                setTimeout(() => { registerSection.style.opacity = '1'; }, 50);
            }, 400);
        } else {
            registerSection.style.opacity = '0';
            setTimeout(() => {
                registerSection.style.display = 'none';
                loginSection.style.display = 'block';
                setTimeout(() => { loginSection.style.opacity = '1'; }, 50);
            }, 400);
        }
    }

    function openForgotPasswordModal() {
        const modalElement = document.getElementById('forgotPasswordModal');
        if (modalElement) {
            document.getElementById('step1Form').style.display = 'block';
            document.getElementById('step2Form').style.display = 'none';
            document.getElementById('resetEmail').value = '';
            document.getElementById('modalAlert').innerHTML = '';

            var myModal = new bootstrap.Modal(modalElement);
            myModal.show();
        } else {
            console.error("Modal elementi bulunamadı!");
        }
    }

    function sendCode() {
        const email = document.getElementById('resetEmail').value;
        const alertDiv = document.getElementById('modalAlert');

        fetch("{{ route('password.send-code') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(res => {
            if (res.status === 200 && res.body.success) {
                document.getElementById('verifiedEmail').value = email;
                document.getElementById('step1Form').style.display = 'none';
                document.getElementById('step2Form').style.display = 'block';
                alertDiv.innerHTML = `<div class="alert alert-success py-2 fs-6">${res.body.message}</div>`;
            } else {
                let msg = res.body.message || 'Bir hata oluştu. E-postayı kontrol edin.';
                alertDiv.innerHTML = `<div class="alert alert-danger py-2 fs-6">${msg}</div>`;
            }
        }).catch(err => {
            alertDiv.innerHTML = `<div class="alert alert-danger py-2 fs-6">Bağlantı hatası oluştu.</div>`;
        });
    }

    function resetPassword() {
        const email = document.getElementById('verifiedEmail').value;
        const code = document.getElementById('resetCode').value;
        const password = document.getElementById('newPassword').value;
        const password_confirmation = document.getElementById('newPasswordConfirmation').value;
        const alertDiv = document.getElementById('modalAlert');

        fetch("{{ route('password.update-with-code') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ email, code, password, password_confirmation })
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(res => {
            if (res.status === 200 && res.body.success) {
                alertDiv.innerHTML = `<div class="alert alert-success py-2 fs-6">${res.body.message} Giriş yapılıyor...</div>`;
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                let msg = res.body.message || 'Bilgileri kontrol edin.';
                alertDiv.innerHTML = `<div class="alert alert-danger py-2 fs-6">${msg}</div>`;
            }
        }).catch(err => {
            alertDiv.innerHTML = `<div class="alert alert-danger py-2 fs-6">Bağlantı hatası oluştu.</div>`;
        });
    }
</script>
@endsection