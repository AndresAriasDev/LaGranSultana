<?php
if (!defined('ABSPATH')) exit;

/**
 * 🎞️ Modal de medios (foto grande / video)
 * Independiente del sistema info-modal
 */
add_action('wp_body_open', function () { ?>
<div id="gs-media-modal"
     class="fixed inset-0 z-[9999] hidden opacity-0 transition-opacity duration-300 ease-out flex items-center justify-center bg-black/90 backdrop-blur-sm">

  <!-- Contenedor principal con altura total -->
  <div id="gs-foto-grande"
       class="relative flex flex-col items-center justify-center w-full h-screen z-[20] overflow-hidden">

    <!-- 📸 Imagen (tamaño fijo uniforme con contador de likes y controles) -->
    <div class="relative w-[90vw] max-w-[500px] h-[75vh] lg:h-[90vh] flex items-center justify-center">

    <!-- ❤️ Contador de Likes (arriba a la derecha) -->
    <div
      id="gs-foto-likes"
      class="absolute top-4 right-4 bg-white text-[var(--color-tx-cafe)] px-6 py-3 rounded-full shadow-2xl flex items-center justify-center z-[45]"
    >
      <span
      id="gs-foto-likes-num"
      class="font-[600] font-[Inter] text-base tracking-tight select-none transition-transform duration-300 ease-out"
    >0</span>
    </div>



      <!-- 📷 Imagen -->
      <img
        id="gs-foto-img"
        src=""
        alt="Foto del modelo"
        class="w-full h-full object-cover rounded-2xl shadow-2xl transition-all duration-500 ease-in-out select-none z-[30]"
      />

      <!-- ⬅️➡️ Flechas + ❤️ Corazón -->
      <div
        class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center justify-between gap-8 bg-white/95 rounded-full px-6 py-3 shadow-2xl backdrop-blur-sm z-[40]"
        style="min-width: 260px;"
      >
        <!-- ⬅️ Flecha izquierda -->
        <button
          id="gs-foto-prev"
          class="w-10 h-10 flex items-center justify-center transition hover:scale-110"
        >
          <img
            src="/wp-content/uploads/2025/10/flecha-izquierda-icon.png"
            alt="Anterior"
            class="w-6 h-6 select-none pointer-events-none"
          />
        </button>

        <!-- ❤️ Corazón -->
        <button
          id="gs-foto-like-btn"
          class="flex items-center justify-center transform transition hover:scale-110"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill=""
            class="w-10 h-10 drop-shadow-md"
          >
            <path
              d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5
              2 6 4 4 6.5 4c1.74 0 3.41 1 4.5 2.09C12.09 5 13.76 4 15.5 4
              18 4 20 6 20 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"
            />
          </svg>
        </button>

        <!-- ➡️ Flecha derecha -->
        <button
          id="gs-foto-next"
          class="w-10 h-10 flex items-center justify-center transition hover:scale-110"
        >
          <img
            src="/wp-content/uploads/2025/10/flecha-derecha-icon.png"
            alt="Siguiente"
            class="w-6 h-6 select-none pointer-events-none"
          />
        </button>
      </div>

    </div>

  </div>
</div>
<?php });
