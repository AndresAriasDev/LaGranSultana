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
    $per_page  = 15;

    if (!$model_id) {
        wp_send_json_error(['message' => 'Modelo no especificado.']);
    }

    // 🔍 Buscar fotos actuales (paginadas)
    $fotos = get_posts([
        'post_type'      => 'fotos',
        'meta_key'       => '_modelo_relacionado',
        'meta_value'     => $model_id,
        'posts_per_page' => $per_page,
        'paged'          => $paged,
    ]);

    // 📦 Obtener TODAS las fotos (para navegación completa)
    $todas = get_posts([
        'post_type'      => 'fotos',
        'meta_key'       => '_modelo_relacionado',
        'meta_value'     => $model_id,
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);

    $total_fotos = count($todas);
    $total_pages = ceil($total_fotos / $per_page);

    ob_start();

    if ($fotos && count($fotos) > 0): ?>
        <?php foreach ($fotos as $foto): 
            $foto_id   = $foto->ID;
            $autor_id  = (int) get_post_field('post_author', $foto_id);
            $likes     = (int) get_post_meta($foto_id, 'likes', true);
            if ($likes < 0) $likes = 0;
        ?>
        <div class="relative group overflow-hidden shadow-sm rounded-[4px] hover:shadow-xl transition-all duration-300 cursor-pointer">
            <div class="aspect-square w-full h-auto relative overflow-hidden rounded-[4px] bg-gray-100 animate-pulse">

                <img 
                    loading="lazy"
                    src="<?php echo esc_url(gs_get_model_image($foto->ID, 'modelo_panel')); ?>" 
                    data-full="<?php echo esc_url(gs_get_model_image($foto->ID, 'full', true)); ?>"
                    data-id="<?php echo esc_attr($foto_id); ?>"
                    data-autor="<?php echo esc_attr($autor_id); ?>"
                    data-likes="<?php echo esc_attr($likes); ?>"
                    alt=""
                    class="w-full h-auto object-cover opacity-0 transition-opacity duration-500 ease-out z-[1]"
                    onload="this.classList.remove('opacity-0','animate-pulse'); this.parentElement.classList.remove('animate-pulse','bg-gray-100');"
                />

                <!-- ✨ Overlay de likes -->
                <div class="absolute inset-0 bg-white/30 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center pointer-events-none">
                    <div class="rounded-xl px-6 py-4 text-center transform scale-95 group-hover:scale-100 transition-transform duration-300 ease-out">
                        <p class="text-white text-4xl font-bold leading-tight tracking-wide drop-shadow-[0_2px_6px_rgba(0,0,0,0.6)] select-none">
                            <?php echo esc_html($likes); ?>
                        </p>
                        <p class="text-white text-sm uppercase tracking-wider mt-1 opacity-90 drop-shadow-[0_1px_3px_rgba(0,0,0,0.5)] select-none">
                            Likes
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- 🧠 JSON oculto con todas las fotos -->
        <script id="gs-photo-list" type="application/json">
            <?php
            echo wp_json_encode(array_map(function($f) {
                return [
                    'id'     => $f->ID,
                    'autor'  => (int) get_post_field('post_author', $f->ID),
                    'likes'  => (int) get_post_meta($f->ID, 'likes', true),
                    'full'   => gs_get_model_image($f->ID, 'full', true),
                ];
            }, $todas));
            ?>
        </script>

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
