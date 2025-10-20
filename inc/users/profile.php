<?php
if (!defined('ABSPATH')) exit;

/**
 * ========================================================
 * 🎯 PERFIL DE USUARIO Y MODELO – La Gran Sultana
 * --------------------------------------------------------
 * Maneja:
 *  - Guardado de información personal (usuario y modelo)
 *  - Cálculo del progreso del perfil
 *  - Interacción con el sistema global de puntos
 * ========================================================
 */

/* ========================================================
 * 1️⃣ GUARDAR INFORMACIÓN DEL PERFIL (USUARIO NORMAL)
 * ======================================================== */
add_action('wp_ajax_gs_save_user_profile', 'gs_save_user_profile');
function gs_save_user_profile() {
    check_ajax_referer('gs_profile_nonce', 'nonce');

    $user_id = get_current_user_id();
    if (!$user_id) wp_send_json_error(['message' => 'No hay sesión activa.']);

    global $wpdb;

    $fields = ['first_name','address','department','birthdate','gender'];

    // ✅ Validar teléfono duplicado
    if (isset($_POST['phone'])) {
        $raw_phone = sanitize_text_field($_POST['phone']);
        $normalized = preg_replace('/[^\d\+]/', '', $raw_phone);
        if (strpos($normalized, '+') !== 0) $normalized = '+505' . $normalized;

        $normalized_clean = preg_replace('/[^\d\+]/', '', $normalized);

        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT user_id FROM {$wpdb->usermeta}
             WHERE meta_key = 'phone'
             AND REPLACE(REPLACE(REPLACE(REPLACE(meta_value,' ','') ,'-',''), '(',''), ')','') =
                 REPLACE(REPLACE(REPLACE(REPLACE(%s,' ','') ,'-',''), '(',''), ')','')
             AND user_id != %d",
            $normalized_clean,
            $user_id
        ));

        if ($exists) {
            wp_send_json_error([
                'message' => 'El número de teléfono ingresado ya está registrado en otra cuenta.',
                'field'   => 'phone'
            ]);
        }

        update_user_meta($user_id, 'phone', $normalized_clean);
    }

    // ✅ Guardar los demás campos
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_user_meta($user_id, $field, sanitize_text_field($_POST[$field]));
        }
    }

    // 📊 Calcular progreso y puntos
    $profile_data = gs_get_profile_completion($user_id);
    $completion   = $profile_data['percentage'];
    $missing      = $profile_data['missing'];

    $already_awarded = get_user_meta($user_id, 'gs_profile_bonus_awarded', true);
    $bonus_just_awarded = false;

    if ($completion >= 100 && !$already_awarded) {
        gs_add_points($user_id, 20, 'Perfil completo', 'profile_complete');
        update_user_meta($user_id, 'gs_profile_bonus_awarded', 1);
        $bonus_just_awarded = true;
    }

    wp_send_json_success([
        'message' => $completion >= 100 ? '🎉 ¡Has completado tu perfil al 100%!' : 'Perfil actualizado correctamente.',
        'completion' => $completion,
        'points' => gs_get_user_points($user_id),
        'bonus_points_given' => 20,
        'has_bonus' => (bool) get_user_meta($user_id, 'gs_profile_bonus_awarded', true),
        'bonus_just_awarded' => $bonus_just_awarded,
        'missing' => $missing
    ]);
}

/* ========================================================
 * 2️⃣ CALCULAR PORCENTAJE PERFIL NORMAL
 * ======================================================== */
function gs_get_profile_completion($user_id) {
    $fields = [
        'first_name'  => 'Nombre completo',
        'phone'       => 'Teléfono',
        'address'     => 'Dirección',
        'department'  => 'Departamento',
        'birthdate'   => 'Fecha de nacimiento',
        'gender'      => 'Género',
    ];

    $filled = 0; $missing = [];

    foreach ($fields as $meta_key => $label) {
        $value = get_user_meta($user_id, $meta_key, true);
        if (!empty($value)) $filled++;
        else $missing[] = $label;
    }

    $avatar = get_user_meta($user_id, 'gs_profile_picture', true);
    if (!empty($avatar)) $filled++;
    else $missing[] = 'Foto de perfil';

    $completion = ($filled / (count($fields) + 1)) * 100;

    return ['percentage' => round($completion), 'missing' => $missing];
}

/* ========================================================
 * 3️⃣ SUBIR FOTO DE PERFIL (GENÉRICO)
 * ======================================================== */
add_action('wp_ajax_gs_upload_profile_picture', 'gs_upload_profile_picture');
function gs_upload_profile_picture() {
    check_ajax_referer('gs_profile_nonce', 'nonce');

    $user_id = get_current_user_id();
    if (!$user_id || empty($_FILES['avatar'])) {
        wp_send_json_error(['message' => 'No se recibió ninguna imagen.']);
    }

    $file = $_FILES['avatar'];
    $allowed = ['image/jpeg','image/png','image/webp'];
    if (!in_array($file['type'], $allowed)) {
        wp_send_json_error(['message' => 'Formato de imagen no permitido.']);
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    $upload = wp_handle_upload($file, ['test_form' => false]);
    if (isset($upload['error'])) wp_send_json_error(['message' => 'Error al subir la imagen.']);

    $url = esc_url($upload['url']);
    update_user_meta($user_id, 'gs_profile_picture', $url);

    // 📊 Detectar tipo de usuario
    $user = get_userdata($user_id);
    $is_model = in_array('modelo', (array)$user->roles);

    // 📈 Recalcular progreso
    $profile_data = $is_model ? gs_get_model_profile_completion($user_id) : gs_get_profile_completion($user_id);
    $completion   = $profile_data['percentage'];

    // 🎁 Bono
    $meta_key = $is_model ? 'gs_model_profile_bonus_awarded' : 'gs_profile_bonus_awarded';
    $bonus_points = $is_model ? 30 : 20;

    $already_awarded = get_user_meta($user_id, $meta_key, true);
    $bonus_just_awarded = false;

    if ($completion >= 100 && !$already_awarded) {
        gs_add_points($user_id, $bonus_points, 'Perfil completo', $is_model ? 'profile_complete_model' : 'profile_complete');
        update_user_meta($user_id, $meta_key, 1);
        $bonus_just_awarded = true;
    }

    wp_send_json_success([
        'message' => 'Foto actualizada correctamente.',
        'completion' => $completion,
        'points' => gs_get_user_points($user_id),
        'bonus_points_given' => $bonus_points,
        'bonus_just_awarded' => $bonus_just_awarded,
        'has_bonus' => (bool) get_user_meta($user_id, $meta_key, true),
    ]);
}

/* ========================================================
 * 👠 GUARDAR INFORMACIÓN DEL PERFIL DE MODELO (AJAX)
 * ======================================================== */
add_action('wp_ajax_gs_save_model_profile', 'gs_save_model_profile');
function gs_save_model_profile() {
    check_ajax_referer('gs_profile_nonce', 'nonce');

    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_send_json_error(['message' => 'No hay sesión activa.']);
    }

    global $wpdb;

    // 🧩 Campos del formulario del modelo
    $fields = [
        'first_name',
        'address',
        'department',
        'gender',
        'height',
        'weight',
        'measurements'
    ];

    /* ✅ Validar y guardar teléfono único */
    if (isset($_POST['phone'])) {
        $raw_phone = sanitize_text_field($_POST['phone']);
        $normalized = preg_replace('/[^\d\+]/', '', $raw_phone);
        if (strpos($normalized, '+') !== 0) {
            $normalized = '+505' . $normalized;
        }

        $normalized_clean = preg_replace('/[^\d\+]/', '', $normalized);
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT user_id 
             FROM {$wpdb->usermeta}
             WHERE meta_key = 'phone'
             AND REPLACE(REPLACE(REPLACE(REPLACE(meta_value, ' ', ''), '-', ''), '(', ''), ')', '') = 
                 REPLACE(REPLACE(REPLACE(REPLACE(%s, ' ', ''), '-', ''), '(', ''), ')', '')
             AND user_id != %d",
            $normalized_clean,
            $user_id
        ));

        if ($exists) {
            wp_send_json_error([
                'message' => 'El número de teléfono ingresado ya está registrado en otra cuenta.',
                'field'   => 'phone'
            ]);
        }

        update_user_meta($user_id, 'phone', $normalized_clean);
    }

    /* ✅ Guardar demás campos */
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_user_meta($user_id, $field, sanitize_text_field($_POST[$field]));
        }
    }

    /* 📊 Recalcular progreso actualizado */
    $profile_data = gs_get_model_profile_completion($user_id);
    $completion   = $profile_data['percentage'];
    $missing      = $profile_data['missing'];

    /* 🧠 Si tenía puntos de perfil normal, eliminarlos */
    $had_user_bonus = get_user_meta($user_id, 'gs_profile_bonus_awarded', true);
    if ($had_user_bonus) {
        gs_remove_points($user_id, 20, 'Eliminación de puntos por cambio a modelo');
        delete_user_meta($user_id, 'gs_profile_bonus_awarded');
    }

    /* 🎁 Bonificación de modelo */
    $already_awarded = get_user_meta($user_id, 'gs_model_profile_bonus_awarded', true);
    $bonus_just_awarded = false;

    if ($completion >= 100 && !$already_awarded) {
        gs_add_points($user_id, 30, 'Perfil de modelo completo', 'profile_complete_model');
        update_user_meta($user_id, 'gs_model_profile_bonus_awarded', 1);
        $bonus_just_awarded = true;
    }

    wp_send_json_success([
        'message' => $completion >= 100 ? '🎉 ¡Has completado tu perfil al 100%!' : 'Perfil actualizado correctamente.',
        'completion' => (float) $completion,  // 🔹 esencial para que JS actualice la barra
        'points' => gs_get_user_points($user_id),
        'bonus_points_given' => 30,
        'has_bonus' => (bool) get_user_meta($user_id, 'gs_model_profile_bonus_awarded', true),
        'bonus_just_awarded' => $bonus_just_awarded,
        'missing' => $missing
    ]);
}


/* ========================================================
 * 5️⃣ CÁLCULO PERFIL MODELO
 * ======================================================== */
function gs_get_model_profile_completion($user_id) {
    $fields = [
        'first_name'   => 'Nombre completo',
        'phone'        => 'Teléfono',
        'address'      => 'Dirección',
        'department'   => 'Departamento',
        'gender'       => 'Género',
        'height'       => 'Altura',
        'weight'       => 'Peso',
        'measurements' => 'Medidas'
    ];

    $filled = 0; $missing = [];

    foreach ($fields as $key => $label) {
        $val = get_user_meta($user_id, $key, true);
        if (!empty($val)) $filled++;
        else $missing[] = $label;
    }

    $avatar = get_user_meta($user_id, 'gs_profile_picture', true);
    if (!empty($avatar)) $filled++;
    else $missing[] = 'Foto de perfil';

    $completion = ($filled / (count($fields) + 1)) * 100;
    return ['percentage' => round($completion), 'missing' => $missing];
}
