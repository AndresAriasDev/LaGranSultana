<?php
// Evitar acceso directo
if ( ! defined( 'ABSPATH' ) ) exit;

$current_user = wp_get_current_user();
?>

<!-- 🧩 CONTENEDOR PRINCIPAL -->
<div class="min-h-screen bg-[var(--color-blanco-bajo)] py-10">

  <div class="w-[94%] mx-auto grid grid-cols-1 md:grid-cols-6 gap-6">

    <!-- 📁 PANEL IZQUIERDO -->
    <aside class="md:col-span-1 bg-white rounded-lg shadow p-4 flex flex-col space-y-3 h-max">
      <a href="<?php echo esc_url( home_url('/') ); ?>" 
         class="block text-[15px] py-2 px-3 rounded-md text-center transition hover:bg-gray-50 border"
         style="color: var(--color-tx-cafe); border-color: var(--color-borde);">
        Volver
      </a>

      <a href="#" 
         class="block text-[15px] py-2 px-3 rounded-md text-center font-semibold text-white"
         style="background-color: var(--color-azul-pr);">
        Mi cuenta
      </a>

      <a href="#" 
         class="block text-[15px] py-2 px-3 rounded-md text-center transition hover:bg-gray-50 border"
         style="color: var(--color-tx-cafe); border-color: var(--color-borde);">
        Actividad
      </a>

      <form method="POST" action="<?php echo wp_logout_url( home_url('/') ); ?>">
        <button type="submit" 
          class="w-full text-[15px] py-2 px-3 rounded-md text-center font-medium transition hover:opacity-90"
          style="background-color: var(--color-amarillo-pr); color: var(--color-tx-blanco);">
          Cerrar sesión
        </button>
      </form>
    </aside>

    <!-- 👤 PANEL CENTRAL -->
    <main class="md:col-span-4 flex flex-col space-y-6">

      <!-- 🧍 Perfil -->
      <section class="bg-white rounded-lg shadow p-6 flex flex-col items-center space-y-4">
        <div class="relative">
          <img src="<?php echo esc_url( get_avatar_url( $current_user->ID, ['size' => 120] ) ); ?>"
               alt="Foto de perfil"
               class="w-28 h-28 rounded-full object-cover border border-gray-200">
          <button class="absolute bottom-1 right-1 bg-[var(--color-azul-pr)] text-white text-xs px-2 py-1 rounded-full hover:opacity-90">
            Cambiar
          </button>
        </div>

        <div class="text-center">
          <h2 class="text-xl font-semibold text-gray-800"><?php echo esc_html( $current_user->display_name ); ?></h2>
          <p class="text-sm text-gray-500">Departamento / Extranjero</p>
        </div>
      </section>
      <?php
// Obtenemos el progreso del perfil y los puntos actuales
$completion = gs_get_profile_completion($current_user->ID);
$points     = gs_get_user_points($current_user->ID);

// Determinar color según porcentaje (para un toque visual)
if ($completion < 50) {
  $bar_color = 'bg-red-400';
} elseif ($completion < 80) {
  $bar_color = 'bg-yellow-400';
} else {
  $bar_color = 'bg-green-500';
}
?>

<!-- 🧮 PROGRESO Y PUNTOS -->
<section class="relative bg-white rounded-lg shadow p-6 mb-6">

<button data-open-info="gs-info-puntos"
        class="absolute top-4 right-4 p-1 rounded-full hover:bg-gray-100 transition"
        aria-label="Información de puntos">
    <img src="<?php echo esc_url( get_site_url() . '/wp-content/uploads/2025/10/usuario-cafe.png' ); ?>" 
         alt="info" 
         class="h-5 w-5 opacity-80 hover:opacity-100 transition">
</button>



  <!-- 🔹 Porcentaje -->
  <div class="flex items-center justify-between mb-4">
    <span class="text-sm text-gray-500"><?php echo intval($completion); ?>%</span>
  </div>

  <!-- Barra de progreso -->
  <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden mb-3">
    <div class="<?php echo esc_attr($bar_color); ?> h-3 transition-all duration-500" style="width: <?php echo esc_attr($completion); ?>%;"></div>
  </div>

  <!-- Puntos -->
  <div class="flex items-center justify-between mt-4">
    <span class="text-sm text-gray-600">Puntos acumulados:</span>
    <span class="text-lg font-semibold" style="color: var(--color-amarillo-pr);">
      <?php echo intval($points); ?> pts
    </span>
  </div>

</section>


      <!-- 🧾 Información del perfil -->
      <section class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Información personal</h3>
        <form class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <label class="text-sm text-gray-600">Nombre</label>
            <input type="text" class="w-full mt-1 border rounded-md px-3 py-2 focus:outline-none"
                   placeholder="Nombre completo">
          </div>

          <div>
            <label class="text-sm text-gray-600">Correo</label>
            <input type="email" class="w-full mt-1 border rounded-md px-3 py-2 bg-gray-100 cursor-not-allowed"
                   value="<?php echo esc_attr( $current_user->user_email ); ?>" readonly>
          </div>

          <div>
            <label class="text-sm text-gray-600">Teléfono</label>
            <input type="text" class="w-full mt-1 border rounded-md px-3 py-2 focus:outline-none"
                   placeholder="Número de teléfono">
          </div>

          <div>
            <label class="text-sm text-gray-600">Dirección</label>
            <input type="text" class="w-full mt-1 border rounded-md px-3 py-2 focus:outline-none"
                   placeholder="Dirección completa">
          </div>

          <div>
            <label class="text-sm text-gray-600">Departamento</label>
            <select class="w-full mt-1 border rounded-md px-3 py-2 focus:outline-none">
              <option value="">Seleccionar...</option>
              <option>Managua</option>
              <option>Granada</option>
              <option>León</option>
              <option>Masaya</option>
              <option>Chontales</option>
              <option>Estelí</option>
              <option>Rivas</option>
              <option>Carazo</option>
              <option>Matagalpa</option>
              <option>Jinotega</option>
              <option>RAAN</option>
              <option>RAAS</option>
              <option>Extranjero</option>
            </select>
          </div>

          <div>
            <label class="text-sm text-gray-600">Fecha de nacimiento</label>
            <input type="date" class="w-full mt-1 border rounded-md px-3 py-2 focus:outline-none">
          </div>

          <div class="md:col-span-2">
            <button type="submit"
              class="w-full mt-4 py-3 rounded-md font-medium text-white transition hover:opacity-90"
              style="background-color: var(--color-amarillo-pr);">
              Guardar cambios
            </button>
          </div>
        </form>
      </section>
    </main>

    <!-- 🧭 PANEL DERECHO -->
    <aside class="md:col-span-1 flex flex-col space-y-4">
      <div class="bg-white rounded-lg shadow p-4 text-center">
        <h4 class="font-semibold text-gray-800 mb-2">Mis reservas</h4>
        <p class="text-sm text-gray-500">No tienes reservas recientes.</p>
      </div>

      <div class="bg-white rounded-lg shadow p-4 text-center">
        <h4 class="font-semibold text-gray-800 mb-2">Favoritos</h4>
        <p class="text-sm text-gray-500">Aún no has guardado productos.</p>
      </div>

      <div class="bg-white rounded-lg shadow p-4 text-center">
        <h4 class="font-semibold text-gray-800 mb-2">Configuración</h4>
        <button class="text-sm text-[var(--color-azul-pr)] hover:underline">Cambiar contraseña</button>
      </div>
    </aside>

  </div>
</div>
