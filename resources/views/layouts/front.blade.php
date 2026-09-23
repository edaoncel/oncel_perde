<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perde & Özel Dikim Sarayı</title>
     <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <style>
        :root {
            --navbar: #faf9f6;
            --off-white: #faf9f6;
            --pure-black: #623345;
            --charcoal: #f3ebeb;
        }

        body {
            background-color: var(--off-white);
            color: var(--charcoal);
        }

        .navbar {
            background-color: #623345 !important;
            border-bottom: 1px solid #e0ded9;
        }

        .navbar-brand img {
            height: 60px; 
            width: auto; 
            object-fit: contain;
        }

        .nav-link {
            color: #ffffff !important;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
            transition: opacity 0.3s ease;
            opacity: 0.85;
        }

        .nav-link:hover, .nav-link.active {
            opacity: 1 !important;
            color: #ffffff !important;
        }

        @media (max-width: 991.98px) {
            .navbar-collapse {
                background-color: #623345;
                padding: 1rem;
                margin-top: 10px;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
            }
        }

        .dropdown-menu {
            border-radius: 0 !important;
            border: 1px solid #e0ded9 !important;
            background-color: #623345 !important;
        }

        .dropdown-item {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #ffffff !important;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
        }

        footer {
            background-color: var(--pure-black) !important;
            color: var(--off-white) !important;
            border-top: 1px solid #faf9f6;
            font-size: 0.85rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .aesthetic-signature {
            color: #d1c7bc;
            transition: all 0.4s ease;
            display: inline-block;
            border-bottom: 1px dotted rgba(255, 255, 255, 0.3);
            padding-bottom: 2px;
            text-decoration: none;
        }
        .aesthetic-signature:hover {
            color: #ffffff;
            border-bottom: 1px solid #ffffff;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg py-3 sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                @php
                    $logoUrl = \App\Models\Setting::where('key', 'logo')->value('value');
                @endphp
                <img src="{{ asset($logoUrl ?? 'images/logo1.png') }}" alt="Logo Görseli">
            </a>
            
            <button class="navbar-toggler border-0 shadow-none text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-2 text-white"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto gap-lg-3 align-items-center mt-3 mt-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Ana Sayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products') ? 'active' : '' }}" href="{{ route('products') }}">Ürünler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">Hakkımızda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('appointment') ? 'active' : '' }}" href="{{ route('appointment') }}">Randevu Al</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">İletişim</a>
                    </li>
                    <li class="nav-item dropdown mt-2 mt-lg-0">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="letter-spacing: 1px; font-size: 13px;">
                            <i class="bi bi-person-circle fs-5"></i>
                        </a>
                        <ul class="dropdown-menu border-0 shadow-sm mt-2 dropdown-menu-end">
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('dashboard.wishlist') }}">
                                    <i class="bi bi-person"></i> Kullanıcı Girişi
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('admin.login') }}">
                                    <i class="bi bi-shield-lock"></i> Yönetici Girişi
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

  
<div class="modal fade" id="kvkkModal" tabindex="-1" aria-labelledby="kvkkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="kvkkModalLabel">KVKK Aydınlatma Metni</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body text-secondary fs-6" style="line-height: 1.6;">
                <p class="fw-bold text-dark">ÖNCEL PERDE KİŞİSEL VERİLERİN KORUNMASI VE İŞLENMESİ AYDINLATMA METNİ</p>

                <p><strong>Öncel Perde</strong> olarak, 6698 sayılı Kişisel Verilerin Korunması Kanunu (“KVKK”) uyarınca, veri sorumlusu sıfatıyla tarafımıza ilettiğiniz kişisel verilerinizin güvenliğine ve gizliliğine önem veriyoruz.</p>

                <h6 class="fw-bold text-dark mt-3">1. İşlenen Kişisel Verileriniz</h6>
                <p>Web sitemizde yer alan iletişim ve randevu formları aracılığıyla tarafımızla paylaştığınız;</p>
                <ul>
                    <li>Kimlik Bilgileri (Ad, Soyad)</li>
                    <li>İletişim Bilgileri (E-posta adresi, Telefon numarası, Adres bilgileri)</li>
                    <li>Talep ve Mesaj İçeriği (Konu, Mesaj metni, Randevu tercihleri)</li>
                </ul>

                <h6 class="fw-bold text-dark mt-3">2. Kişisel Verilerin İşlenme Amaçları</h6>
                <p>Toplanan kişisel verileriniz aşağıdaki amaçlarla işlenmektedir:</p>
                <ul>
                    <li>Tarafınızla iletişim kurabilmek, taleplerinizi ve sorularınızı yanıtlamak,</li>
                    <li>Ölçü, keşif ve montaj randevularını organize etmek ve yönetmek,</li>
                    <li>Müşteri memnuniyeti süreçlerini yürütmek ve hizmet kalitemizi artırmak,</li>
                    <li>Yasal yükümlülüklerimizi yerine getirmek.</li>
                </ul>

                <h6 class="fw-bold text-dark mt-3">3. Kişisel Verilerin Aktarılması</h6>
                <p>Kişisel verileriniz, yasal zorunluluklar saklı kalmak kaydıyla ve yukarıda belirtilen amaçların gerçekleştirilmesi doğrultusunda yetkili kamu kurum ve kuruluşları ile hizmet sağlayıcılarımıza (bilişim ve sunucu altyapı hizmeti veren firmalar) aktarılabilecektir.</p>

                <h6 class="fw-bold text-dark mt-3">4. Kişisel Veri Toplamanın Yöntemi ve Hukuki Sebebi</h6>
                <p>Kişisel verileriniz, web sitemizdeki formların doldurulması suretiyle elektronik ortamda toplanmaktadır. Bu veriler, KVKK'nın 5. maddesinde yer alan <em>"bir sözleşmenin kurulması veya ifasıyla doğrudan doğruya ilgili olması"</em> ve <em>"veri sorumlusunun meşru menfaati"</em> hukuki sebeplerine dayanılarak işlenmektedir.</p>

                <h6 class="fw-bold text-dark mt-3">5. KVKK Kapsamındaki Haklarınız</h6>
                <p>KVKK'nın 11. maddesi uyarınca Öncel Perde'ye başvurarak;</p>
                <ul>
                    <li>Kişisel verilerinizin işlenip işlenmediğini öğrenme,</li>
                    <li>Kişisel verileriniz işlenmişse buna ilişkin bilgi talep etme,</li>
                    <li>Kişisel verilerin düzeltilmesini veya silinmesini isteme haklarına sahipsiniz.</li>
                </ul>
                <p>Haklarınızı kullanmak için taleplerinizi <strong>admin@oncelperde.com</strong> e-posta adresimize iletebilirsiniz.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>


<div id="cookie-consent-banner" class="position-fixed bottom-0 start-0 end-0 bg-dark text-white p-3 shadow-lg" style="z-index: 9999; display: none;">
    <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
        <div class="small">
            Sitemizde size en iyi deneyimi sunabilmek ve hizmetlerimizi geliştirebilmek için çerezler (cookies) kullanıyoruz. 
            Sitemizi kullanmaya devam ederek çerez kullanımını kabul etmiş olursunuz.
        </div>
        <div class="d-flex gap-2 text-nowrap">
            <button id="accept-cookies" class="btn btn-primary btn-sm px-4">Kabul Et</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (!localStorage.getItem("cookieConsent")) {
            document.getElementById("cookie-consent-banner").style.display = "block";
        }

        document.getElementById("accept-cookies").addEventListener("click", function () {
            localStorage.setItem("cookieConsent", "accepted");
            document.getElementById("cookie-consent-banner").style.display = "none";
        });
    });
</script>

    <div class="min-vh-100">
        @yield('content')
    </div>

    <footer class="py-4 mt-5">
        <div class="container text-center">
            <p class="mb-2" style="font-size: 0.8rem; letter-spacing: 2px;">&copy; {{ date('Y') }} ÖNCEL PERDE &mdash; TÜM HAKLARI SAKLIDIR</p>
            <div>
                <a href="https://edaoncel.github.io/edaoncell/project.html" 
                   target="_blank" 
                   class="aesthetic-signature"
                   title="Markanıza değer katan projeleri inceleyin">
                    <span style="font-size: 0.75rem; letter-spacing: 1.5px; opacity: 0.7;">
                        <i class="bi bi-stars me-1 text-warning"></i> Markanıza değer katan bir web sitesi mi arıyorsunuz? <span class="fw-semibold text-white">Eda Öncel</span>
                    </span>
                </a>
            </div>
        </div>
    </footer>
</body>
</html>