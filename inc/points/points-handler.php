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
