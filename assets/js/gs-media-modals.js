document.addEventListener("DOMContentLoaded", () => {
  const mediaModal = document.getElementById("gs-media-modal");
  const fotoImg = document.getElementById("gs-foto-img");
  const prevBtn = document.getElementById("gs-foto-prev");
  const nextBtn = document.getElementById("gs-foto-next");

  if (!mediaModal || !fotoImg) return;

  // =========================================================
  // 📸 ABRIR MODAL DESDE LA GALERÍA PÚBLICA
  // =========================================================
  document.addEventListener("click", (e) => {
    const img = e.target.closest("#public-gallery img");
    if (!img) return;

    const src = img.dataset.full || img.src;
    const likes = img.closest(".group")?.querySelector("span")?.textContent || "0";

    fotoImg.src = src;
    document.getElementById("gs-foto-likes-num").textContent = likes;

    fotoImg.onload = () => {
      console.log("✅ Imagen visible:", fotoImg.naturalWidth, "x", fotoImg.naturalHeight);
      mediaModal.classList.remove("hidden");
      requestAnimationFrame(() => mediaModal.classList.add("opacity-100"));
      document.body.classList.add("overflow-hidden");
      openModal(); // 👈 activa la detección del botón “Atrás”
    };

    // Guardar índice actual
    window.currentPhotoIndex = [...document.querySelectorAll("#public-gallery img")].indexOf(img);
  });

  // =========================================================
  // ⬅️➡️ NAVEGACIÓN ENTRE FOTOS
  // =========================================================
  const changePhoto = (dir) => {
    const imgs = document.querySelectorAll("#public-gallery img");
    if (!imgs.length) return;

    window.currentPhotoIndex =
      (window.currentPhotoIndex + dir + imgs.length) % imgs.length;

    const newImg = imgs[window.currentPhotoIndex];
    const src = newImg.dataset.full || newImg.src;
    const likes = newImg.closest(".group")?.querySelector("span")?.textContent || "0";

    fotoImg.classList.add("opacity-0");
    setTimeout(() => {
      fotoImg.src = src;
      fotoImg.classList.remove("opacity-0");
    }, 150);

    document.getElementById("gs-foto-likes-num").textContent = likes;
  };

  prevBtn?.addEventListener("click", () => changePhoto(-1));
  nextBtn?.addEventListener("click", () => changePhoto(1));

  // =========================================================
  // 🔙 CIERRE CON BOTÓN “ATRÁS” DEL TELÉFONO
  // =========================================================
  let modalOpen = false;

  const openModal = () => {
    if (!modalOpen) {
      history.pushState({ modal: true }, ""); // Añade un paso al historial
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
      history.back(); // Retrocede un paso en el historial
      modalOpen = false;
    }
  };

  // Detectar si el usuario presiona “Atrás” en el navegador/móvil
  window.addEventListener("popstate", () => {
    if (modalOpen) closeModal();
  });

  // =========================================================
  // 👆 CIERRE CON TOQUE FUERA DEL CONTENIDO
  // =========================================================
  mediaModal.addEventListener("click", (e) => {
    if (e.target === mediaModal || e.target.id === "gs-foto-grande") {
      closeModal();
    }
  });

  // =========================================================
  // ⌨️ CIERRE CON TECLA ESC (Desktop)
  // =========================================================
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeModal();
  });

  // =========================================================
  // 📱 CIERRE POR GESTO DE “DESLIZAR HACIA ABAJO”
  // =========================================================
  let startY = 0;
  let endY = 0;

  fotoImg.addEventListener("touchstart", (e) => {
    if (e.touches.length === 1) {
      startY = e.touches[0].clientY;
    }
  });

  fotoImg.addEventListener("touchmove", (e) => {
    if (e.touches.length === 1) {
      endY = e.touches[0].clientY;
    }
  });

  fotoImg.addEventListener("touchend", () => {
    const diffY = endY - startY;
    if (diffY > 80) {
      // Si desliza más de 80px hacia abajo → cerrar modal
      closeModal();
    }
    startY = 0;
    endY = 0;
  });
});
