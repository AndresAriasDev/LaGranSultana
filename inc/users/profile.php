<?php
if (!defined('ABSPATH')) exit;

/**
 * ========================================================
 * 🎯 PERFIL DE USUARIO NORMAL – La Gran Sultana
 * --------------------------------------------------------
 * Maneja:
 *  - Guardado de información personal (AJAX)
 *  - Cálculo del progreso del perfil
 *  - Interacción con el sistema global de puntos
 * ========================================================
 */

/* ========================================================
 * 1️⃣ GUARDAR INFORMACIÓN DEL PERFIL (AJAX)
 * ======================================================== */
add_action('wp_ajax_gs_save_user_profile', 'gs_save_user_profile');
function gs_save_user_profile() {
    check_ajax_referer('gs_profile_nonce', 'nonce');

    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_send_json_error(['message' => 'No hay sesión activa.']);
    }

    global $wpdb;

    // 🧩 Campos básicos del formulario (sin incluir phone aquí)
    $fields = ['first_name','address','department','birthdate','gender'];

    /* ========================================================
     * ✅ VALIDAR Y GUARDAR TELÉFONO (único y normalizado)
     * ======================================================== */
    if (isset($_POST['phone'])) {
        $raw_phone = sanitize_text_field($_POST['phone']);

        // Normalizar: quitar todo excepto + y dígitos
        $normalized = preg_replace('/[^\d\+]/', '', $raw_phone);

        // Si no tiene + al inicio, asumimos +505
        if (strpos($normalized, '+') !== 0) {
            $normalized = '+505' . $normalized;
        }

        // Limpieza final (sin espacios ni guiones)
        $normalized_clean = preg_replace('/[^\d\+]/', '', $normalized);

        // Buscar si existe en otro usuario
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT user_id 
             FROM {$wpdb->usermeta} 
             WHERE meta_key = 'phone' 
             AND REPLACE(REPLACE(REPLACE(REPLACE(meta_value, ' ', ''), '-', ''), '(', ''), ')', '') = REPLACE(REPLACE(REPLACE(REPLACE(%s, ' ', ''), '-', ''), '(', ''), ')', '')
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

        // ✅ Guardar el número normalizado solo si no está duplicado
        update_user_meta($user_id, 'phone', $normalized_clean);
    }

    /* ========================================================
     * ✅ GUARDAR LOS DEMÁS CAMPOS
     * ======================================================== */
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_user_meta($user_id, $field, sanitize_text_field($_POST[$field]));
        }
    }

    /* ========================================================
     * 📊 CALCULAR PROGRESO Y OTORGAR PUNTOS
     * ======================================================== */
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
        'has_bonus' => (bool) get_user_meta($user_id, 'gs_profile_bonus_awarded', true),
        'bonus_just_awarded' => $bonus_just_awarded,
        'missing' => $missing
    ]);
}

/* ========================================================
 * 2️⃣ CALCULAR EL PORCENTAJE DE PERFIL COMPLETADO
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

    $filled = 0;
    $missing = [];

    foreach ($fields as $meta_key => $label) {
        $value = get_user_meta($user_id, $meta_key, true);
        if (!empty($value)) {
            $filled++;
        } else {
            $missing[] = $label;
        }
    }

    // ✅ Verificar foto de perfil (no gravatar por defecto)
    $avatar_url = get_user_meta($user_id, 'gs_profile_picture', true);
    if (!empty($avatar_url)) {
        $filled++;
    } else {
        $missing[] = 'Foto de perfil';
    }

    $total = count($fields) + 1; // +1 por la foto
    $completion = ($filled / $total) * 100;

    return [
        'percentage' => round($completion),
        'missing'    => $missing
    ];
}

/* ========================================================
 * 3️⃣ FUNCIONES AUXILIARES
 * ======================================================== */
function gs_get_user_field($user_id, $field) {
    return get_user_meta($user_id, $field, true);
}

/* ========================================================
 * 📸 SUBIR FOTO DE PERFIL (AJAX)
 * ======================================================== */
add_action('wp_ajax_gs_upload_profile_picture', 'gs_upload_profile_picture');
function gs_upload_profile_picture() {
    check_ajax_referer('gs_profile_nonce', 'nonce');

    $user_id = get_current_user_id();
    if (!$user_id || empty($_FILES['avatar'])) {
        wp_send_json_error(['message' => 'No se recibió ninguna imagen.']);
    }

    $file = $_FILES['avatar'];

    // Validar tipo de archivo
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($file['type'], $allowed)) {
        wp_send_json_error(['message' => 'Formato de imagen no permitido.']);
    }

    // Cargar archivo
    require_once ABSPATH . 'wp-admin/includes/file.php';
    $upload = wp_handle_upload($file, ['test_form' => false]);

    if (isset($upload['error'])) {
        wp_send_json_error(['message' => 'Error al subir la imagen.']);
    }

    $new_avatar_url = $upload['url'];
    $previous_avatar = get_user_meta($user_id, 'gs_profile_picture', true);
    update_user_meta($user_id, 'gs_profile_picture', esc_url($new_avatar_url));

    // ✅ Solo actualizamos la foto; el bono se otorga cuando complete el perfil
        $bonus_just_awarded = false;


// 📊 Recalcular progreso general
$profile_data = gs_get_profile_completion($user_id);
$completion   = $profile_data['percentage'];

// ⚡ Comprobar si justo ahora completó todo
$bonus_just_awarded = false;
$already_awarded = get_user_meta($user_id, 'gs_profile_bonus_awarded', true);
if ($completion >= 100 && !$already_awarded) {
    gs_add_points($user_id, 20, 'Perfil completo', 'profile_complete');
    update_user_meta($user_id, 'gs_profile_bonus_awarded', 1);
    $bonus_just_awarded = true;
}

wp_send_json_success([
    'message' => $bonus_just_awarded 
        ? '🎉 ¡Has completado tu perfil al 100% y ganado 20 puntos!' 
        : 'Foto actualizada correctamente.',
    'avatar_url' => esc_url($new_avatar_url),
    'bonus_just_awarded' => $bonus_just_awarded,
    'points' => gs_get_user_points($user_id),
    'completion' => $completion
]);

}
