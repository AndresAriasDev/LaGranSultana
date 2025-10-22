<?php
if (!defined('ABSPATH')) exit;

/**
 * ===========================================================
 * 👁️ AJAX: Registrar una vista en el perfil del modelo
 * Acción: model_register_view
 * ===========================================================
 */

add_action('wp_ajax_model_register_view', 'lgs_model_register_view');
add_action('wp_ajax_nopriv_model_register_view', 'lgs_model_register_view');

function lgs_model_register_view() {
    $model_id = intval($_POST['model_id'] ?? 0);

    if (!$model_id) {
        wp_send_json_error(['message' => 'ID de modelo no especificado.']);
    }

    // 🔹 Incrementar contador de vistas
    $views = (int) get_post_meta($model_id, 'model_total_views', true);
    $views++;
    update_post_meta($model_id, 'model_total_views', $views);

    wp_send_json_success([
        'views'   => $views,
        'message' => 'Vista registrada correctamente 👁️',
    ]);
}
