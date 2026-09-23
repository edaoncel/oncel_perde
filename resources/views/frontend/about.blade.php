@extends('layouts.front')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
<style>
    :root {
        --primary: #c4babd;
        --gold: #623345;
         --light-bg: #eae4e6; 
        --text-dark: #1a1a1a;
    }

    body { font-family: 'Inter', sans-serif; background-color: var(--light-bg); color: var(--text-dark); }
    .serif-font { font-family: 'Cormorant Garamond', serif; }

    .about-hero { 
    padding: 150px 0;
    background-image: url("{{ asset('images/1.png') }}"); 
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

.hero-layout { 
    width: 100%;
    max-width: 800px; 
    padding: 0 20px;
    margin: 0 auto;
}
    
    .model-box { width: 50%; height: 400px; }
    .hero-content { width: 100%; }
    .hero-title { font-size: 3.8rem; color: #f0edee; line-height: 1.1; margin-bottom: 20px; }
    .story-block { padding: 120px 0; background: var(--light-bg); }
    .story-text h3 { color: #623345; font-size: 2.8rem; margin-bottom: 20px; }
    .story-text p { font-size: 1.1rem; line-height: 1.8; color: #25171c; }

    .vision-section { padding: 100px 0; background: var(--light-bg); }
    .vision-card { background: var(--primary); color: #653d48; padding: 50px; height: 100%; transition: 0.3s; }
    .vision-card h3 { color: var(--gold); font-size: 2rem; margin-bottom: 20px; }
    .vision-card p { opacity: 0.9; line-height: 1.7; }

    .campaign { 
    height: 60vh; 
    background:url("{{ asset('images/about.png') }}");
    background-size: cover; 
    background-position: center;
    background-attachment: fixed;
    display: flex; 
    align-items: center; 
    justify-content: center; 
    text-align: center; 
    color: #fff;
}
    .btn-luxe { 
        padding: 18px 50px; border: 1px solid var(--gold); color: #fff; 
        text-transform: uppercase; text-decoration: none; transition: 0.4s; letter-spacing: 2px;
    }
    .btn-luxe:hover { background: var(--gold); border-color: var(--gold); color: #fff; }

    @media(max-width: 768px) {
        .hero-layout { flex-direction: column; text-align: center; }
        .model-box, .hero-content { width: 100%; }
        .hero-title { font-size: 2.5rem; }
    }

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


<section class="about-hero">
    <div class="hero-layout">
        <div class="hero-content">
            <span class="text-uppercase d-block mb-3" style="letter-spacing: 4px; font-size: 12px; font-weight: 600; color: #c2a66c;">Hakkımızda</span>
            <section class="splash">
                <h1>Zarafetin ve Kusursuz<br> Dikişin Hikayesi</h1>
            </section>
        </div>
    </div>
</section>


<section class="story-block">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 story-text">
                <h3 class="serif-font">Çavdır'ın İlk Kadın Terzisi</h3>
                <p>1994 yılında Çavdır’da küçük bir atölye ile başlayan hayalimiz, bugün evlerinize ve gardırobunuza dokunan incelikli bir sanata dönüştü. Geçmişin sabrını, günümüzün modern estetiğiyle birleştirerek size özel tasarımlar sunuyoruz.</p>
                <p>Biz sadece ürün satmıyor; nesiller boyu süren zanaat mirasımızı, aile sıcaklığı ve kusursuz işçilik prensibiyle paylaşıyoruz.</p>
            </div>
            <div class="col-lg-6">
                <div id="container-1" class="model-box"></div>
            </div>
        </div>
    </div>
</section>


<section class="vision-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="vision-card">
                    <h3 class="serif-font">Vizyonumuz</h3>
                    <p>Zanaatın zamansız izlerini, modern estetikle harmanlayarak Türkiye'nin en nitelikli atölyelerinden biri olmak. Estetiğin ve kalitenin "sessiz ama en güçlü" adresi haline gelmek.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="vision-card">
                    <h3 class="serif-font">Misyonumuz</h3>
                    <p>En kaliteli kumaşları usta ellerle buluşturup, yaşam alanlarına ruh, gardıroplara ise özgün bir kimlik kazandırmak. Ailemizden size uzanan bir nezaketle hizmet etmek.</p>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="campaign">
    <div class="container">
        <h2 class="serif-font" style="font-size: 3.5rem; margin-bottom: 30px;">Öncel Koleksiyonu</h2>
        <a href="{{ route('appointment') }}" class="btn-luxe">Randevu Oluşturun</a>
    </div>
</section>


 <script type="importmap">
  {
    "imports": {
      "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
      "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/"
    }
  }
</script>
<script type="module">
  import * as THREE from 'three';
  import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
  import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

  
  function setupModel(containerId, modelPath, posY, cameraZ, scale, isGold = false) {
    const container = document.getElementById(containerId);
    if(!container) return;
    
    const scene = new THREE.Scene();

    const camera = new THREE.PerspectiveCamera(45, container.clientWidth/container.clientHeight, 0.1, 2000);
    camera.position.set(0, 0, cameraZ);

    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setClearColor(0x000000, 0); 
    renderer.setSize(container.clientWidth, container.clientHeight);
    container.appendChild(renderer.domElement);

    scene.add(new THREE.AmbientLight(0xffffff, 2.0));
    const frontLight = new THREE.DirectionalLight(isGold ? 0xffd700 : 0xffffff, 2.5);
    frontLight.position.set(5, 5, 5);
    scene.add(frontLight);

    let modelMesh = null;
    const loader = new GLTFLoader();
    loader.load(modelPath, (gltf) => {
        modelMesh = gltf.scene;
        modelMesh.traverse((node) => {
            if (node.isMesh) {
                node.material.metalness = 0.8;
                node.material.roughness = 0.2;
                
                if(isGold) {
                    node.material.color.set(0xffd700);
                    node.material.emissive.set(0x332200);
                }
            }
        });
        modelMesh.scale.set(scale, scale, scale);
        modelMesh.position.y = posY;
        scene.add(modelMesh);
    });

    function animate() {
        requestAnimationFrame(animate);
        if (modelMesh) {
            modelMesh.rotation.y += 0.01;
        }
        renderer.render(scene, camera);
    }
    animate();
  }

  setupModel('container-1', "{{ asset('models/machine/singer.glb') }}", -1.1, 3, 1.3, false);

  
</script>
@endsection