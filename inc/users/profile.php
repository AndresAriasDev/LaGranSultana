<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ========================================================
 * 🎯 PERFIL DE USUARIO NORMAL – La Gran Sultana
 * Archivo central para manejar:
 *  - Guardado de información personal (AJAX)
 *  - Cálculo del progreso del perfil
 *  - Interacción con el sistema global de puntos
 * ========================================================
 */
/*******************************************************/
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

    $fields = ['first_name','last_name','phone','address','department','birthdate','gender'];
    
// ✅ Validar teléfono duplicado antes de guardar
if (isset($_POST['phone'])) {
    global $wpdb;
    $phone = sanitize_text_field($_POST['phone']);
    if (!empty($phone)) {
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT user_id FROM {$wpdb->usermeta} 
             WHERE meta_key = 'phone' 
             AND meta_value = %s 
             AND user_id != %d",
            $phone, $user_id
        ));

        if ($exists) {
            // 🚫 Detener inmediatamente la ejecución y devolver error
            wp_send_json_error([
                'message' => 'El número de teléfono ingresado ya está registrado en otra cuenta.',
                'field'   => 'phone'
            ]);
            exit;
        }
    }
}


// ✅ Si todo está correcto, proceder con la actualización normal
foreach ($fields as $field) {
    if (isset($_POST[$field])) {
        update_user_meta($user_id, $field, sanitize_text_field($_POST[$field]));
    }
}


    // Calcular progreso del perfil
    $profile_data = gs_get_profile_completion($user_id);
    $completion   = $profile_data['percentage'];
    $missing      = $profile_data['missing'];

    // Verificar si ya tenía puntos antes
    $already_awarded = get_user_meta($user_id, 'gs_profile_bonus_awarded', true);
    $bonus_just_awarded = false;

    // Si acaba de completar al 100% por primera vez → dar puntos
    if ($completion >= 100 && ! $already_awarded) {
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
        'first_name'  => 'Nombre',
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

    // ✅ Verificar foto de perfil (no Gravatar por defecto)
    $avatar_url = get_avatar_url($user_id);
    if ($avatar_url && strpos($avatar_url, 'gravatar.com/avatar/?d=') === false) {
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

    // 🎯 Asignar puntos solo si es la primera vez
    $already_awarded = get_user_meta($user_id, 'gs_profile_picture_awarded', true);
    $bonus_just_awarded = false;

    if (empty($previous_avatar) && ! $already_awarded) {
        gs_add_points($user_id, 5, 'Cambio de foto de perfil', 'profile_picture');
        update_user_meta($user_id, 'gs_profile_picture_awarded', 1);
        $bonus_just_awarded = true;
    }

    wp_send_json_success([
        'message' => $bonus_just_awarded 
            ? 'Foto actualizada y +5 puntos ganados 👏' 
            : 'Foto actualizada correctamente.',
        'avatar_url' => esc_url($new_avatar_url),
        'bonus_just_awarded' => $bonus_just_awarded,
        'points' => gs_get_user_points($user_id)
    ]);
}
