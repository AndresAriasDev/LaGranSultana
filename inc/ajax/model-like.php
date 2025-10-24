<?php
if (!defined('ABSPATH')) exit;

/**
 * 💖 AJAX: Likes infinitos + registro por usuario
 */
add_action('wp_ajax_sumar_like_foto', 'gs_sumar_like_foto');
add_action('wp_ajax_nopriv_sumar_like_foto', 'gs_sumar_like_foto');

function gs_sumar_like_foto() {
    check_ajax_referer('gs_likes_nonce', 'nonce');

    $foto_id = intval($_POST['foto_id'] ?? 0);
    if (!$foto_id) {
        wp_send_json_error(['message' => 'ID de foto inválido.']);
    }

    // ⚙️ Verificar login
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Debes iniciar sesión para dar like.']);
    }

    $current_user_id = get_current_user_id();
    $foto_autor_id   = (int) get_post_field('post_author', $foto_id);

    // 🚫 Evitar likes del propio autor
    if ($current_user_id === $foto_autor_id) {
        wp_send_json_error(['message' => 'No puedes dar like a tus propias fotos.']);
    }

    // 🚫 Validar tipo de post
    $post_type = get_post_type($foto_id);
    if (!in_array($post_type, ['fotos', 'attachment', 'galeria_modelo', 'foto_modelo'])) {
        wp_send_json_error(['message' => 'Este elemento no es una foto válida.']);
    }

    // =========================================================
    // 💾 Incrementar contador global de la foto
    // =========================================================
    $likes_actuales = (int) get_post_meta($foto_id, 'likes', true);
    $likes_nuevos = $likes_actuales + 1;
    update_post_meta($foto_id, 'likes', $likes_nuevos);

    // =========================================================
    // 🧍‍♂️ Registrar likes del usuario
    // =========================================================
    $user_likes = get_user_meta($current_user_id, 'gs_user_photo_likes', true);
    if (!is_array($user_likes)) $user_likes = [];

    // incrementar el contador para esta foto
    $user_likes[$foto_id] = isset($user_likes[$foto_id])
        ? $user_likes[$foto_id] + 1
        : 1;

    update_user_meta($current_user_id, 'gs_user_photo_likes', $user_likes);

    // ✅ Respuesta
    wp_send_json_success([
        'likes'       => $likes_nuevos,
        'user_likes'  => $user_likes[$foto_id],
        'message'     => 'Like agregado correctamente.'
    ]);
}

/**
 * 💬 AJAX: Verificar si el usuario ya dio al menos un like a una foto
 */
add_action('wp_ajax_check_user_like', 'gs_check_user_like');
add_action('wp_ajax_nopriv_check_user_like', 'gs_check_user_like');

function gs_check_user_like() {
    check_ajax_referer('gs_likes_nonce', 'nonce');

    $foto_id = intval($_POST['foto_id'] ?? 0);
    if (!$foto_id) {
        wp_send_json_error(['message' => 'ID de foto inválido.']);
    }

    if (!is_user_logged_in()) {
        wp_send_json_success(['liked' => false]);
    }

    $user_id = get_current_user_id();
    $user_likes = get_user_meta($user_id, 'gs_user_photo_likes', true);
    $liked = is_array($user_likes)
        && isset($user_likes[$foto_id])
        && $user_likes[$foto_id] > 0;

    wp_send_json_success([
        'liked' => $liked,
        'count' => $liked ? $user_likes[$foto_id] : 0
    ]);
}

// =========================================================
// 📊 Obtener likes de múltiples fotos (para actualización en vivo)
// =========================================================
add_action('wp_ajax_get_likes_bulk', 'gs_get_likes_bulk');
add_action('wp_ajax_nopriv_get_likes_bulk', 'gs_get_likes_bulk');

function gs_get_likes_bulk() {
    $ids = isset($_POST['ids']) ? json_decode(stripslashes($_POST['ids']), true) : [];
    if (empty($ids) || !is_array($ids)) {
        wp_send_json_error(['message' => 'IDs inválidos']);
    }

    $result = [];
    foreach ($ids as $id) {
        $result[$id] = (int) get_post_meta($id, 'likes', true);
    }

    wp_send_json_success($result);
}
