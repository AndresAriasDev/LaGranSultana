<?php
if (!defined('ABSPATH')) exit;

$current_user = wp_get_current_user();
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

<!-- 🧾 INFORMACIÓN PERSONAL -->
<section class="bg-white rounded-xl shadow-sm border border-gray-100 p-7 transition-all">
  <header class="flex items-center gap-2 mb-8 border-b border-gray-100 pb-3">
    <h3 class="text-lg font-semibold text-gray-800">Información personal</h3>
  </header>

  <?php
    $fields = [
      'first_name'  => get_user_meta($current_user->ID, 'first_name', true),
      'display_name_custom' => get_user_meta($current_user->ID, 'display_name_custom', true),
      'bio'         => get_user_meta($current_user->ID, 'bio', true),
      'phone'       => get_user_meta($current_user->ID, 'phone', true),
      'instagram'   => get_user_meta($current_user->ID, 'instagram', true),
      'height'      => get_user_meta($current_user->ID, 'height', true),
      'weight'      => get_user_meta($current_user->ID, 'weight', true),
      'measurements'=> get_user_meta($current_user->ID, 'measurements', true),
      'department'  => get_user_meta($current_user->ID, 'department', true),
      'gender'      => get_user_meta($current_user->ID, 'gender', true),
      'gs_referral_code' => get_user_meta($current_user->ID, 'gs_referral_code', true),
    ];
  ?>

<form id="gs-model-profile-form" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6">

    <!-- Nombre real -->
    <div class="flex flex-col">
      <label class="text-[15px] font-medium text-gray-700 mb-1.5">Nombre completo</label>
      <input type="text" name="first_name" value="<?php echo esc_attr($fields['first_name']); ?>" class="gs-input" placeholder="Tu nombre real">
    </div>

    <!-- Nombre artístico -->
    <div class="flex flex-col">
      <label class="text-[15px] font-medium text-gray-700 mb-1.5">Nombre artístico</label>
      <input type="text" name="display_name_custom" value="<?php echo esc_attr($fields['display_name_custom']); ?>" class="gs-input" placeholder="Ejemplo: Miriam Avilés">
    </div>

    <!-- Biografía -->
    <div class="md:col-span-2 flex flex-col">
      <label class="text-[15px] font-medium text-gray-700 mb-1.5">Biografía</label>
      <textarea name="bio" rows="3" class="gs-input"><?php echo esc_textarea($fields['bio']); ?></textarea>
    </div>

    <!-- Teléfono -->
    <div class="flex flex-col">
      <label class="text-[15px] font-medium text-gray-700 mb-1.5">Teléfono</label>
      <input type="tel" name="phone" value="<?php echo esc_attr($fields['phone']); ?>" class="gs-input" placeholder="Ej: 88888888">
    </div>

    <!-- Instagram -->
    <div class="flex flex-col">
      <label class="text-[15px] font-medium text-gray-700 mb-1.5">Instagram</label>
      <input type="text" name="instagram" value="<?php echo esc_attr($fields['instagram']); ?>" class="gs-input" placeholder="@usuario">
    </div>

    <!-- Altura -->
    <div class="flex flex-col">
      <label class="text-[15px] font-medium text-gray-700 mb-1.5">Altura (cm)</label>
      <input type="number" name="height" value="<?php echo esc_attr($fields['height']); ?>" class="gs-input" placeholder="Ej: 170">
    </div>

    <!-- Peso -->
    <div class="flex flex-col">
      <label class="text-[15px] font-medium text-gray-700 mb-1.5">Peso (kg)</label>
      <input type="number" name="weight" value="<?php echo esc_attr($fields['weight']); ?>" class="gs-input" placeholder="Ej: 55">
    </div>

    <!-- Medidas -->
    <div class="flex flex-col">
      <label class="text-[15px] font-medium text-gray-700 mb-1.5">Medidas (busto-cintura-cadera)</label>
      <input type="text" name="measurements" value="<?php echo esc_attr($fields['measurements']); ?>" class="gs-input" placeholder="Ej: 90-60-90">
    </div>

    <!-- Departamento -->
    <div class="flex flex-col">
      <label class="text-[15px] font-medium text-gray-700 mb-1.5">Departamento</label>
      <input type="text" name="department" value="<?php echo esc_attr($fields['department']); ?>" class="gs-input" placeholder="Ej: Managua">
    </div>

    <!-- Género -->
    <div class="flex flex-col">
      <label class="text-[15px] font-medium text-gray-700 mb-1.5">Género</label>
      <select name="gender" class="gs-input">
        <option value="">Seleccionar...</option>
        <option value="Femenino" <?php selected($fields['gender'], 'Femenino'); ?>>Femenino</option>
        <option value="Masculino" <?php selected($fields['gender'], 'Masculino'); ?>>Masculino</option>
        <option value="Otro" <?php selected($fields['gender'], 'Otro'); ?>>Otro</option>
      </select>
    </div>

    <!-- Código de referido -->
    <div class="flex flex-col">
      <label class="text-[15px] font-medium text-gray-700 mb-1.5">Código de referido</label>
      <input type="text" name="gs_referral_code" value="<?php echo esc_attr($fields['gs_referral_code']); ?>" class="gs-input bg-gray-50 cursor-not-allowed text-gray-500" readonly>
    </div>

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
