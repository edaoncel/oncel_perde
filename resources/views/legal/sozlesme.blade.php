<div class="legal-content" style="font-family: sans-serif; line-height: 1.6; color: #333;">
    <h3 style="text-align: center; color: #000;">MESAFELİ SATIŞ SÖZLEŞMESİ</h3>
    
    <p><strong>1. TARAFLAR</strong><br>
    Satıcı: Öncel<br>
    Alıcı: {{ Auth::check() ? Auth::user()->name : 'Sayın Müşterimiz' }} </p>

    <p><strong>2. KONU</strong><br>
    İşbu sözleşmenin konusu, {{ Auth::check() ? Auth::user()->name : 'nın' }}, Öncel'e ait internet sitesi üzerinden elektronik ortamda siparişini verdiği aşağıda nitelikleri ve satış fiyatı belirtilen ürünün satışı ve teslimi ile ilgili 6502 sayılı Tüketicinin Korunması Hakkında Kanun ve Mesafeli Sözleşmeler Yönetmeliği hükümleri gereğince tarafların hak ve yükümlülüklerinin saptanmasıdır.</p>

    <p><strong>3. ÜRÜN BİLGİLERİ VE ÖDEME</strong><br>
    Ürünlerin cinsi, türü, miktarı, marka/modeli, rengi ve satış bedeli sipariş formunda belirtildiği gibidir. Ödeme kredi kartı veya havale yöntemiyle gerçekleştirilir.</p>

    <p><strong>4. TESLİMAT</strong><br>
    Sipariş edilen ürünler, yasal 30 günlük süreyi aşmamak kaydıyla {{ Auth::check() ? Auth::user()->name : '' }}'nın belirttiği teslimat adresine gönderilir. Kargo masrafları {{ Auth::check() ? Auth::user()->name : '' }}'ya aittir. Öncel, kampanya dahilinde kargo ücretini kendi karşılayabilir.</p>

    <p><strong>5. CAYMA HAKKI</strong><br>
    {{ Auth::check() ? Auth::user()->name : '' }}, hiçbir hukuki ve cezai sorumluluk üstlenmeksizin ve hiçbir gerekçe göstermeksizin, satın aldığı ürünü teslim tarihinden itibaren 14 (on dört) gün içerisinde cayma hakkını kullanarak iade edebilir.</p>

    <p><strong>6. YETKİLİ MAHKEME</strong><br>
    İşbu sözleşmenin uygulanmasında, Sanayi ve Ticaret Bakanlığı tarafından ilan edilen değere kadar Tüketici Hakem Heyetleri ile {{ Auth::check() ? Auth::user()->name : '' }}'nın yerleşim yerindeki Tüketici Mahkemeleri yetkilidir.</p>

    <p style="text-align: right; margin-top: 20px;">
        <em>İşbu sözleşme, elektronik ortamda onaylandığı anda yürürlüğe girer.</em>
    </p>
</div>