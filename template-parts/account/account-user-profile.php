<?php
// Evitar acceso directo
if (!defined('ABSPATH')) exit;

$current_user = wp_get_current_user();

// 🧩 Importar campos comunes reutilizables
require_once get_template_directory() . '/inc/users/partials/fields-common.php';
?>

<!-- 🧍 PERFIL -->
<section class="bg-white rounded-lg shadow p-6 flex flex-col items-center space-y-4">
  <div class="relative">
    <?php
      $avatar_url = get_user_meta($current_user->ID, 'gs_profile_picture', true);
      if (empty($avatar_url)) {
        $avatar_url = get_avatar_url($current_user->ID, ['size' => 120]);
      }
    ?>
    <img id="gs-user-avatar"
         src="<?php echo esc_url($avatar_url); ?>"
         alt="Foto de perfil"
         class="w-28 h-28 rounded-full object-cover border border-gray-200 transition-all duration-300">

    <!-- Botón de cambio de imagen -->
    <button type="button"
            id="gs-change-avatar-btn"
            class="absolute bottom-1 right-1 bg-[var(--color-blanco-pr)] border text-white p-1.5 rounded-full hover:opacity-90"
            aria-label="Cambiar foto de perfil"
            style="border-color: var(--color-borde);">
      <img src="<?php echo esc_url(get_site_url() . '/wp-content/uploads/2025/10/camara.png'); ?>"
           alt="Cambiar"
           class="w-4 h-4">
    </button>

    <input type="file" id="gs-avatar-input" accept="image/*" class="hidden">
  </div>

  <div class="text-center">
    <?php
      // Mostrar solo primer nombre y primer apellido
      $full_name = trim(get_user_meta($current_user->ID, 'first_name', true));
      $name_parts = preg_split('/\s+/', $full_name);

      if (count($name_parts) >= 3) {
          $display_name = $name_parts[0] . ' ' . $name_parts[2];
      } elseif (count($name_parts) >= 2) {
          $display_name = $name_parts[0] . ' ' . $name_parts[1];
      } else {
          $display_name = $name_parts[0];
      }
    ?>
    <h2 class="text-xl font-semibold text-gray-800">
      <?php echo esc_html($display_name); ?>
    </h2>
    <p id="user-department" class="text-sm text-gray-500">
      Departamento: <?php echo esc_html(get_user_meta($current_user->ID, 'department', true)); ?>
    </p>
  </div>
</section>

<?php
$profile_data = gs_get_profile_completion($current_user->ID);
$completion   = $profile_data['percentage'];
$points       = gs_get_user_points($current_user->ID);
$has_bonus    = get_user_meta($current_user->ID, 'gs_profile_bonus_awarded', true);
?>

<?php if (!$has_bonus): ?>
<section id="gs-profile-progress-module" class="relative bg-white rounded-lg shadow p-6 mb-8 transition-all">
  <button data-open-info="gs-info-puntos"
          class="absolute top-4 right-4 p-1 rounded-full hover:bg-gray-100 transition"
          aria-label="Información de puntos">
    <img src="<?php echo esc_url(get_site_url() . '/wp-content/uploads/2025/10/usuario-cafe.png'); ?>" 
         alt="info" class="h-5 w-5 opacity-80 hover:opacity-100 transition">
  </button>

  <div class="flex items-center justify-between mb-4">
    <span class="text-sm text-gray-500" id="gs-profile-completion-text"><?php echo intval($completion); ?>%</span>
  </div>

  <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden mb-3">
    <div id="gs-profile-progress-bar" 
         class="h-3 transition-all duration-500 <?php echo ($completion < 50 ? 'bg-red-400' : ($completion < 80 ? 'bg-yellow-400' : 'bg-green-500')); ?>"
         style="width: <?php echo intval($completion); ?>%;"></div>
  </div>

  <div class="flex items-center justify-between mt-4">
    <span class="text-sm text-gray-600">Puntos acumulados:</span>
    <span id="gs-profile-points" class="text-lg font-semibold" style="color: var(--color-amarillo-pr);">
      <?php echo intval($points); ?> pts
    </span>
  </div>
</section>
<?php endif; ?>

<!-- 🧾 INFORMACIÓN PERSONAL -->
<section class="bg-white rounded-xl shadow-sm border border-gray-100 p-7 transition-all">
  <header class="flex items-center gap-2 mb-8 border-b border-gray-100 pb-3">
    <h3 class="text-lg font-semibold text-gray-800">Información personal</h3>
  </header>

  <form id="gs-user-profile-form" class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6">

    <?php
      // ✅ Campos comunes reutilizados
      gs_render_common_profile_fields($current_user->ID, [
        'first_name',
        'phone',
        'department',
        'address',
        'gender'
      ]);
    ?>

    <!-- Correo electrónico (solo lectura) -->
    <div class="flex flex-col">
      <label class="text-[15px] font-medium text-gray-700 mb-1.5">Correo electrónico</label>
      <input type="email" value="<?php echo esc_attr($current_user->user_email); ?>"
             readonly class="gs-input bg-gray-50 cursor-not-allowed text-gray-500">
    </div>

    <!-- Fecha de nacimiento -->
    <div class="flex flex-col">
      <label class="text-[15px] font-medium text-gray-700 mb-1.5">Fecha de nacimiento</label>
      <input id="gs-birthdate" type="text" name="birthdate"
             value="<?php echo esc_attr(get_user_meta($current_user->ID, 'birthdate', true)); ?>"
             class="gs-input" placeholder="Seleccionar fecha">
    </div>

    <!-- Botón -->
    <div class="md:col-span-2 pt-3">
      <button type="submit"
              class="w-full mt-2 py-3.5 rounded-lg font-medium text-white shadow-sm transition hover:opacity-90 focus:ring-2 focus:ring-offset-2"
              style="background-color: var(--color-amarillo-pr);">
        Guardar cambios
      </button>
    </div>
  </form>
</section>
