<section class="container" style="max-width:760px">
  <div class="page-header">
    <h1>📝 แจ้งปัญหาใหม่</h1>
    <p>กรอกรายละเอียดปัญหาที่คุณพบในชุมชน</p>
  </div>

  <div class="card form-card">
    <form method="POST" action="<?= url('issue/new') ?>" enctype="multipart/form-data"
          id="issue-form" novalidate>
      <?= csrfField() ?>

      <!-- Honeypot (hidden from humans, bots fill it) -->
      <div style="display:none" aria-hidden="true">
        <input type="text" name="_hp_field" tabindex="-1" autocomplete="off">
      </div>
      <!-- Timestamp for anti-spam -->
      <input type="hidden" name="_form_time" value="<?= time() ?>">

      <!-- Title -->
      <div class="form-group">
        <label for="title">หัวข้อปัญหา <span class="required">*</span></label>
        <input type="text" id="title" name="title" required minlength="5" maxlength="200"
               value="<?= e($_POST['title'] ?? '') ?>"
               placeholder="เช่น ถนนซอย 5 มีหลุมขนาดใหญ่"
               aria-required="true">
      </div>

      <!-- Category + Urgency in 2 cols -->
      <div class="form-row">
        <div class="form-group">
          <label for="category_id">หมวดหมู่ <span class="required">*</span></label>
          <select id="category_id" name="category_id" required aria-required="true">
            <option value="">-- เลือกหมวดหมู่ --</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"
              <?= (($_POST['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
              <?= e($cat['icon']) ?> <?= e($cat['name']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="urgency">ระดับความเร่งด่วน <span class="required">*</span></label>
          <select id="urgency" name="urgency" required aria-required="true">
            <option value="low"    <?= (($_POST['urgency']??'medium')==='low')    ? 'selected':'' ?>>🟢 ต่ำ</option>
            <option value="medium" <?= (($_POST['urgency']??'medium')==='medium') ? 'selected':'' ?>>🟡 ปานกลาง</option>
            <option value="high"   <?= (($_POST['urgency']??'medium')==='high')   ? 'selected':'' ?>>🔴 เร่งด่วน</option>
          </select>
        </div>
      </div>

      <!-- Description -->
      <div class="form-group">
        <label for="description">รายละเอียด <span class="required">*</span></label>
        <textarea id="description" name="description" required minlength="10" rows="5"
                  placeholder="อธิบายปัญหาให้ชัดเจน เช่น ตำแหน่ง ขนาด ผลกระทบ..."
                  aria-required="true"><?= e($_POST['description'] ?? '') ?></textarea>
        <span class="char-count" data-target="description" data-max="2000">0/2000</span>
      </div>

      <!-- Location text -->
      <div class="form-group">
        <label for="location_text">สถานที่ (พิมพ์)</label>
        <input type="text" id="location_text" name="location_text" maxlength="300"
               value="<?= e($_POST['location_text'] ?? '') ?>"
               placeholder="เช่น ซอย 5 หมู่ 3 ตำบลตัวอย่าง">
      </div>

      <!-- Map (Leaflet) -->
      <div class="form-group">
        <label>ปักหมุดบนแผนที่ (ไม่บังคับ)</label>
        <div class="map-toolbar">
          <button type="button" id="btn-geolocate" class="btn btn-outline btn-sm" aria-label="ใช้ตำแหน่งปัจจุบัน">
            📍 ใช้ตำแหน่งปัจจุบัน
          </button>
          <small id="geo-status" aria-live="polite"></small>
        </div>
        <div id="issue-map" class="issue-map" aria-label="แผนที่ปักหมุดตำแหน่ง"></div>
        <div class="latlng-display" id="latlng-display" hidden>
          <small>ตำแหน่ง: <span id="latlng-text"></span></small>
        </div>
        <input type="hidden" name="latitude"  id="latitude"  value="<?= e($_POST['latitude']  ?? '') ?>">
        <input type="hidden" name="longitude" id="longitude" value="<?= e($_POST['longitude'] ?? '') ?>">
      </div>

      <!-- Images upload -->
      <div class="form-group">
        <label>แนบรูปภาพ (สูงสุด 3 รูป, แต่ละรูปไม่เกิน 5 MB)</label>
        <div class="upload-zone" id="upload-zone" role="button" tabindex="0"
             aria-label="คลิกหรือลากไฟล์รูปภาพมาวางที่นี่">
          <span class="upload-icon">🖼️</span>
          <p>คลิกหรือลากไฟล์มาวาง</p>
          <small>รองรับ JPG, PNG, WebP, GIF</small>
          <input type="file" name="images[]" id="images" multiple accept="image/*"
                 max="3" aria-label="เลือกรูปภาพ">
        </div>
        <div id="preview-grid" class="preview-grid"></div>
      </div>

      <div class="form-actions">
        <a href="<?= url() ?>" class="btn btn-outline">ยกเลิก</a>
        <button type="submit" class="btn btn-primary" id="submit-btn">
          📤 ส่งรายงานปัญหา
        </button>
      </div>
    </form>
  </div>
</section>

<script>
// Map init (runs after Leaflet CDN loaded in footer)
document.addEventListener('DOMContentLoaded', function () {
  if (typeof L === 'undefined') return;

  const defaultLat = 13.7563, defaultLng = 100.5018; // Bangkok
  const savedLat   = <?= json_encode($_POST['latitude']  ?? null) ?>;
  const savedLng   = <?= json_encode($_POST['longitude'] ?? null) ?>;

  const map = L.map('issue-map', { doubleClickZoom: false })
    .setView([savedLat || defaultLat, savedLng || defaultLng], savedLat ? 15 : 10);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://osm.org/copyright">OpenStreetMap</a>',
    maxZoom: 19
  }).addTo(map);

  let marker = null;
  function setMarker(lat, lng) {
    if (marker) map.removeLayer(marker);
    marker = L.marker([lat, lng], { draggable: true }).addTo(map);
    document.getElementById('latitude').value  = lat.toFixed(7);
    document.getElementById('longitude').value = lng.toFixed(7);
    document.getElementById('latlng-text').textContent = lat.toFixed(5) + ', ' + lng.toFixed(5);
    document.getElementById('latlng-display').hidden = false;
    marker.on('dragend', function () {
      const p = marker.getLatLng();
      setMarker(p.lat, p.lng);
    });
  }

  if (savedLat && savedLng) setMarker(savedLat, savedLng);

  map.on('click', function (e) { setMarker(e.latlng.lat, e.latlng.lng); });

  // Geolocation button
  document.getElementById('btn-geolocate').addEventListener('click', function () {
    const status = document.getElementById('geo-status');
    if (!navigator.geolocation) { status.textContent = 'เบราว์เซอร์ไม่รองรับ GPS'; return; }
    status.textContent = 'กำลังหาตำแหน่ง…';
    navigator.geolocation.getCurrentPosition(function (pos) {
      const lat = pos.coords.latitude, lng = pos.coords.longitude;
      setMarker(lat, lng);
      map.setView([lat, lng], 16);
      status.textContent = 'พบตำแหน่งแล้ว ✓';
    }, function () {
      status.textContent = 'ไม่สามารถระบุตำแหน่งได้';
    });
  });
});
</script>
