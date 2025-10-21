document.addEventListener("DOMContentLoaded", () => {
  console.log("✅ Galería JS inicializado correctamente");

  const uploadInput = document.getElementById("btn-subir-foto");
  const gallery = document.getElementById("galeria-fotos");

  if (!uploadInput || !gallery) {
    console.warn("⚠️ No se encontró el input o la galería en esta vista.");
    return;
  }

  /******************************************************
   * 📸 SUBIR FOTO
   ******************************************************/
  uploadInput.addEventListener("change", async (e) => {
    console.log("📸 Archivo seleccionado para subir...");
    const file = e.target.files[0];
    if (!file) {
      console.warn("⚠️ No se seleccionó ningún archivo.");
      return;
    }

    // Obtener modal global
    const modal = document.getElementById("gs-info-modal");
    const overlay = document.getElementById("gs-info-overlay");
    const inner = document.getElementById("gs-info-inner");
    const modalContent = document.getElementById("gs-info-subida-foto");
    const loader = document.getElementById("gs-upload-loader");
    const success = document.getElementById("gs-upload-success");

    if (!modal || !overlay || !inner || !modalContent) {
      console.warn("⚠️ Modal global no encontrado. Revisa inc/modals/info-modal.php.");
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

    // Preparar datos
    const formData = new FormData();
    formData.append("action", "subir_foto_modelo");
    formData.append("foto", file);

    try {
      console.log("🚀 Enviando imagen al servidor...");
      const response = await fetch(ajaxurl, { method: "POST", body: formData });
      const data = await response.json();

      console.log("📩 Respuesta recibida:", data);

      if (data.success) {
        loader?.classList.add("hidden");
        success?.classList.remove("hidden");

        // Insertar nueva foto
        const newDiv = document.createElement("div");
        newDiv.classList.add(
          "relative", "group", "overflow-hidden",
          "rounded-2xl", "shadow-sm", "hover:shadow-xl",
          "transition-all", "duration-300"
        );
        newDiv.innerHTML = `
          <div class="aspect-square w-full h-auto relative overflow-hidden rounded-2xl">
            <img src="${data.data.image_url}" class="w-full h-full object-cover rounded-2xl" />
            <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col items-center justify-center">
              <p class="text-white text-lg font-semibold tracking-wide">❤️ 0 <span class="text-sm font-normal ml-1">Likes</span></p>
            </div>
            <button class="delete-foto absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-all duration-300 transform hover:scale-110 cursor-pointer" data-id="${data.data.foto_id}" title="Eliminar esta foto">
              <img src="/wp-content/uploads/2025/10/basura-blanco.png" alt="Eliminar" class="w-6 h-6">
            </button>
          </div>`;
        gallery.prepend(newDiv);

        // Dar puntos
        const puntosData = new FormData();
        puntosData.append("action", "sumar_puntos_modelo");
        puntosData.append("puntos", "5");
        await fetch(ajaxurl, { method: "POST", body: puntosData });
      } else {
        alert("⚠️ " + (data.data?.message || "Error desconocido"));
      }
    } catch (err) {
      console.error("❌ Error al subir la foto:", err);
      alert("Error al subir la foto. Ver consola.");
    }
  });

  /******************************************************
   * 🧷 COPIAR LINK
   ******************************************************/
document.addEventListener("click", (e) => {
  if (e.target.id === "gs-copy-link") {
    const link = document.getElementById("gs-profile-link")?.innerText;
    if (!link) return;

    // ✅ Copiar de forma segura (compatibilidad total)
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
 * 🎉 Toast al cerrar el modal de éxito
 ******************************************************/
document.addEventListener("click", (e) => {
  if (e.target.matches("[data-close-info]")) {
    // Mostrar toast de puntos si el modal visible era el de subida de foto
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
   * 🗑️ ELIMINAR FOTO
   ******************************************************/
  document.addEventListener("click", async (e) => {
    const btn = e.target.closest(".delete-foto");
    if (!btn) return;

    const fotoId = btn.dataset.id;
    if (!fotoId) return;

    if (!confirm("¿Seguro que quieres eliminar esta foto?")) return;

    const formData = new FormData();
    formData.append("action", "eliminar_foto_modelo");
    formData.append("foto_id", fotoId);

    try {
      const response = await fetch(ajaxurl, { method: "POST", body: formData });
      const data = await response.json();

      if (data.success) {
        const fotoDiv = btn.closest(".group");
        fotoDiv.style.transition = "opacity 0.3s ease, transform 0.3s ease";
        fotoDiv.style.opacity = "0";
        fotoDiv.style.transform = "scale(0.95)";
        setTimeout(() => fotoDiv.remove(), 300);
      } else {
        alert("⚠️ " + (data.data?.message || "Error al eliminar la foto"));
      }
    } catch (err) {
      console.error("❌ Error al eliminar:", err);
      alert("Error al eliminar la foto.");
    }
  });
});

/********/

/******************************************************
 * 📸 PAGINACIÓN DE GALERÍA AJAX
 ******************************************************/
document.addEventListener("DOMContentLoaded", () => {
  const galeria = document.getElementById("galeria-fotos");
  const paginacion = document.getElementById("galeria-paginacion");
  if (!galeria || !paginacion) return;

  // ⚙️ Configuración inicial
  let currentPage = 1;
  const perPage = 8;

  // 🚀 Inicializar al cargar
  inicializarPaginacion();

  /******************************************************
   * 🔹 Cargar fotos por página vía AJAX
   ******************************************************/
  async function cargarFotos(page = 1) {
    const formData = new FormData();
    formData.append("action", "get_modelo_fotos");
    formData.append("page", page);

    try {
      // Mostrar animación de carga
      galeria.style.opacity = "0.3";
      galeria.style.pointerEvents = "none";

      const response = await fetch(ajaxurl, {
        method: "POST",
        body: formData,
      });

      const data = await response.json();
      if (!data.success) throw new Error("Error al obtener las fotos");

      // Reemplazar el contenido del grid
      galeria.innerHTML = data.data.html;

      // Actualizar paginación
      generarPaginacion(data.data.total_pages, data.data.current_page);

      // Animación suave
      galeria.style.opacity = "1";
      galeria.style.pointerEvents = "auto";
      galeria.classList.add("transition-opacity", "duration-300");

      currentPage = data.data.current_page;
    } catch (err) {
      console.error("❌ Error cargando fotos:", err);
      galeria.innerHTML = `<p class="col-span-full text-center text-red-500 py-8">Error al cargar las fotos.</p>`;
    }
  }

  /******************************************************
   * 🔹 Generar botones de paginación
   ******************************************************/
  function generarPaginacion(totalPages, activePage) {
    paginacion.innerHTML = "";

    if (totalPages <= 1) return; // no mostrar si solo hay una página

    const createButton = (num) => {
      const btn = document.createElement("button");
      btn.textContent = num;
      btn.className =
        "px-3 py-1 rounded-md border border-gray-300 text-sm font-medium transition " +
        (num === activePage
          ? "bg-blue-600 text-white shadow-sm"
          : "hover:bg-gray-100 text-gray-700");
      btn.dataset.page = num;
      return btn;
    };

    // Flecha atrás
    if (activePage > 1) {
      const prev = document.createElement("button");
      prev.innerHTML = "←";
      prev.className =
        "px-3 py-1 rounded-md border border-gray-300 text-sm text-gray-700 hover:bg-gray-100";
      prev.dataset.page = activePage - 1;
      paginacion.appendChild(prev);
    }

    // Botones numéricos
    for (let i = 1; i <= totalPages; i++) {
      paginacion.appendChild(createButton(i));
    }

    // Flecha adelante
    if (activePage < totalPages) {
      const next = document.createElement("button");
      next.innerHTML = "→";
      next.className =
        "px-3 py-1 rounded-md border border-gray-300 text-sm text-gray-700 hover:bg-gray-100";
      next.dataset.page = activePage + 1;
      paginacion.appendChild(next);
    }
  }

  /******************************************************
   * 🔹 Escuchar clics en botones de paginación
   ******************************************************/
  paginacion.addEventListener("click", (e) => {
    const btn = e.target.closest("button[data-page]");
    if (!btn) return;

    const newPage = parseInt(btn.dataset.page);
    if (newPage === currentPage) return;

    cargarFotos(newPage);
    document.getElementById("galeria-loader")?.classList.add("hidden");
    window.scrollTo({ top: galeria.offsetTop - 100, behavior: "smooth" });
  });

  /******************************************************
   * 🔹 Inicializar al cargar
   ******************************************************/
  function inicializarPaginacion() {
    // Pedimos total inicial (página 1)
    cargarFotos(1);
  }
});
