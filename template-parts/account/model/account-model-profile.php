<?php
if (!defined('ABSPATH')) exit;

$current_user = wp_get_current_user();

// 🧩 Importar campos comunes reutilizables
require_once get_template_directory() . '/inc/users/partials/fields-common.php';
?>

<!-- 🧍 PERFIL DE MODELO -->
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

    <!-- Botón cambio de imagen -->
    <button type="button"
            id="gs-change-avatar-btn"
            class="absolute bottom-1 right-1 bg-[var(--color-blanco-pr)] border text-white p-1.5 rounded-full hover:opacity-90"
            aria-label="Cambiar foto de perfil"
            style="border-color: var(--color-borde);">
      <img src="<?php echo esc_url(get_site_url() . '/wp-content/uploads/2025/10/camara.png'); ?>"
           alt="Cambiar" class="w-4 h-4">
    </button>

    <input type="file" id="gs-avatar-input" accept="image/*" class="hidden">
  </div>

  <div class="text-center">
    <h2 class="text-xl font-semibold text-gray-800">
      <?php echo esc_html($current_user->display_name); ?>
    </h2>
    <p class="text-sm text-gray-500">Modelo registrada en La Gran Sultana</p>
  </div>
</section>
<?php
$profile_data = gs_get_model_profile_completion($current_user->ID);
$completion   = $profile_data['percentage'];
$points       = gs_get_user_points($current_user->ID);
$has_bonus    = get_user_meta($current_user->ID, 'gs_model_profile_bonus_awarded', true);
?>

<?php if (! $has_bonus): ?>
<section id="gs-profile-progress-module" class="relative bg-white rounded-lg shadow p-6 mb-8 transition-all">
  <button data-open-info="gs-info-puntos"
          class="absolute top-4 right-4 p-1 rounded-full hover:bg-gray-100 transition"
          aria-label="Información de puntos">
      <img src="<?php echo esc_url( get_site_url() . '/wp-content/uploads/2025/10/usuario-cafe.png' ); ?>" 
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
  <form id="gs-model-profile-form" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6">

    <?php
      // ✅ Campos comunes reutilizados
      gs_render_common_profile_fields($current_user->ID, [
        'first_name',
        'phone',
        'department',
        'address',
        'birthdate',
        'gender'
      ]);
    ?>

    <!-- Altura -->
    <div class="flex flex-col">
      <label class="text-[15px] font-medium text-gray-700 mb-1.5">Altura (cm)</label>
      <input type="number" name="height"
             value="<?php echo esc_attr(get_user_meta($current_user->ID, 'height', true)); ?>"
             class="gs-input" placeholder="Ej: 170">
    </div>

    <!-- Peso -->
    <div class="flex flex-col">
      <label class="text-[15px] font-medium text-gray-700 mb-1.5">Peso (kg)</label>
      <input type="number" name="weight"
             value="<?php echo esc_attr(get_user_meta($current_user->ID, 'weight', true)); ?>"
             class="gs-input" placeholder="Ej: 55">
    </div>

    <!-- Medidas -->
    <div class="flex flex-col">
      <label class="text-[15px] font-medium text-gray-700 mb-1.5">Medidas (busto-cintura-cadera)</label>
      <input type="text" name="measurements"
             value="<?php echo esc_attr(get_user_meta($current_user->ID, 'measurements', true)); ?>"
             class="gs-input" placeholder="Ej: 90-60-90">
    </div>

    <!-- Código de referido (solo lectura, opcional) -->
    <?php
      $ref_code = get_user_meta($current_user->ID, 'gs_referral_code', true);
      if (!empty($ref_code)) :
    ?>
      <div class="flex flex-col">
        <label class="text-[15px] font-medium text-gray-700 mb-1.5">Código de referido</label>
        <input type="text" readonly
               value="<?php echo esc_attr($ref_code); ?>"
               class="gs-input bg-gray-50 cursor-not-allowed text-gray-500">
      </div>
    <?php endif; ?>

    <!-- Botón guardar -->
    <div class="md:col-span-2 pt-3">
      <button type="submit"
              class="w-full mt-2 py-3.5 rounded-lg font-medium text-white shadow-sm transition hover:opacity-90 focus:ring-2 focus:ring-offset-2"
              style="background-color: var(--color-amarillo-pr);">
        Guardar cambios
      </button>
    </div>

  </form>
</section>
