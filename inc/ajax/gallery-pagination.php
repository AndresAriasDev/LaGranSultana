<?php
// 🚫 Seguridad
if ( ! defined('ABSPATH') ) exit;

/**
 * 🔹 AJAX: Obtener fotos del modelo paginadas
 * Acción: get_modelo_fotos
 */
add_action('wp_ajax_get_modelo_fotos', 'gs_get_modelo_fotos');
add_action('wp_ajax_nopriv_get_modelo_fotos', 'gs_get_modelo_fotos');

function gs_get_modelo_fotos() {
    // Verificar login
    if ( ! is_user_logged_in() ) {
        wp_send_json_error(['message' => 'No autorizado']);
    }

    $user_id = get_current_user_id();
    $paged   = intval($_POST['page'] ?? 1);
    $per_page = 8;

    // Obtener fotos del CPT "fotos" relacionadas con el modelo
    $fotos = get_posts([
        'post_type' => 'fotos',
        'meta_key' => '_modelo_relacionado',
        'meta_value' => $user_id,
        'posts_per_page' => $per_page,
        'paged' => $paged,
    ]);

    // Total de fotos (para calcular páginas)
    $total_query = new WP_Query([
    'post_type'      => 'fotos',
    'meta_key'       => '_modelo_relacionado',
    'meta_value'     => $user_id,
    'posts_per_page' => -1,
    'fields'         => 'ids',
    ]);
    $total_fotos = $total_query->found_posts;
    $total_pages = ceil($total_fotos / $per_page);
    wp_reset_postdata();

    ob_start();

    if ($fotos) :
        foreach ($fotos as $foto) : ?>
            <div class="relative group overflow-hidden rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300">
                <!-- Contenedor cuadrado -->
                <div class="aspect-square w-full h-auto relative overflow-hidden rounded-2xl">
                    <?php echo get_the_post_thumbnail($foto->ID, 'medium', [
                        'class' => 'w-full h-full object-cover rounded-2xl transform group-hover:scale-105 transition-transform duration-500 ease-out'
                    ]); ?>

                    <!-- Overlay de Likes -->
                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col items-center justify-center">
                        <p class="text-white text-lg font-semibold tracking-wide">
                            ❤️ <?php echo rand(200, 1800); ?> <span class="text-sm font-normal ml-1">Likes</span>
                        </p>
                    </div>

                    <!-- Botón eliminar -->
                    <button 
                        class="delete-foto absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-all duration-300 transform hover:scale-110 cursor-pointer" 
                        data-id="<?php echo $foto->ID; ?>"
                        title="Eliminar esta foto"
                    >
                        <img src="/wp-content/uploads/2025/10/basura-blanco.png" alt="Eliminar" class="w-6 h-6">
                    </button>
                </div>
            </div>
        <?php endforeach;
    else: ?>
        <p class="text-center text-gray-500 col-span-full py-8">No hay fotos disponibles.</p>
    <?php endif;

    $html = ob_get_clean();

    wp_send_json_success([
        'html'         => $html,
        'total_pages'  => $total_pages,
        'current_page' => $paged,
    ]);
}
