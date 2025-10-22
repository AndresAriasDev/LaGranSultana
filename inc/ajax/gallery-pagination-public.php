<?php

if (!defined('ABSPATH')) exit;

add_action('wp_ajax_get_modelo_fotos_public', 'gs_get_modelo_fotos_public');
add_action('wp_ajax_nopriv_get_modelo_fotos_public', 'gs_get_modelo_fotos_public');

function gs_get_modelo_fotos_public() {
    // ✅ Validar nonce de seguridad
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'gs_public_gallery_nonce')) {
        wp_send_json_error(['message' => 'Permiso denegado.']);
    }

    $model_id = intval($_POST['model_id'] ?? 0);
    $paged     = intval($_POST['page'] ?? 1);
    $per_page  = 12; // ✅ mostrar 20 fotos (5 columnas x 4 filas)

    if (!$model_id) {
        wp_send_json_error(['message' => 'Modelo no especificado.']);
    }

    // 🔍 Buscar fotos del modelo
    $fotos = get_posts([
        'post_type'      => 'fotos',
        'meta_key'       => '_modelo_relacionado',
        'meta_value'     => $model_id,
        'posts_per_page' => $per_page,
        'paged'          => $paged,
    ]);

    $total_query = new WP_Query([
        'post_type'      => 'fotos',
        'meta_key'       => '_modelo_relacionado',
        'meta_value'     => $model_id,
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]);
    $total_fotos = $total_query->found_posts;
    $total_pages = ceil($total_fotos / $per_page);
    wp_reset_postdata();

    ob_start();

    if ($fotos && count($fotos) > 0): ?>
        <?php foreach ($fotos as $foto): ?>
            <div class="relative group overflow-hidden rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300">
                <div class="aspect-square w-full h-auto relative overflow-hidden rounded-2xl bg-gray-100 animate-pulse">
                    <img 
                        loading="lazy"
                        src="<?php echo esc_url(gs_get_model_image($foto->ID, 'modelo_panel')); ?>" 
                        alt=""
                        class="w-full h-auto object-cover rounded-2xl opacity-0 transition-opacity duration-500 ease-out"
                        onload="this.classList.remove('opacity-0','animate-pulse'); this.parentElement.classList.remove('animate-pulse','bg-gray-100');"
                    />
                    <div class="absolute bottom-3 right-3 bg-black/40 px-2 py-1 rounded text-white text-xs flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[var(--color-rojo-pr)]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 
                            2 5.42 4.42 3 7.5 3
                            c1.74 0 3.41.81 4.5 2.09
                            C13.09 3.81 14.76 3 16.5 3
                            19.58 3 22 5.42 22 8.5
                            c0 3.78-3.4 6.86-8.55 11.54
                            L12 21.35z"/>
                        </svg>
                        <span><?php echo rand(10, 300); ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
            <img src="/wp-content/uploads/2025/10/icono-camara.png" alt="Sin fotos" class="w-16 h-16 opacity-70 mb-4">
            <p class="text-[var(--color-tx-azul)] text-lg font-medium">Aún no hay fotos disponibles.</p>
            <p class="text-[var(--color-tx-negro)] text-sm mt-1 opacity-80">Este modelo no ha publicado fotos todavía.</p>
        </div>
    <?php endif;

    $html = ob_get_clean();

    wp_send_json_success([
        'html' => $html,
        'total_pages' => $total_pages,
        'current_page' => $paged,
    ]);
}
