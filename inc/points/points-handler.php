<?php
// 🚫 Evitar acceso directo
if ( ! defined('ABSPATH') ) exit;

/**
 * 🎯 SISTEMA DE PUNTOS GLOBAL - La Gran Sultana
 * Este archivo gestiona todos los puntos del usuario:
 *  - Obtención y almacenamiento
 *  - Suma / resta de puntos
 *  - Prevención de duplicados
 *  - Registro de razones / eventos
 */

/******************************************************
 * 🔹 Obtener puntos actuales de un usuario
 ******************************************************/
if ( ! function_exists('gs_get_user_points') ) {
    function gs_get_user_points( $user_id ) {
        $points = (int) get_user_meta( $user_id, 'gs_points', true );
        return $points > 0 ? $points : 0;
    }
}

/******************************************************
 * 🔹 Sumar puntos al usuario por foto subida (AJAX)
 ******************************************************/
add_action('wp_ajax_sumar_puntos_modelo', 'gs_sumar_puntos_modelo');
add_action('wp_ajax_nopriv_sumar_puntos_modelo', 'gs_sumar_puntos_modelo');

function gs_sumar_puntos_modelo() {
    // Validar usuario
    if ( ! is_user_logged_in() ) {
        wp_send_json_error(['message' => 'Usuario no autenticado.']);
    }

    $user_id = get_current_user_id();
    $puntos  = intval($_POST['puntos'] ?? 0);

    if ($puntos <= 0) {
        wp_send_json_error(['message' => 'Cantidad de puntos inválida.']);
    }

    // ⚙️ Registrar puntos con el sistema central
    $nuevo_total = gs_add_points(
        $user_id,
        $puntos,
        'Subida de nueva foto al perfil de modelo',
        'upload_photo_' . time() // unique key basada en timestamp
    );

    if ( ! $nuevo_total ) {
        wp_send_json_error(['message' => 'Los puntos ya fueron otorgados o no se pudieron sumar.']);
    }

    wp_send_json_success([
        'message'      => 'Puntos sumados correctamente.',
        'nuevo_total'  => $nuevo_total
    ]);
}


/******************************************************
 * 🔹 Sumar puntos al usuario
 ******************************************************/
if ( ! function_exists('gs_add_points') ) {
    function gs_add_points( $user_id, $amount, $reason = '', $unique_key = '' ) {
        if ( ! $user_id || $amount <= 0 ) return false;

        // Evitar duplicar puntos por el mismo evento (opcional)
        if ( $unique_key && gs_has_received_points( $user_id, $unique_key ) ) {
            return false;
        }

        $current = gs_get_user_points( $user_id );
        $new_total = $current + $amount;

        update_user_meta( $user_id, 'gs_points', $new_total );

        // Guardar en historial (si lo quieres implementar después)
        gs_log_points_event( $user_id, $amount, $reason, 'add' );

        // Marcar el evento como completado (si se usó unique_key)
        if ( $unique_key ) gs_mark_points_as_given( $user_id, $unique_key );

        return $new_total;
    }
}

/******************************************************
 * 🔹 Restar puntos al usuario
 ******************************************************/
if ( ! function_exists('gs_remove_points') ) {
    function gs_remove_points( $user_id, $amount, $reason = '' ) {
        if ( ! $user_id || $amount <= 0 ) return false;

        $current = gs_get_user_points( $user_id );
        $new_total = max( 0, $current - $amount );

        update_user_meta( $user_id, 'gs_points', $new_total );
        gs_log_points_event( $user_id, $amount, $reason, 'remove' );

        return $new_total;
    }
}

/******************************************************
 * 🚫 Penalización: restar puntos por eliminar una foto
 ******************************************************/
add_action('wp_ajax_restar_puntos_por_eliminar_foto', 'gs_restar_puntos_por_eliminar_foto');
function gs_restar_puntos_por_eliminar_foto() {
    $user_id = get_current_user_id();
    if (!$user_id) wp_send_json_error(['message' => 'No autorizado']);

    $penalizacion = 20;
    $puntos_actuales = gs_get_user_points($user_id);
    $nuevo_total = max(0, $puntos_actuales - $penalizacion);

    // Actualizar puntos del usuario
    update_user_meta($user_id, 'gs_points', $nuevo_total);

    // Registrar en el historial (opcional pero recomendable)
    gs_log_points_event($user_id, $penalizacion, 'Eliminó una foto', 'remove');

    // Enviar respuesta
    wp_send_json_success([
        'message' => 'Puntos restados correctamente.',
        'restados' => $penalizacion,
        'nuevo_total' => $nuevo_total
    ]);
}



/******************************************************
 * 🔹 Verificar si un usuario ya recibió puntos por un evento
 ******************************************************/
if ( ! function_exists('gs_has_received_points') ) {
    function gs_has_received_points( $user_id, $key ) {
        $received = get_user_meta( $user_id, 'gs_points_awarded', true );
        return is_array( $received ) && in_array( $key, $received );
    }
}

/******************************************************
 * 🔹 Marcar un evento de puntos como entregado
 ******************************************************/
if ( ! function_exists('gs_mark_points_as_given') ) {
    function gs_mark_points_as_given( $user_id, $key ) {
        $received = get_user_meta( $user_id, 'gs_points_awarded', true );
        if ( ! is_array( $received ) ) $received = [];
        if ( ! in_array( $key, $received ) ) {
            $received[] = $key;
            update_user_meta( $user_id, 'gs_points_awarded', $received );
        }
    }
}

/******************************************************
 * 🔹 Registrar evento de puntos (historial interno)
 ******************************************************/
if ( ! function_exists('gs_log_points_event') ) {
    function gs_log_points_event( $user_id, $amount, $reason = '', $type = 'add' ) {
        $logs = get_user_meta( $user_id, 'gs_points_log', true );
        if ( ! is_array( $logs ) ) $logs = [];

        $logs[] = [
            'date'   => current_time('mysql'),
            'amount' => $amount,
            'type'   => $type, // 'add' o 'remove'
            'reason' => $reason,
        ];

        update_user_meta( $user_id, 'gs_points_log', $logs );
    }
}
