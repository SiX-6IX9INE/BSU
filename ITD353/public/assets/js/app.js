/**
 * Community Issue Reporter – app.js
 * Vanilla JS, no external dependencies
 */
(function () {
  "use strict";

  /* ================================================================
     1. THEME TOGGLE  (light / dark)
  ================================================================ */
  const THEME_KEY = "cir_theme";
  const html = document.documentElement;

  function applyTheme(theme) {
    html.dataset.theme = theme;
    localStorage.setItem(THEME_KEY, theme);
  }

  // Initialise from storage (or system pref)
  (function initTheme() {
    const stored = localStorage.getItem(THEME_KEY);
    if (stored) {
      applyTheme(stored);
    } else if (
      window.matchMedia &&
      window.matchMedia("(prefers-color-scheme: dark)").matches
    ) {
      applyTheme("dark");
    } else {
      applyTheme("light");
    }
  })();

  document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("theme-toggle");
    if (toggleBtn) {
      toggleBtn.addEventListener("click", function () {
        applyTheme(html.dataset.theme === "dark" ? "light" : "dark");
      });
    }

    /* ================================================================
       2. HAMBURGER / MOBILE NAV
    ================================================================ */
    const hamburger = document.getElementById("hamburger");
    const mobileNav = document.getElementById("mobile-nav");

    if (hamburger && mobileNav) {
      hamburger.addEventListener("click", function () {
        const expanded = hamburger.getAttribute("aria-expanded") === "true";
        hamburger.setAttribute("aria-expanded", String(!expanded));
        mobileNav.hidden = expanded;
      });
    }

    /* ================================================================
       3. USER DROPDOWN
    ================================================================ */
    const userMenu = document.querySelector(".nav-user-menu");
    if (userMenu) {
      const trigger = userMenu.querySelector("[aria-haspopup]");
      if (trigger) {
        trigger.addEventListener("click", function (e) {
          e.stopPropagation();
          userMenu.classList.toggle("open");
        });
      }

      // Close on outside click
      document.addEventListener("click", function () {
        userMenu.classList.remove("open");
      });

      userMenu.addEventListener("click", function (e) {
        e.stopPropagation();
      });
    }

    /* ================================================================
       4. TOAST SYSTEM
    ================================================================ */
    window.showToast = function (type, message, title) {
      const container = document.getElementById("toast-container");
      if (!container) return;

      const icons = { success: "✅", error: "❌", info: "ℹ️", warning: "⚠️" };

      const toast = document.createElement("div");
      toast.className = "toast toast-" + (type || "info");
      toast.innerHTML =
        '<span class="toast-icon">' +
        (icons[type] || "ℹ️") +
        "</span>" +
        '<div class="toast-body">' +
        (title ? '<div class="toast-title">' + escHtml(title) + "</div>" : "") +
        '<div class="toast-msg">' +
        escHtml(message) +
        "</div>" +
        "</div>" +
        '<button class="toast-close" aria-label="ปิด">✕</button>';

      container.appendChild(toast);

      toast
        .querySelector(".toast-close")
        .addEventListener("click", function () {
          dismissToast(toast);
        });

      setTimeout(function () {
        dismissToast(toast);
      }, 4500);
    };

    function dismissToast(el) {
      el.style.transition = "opacity .25s, transform .25s";
      el.style.opacity = "0";
      el.style.transform = "translateX(100%)";
      setTimeout(function () {
        el.remove();
      }, 280);
    }

    function escHtml(str) {
      const d = document.createElement("div");
      d.textContent = str;
      return d.innerHTML;
    }

    // Show server-side flashes injected into window.__flashes
    if (Array.isArray(window.__flashes)) {
      window.__flashes.forEach(function (f) {
        window.showToast(f.type, f.message);
      });
    }

    /* ================================================================
       5. PASSWORD SHOW / HIDE
    ================================================================ */
    document.querySelectorAll(".toggle-pw").forEach(function (btn) {
      btn.addEventListener("click", function () {
        const input = btn.closest(".input-password").querySelector("input");
        if (!input) return;
        const isText = input.type === "text";
        input.type = isText ? "password" : "text";
        btn.textContent = isText ? "👁" : "🙈";
      });
    });

    /* ================================================================
       6. IMAGE UPLOAD PREVIEW
    ================================================================ */
    const uploadZone = document.getElementById("upload-zone");
    const fileInput = document.getElementById("issue-images");
    const previewGrid = document.getElementById("preview-grid");

    if (uploadZone && fileInput && previewGrid) {
      // Drag events
      uploadZone.addEventListener("dragover", function (e) {
        e.preventDefault();
        uploadZone.classList.add("drag-over");
      });
      uploadZone.addEventListener("dragleave", function () {
        uploadZone.classList.remove("drag-over");
      });
      uploadZone.addEventListener("drop", function (e) {
        e.preventDefault();
        uploadZone.classList.remove("drag-over");
        if (e.dataTransfer.files.length) {
          mergeFiles(e.dataTransfer.files);
        }
      });

      fileInput.addEventListener("change", function () {
        mergeFiles(fileInput.files);
      });

      let dt = new DataTransfer();

      function mergeFiles(files) {
        const MAX_FILES = 3;
        Array.from(files).forEach(function (file) {
          if (dt.items.length >= MAX_FILES) return;
          if (!file.type.startsWith("image/")) return;
          dt.items.add(file);
        });
        fileInput.files = dt.files;
        renderPreviews();
      }

      function renderPreviews() {
        previewGrid.innerHTML = "";
        Array.from(dt.files).forEach(function (file, idx) {
          const reader = new FileReader();
          reader.onload = function (e) {
            const wrap = document.createElement("div");
            wrap.className = "prev-img";
            wrap.innerHTML =
              '<img src="' +
              e.target.result +
              '" alt="preview">' +
              '<button type="button" class="rem-btn" data-idx="' +
              idx +
              '" aria-label="ลบ">✕</button>';
            previewGrid.appendChild(wrap);

            wrap
              .querySelector(".rem-btn")
              .addEventListener("click", function () {
                removeFile(idx);
              });
          };
          reader.readAsDataURL(file);
        });
      }

      function removeFile(idx) {
        const newDt = new DataTransfer();
        Array.from(dt.files).forEach(function (f, i) {
          if (i !== idx) newDt.items.add(f);
        });
        dt = newDt;
        fileInput.files = dt.files;
        renderPreviews();
      }
    }

    /* ================================================================
       7. IMAGE CAROUSEL (issue detail)
    ================================================================ */
    const track = document.querySelector(".carousel-track");
    const prevBtn = document.getElementById("car-prev");
    const nextBtn = document.getElementById("car-next");
    const dotsWrap = document.getElementById("car-dots");

    if (track && prevBtn && nextBtn) {
      const slides = track.querySelectorAll(".carousel-slide");
      let current = 0;

      const dots = dotsWrap
        ? Array.from(dotsWrap.querySelectorAll(".dot"))
        : [];

      function goTo(n) {
        current = (n + slides.length) % slides.length;
        track.style.transform = "translateX(-" + current * 100 + "%)";
        dots.forEach(function (d, i) {
          d.classList.toggle("active", i === current);
        });
      }

      prevBtn.addEventListener("click", function () {
        goTo(current - 1);
      });
      nextBtn.addEventListener("click", function () {
        goTo(current + 1);
      });

      dots.forEach(function (d, i) {
        d.addEventListener("click", function () {
          goTo(i);
        });
      });

      // Keyboard
      document.addEventListener("keydown", function (e) {
        if (e.key === "ArrowLeft") goTo(current - 1);
        if (e.key === "ArrowRight") goTo(current + 1);
      });

      // Touch swipe
      let startX = 0;
      track.addEventListener(
        "touchstart",
        function (e) {
          startX = e.touches[0].clientX;
        },
        { passive: true },
      );
      track.addEventListener("touchend", function (e) {
        const diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 40) goTo(diff > 0 ? current + 1 : current - 1);
      });

      goTo(0);
    }

    /* ================================================================
       8. VOTE AJAX
    ================================================================ */
    const voteBtn = document.getElementById("vote-btn");
    if (voteBtn) {
      voteBtn.addEventListener("click", function () {
        voteBtn.disabled = true;
        const url = voteBtn.dataset.url;
        const csrfToken = document.querySelector('meta[name="csrf-token"]');

        fetch(url, {
          method: "POST",
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-Token": csrfToken ? csrfToken.content : "",
          },
        })
          .then(function (r) {
            return r.json();
          })
          .then(function (d) {
            if (d.ok) {
              const countEl = document.getElementById("vote-count-big");
              const labelEl = document.getElementById("vote-label");
              if (countEl) countEl.textContent = d.count;
              if (labelEl) labelEl.textContent = d.count + " เสียง";
              voteBtn.textContent = d.voted
                ? "❤️ ยกเลิกโหวต"
                : "🤍 โหวตสนับสนุน";
              voteBtn.classList.toggle("btn-primary", d.voted);
              voteBtn.classList.toggle("btn-outline", !d.voted);
              window.showToast(
                "success",
                d.voted ? "โหวตแล้ว!" : "ยกเลิกโหวตแล้ว",
              );
            } else if (d.login_required) {
              window.location.href = "/pages/login.php";
            } else {
              window.showToast("error", d.message || "เกิดข้อผิดพลาด");
            }
          })
          .catch(function () {
            window.showToast("error", "ไม่สามารถเชื่อมต่อได้");
          })
          .finally(function () {
            voteBtn.disabled = false;
          });
      });
    }

    /* ================================================================
       9. CHARACTER COUNTER
    ================================================================ */
    document.querySelectorAll("[data-maxlen]").forEach(function (el) {
      const target = document.querySelector(el.dataset.target);
      const maxLen = parseInt(el.dataset.maxlen, 10);
      if (!target) return;

      function update() {
        const remaining = maxLen - target.value.length;
        el.textContent = remaining + " ตัวอักษรที่เหลือ";
        el.style.color = remaining < 20 ? "var(--c-high)" : "";
      }

      target.addEventListener("input", update);
      update();
    });

    /* ================================================================
       10. AUTO-SUBMIT SELECTS (filter forms)
    ================================================================ */
    document.querySelectorAll("[data-auto-submit]").forEach(function (sel) {
      sel.addEventListener("change", function () {
        sel.closest("form").submit();
      });
    });

    /* ================================================================
       11. LEAFLET MAP – ISSUE NEW (geolocation + pin drop)
    ================================================================ */
    const issueMapEl = document.getElementById("issue-map");
    if (issueMapEl && window.L) {
      const latInput = document.getElementById("latitude");
      const lngInput = document.getElementById("longitude");
      const geoBtn = document.getElementById("btn-geolocate");
      const latDisplay = document.getElementById("latlng-display");

      const map = L.map("issue-map").setView([13.75, 100.52], 12);
      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "© OpenStreetMap contributors",
        maxZoom: 19,
      }).addTo(map);

      let marker = null;

      function setPin(lat, lng) {
        if (marker) map.removeLayer(marker);
        marker = L.marker([lat, lng], { draggable: true }).addTo(map);
        latInput.value = lat.toFixed(6);
        lngInput.value = lng.toFixed(6);
        if (latDisplay)
          latDisplay.textContent = lat.toFixed(5) + ", " + lng.toFixed(5);
        marker.on("dragend", function (e) {
          const pos = e.target.getLatLng();
          setPin(pos.lat, pos.lng);
        });
      }

      map.on("click", function (e) {
        setPin(e.latlng.lat, e.latlng.lng);
      });

      if (geoBtn) {
        geoBtn.addEventListener("click", function () {
          if (!navigator.geolocation) {
            window.showToast("warning", "เบราว์เซอร์ไม่รองรับ Geolocation");
            return;
          }
          geoBtn.disabled = true;
          navigator.geolocation.getCurrentPosition(
            function (pos) {
              const { latitude: lat, longitude: lng } = pos.coords;
              map.setView([lat, lng], 16);
              setPin(lat, lng);
              geoBtn.disabled = false;
            },
            function () {
              window.showToast("error", "ไม่สามารถระบุตำแหน่งได้");
              geoBtn.disabled = false;
            },
          );
        });
      }

      // Restore saved value
      if (latInput.value && lngInput.value) {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);
        if (!isNaN(lat) && !isNaN(lng)) {
          map.setView([lat, lng], 15);
          setPin(lat, lng);
        }
      }
    }

    /* ================================================================
       12. LEAFLET MAP – DETAIL VIEW (lazy load toggle)
    ================================================================ */
    const detailMapWrap = document.getElementById("detail-map-wrap");
    const showMapBtn = document.getElementById("btn-show-map");
    if (detailMapWrap && showMapBtn && window.L) {
      let detailMapInit = false;
      showMapBtn.addEventListener("click", function () {
        detailMapWrap.hidden = false;
        showMapBtn.hidden = true;
        if (!detailMapInit) {
          detailMapInit = true;
          const lat = parseFloat(detailMapWrap.dataset.lat);
          const lng = parseFloat(detailMapWrap.dataset.lng);
          if (!isNaN(lat) && !isNaN(lng)) {
            const m = L.map("detail-map").setView([lat, lng], 15);
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
              attribution: "© OpenStreetMap contributors",
              maxZoom: 19,
            }).addTo(m);
            L.marker([lat, lng]).addTo(m);
          }
        }
      });
    }

    /* ================================================================
       13. SHARE / COPY LINK
    ================================================================ */
    const copyLinkBtn = document.getElementById("btn-copy-link");
    if (copyLinkBtn) {
      copyLinkBtn.addEventListener("click", function () {
        const shareBtn = document.getElementById("btn-share");
        if (shareBtn && navigator.share) {
          navigator
            .share({
              title: document.title,
              url: window.location.href,
            })
            .catch(function () {});
          return;
        }
        navigator.clipboard
          .writeText(window.location.href)
          .then(function () {
            window.showToast("success", "คัดลอกลิงก์แล้ว");
          })
          .catch(function () {
            window.showToast("error", "คัดลอกไม่สำเร็จ");
          });
      });
    }

    const shareBtn = document.getElementById("btn-share");
    if (shareBtn && navigator.share) {
      shareBtn.addEventListener("click", function () {
        navigator
          .share({
            title: document.title,
            url: window.location.href,
          })
          .catch(function () {});
      });
    }

    /* ================================================================
       14. ADMIN – EDIT CATEGORY MODAL
    ================================================================ */
    window.openEditCat = function (cat) {
      const modal = document.getElementById("edit-cat-modal");
      if (!modal) return;
      document.getElementById("edit-cat-id").value = cat.id;
      document.getElementById("edit-cat-name").value = cat.name;
      document.getElementById("edit-cat-icon").value = cat.icon || "";
      document.getElementById("edit-cat-color").value = cat.color || "#3b82f6";
      modal.hidden = false;
    };

    window.closeEditCat = function () {
      const modal = document.getElementById("edit-cat-modal");
      if (modal) modal.hidden = true;
    };

    // Close modal on overlay click
    document.querySelectorAll(".modal-overlay").forEach(function (overlay) {
      overlay.addEventListener("click", function (e) {
        if (e.target === overlay) overlay.hidden = true;
      });
    });

    // Close modal on Escape
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") {
        document.querySelectorAll(".modal-overlay").forEach(function (m) {
          m.hidden = true;
        });
      }
    });

    /* ================================================================
       15. ADMIN CHARTS (Canvas)
    ================================================================ */
    drawWeeklyChart();
    drawDonutChart();

    function drawWeeklyChart() {
      const canvas = document.getElementById("chart-weekly");
      if (!canvas) return;
      const raw = canvas.dataset.values;
      const labels = canvas.dataset.labels;
      if (!raw || !labels) return;

      const values = JSON.parse(raw);
      const lbls = JSON.parse(labels);
      const ctx = canvas.getContext("2d");
      const W = canvas.width,
        H = canvas.height;
      const pad = { top: 16, bottom: 32, left: 32, right: 16 };
      const chartH = H - pad.top - pad.bottom;
      const chartW = W - pad.left - pad.right;
      const max = Math.max(...values, 1);
      const barW = (chartW / lbls.length) * 0.55;
      const gap = (chartW / lbls.length) * 0.45;

      ctx.clearRect(0, 0, W, H);

      const isDark = document.documentElement.dataset.theme === "dark";
      const textColor = isDark ? "#94a3b8" : "#64748b";
      const barColor = "#3b82f6";

      values.forEach(function (v, i) {
        const x = pad.left + i * (barW + gap);
        const barH = (v / max) * chartH;
        const y = pad.top + chartH - barH;

        ctx.fillStyle = barColor;
        const r = 3;
        ctx.beginPath();
        ctx.moveTo(x + r, y);
        ctx.lineTo(x + barW - r, y);
        ctx.quadraticCurveTo(x + barW, y, x + barW, y + r);
        ctx.lineTo(x + barW, y + barH);
        ctx.lineTo(x, y + barH);
        ctx.lineTo(x, y + r);
        ctx.quadraticCurveTo(x, y, x + r, y);
        ctx.closePath();
        ctx.fill();

        // label
        ctx.fillStyle = textColor;
        ctx.font = "10px Kanit, sans-serif";
        ctx.textAlign = "center";
        ctx.fillText(lbls[i], x + barW / 2, H - 6);
        ctx.fillText(String(v), x + barW / 2, y - 4);
      });
    }

    function drawDonutChart() {
      const canvas = document.getElementById("chart-donut");
      if (!canvas) return;
      const rawV = canvas.dataset.values;
      const rawC = canvas.dataset.colors;
      if (!rawV) return;

      const values = JSON.parse(rawV);
      const colors = JSON.parse(rawC || "[]");
      const total =
        values.reduce(function (a, b) {
          return a + b;
        }, 0) || 1;
      const ctx = canvas.getContext("2d");
      const W = canvas.width,
        H = canvas.height;
      const cx = W / 2,
        cy = H / 2,
        r = Math.min(W, H) / 2 - 10;
      const inner = r * 0.6;
      let start = -Math.PI / 2;

      ctx.clearRect(0, 0, W, H);

      values.forEach(function (v, i) {
        const sweep = (v / total) * 2 * Math.PI;
        ctx.beginPath();
        ctx.moveTo(cx, cy);
        ctx.arc(cx, cy, r, start, start + sweep);
        ctx.closePath();
        ctx.fillStyle = colors[i] || "#3b82f6";
        ctx.fill();
        start += sweep;
      });

      // Hole
      ctx.beginPath();
      ctx.arc(cx, cy, inner, 0, 2 * Math.PI);
      const isDark = document.documentElement.dataset.theme === "dark";
      ctx.fillStyle = isDark ? "#1e293b" : "#ffffff";
      ctx.fill();

      // Total label
      ctx.fillStyle = isDark ? "#f1f5f9" : "#0f172a";
      ctx.font = "bold 16px Kanit, sans-serif";
      ctx.textAlign = "center";
      ctx.textBaseline = "middle";
      ctx.fillText(String(total), cx, cy);
    }

    // Re-draw charts when theme toggles (colour changes)
    const themeBtn = document.getElementById("theme-toggle");
    if (themeBtn) {
      themeBtn.addEventListener("click", function () {
        // Defer until dataset is updated
        setTimeout(function () {
          drawWeeklyChart();
          drawDonutChart();
        }, 50);
      });
    }
  }); // end DOMContentLoaded
})();
