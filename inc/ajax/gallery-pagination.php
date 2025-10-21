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

if ($fotos && count($fotos) > 0) : ?>
    <?php foreach ($fotos as $foto) : ?>
        <div class="relative group overflow-hidden rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300">
            <div class="aspect-square w-full h-auto relative overflow-hidden rounded-2xl bg-gray-100 animate-pulse">
                <img 
                    loading="lazy"
                    src="<?php echo gs_get_model_image($foto->ID, 'modelo_panel'); ?>" 
                    alt="" 
                    class="w-full h-full object-cover rounded-2xl opacity-0 transition-opacity duration-500 ease-out"
                    onload="this.classList.remove('opacity-0', 'animate-pulse'); this.parentElement.classList.remove('animate-pulse', 'bg-gray-100');"
                />

                <!-- 🔹 Overlay de Likes -->
                <div class="absolute inset-0 bg-white/30 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center">
                    <div class="rounded-xl px-6 py-4 text-center transform scale-95 group-hover:scale-100 transition-transform duration-300 ease-out">
                        <p class="text-[var(--color-blanco-pr)] text-4xl font-bold leading-tight tracking-wide drop-shadow-[0_2px_4px_rgba(0,0,0,0.7)]">
                            <?php echo rand(200, 1800); ?>
                        </p>
                        <p class="text-[var(--color-blanco-pr)] text-sm uppercase tracking-wider mt-1 opacity-90 drop-shadow-[0_1px_3px_rgba(0,0,0,0.6)]">
                            Likes
                        </p>
                    </div>
                </div>

                <!-- 🔸 Botón eliminar -->
                <button 
                    class="delete-foto absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-all duration-300 transform hover:scale-110 cursor-pointer bg-[var(--color-rojo-pr)] rounded-lg p-2"
                    data-id="<?php echo $foto->ID; ?>"
                    title="Eliminar esta foto"
                >
                    <img src="/wp-content/uploads/2025/10/basura-blanco.png" alt="Eliminar" class="w-5 h-5">
                </button>
            </div>
        </div>
    <?php endforeach; ?>
<?php else : ?>
    <div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
        <img src="/wp-content/uploads/2025/10/icono-camara.png" alt="Sin fotos" class="w-16 h-16 opacity-70 mb-4">
        <p class="text-[var(--color-tx-azul)] text-lg font-medium">
            Aún no has publicado fotos.
        </p>
        <p class="text-[var(--color-tx-negro)] text-sm mt-1 opacity-80">
            Sube tu primera foto para comenzar a destacar en la galería.
        </p>
    </div>
<?php endif;


    $html = ob_get_clean();

    wp_send_json_success([
        'html'         => $html,
        'total_pages'  => $total_pages,
        'current_page' => $paged,
    ]);
}
