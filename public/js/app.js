// ============================================================
// public/js/app.js
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

  // ── GPS detect ─────────────────────────────────────────────
  const gpsBtn = document.getElementById('gps-btn');
  if (gpsBtn) {
    gpsBtn.addEventListener('click', function () {
      if (!navigator.geolocation) { alert('Geolocation not supported.'); return; }
      gpsBtn.textContent = 'Detecting…';
      gpsBtn.disabled = true;
      navigator.geolocation.getCurrentPosition(
        pos => {
          document.getElementById('latitude').value  = pos.coords.latitude.toFixed(6);
          document.getElementById('longitude').value = pos.coords.longitude.toFixed(6);
          const locField = document.getElementById('location');
          if (locField && !locField.value)
            locField.value = pos.coords.latitude.toFixed(4) + ', ' + pos.coords.longitude.toFixed(4);
          gpsBtn.textContent = '✓ Location detected';
          gpsBtn.style.background = '#10b981';
          gpsBtn.style.color = '#fff';
          gpsBtn.style.borderColor = '#10b981';
        },
        () => {
          alert('Could not get location. Enter manually.');
          gpsBtn.textContent = '📍 Detect my location';
          gpsBtn.disabled = false;
        }
      );
    });
  }

  // ── Photo preview ───────────────────────────────────────────
  const photoInput = document.getElementById('photo');
  if (photoInput) {
    photoInput.addEventListener('change', function () {
      const preview = document.getElementById('photo-preview');
      if (!preview || !this.files[0]) return;
      const reader = new FileReader();
      reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
      reader.readAsDataURL(this.files[0]);
    });
  }

  // ── Upvote AJAX ─────────────────────────────────────────────
  document.querySelectorAll('.upvote-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      const id = this.dataset.id;
      const countEl = this.querySelector('.upvote-count');
      fetch(APP_URL + '/pages/upvote.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'report_id=' + id + '&csrf=' + CSRF_TOKEN
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          if (countEl) countEl.textContent = data.upvotes;
          this.classList.toggle('voted', data.voted);
        } else if (data.redirect) {
          window.location.href = data.redirect;
        }
      });
    });
  });

  // ── Map init ────────────────────────────────────────────────
  if (document.getElementById('map') && typeof L !== 'undefined' && typeof MAP_REPORTS !== 'undefined') {
    const center = MAP_REPORTS.length
      ? [MAP_REPORTS[0].lat, MAP_REPORTS[0].lng]
      : [3.848, 11.502];

    const map = L.map('map').setView(center, 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const iconColors = { pending: '#f59e0b', in_progress: '#3b82f6', resolved: '#10b981', rejected: '#ef4444' };

    MAP_REPORTS.forEach(r => {
      if (!r.lat || !r.lng) return;
      const color = iconColors[r.status] || '#6b7280';
      const icon  = L.divIcon({
        className: '',
        html: `<div style="width:28px;height:28px;background:${color};border:3px solid #fff;border-radius:50%;box-shadow:0 2px 6px rgba(0,0,0,.3)"></div>`,
        iconSize: [28, 28], iconAnchor: [14, 14]
      });
      L.marker([r.lat, r.lng], { icon }).addTo(map)
       .bindPopup(`<strong>${r.title}</strong><br><small>${r.category}</small><br>
         <a href="${APP_URL}/pages/report.php?id=${r.id}" style="color:#1a56db">View details →</a>`);
    });
  }

  // ── Single report map ───────────────────────────────────────
  if (document.getElementById('mini-map') && typeof L !== 'undefined' && typeof REPORT_LAT !== 'undefined') {
    const map = L.map('mini-map').setView([REPORT_LAT, REPORT_LNG], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap'
    }).addTo(map);
    L.marker([REPORT_LAT, REPORT_LNG]).addTo(map);
  }

});
