@extends('layouts.front')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
<style>
    :root {
        --gold: #d4af37;
        --off-white: #eae4e6;
        --pure-black: #653d48;
        --charcoal: #1a1a1a;
    }

    .contact-wrapper {
        background-color: var(--off-white);
        font-family: 'Montserrat', sans-serif;
        padding: 80px 0;
    }

    
    .contact-card-luxe {
        background: #ffffff;
        border: 1px solid #e0ded9;
        border-radius: 0;
        overflow: hidden;
        height: 100%;
        box-shadow: 0 5px 15px rgba(0,0,0,0.03);
        transition: transform 0.4s ease, border-color 0.4s ease;
    }

    .contact-card-luxe:hover {
        transform: translateY(-5px);
        border-color: var(--gold);
    }

    .card-img-wrapper {
        height: 220px;
        overflow: hidden;
        position: relative;
    }

    .card-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .contact-card-luxe:hover .card-img-wrapper img {
        transform: scale(1.05);
    }

    .card-content-luxe {
        padding: 30px 20px;
        text-align: center;
    }

    .luxe-card-title {
        font-family: 'Cinzel', serif;
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: var(--pure-black);
        margin-bottom: 15px;
        font-weight: 600;
    }

    .luxe-card-text {
        color: var(--charcoal);
        font-size: 0.85rem;
        line-height: 1.6;
        margin-bottom: 0;
    }

    .luxe-card-text a {
        color: var(--charcoal);
        transition: color 0.3s ease;
        text-decoration: none;
    }

    .luxe-card-text a:hover {
        color: var(--gold);
    }

    .contact-form-card {
        background: #ffffff;
        border: 1px solid #e0ded9;
        border-radius: 0;
        padding: 50px;
        margin-top: 60px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
    }

    .form-title-luxe {
        font-family: 'Cinzel', serif;
        font-size: 1.75rem;
        text-transform: uppercase;
        letter-spacing: 3px;
        color: var(--pure-black);
        margin-bottom: 35px;
        text-align: center;
    }

    .form-label-luxe {
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        color: var(--charcoal);
        font-weight: 500;
        margin-bottom: 8px;
    }

    .input-luxe {
        border-radius: 0;
        border-color: #cfcfcf;
        padding: 14px 18px;
        font-size: 0.9rem;
        color: var(--pure-black);
        background-color: #ffffff;
    }

    .input-luxe:focus {
        border-color: var(--pure-black);
        box-shadow: none;
        background-color: #ffffff;
    }

    textarea.input-luxe {
        resize: none;
    }

    .btn-luxe {
        background: var(--pure-black);
        color: #ffffff;
        border-radius: 0;
        text-transform: uppercase;
        letter-spacing: 3px;
        font-size: 0.9rem;
        padding: 18px 30px;
        border: none;
        transition: all 0.5s ease;
        width: 100%;
        margin-top: 10px;
    }

    .btn-luxe:hover {
        background: var(--gold);
        color: var(--pure-black);
    }

    .profile-section {
        padding: 40px 0;
    }

    .logo-card {
        position: relative;
        overflow: hidden;
        cursor: pointer;
        aspect-ratio: 3 / 4;
        width: 100%;
        border-radius: 10px;
    }

    .logo-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        display: block;
        filter: grayscale(100%);
        transition: filter 0.5s ease, transform 0.5s ease;
    }

    .logo-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 20px 10px;
        background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
        color: #ffffff;
        text-align: center;
        font-weight: bold;
        font-family: sans-serif;
        letter-spacing: 1px;
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .logo-card:hover .logo-img {
        filter: grayscale(0%);
        transform: scale(1.05);
    }

    .logo-card:hover .logo-overlay {
        opacity: 1;
    }

    .social-links a {
        color: #000000 !important;
        transition: all 0.3s ease;
        display: inline-block;
    }

    .social-links a:hover {
        transform: scale(1.2);
    }

    .social-links a:hover[aria-label="Instagram"] { color: #E1306C !important; }
    .social-links a:hover[aria-label="Facebook"] { color: #4267B2 !important; }
    .social-links a:hover[aria-label="WhatsApp"] { color: #25D366 !important; }

    .contact-hero {
        width: 100%;
        min-height: 400px;
        padding: 140px 0 80px 0;
        background-image: url("{{ asset('images/2.png') }}");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-bottom: 1px solid #e0dcce;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    .splash h1 {
        font-family: 'Georgia', 'Times New Roman', Times, serif;
        font-size: clamp(3rem, 4vw, 6rem);
        font-weight: 300;
        font-style: italic;
        letter-spacing: -0.02em;
        line-height: 0.9;
        margin-bottom: 2rem;
        color: #f8f9f8;
    }
</style>

<div class="contact-hero"> 
    <span class="text-uppercase d-block mb-3" style="letter-spacing: 4px; font-size: 12px; font-weight: 600; color: #c2a66c;">İletişim</span>
    <section class="splash">
        <h1>Öncel ile İletişime Geçin</h1>
    </section>
</div>

<div class="contact-wrapper">
    <div class="container">
        
       
        <div class="row g-4 justify-content-center profile-section">
            <div class="col-6 col-md-4 col-lg-3">
                <div class="logo-card">
                    <img src="{{ asset('adminImages/ozlem.png') }}" alt="Özlem Öncel" class="logo-img">
                    <div class="logo-overlay">ÖZLEM ÖNCEL</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="logo-card">
                    <img src="{{ asset('adminImages/tahsin.png') }}" alt="Tahsin Öncel" class="logo-img">
                    <div class="logo-overlay">TAHSİN ÖNCEL</div>
                </div>
            </div>

            <div class="col-12 text-center mt-4">
                <div class="fs-4 social-links d-flex justify-content-center align-items-center gap-3">
                    <a href="https://www.instagram.com/perdetasarimkiyafet/" class="text-dark text-decoration-none" target="_blank" aria-label="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="https://www.facebook.com/tahsin.oncel.5" class="text-dark text-decoration-none" target="_blank" aria-label="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <div class="dropdown d-inline-block">
                        <a href="#" class="text-dark text-decoration-none" id="whatsappDropdown" data-bs-toggle="dropdown" aria-expanded="false" aria-label="WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-center shadow-sm" aria-labelledby="whatsappDropdown">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="https://wa.me/905436011884" target="_blank">
                                    <i class="bi bi-whatsapp text-success"></i> Tahsin Öncel
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="https://wa.me/905417922550" target="_blank">
                                    <i class="bi bi-whatsapp text-success"></i> Özlem Öncel
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="row g-4 justify-content-center mb-4">
            <div class="col-lg-4 col-md-6">
                <div class="contact-card-luxe">
                    <a href="https://maps.app.goo.gl/v62goMDmLvxBVd6Y8" target="_blank" style="text-decoration: none; color: inherit;">
                        <div class="card-img-wrapper">
                            <img src="{{ asset('images/contact/3.png') }}" alt="Adres Görseli">
                        </div>
                        <div class="card-content-luxe">
                            <h3 class="luxe-card-title">Adresimiz</h3>
                            <p class="luxe-card-text">
                                PTT Karşısı, Çınar, Gürpınar Cd. No:26<br>
                                15900 Çavdır / Burdur
                            </p>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="contact-card-luxe">
                    <div class="card-img-wrapper">
                        <img src="{{ asset('images/contact/2.png') }}" alt="Telefon Görseli">
                    </div>
                    <div class="card-content-luxe">
                        <h3 class="luxe-card-title">Telefon</h3>
                        <p class="luxe-card-text">
                            <a href="tel:+905436011884">+90 (543) 601 18 84 - Tahsin Öncel</a><br>
                            <a href="tel:+905417922550">+90 (541) 792 25 50 - Özlem Öncel</a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="contact-card-luxe">
                    <div class="card-img-wrapper">
                        <img src="{{ asset('images/contact/1.png') }}" alt="Mail Görseli">
                    </div>
                    <div class="card-content-luxe">
                        <h3 class="luxe-card-title">E-posta</h3>
                        <p class="luxe-card-text">
                            <a href="mailto:oncel_perde@gmail.com">oncel_perde@gmail.com</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="contact-form-card">
                    <h2 class="form-title-luxe">Bize Mesaj Yazın</h2>
                    
                    @if(session('success'))
                        <div class="alert alert-success rounded-0 border-0 py-3 mb-4 text-center" style="letter-spacing: 1px; font-size: 0.9rem;">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label-luxe">Ad Soyad</label>
                                <input type="text" name="name" class="form-control input-luxe shadow-none" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-luxe">Telefon Numarası</label>
                                <input type="text" name="phone" class="form-control input-luxe shadow-none" placeholder="05XXXXXXXXX" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label-luxe">Konu</label>
                                <input type="text" name="subject" class="form-control input-luxe shadow-none" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label-luxe">Mesajınız</label>
                                <textarea name="message" rows="6" class="form-control input-luxe shadow-none" required></textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="kvkk" id="kvkk" required>
                                    <label class="form-check-label" for="kvkk" style="font-size: 0.85rem; color: var(--charcoal);">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#kvkkModal" style="color: var(--pure-black); text-decoration: underline;">KVKK Aydınlatma Metni</a>'ni okudum ve kabul ediyorum.
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-luxe">Gönder</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection