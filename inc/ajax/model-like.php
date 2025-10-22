<?php
if (!defined('ABSPATH')) exit;

/**
 * ===========================================================
 * ❤️ AJAX: Dar "like" a una foto de modelo
 * Acción: model_like_photo
 * ===========================================================
 */

add_action('wp_ajax_model_like_photo', 'lgs_model_like_photo');
add_action('wp_ajax_nopriv_model_like_photo', 'lgs_model_like_photo');

function lgs_model_like_photo() {
    $photo_id = intval($_POST['photo_id'] ?? 0);

    if (!$photo_id) {
        wp_send_json_error(['message' => 'ID de foto inválido.']);
    }

    // Si el usuario no está logueado, pedir login
    if (!is_user_logged_in()) {
        wp_send_json(['require_login' => true]);
    }

    $user_id = get_current_user_id();

    // 🔸 Evitar múltiples likes del mismo usuario
    $liked_users = (array) get_post_meta($photo_id, '_liked_users', true);
    if (in_array($user_id, $liked_users)) {
        wp_send_json_error(['message' => 'Ya diste like a esta foto.']);
    }

    // 🔸 Incrementar contador
    $total_likes = (int) get_post_meta($photo_id, 'total_likes', true);
    $total_likes++;

    // 🔸 Actualizar metadatos
    $liked_users[] = $user_id;
    update_post_meta($photo_id, '_liked_users', $liked_users);
    update_post_meta($photo_id, 'total_likes', $total_likes);

    wp_send_json_success([
        'total_likes' => $total_likes,
        'message'     => 'Like agregado correctamente ❤️',
    ]);
}
