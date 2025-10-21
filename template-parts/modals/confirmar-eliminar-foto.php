<?php ?>

<!-- 🧩 Modal: Confirmar eliminación de foto -->
<div id="gs-info-eliminar-foto" class="hidden opacity-0">
  <div class="p-8 text-center animate-[wiggle_0.3s_ease-in-out]">
    
    <!-- 🔴 Ícono con fondo suave -->
    <div class="w-16 h-16 mx-auto mb-5 rounded-full bg-[var(--color-rojo-pr)] bg-opacity-10 flex items-center justify-center">
      <img src="/wp-content/uploads/2025/10/basura-blanco.png" alt="Eliminar" class="w-7 h-7 opacity-90">
    </div>

    <!-- 🧠 Título y mensaje -->
    <h2 class="text-xl font-semibold text-gray-800 mb-2">¿Eliminar esta foto?</h2>
    <p class="text-sm text-gray-600 mb-6 leading-relaxed">
      Si eliminas esta foto, se te restarán 
      <span class="font-semibold text-[var(--color-rojo-pr)]">20 puntos</span> 
      de tu total actual. Esta acción no se puede deshacer.
    </p>

    <!-- ⚙️ Botones -->
    <div class="flex justify-center gap-3">
      <button data-close-info
        class="px-5 py-2 rounded-lg text-sm font-medium border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
        Cancelar
      </button>
      <button id="gs-confirmar-eliminar" 
        class="px-5 py-2 rounded-lg text-sm font-semibold text-white bg-[var(--color-rojo-pr)] hover:bg-red-600 transition">
        Sí, eliminar
      </button>
    </div>
  </div>

  <!-- 🌀 Animación personalizada -->
  <style>
    @keyframes wiggle {
      0%, 100% { transform: rotate(0deg); }
      15% { transform: rotate(-3deg); }
      30% { transform: rotate(3deg); }
      45% { transform: rotate(-2deg); }
      60% { transform: rotate(2deg); }
      75% { transform: rotate(-1deg); }
      90% { transform: rotate(1deg); }
    }
  </style>
</div>

