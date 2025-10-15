<?php
// 🚫 Evitar acceso directo
if ( ! defined( 'ABSPATH' ) ) exit;

/******************************************************
 * 🎯 SISTEMA DE PUNTOS - LA GRAN SULTANA
 * Archivo: inc/points/points-handler.php
 * Descripción: Controla la asignación y manejo de puntos de usuario.
 ******************************************************/

/**
 * CONSTANTES GLOBALES
 * (Puedes ajustarlas según la lógica del negocio)
 */
define('GS_POINTS_PROFILE_COMPLETE', 20); // Puntos por completar perfil
define('GS_POINTS_REGISTER', 5);           // Puntos por registrarse
define('GS_POINTS_FIRST_PURCHASE', 10);    // Puntos por primera compra futura


/******************************************************
 * 🔹 FUNCIONES BASE
 ******************************************************/

/**
 * Obtiene el total actual de puntos de un usuario
 */
function gs_get_user_points($user_id) {
    $points = (int) get_user_meta($user_id, '_user_points', true);
    return max(0, $points); // nunca negativo
}

/**
 * Asigna puntos a un usuario
 */
function gs_add_user_points($user_id, $amount, $reason = '') {
    $current = gs_get_user_points($user_id);
    $new_total = $current + (int) $amount;

    update_user_meta($user_id, '_user_points', $new_total);

    // Registrar log si querés histórico
    gs_log_user_points($user_id, $amount, $reason);

    return $new_total;
}

/**
 * Resta puntos a un usuario
 */
function gs_remove_user_points($user_id, $amount, $reason = '') {
    $current = gs_get_user_points($user_id);
    $new_total = max(0, $current - (int) $amount);

    update_user_meta($user_id, '_user_points', $new_total);
    gs_log_user_points($user_id, -$amount, $reason);

    return $new_total;
}

/**
 * Guarda un log de puntos (opcional)
 */
function gs_log_user_points($user_id, $amount, $reason = '') {
    $log = get_user_meta($user_id, '_points_log', true);
    if ( ! is_array($log) ) $log = [];

    $log[] = [
        'date' => current_time('mysql'),
        'amount' => $amount,
        'reason' => $reason,
    ];

    update_user_meta($user_id, '_points_log', $log);
}

/**
 * Obtiene el porcentaje actual de perfil completado
 */
function gs_get_profile_completion($user_id) {
    return (int) get_user_meta($user_id, '_profile_completion', true);
}

/**
 * Actualiza el porcentaje de perfil completado según los campos
 */
function gs_update_profile_completion($user_id) {
    $user = get_userdata($user_id);

    $fields = [
        'first_name',
        'phone',
        'address',
        'department',
        'birth_date'
    ];

    $completed = 0;
    foreach ($fields as $field) {
        $value = get_user_meta($user_id, $field, true);
        if ( ! empty($value) ) $completed++;
    }

    $percentage = ($completed / count($fields)) * 100;
    update_user_meta($user_id, '_profile_completion', $percentage);

    // Si el usuario alcanzó 100%, darle puntos si aún no los tiene
    if ($percentage == 100 && ! get_user_meta($user_id, '_profile_points_awarded', true)) {
        gs_add_user_points($user_id, GS_POINTS_PROFILE_COMPLETE, 'Perfil completo');
        update_user_meta($user_id, '_profile_points_awarded', true);
    }

    return $percentage;
}


/******************************************************
 * 🪄 EVENTOS AUTOMÁTICOS
 ******************************************************/

/**
 * Cuando un usuario se registra, darle puntos iniciales
 */
function gs_points_on_user_register($user_id) {
    gs_add_user_points($user_id, GS_POINTS_REGISTER, 'Registro de cuenta');
}
add_action('user_register', 'gs_points_on_user_register');

/**
 * Cuando el usuario actualiza su perfil desde el panel
 * (más adelante conectaremos este hook al formulario)
 */
function gs_points_on_profile_update($user_id) {
    gs_update_profile_completion($user_id);
}
add_action('profile_update', 'gs_points_on_profile_update');

