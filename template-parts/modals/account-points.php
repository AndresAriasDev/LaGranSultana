<?php
// Evitar acceso directo
if (!defined('ABSPATH')) exit;
?>

<!-- 🎯 MÓDULO DE PUNTOS -->
<div class="min-h-screen bg-[var(--color-blanco-bajo)] py-10">
  <div class="w-[94%] mx-auto grid grid-cols-1 md:grid-cols-6 gap-6">

    <!-- 📁 PANEL IZQUIERDO -->
    <?php get_template_part('template-parts/account/account-sidebar'); ?>

    <!-- 👑 CONTENIDO PRINCIPAL -->
    <main class="md:col-span-4 flex flex-col space-y-6">
      <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 transition-all">
        
        <!-- 🔹 Encabezado -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
          <h2 class="text-2xl font-bold text-gray-800">Puntos acumulados</h2>

          <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 px-5 py-2 rounded-lg shadow-sm">
            <span class="text-3xl font-extrabold text-gray-800">150</span>
            <span class="text-2xl">🎉</span>
          </div>
        </div>

        <!-- 🔸 Descripción -->
        <p class="text-sm text-gray-600 mt-3">
          Canjea tus puntos por recompensas disponibles según tu progreso.
        </p>

        <!-- 🧱 Grid de recompensas -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-5 mt-8">

          <!-- 💰 Premio bloqueado -->
          <div class="flex flex-col items-center justify-center border border-gray-200 bg-gray-50 rounded-lg py-6 px-4 text-center text-gray-400 select-none">
            <span class="text-xl font-bold">C$50</span>
            <span class="text-[13px] mt-1">Necesitas 140 puntos</span>
            <div class="mt-2 text-gray-400 text-sm flex items-center gap-1">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11V17M12 7H12.01M5.07 21h13.86A2.07 2.07 0 0021 18.93V5.07A2.07 2.07 0 0018.93 3H5.07A2.07 2.07 0 003 5.07v13.86A2.07 2.07 0 005.07 21z" />
              </svg>
              Bloqueado
            </div>
          </div>

          <!-- 💎 Premio disponible -->
          <div data-open-info="gs-info-reward" data-reward-name="C$100"
               class="flex flex-col items-center justify-center border border-gray-200 rounded-lg py-6 px-4 text-center cursor-pointer hover:border-[var(--color-amarillo-pr)] hover:shadow-md transition">
            <span class="text-xl font-bold text-gray-800">C$100</span>
            <span class="text-[13px] mt-1 text-gray-500">Toca para obtener</span>
          </div>

          <!-- 🏆 Premio bloqueado -->
          <div class="flex flex-col items-center justify-center border border-gray-200 bg-gray-50 rounded-lg py-6 px-4 text-center text-gray-400 select-none">
            <span class="text-xl font-bold">C$150</span>
            <span class="text-[13px] mt-1">Necesitas 450 puntos</span>
            <div class="mt-2 text-gray-400 text-sm flex items-center gap-1">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11V17M12 7H12.01M5.07 21h13.86A2.07 2.07 0 0021 18.93V5.07A2.07 2.07 0 0018.93 3H5.07A2.07 2.07 0 003 5.07v13.86A2.07 2.07 0 005.07 21z" />
              </svg>
              Bloqueado
            </div>
          </div>

        </div>
      </section>
    </main>

    <!-- 🧭 PANEL DERECHO -->
    <?php get_template_part('template-parts/account/account-sidebar-right'); ?>

  </div>

  <!-- 🎁 Contenido del modal (solo mensaje dentro del modal global) -->
  <?php get_template_part('template-parts/modals/puntos-recompensa-info'); ?>

</div>
