<?php
// Evitar acceso directo
if (!defined('ABSPATH')) exit;

$current_user = wp_get_current_user();
$view = isset($_GET['view']) ? sanitize_text_field($_GET['view']) : 'perfil';
?>

<!-- 🧩 CONTENEDOR PRINCIPAL -->
<div class="min-h-screen bg-[var(--color-blanco-bajo)] py-10">
  <div class="w-[94%] mx-auto grid grid-cols-1 md:grid-cols-6 gap-6">

    <!-- 📁 PANEL IZQUIERDO -->
    <aside class="md:col-span-1 bg-white rounded-lg shadow p-4 flex flex-col space-y-3 h-max">

      <a href="<?php echo esc_url(home_url('/')); ?>" 
         class="block text-[15px] py-2 px-3 rounded-md text-center transition hover:bg-gray-50 border"
         style="color: var(--color-tx-cafe); border-color: var(--color-borde);">
        Volver
      </a>

      <!-- Perfil -->
      <a href="<?php echo esc_url(site_url('/mi-cuenta/?view=perfil')); ?>"
         class="block text-[15px] py-2 px-3 rounded-md text-center font-medium transition 
               <?php echo ($view === 'perfil') 
                    ? 'text-white font-semibold' 
                    : 'text-[var(--color-tx-cafe)] hover:bg-gray-50 border'; ?>"
         style="<?php echo ($view === 'perfil') 
                    ? 'background-color: var(--color-azul-pr);' 
                    : 'border-color: var(--color-borde);'; ?>">
        Perfil
      </a>

      <!-- Galería -->
      <a href="<?php echo esc_url(site_url('/mi-cuenta/?view=galeria')); ?>"
         class="block text-[15px] py-2 px-3 rounded-md text-center font-medium transition 
               <?php echo ($view === 'galeria') 
                    ? 'text-white font-semibold' 
                    : 'text-[var(--color-tx-cafe)] hover:bg-gray-50 border'; ?>"
         style="<?php echo ($view === 'galeria') 
                    ? 'background-color: var(--color-azul-pr);' 
                    : 'border-color: var(--color-borde);'; ?>">
        Galería
      </a>

      <!-- Referidos -->
      <a href="<?php echo esc_url(site_url('/mi-cuenta/?view=referidos')); ?>"
         class="block text-[15px] py-2 px-3 rounded-md text-center font-medium transition 
               <?php echo ($view === 'referidos') 
                    ? 'text-white font-semibold' 
                    : 'text-[var(--color-tx-cafe)] hover:bg-gray-50 border'; ?>"
         style="<?php echo ($view === 'referidos') 
                    ? 'background-color: var(--color-azul-pr);' 
                    : 'border-color: var(--color-borde);'; ?>">
        Mis referidos
      </a>

      <!-- Puntos -->
      <a href="<?php echo esc_url(site_url('/mi-cuenta/?view=puntos')); ?>"
         class="block text-[15px] py-2 px-3 rounded-md text-center font-medium transition 
               <?php echo ($view === 'puntos') 
                    ? 'text-white font-semibold' 
                    : 'text-[var(--color-tx-cafe)] hover:bg-gray-50 border'; ?>"
         style="<?php echo ($view === 'puntos') 
                    ? 'background-color: var(--color-azul-pr);' 
                    : 'border-color: var(--color-borde);'; ?>">
        Puntos
      </a>

      <!-- Configuración -->
      <a href="<?php echo esc_url(site_url('/mi-cuenta/?view=configuracion')); ?>"
         class="block text-[15px] py-2 px-3 rounded-md text-center font-medium transition 
               <?php echo ($view === 'configuracion') 
                    ? 'text-white font-semibold' 
                    : 'text-[var(--color-tx-cafe)] hover:bg-gray-50 border'; ?>"
         style="<?php echo ($view === 'configuracion') 
                    ? 'background-color: var(--color-azul-pr);' 
                    : 'border-color: var(--color-borde);'; ?>">
        Configuración
      </a>

      <!-- Cerrar sesión -->
      <form method="POST" action="<?php echo wp_logout_url(home_url('/')); ?>">
        <button type="submit" 
          class="w-full text-[15px] py-2 px-3 rounded-md text-center font-medium transition hover:opacity-90"
          style="background-color: var(--color-amarillo-pr); color: var(--color-tx-blanco);">
          Cerrar sesión
        </button>
      </form>
    </aside>

    <!-- 👑 CONTENIDO CENTRAL DINÁMICO -->
    <main class="md:col-span-4 flex flex-col space-y-6">
      <?php
      switch ($view) {
        case 'galeria':
          get_template_part('template-parts/account/model/account-model-gallery');
          break;

        case 'referidos':
          get_template_part('template-parts/account/model/account-model-referrals');
          break;

        case 'puntos':
          get_template_part('template-parts/account/model/account-model-points');
          break;

        case 'configuracion':
          get_template_part('template-parts/account/model/account-model-settings');
          break;

        default:
          get_template_part('template-parts/account/model/account-model-profile');
          break;
      }
      ?>
    </main>

    <!-- 🧭 PANEL DERECHO -->
    <aside class="md:col-span-1 flex flex-col space-y-4">
      <div class="bg-white rounded-lg shadow p-4 text-center">
        <h4 class="font-semibold text-gray-800 mb-2">Estadísticas</h4>
        <p class="text-sm text-gray-500">Próximamente: vistas y seguidores.</p>
      </div>

      <div class="bg-white rounded-lg shadow p-4 text-center">
        <h4 class="font-semibold text-gray-800 mb-2">Soporte</h4>
        <button class="text-sm text-[var(--color-azul-pr)] hover:underline">Contactar</button>
      </div>
    </aside>
  </div>
</div>
