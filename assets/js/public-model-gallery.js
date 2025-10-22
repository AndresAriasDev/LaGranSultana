document.addEventListener("DOMContentLoaded", () => {
  const gallery = document.getElementById("public-gallery");
  const paginacion = document.getElementById("public-gallery-pagination");
  const loader = document.getElementById("public-gallery-loader");
  if (!gallery || !paginacion) return;

  const modelId = gallery.dataset.modelId;

  // 🔹 Función para cargar fotos
  function cargarFotos(page = 1) {
    const formData = new FormData();
    formData.append("action", "get_modelo_fotos_public");
    formData.append("page", page);
    formData.append("model_id", modelId);
    formData.append("nonce", gs_public_gallery.nonce); // ✅ incluimos el nonce

    loader?.classList.remove("hidden");

    fetch(gs_public_gallery.ajaxurl, {
      method: "POST",
      body: formData,
    })
      .then((res) => {
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return res.json();
      })
      .then((data) => {
        loader?.classList.add("hidden");

        if (!data.success) {
          console.error("❌ Error servidor:", data.data?.message || "Desconocido");
          throw new Error("Error al cargar fotos");
        }

        gallery.innerHTML = data.data.html;
        generarPaginacion(data.data.total_pages, data.data.current_page);
      })
      .catch((err) => {
        loader?.classList.add("hidden");
        console.error("❌ Error galería pública:", err);
      });
  }

  // 🔹 Crear botones de paginación
  function generarPaginacion(totalPages, activePage) {
    paginacion.innerHTML = "";
    if (totalPages <= 1) return;

    const makeBtn = (num, label = null) => {
      const btn = document.createElement("button");
      btn.textContent = label || num;
      btn.dataset.page = num;
      btn.className =
        "px-3 py-1 rounded-md border text-sm font-medium transition " +
        (num === activePage
          ? "bg-[var(--color-azul-pr)] text-white border-[var(--color-azul-pr)] shadow-sm"
          : "bg-gray-100 text-gray-700 hover:bg-gray-200");
      return btn;
    };

    if (activePage > 1) paginacion.appendChild(makeBtn(activePage - 1, "←"));
    for (let i = 1; i <= totalPages; i++) paginacion.appendChild(makeBtn(i));
    if (activePage < totalPages) paginacion.appendChild(makeBtn(activePage + 1, "→"));
  }

  // 🔹 Click en paginación
  paginacion.addEventListener("click", (e) => {
    const btn = e.target.closest("button[data-page]");
    if (!btn) return;
    const page = parseInt(btn.dataset.page);
    cargarFotos(page);
    window.scrollTo({ top: gallery.offsetTop - 100, behavior: "smooth" });
  });

  // 🚀 Inicializar
  cargarFotos(1);
});
