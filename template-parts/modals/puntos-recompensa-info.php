<?php ?>
<div id="gs-info-reward" class="hidden opacity-0 transition-opacity duration-300">
  <div class="p-8 text-center">
    <h2 class="text-xl md:text-2xl font-extrabold text-[var(--color-azul-pr)] mb-3">
      🎁 ¡Has desbloqueado una recompensa!
    </h2>
    <p id="gs-points-reward-name" class="text-sm text-gray-600 leading-relaxed max-w-sm mx-auto">
      Has desbloqueado el premio <strong>C$100</strong>. ¿Deseas reclamarlo ahora?
    </p>
    <div class="text-center mt-8 flex justify-center gap-3">
      <button id="gs-points-confirm"
              class="px-6 py-2.5 rounded-md font-medium text-white hover:opacity-90 transition shadow-sm"
              style="background-color: var(--color-amarillo-pr);">
        Sí, reclamar
      </button>
      <button data-close-info
              class="px-6 py-2.5 rounded-md font-medium border text-gray-700 hover:bg-gray-50 transition">
        Cancelar
      </button>
    </div>
  </div>
</div>
