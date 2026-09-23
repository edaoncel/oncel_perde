@extends('layouts.front')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary: #2c3e50;
        --accent: #e74c3c;
        --success: #27ae60;
        --light-bg: #eae4e6;
        --border-radius: 16px;
    }

    body { 
    background-color: #ffffff; 
    
    font-family: var(--font-editorial);
    
    line-height: 1.6;
    color: #2d2d2d; 
}

    .wizard-container {
    width: 100%;
    background: #ffffff;
    padding: 40px;
    position: relative;
    z-index: 10;
}

    .progress-bar-custom {
        display: flex;
        justify-content: space-between;
        margin-bottom: 40px;
        position: relative;
    }
    .progress-bar-custom::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 4px;
        background: #e9ecef;
        z-index: 1;
        border-radius: 2px;
    }
    .progress-line {
        position: absolute;
        top: 20px;
        left: 0;
        height: 4px;
        background: var(--success);
        z-index: 1;
        width: 0%;
        transition: width 0.4s ease;
        border-radius: 2px;
    }
    .progress-step {
        width: 45px;
        height: 45px;
        background: #ffffff;
        border: 3px solid #e9ecef;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #6c757d;
        z-index: 2;
        transition: all 0.3s ease;
        position: relative;
    }
    .progress-step.active {
        border-color: var(--success);
        color: var(--success);
        transform: scale(1.1);
    }
    .progress-step span {
        position: absolute;
        bottom: -30px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        color: #6c757d;
    }

    .step { display: none; opacity: 0; transition: opacity 0.3s ease-in-out; }
    .step.active { display: block; opacity: 1; }

    .tile-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 20px;
        margin-top: 25px;
    }
    .tile-item {
        background: #fff;
        border: 2px solid #eef2f5;
        border-radius: 14px;
        overflow: hidden;
        text-align: center;
        cursor: pointer;
        transition: all 0.25s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        position: relative;
    }
    .tile-item:hover {
        transform: translateY(-5px);
        border-color: var(--success);
        box-shadow: 0 8px 20px rgba(39, 174, 96, 0.15);
    }
    .tile-item.selected {
        border-color: var(--success);
        background: #f4faf7;
        transform: translateY(-2px);
    }
    .tile-image-wrapper {
        width: 100%;
        height: 150px;
        background: #f8f9fa;
        overflow: hidden;
        border-bottom: 1px solid #eef2f5;
    }
    .tile-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .tile-item:hover img {
        transform: scale(1.05);
    }
    .tile-item span {
        display: block;
        padding: 12px 10px;
        font-size: 13px;
        font-weight: 600;
        color: #2c3e50;
    }
    .tile-item.selected span {
        color: var(--success);
    }
    .tile-item .select-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        background: var(--success);
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        opacity: 0;
        transform: scale(0);
        transition: all 0.2s ease;
        z-index: 2;
    }
    .tile-item.selected .select-badge {
        opacity: 1;
        transform: scale(1);
    }

    .image-upload-box {
        border: 2px dashed #cbd5e1;
        background: #f8fafc;
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .image-upload-box:hover {
        border-color: var(--success);
        background: #f0fdf4;
    }

    .table-container-card {
        background: #fff;
        border: 1px solid #eef2f5;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.04);
        text-align: center;
    }
    #bedenTablosuGosterge {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        object-fit: contain;
    }

    .btn-nav { 
        padding: 14px 35px; 
        border-radius: 12px; 
        border: none; 
        font-weight: bold; 
        cursor: pointer; 
        transition: all 0.2s; 
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 576px) {
        .btn-nav { width: 100%; padding: 16px; margin-bottom: 10px; }
        .d-flex.justify-content-between { flex-direction: column-reverse; }
        .tile-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 12px; }
    }

.tools-panel {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 15px;
    background: #f8f9fa;
    padding: 10px;
    border-radius: 12px;
}
.tool-btn {
    width: 35px;
    height: 35px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

    .appoinment-hero {
    padding: 150px 0; 
    background-image: url("{{ asset('images/3.png') }}"); 
    background-size: cover; 
    background-position: center; 
    background-repeat: no-repeat;
    color: #f2eeef; 
    text-align: center; 
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 50vh; 
}

.appoinment-title {
    font-size: 5rem;
    font-weight: 400;
    color: #f5f2f2; 
    letter-spacing: -1px;
    line-height: 1.1;
    margin-bottom: 25px;
    font-family: 'Playfair Display', serif; 
}
    .gold-separator {
        height: 1px;
        width: 80px;
        background-color: #c2a66c;
        margin: 35px auto;
    }
.tool-btn.active { background: var(--success); color: #fff; }
.color-picker { width: 30px; height: 30px; border: none; cursor: pointer; border-radius: 5px; }

.splash h1 {
      font-family: 'Georgia', 'Times New Roman', Times, serif;
      font-size: clamp(4rem, 4vw, 10rem);
      font-weight: 300;
      font-style: italic;
      letter-spacing: -0.02em;
      line-height: 0.9;
      margin-bottom: 2rem;
    }
</style>

<section class="appoinment-hero">
    <div class="container col-lg-8">
        <span class="text-uppercase d-block mb-3" style="letter-spacing: 4px; font-size: 12px; font-weight: 600; color: #c2a66c;">Randevu Al</span>
        <section class="splash">
            <h1>Zamansız Parçalar için</h1>
        </section>
    </div>
</section>
<div class="wizard-container">
    <div class="progress-bar-custom">
        <div class="progress-line" id="progressLine"></div>
        <div class="progress-step active" id="p1">1<span>Manken Seçimi</span></div>
        <div class="progress-step" id="p2">2<span>Kıyafet Detayı</span></div>
        <div class="progress-step" id="p3">3<span>Çizim & Model</span></div>
        <div class="progress-step" id="p4">4<span>Vücut Ölçüleri</span></div>
    </div>

    <form id="appointmentWizardForm" enctype="multipart/form-data">
        @csrf
        
        <div class="step active" id="step1">
            <h3 class="mb-3" style="color: var(--primary); font-weight: 700;">Adım 1: Mankeninizi Oluşturun</h3>
            <p class="text-muted mb-4">Lütfen tasarıma başlamadan önce giyecek kişinin özelliklerini seçin.</p>
            
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="custom-form-label">Cinsiyet / Yaş Grubu</label>
                    <select id="manken_cinsiyet" name="manken_cinsiyet" class="form-select form-control-lg" onchange="updateInterfaces()" style="border-radius: 10px;">
                        <option value="kadin">Kadın</option>
                        <option value="erkek">Erkek</option>
                        <option value="kiz_genc">Genç Kız</option>
                        <option value="erkek_genc">Genç Erkek</option>
                        <option value="kiz_cocuk">Kız Çocuk</option>
                        <option value="erkek_cocuk">Erkek Çocuk</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="custom-form-label">Vücut Yapısı (Kilo)</label>
                    <select id="manken_kilo" name="manken_kilo" class="form-select form-control-lg" onchange="updateInterfaces()" style="border-radius: 10px;">
                        <option value="zayif">Zayıf</option>
                        <option value="orta">Orta Kilolu</option>
                        <option value="sisman">Battal</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="custom-form-label">Boy</label>
                    <select id="manken_boy" name="manken_boy" class="form-select form-control-lg" style="border-radius: 10px;">
                        <option value="uzun">Uzun</option>
                        <option value="orta_boylu">Orta Boylu</option>
                        <option value="kisa">Kısa</option>
                    </select>
                </div>
            </div>
            
            <div class="text-end mt-5">
                <button type="button" class="btn-nav btn-next" onclick="nextStep(2)">İlerle <i class="fa-solid fa-arrow-right ms-2"></i></button>
            </div>
        </div>

        <div class="step" id="step2">
            <h3 class="mb-3" style="color: var(--primary); font-weight: 700;">Adım 2: Tasarlamak İstediğiniz Kıyafet</h3>
            <p class="text-muted mb-3">Geniş terzilik kataloğumuzdan ilgili kategoriyi seçip, hayalinizdeki tasarımı özetleyin.</p>
            
            <div class="mb-4">
                <label class="custom-form-label">Tasarım Hakkında Açıklama</label>
                <textarea name="tasarim_ozeti" class="form-control" rows="3" placeholder="Örn: Yaka kısmı taş detaylı, kruvaze kesim, astarlı bir model istiyorum..." style="border-radius: 12px; padding: 15px;"></textarea>
            </div>
            
            <label class="custom-form-label">Kıyafet Kategorisi</label>
            <div class="tile-grid">
                <div class="tile-item" onclick="selectCategory('tshirt', this)">
                    <div class="select-badge"><i class="fa-solid fa-check"></i></div>
                    <div class="tile-image-wrapper"><img src="{{ asset('images/categories/tshirt.jpg') }}" alt="Tişört Görseli" onerror="this.src='https://placehold.co/300x250?text=Tisort'"></div>
                    <span>Tişört / Bluz</span>
                </div>
                <div class="tile-item" onclick="selectCategory('gomlek', this)">
                    <div class="select-badge"><i class="fa-solid fa-check"></i></div>
                    <div class="tile-image-wrapper"><img src="{{ asset('images/categories/gomlek.jpg') }}" alt="Gömlek Görseli" onerror="this.src='https://placehold.co/300x250?text=Gomlek'"></div>
                    <span>Gömlek / Tunik</span>
                </div>
                <div class="tile-item" onclick="selectCategory('yelek', this)">
                    <div class="select-badge"><i class="fa-solid fa-check"></i></div>
                    <div class="tile-image-wrapper"><img src="{{ asset('images/categories/yelek.jpg') }}" alt="Yelek Görseli" onerror="this.src='https://placehold.co/300x250?text=Yelek'"></div>
                    <span>Yelek</span>
                </div>
                <div class="tile-item" onclick="selectCategory('cepken', this)">
                    <div class="select-badge"><i class="fa-solid fa-check"></i></div>
                    <div class="tile-image-wrapper"><img src="{{ asset('images/categories/cepken.jpg') }}" alt="Cepken Görseli" onerror="this.src='https://placehold.co/300x250?text=Cepken'"></div>
                    <span>Cepken</span>
                </div>
                <div class="tile-item" onclick="selectCategory('pantolon', this)">
                    <div class="select-badge"><i class="fa-solid fa-check"></i></div>
                    <div class="tile-image-wrapper"><img src="{{ asset('images/categories/pantolon.jpg') }}" alt="Pantolon Görseli" onerror="this.src='https://placehold.co/300x250?text=Pantolon'"></div>
                    <span>Pantolon / Şort</span>
                </div>
                <div class="tile-item" onclick="selectCategory('etek', this)">
                    <div class="select-badge"><i class="fa-solid fa-check"></i></div>
                    <div class="tile-image-wrapper"><img src="{{ asset('images/categories/etek.jpg') }}" alt="Etek Görseli" onerror="this.src='https://placehold.co/300x250?text=Etek'"></div>
                    <span>Etek</span>
                </div>
                <div class="tile-item" onclick="selectCategory('ikili_takim', this)">
                    <div class="select-badge"><i class="fa-solid fa-check"></i></div>
                    <div class="tile-image-wrapper"><img src="{{ asset('images/categories/ikili_takim.jpg') }}" alt="İkili Takım Görseli" onerror="this.src='https://placehold.co/300x250?text=Ikili+Takim'"></div>
                    <span>İkili Takım (Pantolon & Üst)</span>
                </div>
                <div class="tile-item" onclick="selectCategory('takim_elbise', this)">
                    <div class="select-badge"><i class="fa-solid fa-check"></i></div>
                    <div class="tile-image-wrapper"><img src="{{ asset('images/categories/takim.jpg') }}" alt="Takım Görseli" onerror="this.src='https://placehold.co/300x250?text=Takim+Elbise'"></div>
                    <span>Takım Elbise / Döpiyes</span>
                </div>
                <div class="tile-item" onclick="selectCategory('gunluk_elbise', this)">
                    <div class="select-badge"><i class="fa-solid fa-check"></i></div>
                    <div class="tile-image-wrapper"><img src="{{ asset('images/categories/elbise.jpg') }}" alt="Günlük Elbise Görseli" onerror="this.src='https://placehold.co/300x250?text=Gunluk+Elbise'"></div>
                    <span>Günlük Elbise</span>
                </div>
                <div class="tile-item" onclick="selectCategory('abiye', this)">
                    <div class="select-badge"><i class="fa-solid fa-check"></i></div>
                    <div class="tile-image-wrapper"><img src="{{ asset('images/categories/abiye.jpg') }}" alt="Abiye Görseli" onerror="this.src='https://placehold.co/300x250?text=Abiye+Elbise'"></div>
                    <span>Abiye</span>
                </div>
                <div class="tile-item" onclick="selectCategory('kimono', this)">
                    <div class="select-badge"><i class="fa-solid fa-check"></i></div>
                    <div class="tile-image-wrapper"><img src="{{ asset('images/categories/kimono.jpg') }}" alt="Kimono Görseli" onerror="this.src='https://placehold.co/300x250?text=Kimono'"></div>
                    <span>Kimono</span>
                </div>
                <div class="tile-item" onclick="selectCategory('ceket', this)">
                    <div class="select-badge"><i class="fa-solid fa-check"></i></div>
                    <div class="tile-image-wrapper"><img src="{{ asset('images/categories/ceket.jpg') }}" alt="Ceket Görseli" onerror="this.src='https://placehold.co/300x250?text=Ceket+Blazer'"></div>
                    <span>Ceket / Blazer</span>
                </div>
                <div class="tile-item" onclick="selectCategory('tulum', this)">
                    <div class="select-badge"><i class="fa-solid fa-check"></i></div>
                    <div class="tile-image-wrapper"><img src="{{ asset('images/categories/tulum.jpg') }}" alt="Tulum Görseli" onerror="this.src='https://placehold.co/300x250?text=Tulum'"></div>
                    <span>Tulum</span>
                </div>
                <div class="tile-item" onclick="selectCategory('kaban', this)">
                    <div class="select-badge"><i class="fa-solid fa-check"></i></div>
                    <div class="tile-image-wrapper"><img src="{{ asset('images/categories/kaban.jpg') }}" alt="Kaban Görseli" onerror="this.src='https://placehold.co/300x250?text=Kaban+Palto'"></div>
                    <span>Kaban / Palto</span>
                </div>
                <div class="tile-item" onclick="selectCategory('trenckot', this)">
                    <div class="select-badge"><i class="fa-solid fa-check"></i></div>
                    <div class="tile-image-wrapper"><img src="{{ asset('images/categories/trenckot.jpg') }}" alt="Trençkot Görseli" onerror="this.src='https://placehold.co/300x250?text=Trenckot'"></div>
                    <span>Trençkot / Pardösü</span>
                </div>
                <div class="tile-item" onclick="selectCategory('deri_ceket', this)">
                    <div class="select-badge"><i class="fa-solid fa-check"></i></div>
                    <div class="tile-image-wrapper"><img src="{{ asset('images/categories/deri.jpg') }}" alt="Deri Ceket Görseli" onerror="this.src='https://placehold.co/300x250?text=Deri+Giyim'"></div>
                    <span>Deri Ceket / Mont</span>
                </div>
                <div class="tile-item" onclick="selectCategory('tesettur_giyim', this)">
                    <div class="select-badge"><i class="fa-solid fa-check"></i></div>
                    <div class="tile-image-wrapper"><img src="{{ asset('images/categories/tesettur.jpg') }}" alt="Tesettür Görseli" onerror="this.src='https://placehold.co/300x250?text=Tesettur+Giyim'"></div>
                    <span>Abaya / Ferace</span>
                </div>
                <div class="tile-item" onclick="selectCategory('nakis_tasarim', this)">
                    <div class="select-badge"><i class="fa-solid fa-check"></i></div>
                    <div class="tile-image-wrapper"><img src="{{ asset('images/categories/nakis.jpg') }}" alt="Nakış Tasarım Görseli" onerror="this.src='https://placehold.co/300x250?text=Nakis+Tasarim'"></div>
                    <span>Nakış Tasarım</span>
                </div>
                <div class="tile-item" onclick="selectCategory('diger', this)">
                    <div class="select-badge"><i class="fa-solid fa-check"></i></div>
                    <div class="tile-image-wrapper"><img src="{{ asset('images/categories/diger.png') }}" alt="Diğer Görseli" onerror="this.src='https://placehold.co/300x250?text=Diğer'"></div>
                    <span>Diğer</span>
                </div>
            </div>
            
            <input type="hidden" name="secilen_kategori" id="secilen_kategori_input" required>

            <div class="mt-5 d-flex justify-content-between">
                <button type="button" class="btn-nav btn-prev" onclick="prevStep(1)"><i class="fa-solid fa-arrow-left me-2"></i> Geri</button>
                <button type="button" class="btn-nav btn-next" onclick="nextStep(3)">Tasarıma Geç <i class="fa-solid fa-palette ms-2"></i></button>
            </div>
        </div>

<div class="step" id="step3">
    <h3 class="mb-3" style="color: var(--primary); font-weight: 700;">Adım 3: Modelinizi Çizin veya Referans Fotoğraf Yükleyin</h3>
    <p class="text-muted mb-4">Dilerseniz kroki üzerine fırçayla çizebilir, dilerseniz de beğendiğiniz kıyafet modellerinin görsellerini aşağıya ekleyebilirsiniz.</p>
    
    <div class="row">
            <h5 class="fw-bold"><i class="fa-solid fa-circle-play me-2"></i> Nasıl Tasarlanır?</h5>
        <div class="col-lg-5 col-md-12 mb-4">
            <div class="video-card mb-3">
                <video width="100%" autoplay muted loop playsinline class="rounded shadow">
                    <source src="{{ asset('video/3.mp4') }}" type="video/mp4">
                </video>
            </div>
            <div class="bg-white p-3 rounded-4 border">
                <h6 class="fw-bold mb-2 text-secondary"><i class="fa-solid fa-images me-2"></i> İlham Alınan / Beğenilen Modelleri Yükleyin</h6>
                <p class="text-muted small mb-3">Terzimizin modeli tam çıkartabilmesi için birden fazla farklı cephe fotoğrafı veya detay görseli ekleyebilirsiniz.</p>
                
                <div class="image-upload-box" onclick="document.getElementById('referans_resimler').click()" style="cursor:pointer; border: 2px dashed #ccc; padding: 20px; text-align: center;">
                    <i class="fa-solid fa-cloud-arrow-up fa-2x mb-2 text-muted"></i>
                    <p class="mb-0 fw-semibold text-secondary small">Tıklayın veya Fotoğrafları Buraya Sürükleyin</p>
                    <span class="text-muted style-small" style="font-size: 11px;">(PNG, JPG, JPEG seçebilirsiniz)</span>
                </div>
                <input type="file" id="referans_resimler" name="referans_resimler[]" multiple accept="image/*" class="d-none" onchange="handleFileSelect(this)">
                <div class="preview-gallery" id="galeriOnizleme"></div>
            </div>
        </div>

        <div class="col-lg-7 col-md-12 text-center">
    <div class="design-studio-box">
        
        <div class="canvas-wrapper" style="position: relative; width: 200px; height: 550px; margin: 0 auto; overflow: hidden;">
            <img id="mankenArkaplan" src="{{ asset('models/manken/kadin_orta.png') }}" 
                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain; z-index: 1;">
            
            <canvas id="cizimCanvas" width="350" height="550" 
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; cursor: crosshair; touch-action: none; z-index: 2;">
            </canvas>
        </div>

            <div class="tools-panel">
                <button type="button" class="tool-btn" onclick="setTool('pen')" title="Kalem"><i class="fa-solid fa-pencil"></i></button>
                <button type="button" class="tool-btn" onclick="setTool('eraser')" title="Silgi"><i class="fa-solid fa-eraser"></i></button>
                <input type="color" class="color-picker" id="colorPicker" value="#000000">
                <input type="range" id="lineWidth" min="1" max="15" value="5" title="Kalınlık">
                <button type="button" class="tool-btn" onclick="undo()" title="Geri Al"><i class="fa-solid fa-rotate-left"></i></button>
                <button type="button" class="tool-btn" onclick="clearCanvas()" title="Temizle"><i class="fa-solid fa-trash"></i></button>
            </div>
        </div>
    </div>
</div>
            
            <div class="mt-5 d-flex justify-content-between">
                <button type="button" class="btn-nav btn-prev" onclick="prevStep(2)"><i class="fa-solid fa-arrow-left me-2"></i> Geri</button>
                <button type="button" class="btn-nav btn-next" onclick="nextStep(4)">Ölçülere Geç <i class="fa-solid fa-tape ms-2"></i></button>
            </div>
        </div>

        <div class="step" id="step4">
            <h3 class="mb-3" style="color: var(--primary); font-weight: 700;">Adım 4: Vücut Ölçüleri & Referans Tablo</h3>
            <p class="text-muted mb-4">Temel ölçülerinizi girin. Detaylar ve özel istekleriniz için sizinle iletişime geçeceğiz.</p>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="custom-form-label"><i class="fa-solid fa-circle-dot me-2"></i> Göğüs (cm)</label>
                            <input type="number" name="gogus" class="form-control form-control-lg rounded-3" placeholder="0">
                        </div>
                        <div class="col-md-4">
                            <label class="custom-form-label"><i class="fa-solid fa-circle-dot me-2"></i> Bel (cm)</label>
                            <input type="number" name="bel" class="form-control form-control-lg rounded-3" placeholder="0">
                        </div>
                        <div class="col-md-4">
                            <label class="custom-form-label"><i class="fa-solid fa-circle-dot me-2"></i> Kalça (cm)</label>
                            <input type="number" name="kalca" class="form-control form-control-lg rounded-3" placeholder="0">
                        </div>
                        
                        <div class="col-md-12 mt-4">
                            <label class="custom-form-label"><i class="fa-solid fa-ruler-combined me-2"></i> Eklemek İstediğiniz Diğer Ölçüler / Notlar</label>
                            <textarea name="ekstra_olculer" class="form-control rounded-3" rows="5" placeholder="Seçtiğiniz kıyafete göre eklemek istediğiniz ekstra detay veya ölçüleri buraya not düşebilirsiniz (Örn: Kol boyu, etek boyu, omuz genişliği vb.)."></textarea>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6 mb-3">
                                <label class="custom-form-label"><i class="fa-solid fa-user"></i> Adınız Soyadınız</label>
                                <input type="text" name="name" class="form-control rounded-3" placeholder="Ad Soyad" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="custom-form-label"><i class="fa-solid fa-phone"></i> Telefon Numaranız</label>
                                <input type="tel" name="phone" class="form-control rounded-3" placeholder="Örn: 05........." required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="table-container-card">
                        <h5 class="mb-3" style="color: var(--primary); font-weight: 600;"><i class="fa-solid fa-table me-2"></i> Standart Ölçü Tablosu</h5>
                        <img id="bedenTablosuGosterge" src="{{ asset('models/kadin_beden_tablosu.png') }}" alt="Beden Tablosu  Görseli">
                    </div>
                </div>
            </div>

            <div class="mt-5 d-flex justify-content-between">
                <button type="button" class="btn-nav btn-prev" onclick="prevStep(3)"><i class="fa-solid fa-arrow-left me-2"></i> Geri</button>
                <button type="submit" class="btn-nav btn-next" style="background: #e67e22;">Randevu Oluştur ve Kaydet <i class="fa-solid fa-circle-check ms-2"></i></button>
            </div>
        </div>
    </form>
</div>

<script>
    let currentStep = 1;
    let selectedFilesArray = [];

    window.onload = function() {
        updateInterfaces();
    };

    function updateDrawingMannequin() {
        const cins = document.getElementById('manken_cinsiyet').value; 
        const kilo = document.getElementById('manken_kilo').value;      
        const img = document.getElementById('mankenArkaplan');
        
        let mankenGorseli = `${cins}_${kilo}`;
        img.src = "{{ asset('models/') }}" + "/" + mankenGorseli + ".png?t=" + new Date().getTime();
        
        img.onerror = function() { this.src = "{{ asset('models/kadin_orta.png') }}"; };
    }

    function updateSizeTable() {
        const cins = document.getElementById('manken_cinsiyet').value;
        const tabloImg = document.getElementById('bedenTablosuGosterge');
        let tabloAdi = (cins.includes('kadin') || cins.includes('kiz')) ? "kadin_beden_tablosu" : "erkek_beden_tablosu";
        tabloImg.src = "{{ asset('models/') }}" + "/" + tabloAdi + ".png";
    }

    function updateInterfaces() {
        updateDrawingMannequin();
        updateSizeTable();
    }

    function nextStep(stepNum) {
        document.getElementById('step' + currentStep).classList.remove('active');
        currentStep = stepNum;
        document.getElementById('step' + currentStep).classList.add('active');
        window.scrollTo({ top: 0, behavior: 'smooth' });
        updateProgress(currentStep); 
    }

    function prevStep(stepNum) {
        document.getElementById('step' + currentStep).classList.remove('active');
        currentStep = stepNum;
        document.getElementById('step' + currentStep).classList.add('active');
        window.scrollTo({ top: 0, behavior: 'smooth' });
        updateProgress(currentStep);
    }

    function updateProgress(step) {
        const steps = document.querySelectorAll('.progress-step');
        const line = document.querySelector('.progress-line');
        steps.forEach((s, index) => {
            if(index + 1 <= step) s.classList.add('active');
            else s.classList.remove('active');
        });
        if(line) line.style.width = ((step - 1) / (steps.length - 1) * 100) + '%';
    }

    function selectCategory(cat, element) {
        document.querySelectorAll('.tile-item').forEach(el => el.classList.remove('selected'));
        element.classList.add('selected');
        document.getElementById('secilen_kategori_input').value = cat;
        console.log("Seçilen kategori inputa aktarıldı: " + cat);
    }

    function handleFileSelect(input) {
        if (input.files && input.files[0]) { console.log("Dosya seçildi"); }
    }

    document.getElementById('appointmentWizardForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Sayfanın yenilenmesini engelle
    
    let formData = new FormData(this);
    const canvas = document.getElementById('cizimCanvas');
    
    if (!isCanvasBlank(canvas)) {
        const dataURL = canvas.toDataURL('image/png', 0.8);
        formData.append('cizim_katmani', dataURL);
    }
    
    selectedFilesArray.forEach((file) => { 
        if(file !== null) formData.append('referans_resimler[]', file); 
    });

    fetch("{{ route('appointment.store') }}", {
        method: "POST",
        headers: { 
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
            "Accept": "application/json" 
        },
        body: formData
    })
    .then(async response => {
        const data = await response.json();
        if(response.ok) {
            alert(data.message || "İşlem başarılı!");
            this.reset();
            clearCanvas();
            nextStep(1);   
        } else {
            console.error("Hata Detayı:", data);
            alert("Hata: " + (data.message || "Bir sorun oluştu."));
        }
    })
    .catch(err => console.error("Sunucu Hatası:", err));
});

    const canvas = document.getElementById('cizimCanvas');
const ctx = canvas.getContext('2d');
let drawing = false;
let tool = 'pen';
let history = [];

function getMousePos(e) {
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;

    let clientX = e.clientX;
    let clientY = e.clientY;

    if (e.touches && e.touches.length > 0) {
        clientX = e.touches[0].clientX;
        clientY = e.touches[0].clientY;
    }

    return {
        x: (clientX - rect.left) * scaleX,
        y: (clientY - rect.top) * scaleY
    };
}

function startPosition(e) {
    drawing = true;
    saveState(); 
    const pos = getMousePos(e);
    ctx.beginPath();
    ctx.moveTo(pos.x, pos.y);
}

function endPosition() {
    drawing = false;
    ctx.beginPath();
}

function draw(e) {
    if (!drawing) return;
    e.preventDefault(); 

    const pos = getMousePos(e);

    ctx.lineWidth = document.getElementById('lineWidth').value;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';

    if (tool === 'eraser') {
        ctx.globalCompositeOperation = "destination-out"; 
        ctx.strokeStyle = "rgba(0,0,0,1)"; 
    } else {
        ctx.globalCompositeOperation = "source-over";
        ctx.strokeStyle = document.getElementById('colorPicker').value;
    }

    ctx.lineTo(pos.x, pos.y);
    ctx.stroke();
}

canvas.addEventListener('mousedown', startPosition);
canvas.addEventListener('mouseup', endPosition);
canvas.addEventListener('mousemove', draw);
canvas.addEventListener('mouseleave', endPosition); 
canvas.addEventListener('touchstart', startPosition, {passive: false});
canvas.addEventListener('touchend', endPosition);
canvas.addEventListener('touchmove', draw, {passive: false});

function setTool(t) { 
    tool = t; 
    document.querySelectorAll('.tool-btn').forEach(btn => btn.classList.remove('active'));
    event.currentTarget.classList.add('active');
}

function saveState() {
    if (!isCanvasBlank(canvas)) {
        history.push(canvas.toDataURL('image/png', 0.5));
    }
}

function isCanvasBlank(canvas) {
    const blank = document.createElement('canvas');
    blank.width = canvas.width;
    blank.height = canvas.height;
    return canvas.toDataURL() === blank.toDataURL();
}

function undo() {
    if (history.length > 0) {
        let img = new Image();
        img.src = history.pop();
        img.onload = () => { 
            ctx.clearRect(0, 0, canvas.width, canvas.height); 
            ctx.globalCompositeOperation = "source-over";
            ctx.drawImage(img, 0, 0); 
        };
    }
}

function clearCanvas() { 
    ctx.clearRect(0, 0, canvas.width, canvas.height); 
    history = []; 
}
</script>
@endsection