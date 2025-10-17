<?php
// Evitar acceso directo
if ( ! defined( 'ABSPATH' ) ) exit;

$current_user = wp_get_current_user();
?>

<!-- 🧩 CONTENEDOR PRINCIPAL -->
<div class="min-h-screen bg-[var(--color-blanco-bajo)] py-10">
  <div class="w-[94%] mx-auto grid grid-cols-1 md:grid-cols-6 gap-6">
<?php
$view = isset($_GET['view']) ? sanitize_text_field($_GET['view']) : 'perfil';
?>

<aside class="md:col-span-1 bg-white rounded-lg shadow p-4 flex flex-col space-y-3 h-max">

  <a href="<?php echo esc_url( home_url('/') ); ?>" 
     class="block text-[15px] py-2 px-3 rounded-md text-center transition hover:bg-gray-50 border"
     style="color: var(--color-tx-cafe); border-color: var(--color-borde);">
    Volver
  </a>

  <!-- Mi cuenta -->
  <a href="<?php echo esc_url( site_url('/mi-cuenta/') ); ?>"
     class="block text-[15px] py-2 px-3 rounded-md text-center font-medium transition 
            <?php echo ($view === 'perfil') 
              ? 'text-white font-semibold' 
              : 'text-[var(--color-tx-cafe)] hover:bg-gray-50 border'; ?>"
     style="<?php echo ($view === 'perfil') 
              ? 'background-color: var(--color-azul-pr);' 
              : 'border-color: var(--color-borde);'; ?>">
    Mi cuenta
  </a>

  <!-- Actividad -->
  <a href="<?php echo esc_url( site_url('/mi-cuenta/?view=actividad') ); ?>"
     class="block text-[15px] py-2 px-3 rounded-md text-center font-medium transition 
            <?php echo ($view === 'actividad') 
              ? 'text-white font-semibold' 
              : 'text-[var(--color-tx-cafe)] hover:bg-gray-50 border'; ?>"
     style="<?php echo ($view === 'actividad') 
              ? 'background-color: var(--color-azul-pr);' 
              : 'border-color: var(--color-borde);'; ?>">
    Actividad
  </a>

  <!-- Puntos -->
  <a href="<?php echo esc_url( site_url('/mi-cuenta/?view=puntos') ); ?>"
     class="block text-[15px] py-2 px-3 rounded-md text-center font-medium transition 
            <?php echo ($view === 'puntos') 
              ? 'text-white font-semibold' 
              : 'text-[var(--color-tx-cafe)] hover:bg-gray-50 border'; ?>"
     style="<?php echo ($view === 'puntos') 
              ? 'background-color: var(--color-azul-pr);' 
              : 'border-color: var(--color-borde);'; ?>">
    Puntos
  </a>

  <!-- Cerrar sesión -->
  <form method="POST" action="<?php echo wp_logout_url( home_url('/') ); ?>">
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
  $view = isset($_GET['view']) ? sanitize_text_field($_GET['view']) : 'perfil';
  $user = wp_get_current_user();
  $is_model = in_array('modelo', (array) $user->roles);

  if ($view === 'puntos') {
      get_template_part('template-parts/modals/account-points');
  } elseif ($view === 'actividad') {
      get_template_part('template-parts/account/account-activity');
  } else {
      if ($is_model) {
          get_template_part('template-parts/account/account-model-profile');
      } else {
          get_template_part('template-parts/account/account-user-profile');
      }
  }
  ?>
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
