document.addEventListener("DOMContentLoaded", () => {
  console.log("✅ Galería JS inicializado correctamente");

  const uploadInput = document.getElementById("btn-subir-foto");
  const gallery = document.getElementById("galeria-fotos");
  const paginacion = document.getElementById("galeria-paginacion");
  if (!gallery || !paginacion) return;

  /******************************************************
   * 🔹 FUNCIÓN GLOBAL: Cargar fotos por página
   ******************************************************/
  window.cargarFotos = function (page = 1) {
    const formData = new FormData();
    formData.append("action", "get_modelo_fotos");
    formData.append("page", page);

    const loader = document.getElementById("galeria-loader");
    if (loader) loader.classList.remove("hidden");

    fetch(ajaxurl, {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        if (!data.success) throw new Error("Error al cargar fotos.");

        gallery.innerHTML = data.data.html;
        gallery.dataset.current = data.data.current_page;

        // Ocultar loader
        if (loader) loader.classList.add("hidden");

        // Generar paginación
        generarPaginacion(data.data.total_pages, data.data.current_page);
      })
      .catch((err) => {
        console.error("❌ Error al cargar fotos:", err);
      });
  };

  /******************************************************
   * 🔹 Generar botones de paginación
   ******************************************************/
  function generarPaginacion(totalPages, activePage) {
    paginacion.innerHTML = "";
    if (totalPages <= 1) return;

    const createBtn = (num, label = null) => {
      const btn = document.createElement("button");
      btn.textContent = label || num;
      btn.dataset.page = num;
      btn.className =
        "px-3 py-1 rounded-md border text-sm font-medium transition " +
        (num === activePage
          ? "bg-blue-600 text-white border-blue-600 shadow-sm"
          : "bg-gray-100 text-gray-700 hover:bg-gray-200");
      return btn;
    };

    // ← Prev
    if (activePage > 1) paginacion.appendChild(createBtn(activePage - 1, "←"));

    // Numbers
    for (let i = 1; i <= totalPages; i++) paginacion.appendChild(createBtn(i));

    // → Next
    if (activePage < totalPages) paginacion.appendChild(createBtn(activePage + 1, "→"));
  }

  /******************************************************
   * 🔹 Click en botones de paginación
   ******************************************************/
  paginacion.addEventListener("click", (e) => {
    const btn = e.target.closest("button[data-page]");
    if (!btn) return;
    const newPage = parseInt(btn.dataset.page);
    cargarFotos(newPage);
    window.scrollTo({ top: gallery.offsetTop - 100, behavior: "smooth" });
  });

  /******************************************************
   * 📸 Subir foto
   ******************************************************/
  if (uploadInput) {
    uploadInput.addEventListener("change", async (e) => {
      console.log("📸 Archivo seleccionado para subir...");
      const file = e.target.files[0];
      if (!file) return;

      // Modal global
      const modal = document.getElementById("gs-info-modal");
      const overlay = document.getElementById("gs-info-overlay");
      const inner = document.getElementById("gs-info-inner");
      const modalContent = document.getElementById("gs-info-subida-foto");
      const loader = document.getElementById("gs-upload-loader");
      const success = document.getElementById("gs-upload-success");

      if (!modal || !overlay || !inner || !modalContent) {
        console.warn("⚠️ Modal global no encontrado.");
        return;
      }

      // Mostrar modal
      overlay.classList.remove("hidden");
      modal.classList.remove("hidden");
      document.body.classList.add("overflow-hidden");

      requestAnimationFrame(() => {
        overlay.classList.add("opacity-100");
        inner.classList.remove("opacity-0", "scale-95");
        inner.classList.add("opacity-100", "scale-100");
        modalContent.classList.remove("hidden", "opacity-0");
        loader?.classList.remove("hidden");
        success?.classList.add("hidden");
      });

      // Enviar datos
      const formData = new FormData();
      formData.append("action", "subir_foto_modelo");
      formData.append("foto", file);

      try {
        console.log("🚀 Subiendo imagen al servidor...");
        const response = await fetch(ajaxurl, { method: "POST", body: formData });
        const data = await response.json();

        console.log("📩 Respuesta recibida:", data);

        if (data.success) {
          loader?.classList.add("hidden");
          success?.classList.remove("hidden");

          // ✅ Recargar primera página
          cargarFotos(1);

          // ➕ Sumar puntos
          const puntosData = new FormData();
          puntosData.append("action", "sumar_puntos_modelo");
          puntosData.append("puntos", "5");
          await fetch(ajaxurl, { method: "POST", body: puntosData });
        } else {
          alert("⚠️ " + (data.data?.message || "Error desconocido."));
        }
      } catch (err) {
        console.error("❌ Error al subir la foto:", err);
        alert("Error al subir la foto. Revisa la consola.");
      }
    });
  }

  /******************************************************
   * 🧷 Copiar link del perfil
   ******************************************************/
  document.addEventListener("click", (e) => {
    if (e.target.id === "gs-copy-link") {
      const link = document.getElementById("gs-profile-link")?.innerText;
      if (!link) return;

      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(link);
      } else {
        const tempInput = document.createElement("input");
        tempInput.value = link;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
      }

      e.target.innerText = "Copiado!";
      setTimeout(() => (e.target.innerText = "Copiar"), 2000);
    }
  });

  /******************************************************
   * 🎉 Toast al cerrar modal de éxito
   ******************************************************/
  document.addEventListener("click", (e) => {
    if (e.target.matches("[data-close-info]")) {
      const modalSubida = document.getElementById("gs-info-subida-foto");
      if (modalSubida && !modalSubida.classList.contains("hidden")) {
        if (typeof gsToast === "function") {
          gsToast("🎉 Has ganado 5 puntos por subir una foto nueva.", "success");
        } else {
          alert("🎉 Has ganado 5 puntos por subir una foto nueva.");
        }
      }
    }
  });

/******************************************************
 * 🗑️ ELIMINAR FOTO (con modal y penalización)
 ******************************************************/
document.addEventListener("click", async (e) => {
  const btn = e.target.closest(".delete-foto");
  if (!btn) return;

  // Guardamos el ID de la foto seleccionada
  const fotoId = btn.dataset.id;
  if (!fotoId) return;
  window.fotoAEliminar = fotoId;

  // Mostrar modal de confirmación
  const overlay = document.getElementById("gs-info-overlay");
  const modal = document.getElementById("gs-info-modal");
  const inner = document.getElementById("gs-info-inner");
  const target = document.getElementById("gs-info-eliminar-foto");

  if (overlay && modal && inner && target) {
    overlay.classList.remove("hidden");
    modal.classList.remove("hidden");
    document.body.classList.add("overflow-hidden");

    // Reiniciar animación "wiggle"
    target.classList.remove("animate-[wiggle_0.3s_ease-in-out]");
    void target.offsetWidth; // ⚡ Forzar reflow
    target.classList.add("animate-[wiggle_0.3s_ease-in-out]");

    requestAnimationFrame(() => {
      overlay.classList.add("opacity-100");
      inner.classList.remove("opacity-0", "scale-95");
      inner.classList.add("opacity-100", "scale-100");
      target.classList.remove("hidden", "opacity-0");
    });
  }
});

// Confirmar eliminación
document.getElementById("gs-confirmar-eliminar")?.addEventListener("click", async () => {
  const fotoId = window.fotoAEliminar;
  if (!fotoId) return;

  // Cerrar modal
  document.querySelectorAll("[data-close-info]").forEach((btn) => btn.click());

  // 🔸 Eliminar foto
  const formData = new FormData();
  formData.append("action", "eliminar_foto_modelo");
  formData.append("foto_id", fotoId);

  try {
    const response = await fetch(ajaxurl, { method: "POST", body: formData });
    const data = await response.json();

    if (data.success) {
      // 🔄 Animación de salida visual
      const fotoDiv = document.querySelector(`.delete-foto[data-id="${fotoId}"]`)?.closest(".group");
      if (fotoDiv) {
        fotoDiv.style.transition = "opacity 0.3s ease, transform 0.3s ease";
        fotoDiv.style.opacity = "0";
        fotoDiv.style.transform = "scale(0.95)";
        setTimeout(async () => {
          fotoDiv.remove();

          // ⚙️ Después de eliminar, intentamos completar el hueco
          const gallery = document.getElementById("galeria-fotos");
          if (gallery) {
            const totalActual = gallery.querySelectorAll(".group").length;
            const offset = totalActual; // pedimos la siguiente según cantidad actual
            const formNext = new FormData();
            formNext.append("action", "get_next_model_photo");
            formNext.append("offset", offset);

            try {
              const resNext = await fetch(ajaxurl, { method: "POST", body: formNext });
              const nextData = await resNext.json();

              if (nextData.success && nextData.data.html) {
                // Crear contenedor temporal
                const temp = document.createElement("div");
                temp.innerHTML = nextData.data.html.trim();
                const newPhoto = temp.firstElementChild;

                // 🔹 Agregar animación de aparición suave
                newPhoto.style.opacity = "0";
                newPhoto.style.transform = "scale(0.92)";
                newPhoto.style.transition = "opacity 0.4s ease, transform 0.4s ease";

                // Insertar al final de la galería
                gallery.insertAdjacentElement("beforeend", newPhoto);

                // Activar animación
                requestAnimationFrame(() => {
                  newPhoto.style.opacity = "1";
                  newPhoto.style.transform = "scale(1)";
                });
              } else {
                console.log("No hay más fotos para rellenar.");
              }
            } catch (err) {
              console.error("❌ Error al intentar rellenar galería:", err);
            }
          }
        }, 300);
      }

      // ⚖️ Penalizar puntos
      const puntosData = new FormData();
      puntosData.append("action", "restar_puntos_por_eliminar_foto");
      const res = await fetch(ajaxurl, { method: "POST", body: puntosData });
      const puntosResp = await res.json();

      if (puntosResp.success) {
        if (typeof gsToast === "function") {
          gsToast("⚠️ Has perdido 20 puntos por eliminar una foto.", "warning");
        } else {
          alert("⚠️ Has perdido 20 puntos por eliminar una foto.");
        }
      }
    } else {
      alert("⚠️ " + (data.data?.message || "Error al eliminar la foto"));
    }
  } catch (err) {
    console.error("❌ Error al eliminar:", err);
    alert("Error al eliminar la foto.");
  }
});


  /******************************************************
   * 🚀 Inicializar galería al cargar
   ******************************************************/
  cargarFotos(1);
});
