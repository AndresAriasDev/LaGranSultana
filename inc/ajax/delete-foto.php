<?php
// 🔹 Acción AJAX: eliminar foto del modelo
add_action('wp_ajax_eliminar_foto_modelo', 'gs_eliminar_foto_modelo');

function gs_eliminar_foto_modelo() {
    // Verificar login
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'No autorizado.']);
    }

    $user = wp_get_current_user();

    // Validar ID recibido
    if (empty($_POST['foto_id'])) {
        wp_send_json_error(['message' => 'ID de foto no recibido.']);
    }

    $foto_id = intval($_POST['foto_id']);
    $post = get_post($foto_id);

    if (!$post || $post->post_type !== 'fotos') {
        wp_send_json_error(['message' => 'Foto no válida.']);
    }

    // Verificar que la foto pertenezca al modelo actual
    $owner_id = get_post_meta($foto_id, '_modelo_relacionado', true);

    if ($owner_id != $user->ID) {
        wp_send_json_error(['message' => 'No puedes eliminar esta foto.']);
    }

    // 🧹 Eliminar post y miniatura
    wp_delete_post($foto_id, true);

    wp_send_json_success(['message' => 'Foto eliminada correctamente.']);
}
