<?php
// 🔹 Acción AJAX: subir nueva foto de modelo
add_action('wp_ajax_subir_foto_modelo', 'gs_subir_foto_modelo');

function gs_subir_foto_modelo() {

    // 🧩 Validar usuario logueado
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Debes iniciar sesión.']);
    }

    $user = wp_get_current_user();

    // 🧩 Validar rol de modelo
    if (!in_array('modelo', (array)$user->roles)) {
        wp_send_json_error(['message' => 'Solo los modelos pueden subir fotos.']);
    }

    // 🧩 Validar archivo recibido
    if (empty($_FILES['foto'])) {
        wp_send_json_error(['message' => 'No se recibió ninguna imagen.']);
    }

    // 📸 Subir archivo a la librería de medios
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    $upload = wp_handle_upload($_FILES['foto'], ['test_form' => false]);

    if (isset($upload['error'])) {
        wp_send_json_error(['message' => $upload['error']]);
    }

// Obtener número incremental para el modelo
$count = new WP_Query([
    'post_type'      => 'fotos',
    'author'         => $user->ID,
    'posts_per_page' => -1,
    'fields'         => 'ids'
]);
$numero_foto = $count->found_posts + 1;

// Crear el post tipo “fotos”
$foto_id = wp_insert_post([
    'post_type'   => 'fotos',
    'post_status' => 'publish',
    'post_title'  => 'Foto de ' . $user->display_name . ' ' . $numero_foto,
    'post_name'   => $numero_foto, // <-- slug numérico
    'post_author' => $user->ID,
]);

    // 🖼️ Asociar la imagen subida como featured image
    $attachment = [
        'post_mime_type' => $_FILES['foto']['type'],
        'post_title'     => sanitize_file_name($_FILES['foto']['name']),
        'post_content'   => '',
        'post_status'    => 'inherit'
    ];

    $attach_id = wp_insert_attachment($attachment, $upload['file'], $foto_id);

    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
    wp_update_attachment_metadata($attach_id, $attach_data);
    set_post_thumbnail($foto_id, $attach_id);

    // 🔗 Relacionar la foto con el modelo (fase 2)
    update_post_meta($foto_id, '_modelo_relacionado', $user->ID);

    // ✅ Respuesta exitosa
    wp_send_json_success([
        'message'   => 'Foto subida correctamente',
        'foto_id'   => $foto_id,
        'image_url' => wp_get_attachment_image_url($attach_id, 'medium'),
    ]);
}
