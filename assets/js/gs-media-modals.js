document.addEventListener("DOMContentLoaded", () => {
  const mediaModal = document.getElementById("gs-media-modal");
  const fotoImg = document.getElementById("gs-foto-img");
  const prevBtn = document.getElementById("gs-foto-prev");
  const nextBtn = document.getElementById("gs-foto-next");
  const likeBtn = document.getElementById("gs-foto-like-btn");
  const likesNum = document.getElementById("gs-foto-likes-num");
  const heart = likeBtn?.querySelector("svg");

  if (!mediaModal || !fotoImg || !likeBtn) return;

  // =========================================================
  // 📸 ABRIR MODAL DESDE LA GALERÍA
  // =========================================================
// 📸 ABRIR MODAL DESDE LA GALERÍA
document.addEventListener("click", (e) => {
  const img = e.target.closest("#public-gallery img");
  if (!img) return;

  // 🧠 Asegurar que la lista JSON esté cargada
  if (!window.gsAllPhotos) {
    const photoListEl = document.getElementById("gs-photo-list");
    if (photoListEl) {
      try {
        window.gsAllPhotos = JSON.parse(photoListEl.textContent);
      } catch (err) {
        console.error("❌ Error al leer gs-photo-list:", err);
        window.gsAllPhotos = [];
      }
    } else {
      window.gsAllPhotos = [];
    }
  }

  // Datos de la imagen clickeada
  const fotoId = parseInt(img.dataset.id);
  const fotoData = window.gsAllPhotos.findIndex((f) => parseInt(f.id) === fotoId);
  window.currentPhotoIndex = fotoData >= 0 ? fotoData : 0;

  const fotoInfo = window.gsAllPhotos[window.currentPhotoIndex] || {};

  fotoImg.src = fotoInfo.full || img.dataset.full || img.src;
  fotoImg.dataset.id = fotoInfo.id || fotoId;
  fotoImg.dataset.autor = fotoInfo.autor || img.dataset.autor;
  likesNum.textContent = fotoInfo.likes || img.dataset.likes || "0";

  fotoImg.onload = () => {
    mediaModal.classList.remove("hidden");
    requestAnimationFrame(() => mediaModal.classList.add("opacity-100"));
    document.body.classList.add("overflow-hidden");
    openModal();
    actualizarEstadoCorazon(fotoImg.dataset.id);
  };
});


// =========================================================
// ⬅️➡️ NAVEGACIÓN ENTRE FOTOS (usa lista completa)
// =========================================================
const changePhoto = (dir) => {
  const photos = window.gsAllPhotos || [];
  if (!photos.length) return;

  window.currentPhotoIndex = (window.currentPhotoIndex + dir + photos.length) % photos.length;
  const newFoto = photos[window.currentPhotoIndex];
  if (!newFoto) return;

  heart.setAttribute("fill", "#9ca3af");
  likesNum.textContent = newFoto.likes || 0;

  fotoImg.classList.add("opacity-0");
  setTimeout(() => {
    fotoImg.src = newFoto.full;
    fotoImg.dataset.id = newFoto.id;
    fotoImg.dataset.autor = newFoto.autor;
    fotoImg.classList.remove("opacity-0");
    requestAnimationFrame(() => actualizarEstadoCorazon(newFoto.id));
  }, 150);
};

prevBtn?.addEventListener("click", () => changePhoto(-1));
nextBtn?.addEventListener("click", () => changePhoto(1));



  // =========================================================
  // ❤️ ANIMACIÓN DE CORAZONES FLOTANTES
  // =========================================================
  function lanzarCorazones(x, y) {
    const num = 6;
    for (let i = 0; i < num; i++) {
      const heart = document.createElement("div");
      heart.innerHTML = "❤️";
      Object.assign(heart.style, {
        position: "fixed",
        left: `${x + (Math.random() * 40 - 20)}px`,
        top: `${y + (Math.random() * 20 - 10)}px`,
        fontSize: `${Math.random() * 16 + 16}px`,
        opacity: "1",
        transition: "all 1.2s ease-out",
        zIndex: "99999",
        pointerEvents: "none",
      });
      document.body.appendChild(heart);
      requestAnimationFrame(() => {
        heart.style.transform = `translateY(-100px) scale(${Math.random() * 0.8 + 0.6}) rotate(${Math.random() * 30 - 15}deg)`;
        heart.style.opacity = "0";
      });
      setTimeout(() => heart.remove(), 1200);
    }
  }

// =========================================================
// 💖 Consultar estado del like desde el servidor
// =========================================================
async function actualizarEstadoCorazon(fotoId) {
  // ⚪ No logueado → siempre gris
  if (!gs_likes.currentUser || gs_likes.currentUser == 0) {
    heart.setAttribute("fill", "#9ca3af");
    return;
  }

  try {
    const formData = new FormData();
    formData.append("action", "check_user_like");
    formData.append("foto_id", fotoId);
    formData.append("nonce", gs_likes.nonce);

    const res = await fetch(gs_likes.ajaxurl, { method: "POST", body: formData });
    const data = await res.json();

    if (!data.success) {
      heart.setAttribute("fill", "#9ca3af");
      return;
    }

    const liked = data.data.liked === true;
    // 🔴 Aplicar color según si ya dio al menos 1 like
    heart.setAttribute("fill", liked ? "#f87171" : "#9ca3af");
  } catch (err) {
    console.error("❌ Error al verificar estado del like:", err);
    heart.setAttribute("fill", "#9ca3af");
  }
}

// =========================================================
// 📤 Enviar like al servidor (modo TapTap con registro por usuario)
// =========================================================
async function enviarLike(fotoId, autorId) {
  const formData = new FormData();
  formData.append("action", "sumar_like_foto");
  formData.append("foto_id", fotoId);
  formData.append("nonce", gs_likes.nonce);

  try {
    const res = await fetch(gs_likes.ajaxurl, { method: "POST", body: formData });
    const data = await res.json();

    if (data.success) {
      // actualizar contador visible
      likesNum.textContent = data.data.likes;

      // sincronizar valor en galería
      const currentGalleryImg = document.querySelector(`#public-gallery img[data-id='${fotoId}']`);
      if (currentGalleryImg) currentGalleryImg.dataset.likes = data.data.likes;

      // animación leve del número
      likesNum.style.transition = "transform 0.3s ease";
      likesNum.style.transform = "scale(1.3)";
      setTimeout(() => (likesNum.style.transform = "scale(1)"), 300);

      // marcar el corazón rojo (sin parpadeo)
      heart.setAttribute("fill", "#f87171");
    } else {
      gsToast?.(data.data?.message || "Error al dar like.", "warning");
    }
  } catch (err) {
    console.error("❌ Error al enviar like:", err);
  }
}



  // =========================================================
  // 🎯 CLICK EN CORAZÓN
  // =========================================================
  likeBtn.addEventListener("click", async (e) => {
    const fotoId = fotoImg.dataset.id;
    const autorId = fotoImg.dataset.autor;
    if (!fotoId) return;

    // 🔐 Usuario no logueado → cerrar modal de foto y abrir modal de registro
    if (!gs_likes.currentUser || gs_likes.currentUser == 0) {
      if (typeof closeModal === "function") closeModal();
      else {
        const mediaModal = document.getElementById("gs-media-modal");
        if (mediaModal) {
          mediaModal.classList.remove("opacity-100");
          setTimeout(() => mediaModal.classList.add("hidden"), 250);
          document.body.classList.remove("overflow-hidden");
        }
      }

      setTimeout(() => {
        const registerTrigger = document.querySelector("[data-open-register]");
        if (registerTrigger) registerTrigger.click();
        else console.warn("⚠️ No se encontró el botón [data-open-register]");
      }, 300);

      heart.setAttribute("fill", "#9ca3af");
      return;
    }

    // 🚫 Evitar likes propios
    if (parseInt(gs_likes.currentUser) === parseInt(autorId)) {
      gsToast?.("No puedes dar like a tus propias fotos.", "warning");
      return;
    }

    // 💖 Animación visual
    const rect = likeBtn.getBoundingClientRect();
    lanzarCorazones(rect.left + rect.width / 3, rect.top);
    heart.classList.add("scale-125");
    setTimeout(() => heart.classList.remove("scale-125"), 150);

    // 📤 Enviar like
    await enviarLike(fotoId, autorId);
  });

  // =========================================================
  // 🔙 CIERRE DEL MODAL
  // =========================================================
  let modalOpen = false;
  const openModal = () => {
    if (!modalOpen) {
      history.pushState({ modal: true }, "");
      modalOpen = true;
    }
  };
  const closeModal = () => {
    mediaModal.classList.remove("opacity-100");
    setTimeout(() => {
      mediaModal.classList.add("hidden");
      document.body.classList.remove("overflow-hidden");
    }, 250);
    if (modalOpen) {
      history.back();
      modalOpen = false;
    }
  };

  window.addEventListener("popstate", () => modalOpen && closeModal());
  mediaModal.addEventListener("click", (e) => {
    if (e.target === mediaModal || e.target.id === "gs-foto-grande") closeModal();
  });
  document.addEventListener("keydown", (e) => e.key === "Escape" && closeModal());

  // 📱 Deslizar hacia abajo
  let startY = 0;
  fotoImg.addEventListener("touchstart", (e) => (startY = e.touches[0].clientY));
  fotoImg.addEventListener("touchend", (e) => {
    const endY = e.changedTouches[0].clientY;
    if (endY - startY > 80) closeModal();
  });
});

// =========================================================
// 🔁 Actualización automática de contadores de likes en galería
// =========================================================
setInterval(async () => {
  const fotos = document.querySelectorAll("#public-gallery img[data-id]");
  if (!fotos.length) return;

  const ids = Array.from(fotos).map(img => img.dataset.id);

  const formData = new FormData();
  formData.append("action", "get_likes_bulk");
  formData.append("ids", JSON.stringify(ids));

  try {
    const res = await fetch(gs_likes.ajaxurl, { method: "POST", body: formData });
    const data = await res.json();

    if (data.success && data.data) {
      Object.entries(data.data).forEach(([id, total]) => {
        const imgContainer = document.querySelector(`#public-gallery img[data-id='${id}']`)?.parentElement;
        if (!imgContainer) return;
        const likeSpan = imgContainer.querySelector("span");
        if (likeSpan && likeSpan.textContent !== String(total)) {
          likeSpan.textContent = total;

          // animación leve de rebote
          likeSpan.style.transform = "scale(1.2)";
          setTimeout(() => (likeSpan.style.transform = "scale(1)"), 300);
        }
      });
    }
  } catch (err) {
    console.error("Error actualizando contadores:", err);
  }
}, 5000); // cada 5 segundos
