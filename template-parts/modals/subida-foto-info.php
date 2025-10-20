<?php 

?>

<!-- 🖼️ Modal de Subida de Foto -->
<div id="gs-info-subida-foto" class="hidden opacity-0 transition-opacity duration-300 text-center py-10 px-6">

  <!-- ✅ Ícono centrado -->
  <div class="flex justify-center mb-5">
    <div class="bg-green-100 p-3 rounded-full flex items-center justify-center shadow-inner">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
      </svg>
    </div>
  </div>

  <!-- 🎉 Mensaje de éxito -->
  <h3 class="text-xl font-bold text-gray-800 mb-2">¡Foto subida con éxito!</h3>
  <p class="text-gray-600 mb-4">Ganaste <span class="text-green-600 font-semibold">+5 puntos</span></p>

  <!-- 💬 Mensaje motivacional -->
  <p class="text-sm text-gray-500 mb-4">💡 <strong>Empieza a generar likes</strong> compartiendo tu perfil con tus seguidores.</p>

  <!-- 🔗 Link y botón copiar -->
  <div class="bg-gray-100 px-3 py-2 rounded-lg text-sm flex items-center justify-between gap-2 mb-6 shadow-inner border border-gray-200 max-w-xs mx-auto">
    <span id="gs-profile-link" class="truncate text-gray-700 text-left"><?php echo esc_url(site_url('/modelo/' . wp_get_current_user()->user_nicename)); ?></span>
    <button id="gs-copy-link" class="text-blue-600 font-semibold hover:underline whitespace-nowrap">Copiar</button>
  </div>

  <!-- ✅ Botón aceptar -->
  <button data-close-info class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2 rounded-lg font-medium shadow-md transition">
    Aceptar
  </button>
</div>

