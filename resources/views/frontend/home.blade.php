@extends('layouts.front')
@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500&display=swap" rel="stylesheet">

<style>
  * { margin: 0; padding: 0; outline: none !important; box-sizing: border-box; }
    canvas { display: block; outline: none !important; }

    *, *::before, *::after {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --cream: #f5f0ebee;
      --cream-dim: #f5f0eb88;
      --dark: #1a1412;
      --accent: #8b6f5c;
      --accent-light: #c4a882;
      --brand-color: #623345;
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      background: transparent !important;
      color: var(--cream);
      font-family: 'Arial', 'Helvetica', 'Segoe UI', Tahoma, sans-serif;
      font-weight: 300;
      overflow-x: hidden;
    }

    .splash {
      height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      position: relative;
      scroll-snap-align: start;
    }

    .splash .overline {
      font-family: 'Georgia', 'Times New Roman', Times, serif;
      font-size: 0.75rem;
      font-weight: 300;
      letter-spacing: 0.5em;
      text-transform: uppercase;
      color: var(--cream-dim);
      margin-bottom: 2rem;
    }

    .splash h1 {
      font-family: 'Georgia', 'Times New Roman', Times, serif;
      font-size: clamp(4rem, 12vw, 10rem);
      font-weight: 400;
      font-style: italic;
      letter-spacing: -0.02em;
      line-height: 0.9;
      margin-bottom: 2rem;
    }

    .splash .subtitle {
      font-family: 'Georgia', 'Times New Roman', Times, serif;
      font-size: clamp(1rem, 2vw, 1.4rem);
      font-weight: 300;
      letter-spacing: 0.3em;
      text-transform: uppercase;
      color: var(--cream-dim);
    }

    .splash .rule {
      width: 1px;
      height: 60px;
      background: var(--cream-dim);
      margin: 2.5rem auto;
    }

    .splash .scroll-hint {
      margin-top: 2rem;
      font-family: 'Georgia', 'Times New Roman', Times, serif;
      font-size: 0.7rem;
      letter-spacing: 0.4em;
      text-transform: uppercase;
      color: var(--cream-dim);
    }

    .splash .scroll-hint::after {
      content: '';
      display: block;
      width: 1px;
      height: 50px;
      background: var(--cream-dim);
      margin: 1rem auto 0;
      animation: pulse 2.5s ease-in-out infinite;
    }

    @keyframes pulse {
      0%, 100% { opacity: 0.2; transform: scaleY(0.5); }
      50% { opacity: 0.8; transform: scaleY(1); }
    }

    .sections {
      max-width: 700px;
      margin: 0 auto;
      padding: 0 2rem;
    }

    .section {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 6rem 0;
      scroll-snap-align: start;
    }

    .section .number {
      font-family: 'Georgia', 'Times New Roman', Times, serif;
      font-size: 0.7rem;
      letter-spacing: 0.4em;
      text-transform: uppercase;
      color: var(--cream-dim);
      margin-bottom: 3rem;
    }

    .section h2 {
      font-family: 'Georgia', 'Times New Roman', Times, serif;
      font-size: clamp(2rem, 5vw, 3.5rem);
      font-weight: 400;
      font-style: italic;
      margin-bottom: 2rem;
      line-height: 1.15;
    }

    .section:not(:first-child) h2 {
      opacity: 0.8;
    }

    .section p {
      font-size: clamp(1rem, 1.8vw, 1.2rem);
      line-height: 2;
      color: var(--cream-dim);
      max-width: 500px;
    }

    .section .divider {
      width: 40px;
      height: 1px;
      background: var(--cream-dim);
      margin: 2.5rem auto;
    }

    .section .quote {
      font-family: 'Georgia', 'Times New Roman', Times, serif;
      font-style: italic;
      font-size: clamp(1.1rem, 2vw, 1.4rem);
      line-height: 1.8;
      color: var(--cream);
      max-width: 460px;
      opacity: 0.8;
    }

    .finale {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 4rem 2rem;
      scroll-snap-align: start;
    }

    .finale .overline {
      font-family: 'Georgia', 'Times New Roman', Times, serif;
      font-size: 0.7rem;
      letter-spacing: 0.5em;
      text-transform: uppercase;
      color: var(--cream-dim);
      margin-bottom: 2rem;
    }

    .finale h2 {
      font-family: 'Georgia', 'Times New Roman', Times, serif;
      font-size: clamp(3rem, 8vw, 7rem);
      font-weight: 400;
      font-style: italic;
      line-height: 0.95;
      letter-spacing: -0.02em;
      margin-bottom: 3rem;
    }

    .finale p {
      font-family: 'Georgia', 'Times New Roman', Times, serif;
      font-size: 1.15rem;
      line-height: 2;
      color: var(--cream-dim);
      max-width: 440px;
    }

    .finale .cta {
      display: inline-block;
      margin-top: 3.5rem;
      padding: 1em 3.5em;
      font-family: 'Georgia', 'Times New Roman', Times, serif;
      font-size: 0.75rem;
      font-weight: 400;
      letter-spacing: 0.35em;
      text-transform: uppercase;
      color: var(--dark);
      background: var(--cream);
      border: none;
      cursor: pointer;
      text-decoration: none;
      transition: background 0.3s ease, transform 0.2s ease;
    }

    .finale .cta:hover {
      background: var(--accent-light);
      color: var(--dark);
      transform: translateY(-2px);
    }

    .gui-panel {
      position: fixed;
      top: 12px;
      right: 12px;
      z-index: 9999;
      width: 280px;
      font-family: 'Inter', sans-serif;
      font-size: 11px;
      color: #e0dcd8;
      user-select: none;
      max-height: calc(100vh - 24px);
      display: flex;
      flex-direction: column;
    }

    .gui-panel-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 14px;
      background: rgba(18, 14, 12, 0.92);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255,255,255,0.06);
      border-radius: 8px 8px 0 0;
      cursor: pointer;
      letter-spacing: 0.08em;
      font-weight: 500;
      font-size: 10px;
      text-transform: uppercase;
    }

    .gui-panel.collapsed .gui-panel-header {
      border-radius: 50%;
      width: 36px;
      height: 36px;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-left: auto;
    }

    .gui-panel-header .gui-header-label {
      transition: opacity 0.15s ease;
    }

    .gui-panel.collapsed .gui-header-label {
      display: none;
    }

    .gui-panel-header .gui-chevron {
      font-size: 14px;
      opacity: 0.5;
    }

    .gui-panel.collapsed .gui-chevron {
      display: none;
    }

    .gui-panel-header .gui-icon {
      display: none;
      width: 16px;
      height: 16px;
      opacity: 0.7;
    }

    .gui-panel.collapsed .gui-icon {
      display: block;
    }

    .gui-panel-body {
      background: rgba(18, 14, 12, 0.88);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255,255,255,0.06);
      border-top: none;
      border-radius: 0 0 8px 8px;
      overflow-y: auto;
      overflow-x: hidden;
      max-height: calc(100vh - 70px);
      scrollbar-width: thin;
      scrollbar-color: rgba(255,255,255,0.1) transparent;
    }

    .gui-panel.collapsed .gui-panel-body {
      display: none;
    }

    canvas {
        position: fixed;
        top: 0;
        left: 0;
        z-index: -1;
        pointer-events: none;
    }

    #guiPanel {
        pointer-events: auto;
    }

    .floating-container {
      position: fixed;
      bottom: 30px;
      left: 30px;
      z-index: 99999;
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .floating-btn {
      width: 52px;
      height: 52px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #faf9f6;
      font-size: 22px;
      cursor: pointer;
      box-shadow: 0 8px 24px rgba(0,0,0,0.3);
      backdrop-filter: blur(10px);
      transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      border: 1px solid rgba(255, 255, 255, 0.15);
      text-decoration: none;
    }

    .floating-btn:hover {
      transform: scale(1.1);
      color: #ffffff;
    }

    .btn-whatsapp {
      background: rgba(37, 211, 102, 0.85);
    }
    .btn-whatsapp:hover {
      background: #25d366;
    }

    .btn-ai {
      background: rgba(98, 51, 69, 0.85);
      border-color: rgba(196, 168, 130, 0.3);
    }
    .btn-ai:hover {
      background: #623345;
      box-shadow: 0 8px 24px rgba(98, 51, 69, 0.5);
    }

    .wa-options {
      position: absolute;
      bottom: 65px;
      left: 0;
      background: rgba(26, 20, 18, 0.95);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 12px;
      padding: 10px;
      width: 220px;
      display: none;
      flex-direction: column;
      gap: 8px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }

    .wa-options.active {
      display: flex;
      animation: fadeIn 0.2s ease;
    }

    .wa-link {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 8px 12px;
      color: #f5f0eb;
      text-decoration: none;
      font-size: 12px;
      border-radius: 6px;
      transition: background 0.2s;
    }

    .wa-link:hover {
      background: rgba(255,255,255,0.08);
      color: #c4a882;
    }

    .ai-modal {
      position: fixed;
      bottom: 95px;
      left: 30px;
      width: 350px;
      height: 480px;
      background: rgba(22, 18, 16, 0.95);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(196, 168, 130, 0.2);
      border-radius: 16px;
      z-index: 99999;
      display: none;
      flex-direction: column;
      overflow: hidden;
      box-shadow: 0 20px 40px rgba(0,0,0,0.6);
      font-family: 'Inter', sans-serif;
    }

    .ai-modal.active {
      display: flex;
      animation: fadeIn 0.3s ease;
    }

    .ai-header {
      background: #623345;
      padding: 14px 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .ai-header-title {
      font-family: 'Georgia', serif;
      color: #f5f0eb;
      font-size: 14px;
      font-style: italic;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .ai-close {
      background: none;
      border: none;
      color: #f5f0eb;
      font-size: 18px;
      cursor: pointer;
      opacity: 0.7;
    }
    .ai-close:hover { opacity: 1; }

    .ai-body {
      flex: 1;
      padding: 15px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .chat-msg {
      max-width: 85%;
      padding: 10px 14px;
      border-radius: 12px;
      font-size: 12px;
      line-height: 1.5;
    }

    .chat-msg.bot {
      background: rgba(255,255,255,0.06);
      color: #e0dcd8;
      align-self: flex-start;
      border-bottom-left-radius: 2px;
    }

    .chat-msg.user {
      background: #623345;
      color: #f5f0eb;
      align-self: flex-end;
      border-bottom-right-radius: 2px;
    }

    .quick-questions {
      padding: 10px 15px;
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      border-top: 1px solid rgba(255,255,255,0.05);
      background: rgba(0,0,0,0.2);
    }

    .q-btn {
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(196, 168, 130, 0.2);
      color: #c4a882;
      padding: 6px 10px;
      border-radius: 20px;
      font-size: 11px;
      cursor: pointer;
      transition: all 0.2s;
    }

    .q-btn:hover {
      background: #c4a882;
      color: #1a1412;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

@keyframes cookieWiggle {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(-10deg) scale(1.05); }
    75% { transform: rotate(10deg) scale(1.05); }
}

.cookie-icon-animated {
    display: inline-block;
    font-size: 3.5rem;
    animation: cookieWiggle 2.5s ease-in-out infinite;
    filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));
}

.cookie-modal-content {
    background: rgba(22, 18, 16, 0.96) !important;
    backdrop-filter: blur(20px);
    border: 1px solid rgba(196, 168, 130, 0.3) !important;
    border-radius: 20px !important;
    transform: scale(0.9);
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.modal.show .cookie-modal-content {
    transform: scale(1);
}
</style>

 
  <section class="splash">
    <p class="overline">ÖNCE ÖNCEL</p>
    <h1>Zanaatin<br>Estetik Hali</h1>
    <div class="rule"></div>
    <p class="subtitle">ÖNCEL</p>
    <div class="scroll-hint">Kaydırın</div>
  </section>

  
  <div class="sections">
    <div class="section">
      <p class="number">Bölüm I</p>
      <h2></h2>
      <div class="divider"></div>
      <p>Giyinmek yalnızca bir örtünme eylemi değildir. Duruşun, karakterin ve sessiz bir lüksün üzerinizdeki imzasıdır. Dikişlerimizdeki her milimetre, geleneksel zanaata duyduğumuz saygının eseridir.</p>
    </div>

    <div class="section">
      <p class="number">Bölüm II</p>
      <h2>Milimetrik <br>Mimari</h2>
      <div class="divider"></div>
      <p>Vücudunuzun anatomik yapısına göre şekillenen kalıplarımız, ikinci bir deri gibi üzerinize oturur. Kusursuz siluet, ancak detaylardaki tavizsiz titizlikle elde edilir.</p>
      <div class="divider"></div>
      <p class="quote">"Zaman akar, stil kalır. Sizin imzanız ise, dikişlerimizin arasındaki o görünmez özenle yazılır."</p>
    </div>

    <div class="section">
      <p class="number">Bölüm III</p>
      <h2>Atölyemize<br>Konuk Olun</h2>
      <div class="divider"></div>
      <p>Kişiye özel dikim (bespoke) deneyimini başlatmak veya stil danışmanlarımızla özel bir görüşme gerçekleştirmek için randevu talebinizi iletebilirsiniz.</p>
    </div>
  </div>

 
  <div class="finale">
    <p class="overline">Yeni Koleksiyon</p>
    <h2>Zarafet,<br>sade bir duruşun<br>sükunetidir.</h2>
    <p>Öncel imzasını taşıyan kusursuz silüetler. Modern çizgiler ile geleneksel zanaatın, detaylarda gizli mükemmel uyumu.</p>
    <a class="cta" href="{{ route('appointment') }}" onclick="window.scrollTo({ top: 0, behavior: 'smooth' })">Randevu Oluşturun</a>
  </div>


  <div class="gui-panel collapsed" id="guiPanel">
    <div id="guiPanelHeader">
      <span class="gui-header-label">Öncel</span>
      <span class="gui-chevron">▼</span>
    </div>
    <div class="gui-panel-body" id="guiPanelBody"></div>
  </div>

  
  <div class="floating-container">
    <div style="position: relative;">
      <div class="wa-options" id="waOptions">
        <a href="https://wa.me/905436011884?text=Merhaba,%20Öncel%20Perde%20hakkında%20bilgi%20almak%20istiyorum." target="_blank" class="wa-link">
          <i class="bi bi-whatsapp"></i> TAHSİN ÖNCEL
        </a>
        <a href="https://wa.me/905417922550?text=Merhaba,%20Öncel%20Perde%20hakkında%20bilgi%20almak%20istiyorum." target="_blank" class="wa-link">
          <i class="bi bi-whatsapp"></i> ÖZLEM ÖNCEL
        </a>
      </div>
      <button class="floating-btn btn-whatsapp" onclick="toggleWhatsApp()" title="WhatsApp İletişim">
        <i class="bi bi-whatsapp"></i>
      </button>
    </div>

   
    <button class="floating-btn btn-ai" onclick="toggleAI()" title="Öncel Stil Asistanı">
      <i class="bi bi-robot"></i>
    </button>
  </div>


  <div class="ai-modal" id="aiModal">
    <div class="ai-header">
      <span class="ai-header-title"><i class="bi bi-stars"></i> Öncel Stil Asistanı</span>
      <button class="ai-close" onclick="toggleAI()">&times;</button>
    </div>
    <div class="ai-body" id="aiBody">
      <div class="chat-msg bot">
        Merhaba! Ben Öncel Perde Yapay Zeka Asistanıyım. Size nasıl yardımcı olabilirim? Aşağıdaki hazır sorulardan birini seçebilir ya da merak ettiğiniz konuyu sorabilirsiniz.
      </div>
    </div>
    <div class="quick-questions">
      <button class="q-btn" onclick="askAI('Hangi hizmetleri sunuyorsunuz?')">Hizmetlerimiz</button>
      <button class="q-btn" onclick="askAI('Özel dikim süreci nasıl işler?')">Özel Dikim Sanatı</button>
      <button class="q-btn" onclick="askAI('Öncel Perde’nin hikayesi nedir?')">Hikayemiz</button>
      <button class="q-btn" onclick="askAI('Atölyeniz nerede?')">Adres & Lokasyon</button>
      <button class="q-btn" onclick="askAI('Nasıl randevu alabilirim?')">Randevu Oluştur</button>
    </div>
  </div>

 
<div class="modal fade" id="firstVisitCookieModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content cookie-modal-content shadow-lg text-center p-3">
            
            <div class="modal-body pt-4">
                <div class="mb-3">
                    <span class="cookie-icon-animated">🍪</span>
                </div>

                <h4 class="font-serif italic text-white mb-3" style="font-family: 'Georgia', serif; font-size: 1.6rem; color: #f5f0eb;">
                    Küçük Bir Kurabiye Molası?
                </h4>

                <p style="color: #c4a882; font-size: 0.95rem; line-height: 1.6;" class="px-2">
                    Sitemizde size en tatlı ve kusursuz e-ticaret deneyimini sunabilmek için dijital çerezlerden (cookies) faydalanıyoruz.
                </p>

                <p class="small mb-0" style="color: rgba(245, 240, 235, 0.5); font-size: 0.8rem;">
                    Detaylı bilgi için 
                    <a href="#" data-bs-toggle="modal" data-bs-target="#kvkkModal" class="text-decoration-underline" style="color: #c4a882;">KVKK Metnimizi</a> 
                    inceleyebilirsiniz.
                </p>
            </div>

            <div class="modal-footer border-0 d-flex justify-content-center gap-3 pb-3">
                <button type="button" class="btn btn-outline-light btn-sm px-4 py-2" style="border-radius: 25px; font-size: 0.8rem; letter-spacing: 1px;" onclick="handleCookieChoice('rejected')">
                    Sadece Zorunlular
                </button>
                <button type="button" class="btn btn-sm px-4 py-2 fw-bold" style="background: #623345; color: #f5f0eb; border-radius: 25px; font-size: 0.8rem; letter-spacing: 1px; border: 1px solid rgba(196, 168, 130, 0.4);" onclick="handleCookieChoice('accepted')">
                    Hepsini Kabul Et 🍪
                </button>
            </div>

        </div>
    </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    if (!localStorage.getItem("cookieConsent")) {
        setTimeout(function() {
            var cookieModalElement = document.getElementById('firstVisitCookieModal');
            if (cookieModalElement) {
                var cookieModal = new bootstrap.Modal(cookieModalElement);
                cookieModal.show();
            }
        }, 600);
    }
});

function handleCookieChoice(choice) {
    localStorage.setItem("cookieConsent", choice);

    var cookieModalElement = document.getElementById('firstVisitCookieModal');
    var modalInstance = bootstrap.Modal.getInstance(cookieModalElement);
    if (modalInstance) {
        modalInstance.hide();
    }
}
</script>

  <script>
    function toggleWhatsApp() {
      const waOptions = document.getElementById('waOptions');
      const aiModal = document.getElementById('aiModal');
      aiModal.classList.remove('active');
      waOptions.classList.toggle('active');
    }

    function toggleAI() {
      const aiModal = document.getElementById('aiModal');
      const waOptions = document.getElementById('waOptions');
      waOptions.classList.remove('active');
      aiModal.classList.toggle('active');
    }

    const aiResponses = {
      'Hangi hizmetleri sunuyorsunuz?': '1994’ten bu yana evlerinize ve gardırobunuza dokunan incelikli bir zanaat sunuyoruz. Özel dikim perde tasarımları, mekana özel kumaş ve stil danışmanlığı ile kişiye özel terzilik ve dikim hizmetleri veriyoruz.',
      
      'Nasıl randevu alabilirim?': 'Gürpınar Caddesi’ndeki atölyemizde sizleri ağırlamaktan mutluluk duyarız. Sayfanın en altındaki "Randevu Oluşturun" butonundan ya da üst menüdeki "Randevu Al" sayfasından kolayca tarih seçebilirsiniz.',
      
      'Özel dikim süreci nasıl işler?': 'Geçmişin sabrını günümüzün modern estetiğiyle birleştiriyoruz. En kaliteli kumaşları usta ellerle buluşturarak, yaşam alanlarınıza ruh, gardırobunuza özgün bir kimlik kazandıracak milimetrik ölçümlü tasarımlar hazırlıyoruz.',
      
      'Atölyeniz nerede?': 'Atölyemiz Burdur Çavdır’dadır.\nAçık Adres: PTT Karşısı, Çınar, Gürpınar Cd. No:26, 15900 Çavdır / Burdur.\nDilerseniz WhatsApp’tan konum isteyebilirsiniz.',
      
      'Öncel Perde’nin hikayesi nedir?': '1994 yılında Çavdır’ın İlk Kadın Terzisi unvanıyla başlayan hayalimiz; bugün nesiller boyu süren zanaat mirasını, aile sıcaklığı ve kusursuz işçilik prensibiyle sürdüren bir atölyeye dönüştü.'
    };

    function askAI(question) {
      const aiBody = document.getElementById('aiBody');
      
      const userMsg = document.createElement('div');
      userMsg.className = 'chat-msg user';
      userMsg.innerText = question;
      aiBody.appendChild(userMsg);

      aiBody.scrollTop = aiBody.scrollHeight;

      setTimeout(() => {
        const botMsg = document.createElement('div');
        botMsg.className = 'chat-msg bot';
        botMsg.innerText = aiResponses[question] || 'Talebinizle ilgili detaylı bilgi almak için dilediğiniz zaman WhatsApp üzerinden stil danışmanlarımızla iletişime geçebilirsiniz.';
        aiBody.appendChild(botMsg);
        aiBody.scrollTop = aiBody.scrollHeight;
      }, 500);
    }
  </script>

  <script type="importmap">
  {
    "imports": {
      "three": "https://cdn.jsdelivr.net/npm/three@0.183.2/build/three.module.js",
      "three/webgpu": "https://cdn.jsdelivr.net/npm/three@0.183.2/build/three.webgpu.js",
      "three/tsl": "https://cdn.jsdelivr.net/npm/three@0.183.2/build/three.tsl.js",
      "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.183.2/examples/jsm/",
      "stats-gl": "https://cdn.jsdelivr.net/npm/stats-gl@4.0.2/dist/main.js",
      "eases-jsnext": "https://cdn.jsdelivr.net/npm/eases-jsnext@1.0.10/dist/eases.es.js"
    }
  }
  </script>
  <script type="module" src="{{ asset('js/scene.js') }}"></script>
@endsection