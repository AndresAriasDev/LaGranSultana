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
  document.addEventListener("click", (e) => {
    const img = e.target.closest("#public-gallery img");
    if (!img) return;

    const src = img.dataset.full || img.src;
    const likes = img.dataset.likes || "0";
    const id = img.dataset.id || null;
    const autor = img.dataset.autor || null;

    fotoImg.src = src;
    fotoImg.dataset.id = id;
    fotoImg.dataset.autor = autor;
    likesNum.textContent = likes;

    fotoImg.onload = () => {
      mediaModal.classList.remove("hidden");
      requestAnimationFrame(() => mediaModal.classList.add("opacity-100"));
      document.body.classList.add("overflow-hidden");
      openModal();
      actualizarEstadoCorazon(id); // siempre se define correctamente el color
    };

    window.currentPhotoIndex = [...document.querySelectorAll("#public-gallery img")].indexOf(img);
  });

  // =========================================================
  // ⬅️➡️ NAVEGACIÓN ENTRE FOTOS
  // =========================================================
    const changePhoto = (dir) => {
      const imgs = document.querySelectorAll("#public-gallery img");
      if (!imgs.length) return;

      // índice circular
      window.currentPhotoIndex = (window.currentPhotoIndex + dir + imgs.length) % imgs.length;

      const newImg = imgs[window.currentPhotoIndex];
      const src = newImg.dataset.full || newImg.src;
      const likes = newImg.dataset.likes || "0";
      const id = newImg.dataset.id || null;
      const autor = newImg.dataset.autor || null;

      // 💖 Reset visual inmediato antes del cambio
      heart.setAttribute("fill", "#9ca3af"); // gris sin transición ni flash
      likesNum.textContent = likes;

      // Transición suave de imagen
      fotoImg.classList.add("opacity-0");
      setTimeout(() => {
        fotoImg.src = src;
        fotoImg.dataset.id = id;
        fotoImg.dataset.autor = autor;
        fotoImg.classList.remove("opacity-0");

        // Esperamos un frame para asegurarnos que la nueva imagen esté visible
        requestAnimationFrame(() => {
          actualizarEstadoCorazon(id); // aplica el color real
        });
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

