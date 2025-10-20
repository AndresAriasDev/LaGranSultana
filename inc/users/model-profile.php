<?php
if (!defined('ABSPATH')) exit;

/**
 * ========================================================
 * 🎯 PERFIL DE MODELO – La Gran Sultana
 * --------------------------------------------------------
 * Este archivo maneja:
 *  - Guardado de información personal (AJAX)
 *  - Cálculo del progreso del perfil
 *  - Interacción con el sistema global de puntos
 * ========================================================
 */

/*******************************************************
 * 1️⃣ GUARDAR INFORMACIÓN DEL PERFIL DE MODELO (AJAX)
 *******************************************************/
add_action('wp_ajax_gs_save_model_profile', 'gs_save_model_profile');
function gs_save_model_profile() {
    check_ajax_referer('gs_profile_nonce', 'nonce');

    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_send_json_error(['message' => 'No hay sesión activa.']);
    }

    // 🧩 Campos del formulario del modelo
    $fields = [
        'first_name',
        'phone',
        'department',
        'address',
        'gender',
        'height',
        'weight',
        'measurements',
    ];

    global $wpdb;

    // ✅ Validar teléfono duplicado
    if (!empty($_POST['phone'])) {
        $phone = sanitize_text_field($_POST['phone']);
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT user_id FROM {$wpdb->usermeta} 
             WHERE meta_key = 'phone' 
             AND meta_value = %s 
             AND user_id != %d",
            $phone, $user_id
        ));
        if ($exists) {
            wp_send_json_error([
                'message' => 'El número de teléfono ingresado ya está registrado en otra cuenta.',
                'field'   => 'phone'
            ]);
        }
    }

    // ✅ Guardar campos
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_user_meta($user_id, $field, sanitize_text_field($_POST[$field]));
        }
    }

    // 📌 Calcular progreso
    $profile_data = gs_get_model_profile_completion($user_id);
    $completion   = $profile_data['percentage'];
    $missing      = $profile_data['missing'];

    // 🎁 Otorgar puntos si completa el perfil por primera vez
    $already_awarded = get_user_meta($user_id, 'gs_model_profile_bonus_awarded', true);
    $bonus_just_awarded = false;

    if ($completion >= 100 && !$already_awarded) {
        gs_add_points($user_id, 30, 'Perfil de modelo completo', 'model_profile_complete');
        update_user_meta($user_id, 'gs_model_profile_bonus_awarded', 1);
        $bonus_just_awarded = true;
    }

    wp_send_json_success([
        'message' => $completion >= 100 
            ? '🎉 ¡Has completado tu perfil al 100%!' 
            : 'Perfil actualizado correctamente.',
        'completion' => $completion,
        'points' => gs_get_user_points($user_id),
        'bonus_just_awarded' => $bonus_just_awarded,
        'missing' => $missing
    ]);
}

/*******************************************************
 * 2️⃣ CÁLCULO DE PERFIL COMPLETO (MODELO)
 *******************************************************/
function gs_get_model_profile_completion($user_id) {
    $fields = [
        'first_name'   => 'Nombre completo',
        'phone'        => 'Teléfono',
        'department'   => 'Departamento',
        'address'      => 'Dirección',
        'gender'       => 'Género',
        'height'       => 'Altura',
        'weight'       => 'Peso',
        'measurements' => 'Medidas',
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

    // ✅ Verificar si tiene foto de perfil (no gravatar)
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
