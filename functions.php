<?php
/**
 * Funciones principales del tema Gran Sultana
 */

/**
 * Configuración base del tema
 */
function gran_sultana_setup() {
    add_theme_support('title-tag');       // Permite que WP maneje el <title>
    add_theme_support('post-thumbnails'); // Activa imágenes destacadas
    register_nav_menus([
        'primary' => __('Menú Principal', 'lagransultana'),
    ]);
}
add_action('after_setup_theme', 'gran_sultana_setup');

/**
 * Cargar estilos principales (Tailwind compilado)
 */
function gran_sultana_enqueue_scripts() {
    wp_enqueue_style(
        'gran-sultana-style',
        get_template_directory_uri() . '/assets/css/style.css',
        [],
        filemtime(get_template_directory() . '/assets/css/style.css') // Cache-busting automático
    );
}
add_action('wp_enqueue_scripts', 'gran_sultana_enqueue_scripts');


/**
 * Ocultar la barra de administración de WordPress en el frontend
 * para todos los usuarios excepto administradores.
 */
add_action('after_setup_theme', function() {
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
});



/**
 * Incluir archivos modulares
 */
require_once get_template_directory() . '/inc/roles/roles.php';               // Roles personalizados base
require_once get_template_directory() . '/inc/cpt/cpt-modelos.php';
    // CPT Modelos
require_once get_template_directory() . '/inc/cpt/cpt-fotos.php';       // CPT Fotos
require_once get_template_directory() . '/inc/roles/rol-modelo.php';    // Lógica específica del rol Modelo
require_once get_template_directory() . '/inc/auth/modal.php';
require_once get_template_directory() . '/inc/ajax/upload-foto.php';
require_once get_template_directory() . '/inc/ajax/delete-foto.php';
/**
 * Incluir endpoints AJAX del perfil público del modelo
 */
require_once get_template_directory() . '/inc/ajax/model-follow.php';
require_once get_template_directory() . '/inc/ajax/model-like.php';
require_once get_template_directory() . '/inc/ajax/model-views.php';
require_once get_template_directory() . '/inc/ajax/gallery-pagination.php';
require_once get_template_directory() . '/inc/ajax/gallery-pagination-public.php';
require_once get_template_directory() . '/inc/modals/info-modal.php';
require_once get_template_directory() . '/inc/shortcodes/register.php';
require_once get_template_directory() . '/inc/shortcodes/login.php';
require_once get_template_directory() . '/inc/auth/login-handler.php';
require_once get_template_directory() . '/inc/users/profile.php';
// Sistema de puntos
require_once get_template_directory() . '/inc/points/points-handler.php';
require_once get_template_directory() . '/inc/auth/register-handler.php';
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script(
        'gran-sultana-auth',
        get_template_directory_uri() . '/assets/js/auth.js',
        [],
        filemtime(get_template_directory() . '/assets/js/auth.js'),
        true
    );

    wp_localize_script('gran-sultana-auth', 'gsAuth', [
        'ajaxUrl' => admin_url('admin-ajax.php')
    ]);
});
require_once get_template_directory() . '/inc/users/hooks.php';
/**
 * Permisos especiales para Administrador (gestión de Fotos)
 */
add_action('after_setup_theme', function () {
    $admin = get_role('administrator');
    if ( $admin ) {
        $caps = [
            'edit_foto','read_foto','delete_foto',
            'edit_fotos','edit_others_fotos','publish_fotos','read_private_fotos',
            'delete_fotos','delete_private_fotos','delete_published_fotos','delete_others_fotos',
            'edit_private_fotos','edit_published_fotos','create_fotos',
        ];
        foreach ($caps as $cap) {
            $admin->add_cap($cap);
        }
    }
});
/**
 * Forzar plantilla pública del modelo
 */
function lgs_load_model_template($template) {
    if (is_singular('modelo')) {
        $new_template = locate_template(array('/template-parts/model/public-model-profile.php'));
        if (!empty($new_template)) {
            return $new_template;
        }
    }
    return $template;
}
add_filter('single_template', 'lgs_load_model_template');

/***************/

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script(
        'gran-sultana-auth',
        get_template_directory_uri() . '/assets/js/auth.js',
        [],
        filemtime(get_template_directory() . '/assets/js/auth.js'),
        true
    );
});
wp_enqueue_script(
  'gs-mobile-menu',
  get_template_directory_uri() . '/assets/js/mobile-menu.js',
  array(),
  '1.0.0',
  true // ⬅️ esto es lo importante
);

// Script global para modales
function gs_enqueue_modals_script() {
    if (is_page('mi-cuenta')) {
        wp_enqueue_script(
            'gs-modals',
            get_template_directory_uri() . '/assets/js/gs-modals.js',
            array('jquery'),
            filemtime(get_template_directory() . '/assets/js/gs-modals.js'),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'gs_enqueue_modals_script');


wp_enqueue_script(
  'gs-user-profile',
  get_template_directory_uri() . '/assets/js/user-profile.js',
  array('jquery'),
  '1.0.0',
  true
);
wp_localize_script('gs-user-profile', 'gsProfile', array(
  'ajaxUrl' => admin_url('admin-ajax.php'),
  'nonce'   => wp_create_nonce('gs_profile_nonce')
));

//////////////////////////////

add_action('wp_enqueue_scripts', 'gs_enqueue_scripts_condicionales');
function gs_enqueue_scripts_condicionales() {

    // Obtener la URL actual
    $current_url = home_url(add_query_arg(array(), $GLOBALS['wp']->request));

    // Cargar script en la galería privada del modelo (panel)
    if (isset($_GET['view']) && $_GET['view'] === 'galeria' && strpos($current_url, 'mi-cuenta') !== false) {
        gs_enqueue_model_gallery_script();
        return;
    }

    // Cargar script en las páginas del CPT "fotos"
    if (is_singular('fotos')) {
        gs_enqueue_model_gallery_script();
        return;
    }
}

/**
 * Función auxiliar que encola y localiza el script de la galería
 */
function gs_enqueue_model_gallery_script() {
    wp_enqueue_script(
        'model-gallery',
        get_template_directory_uri() . '/assets/js/model-gallery.js',
        array('jquery'),
        '1.0.0',
        true
    );

    // ✅ Cambiar el nombre de la variable localizada para evitar conflicto
    wp_localize_script('model-gallery', 'gs_private_gallery', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('gs_private_gallery_nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'gs_enqueue_model_gallery_script');


add_action('wp_enqueue_scripts', function() {
    if (is_singular('modelo')) { 
        wp_enqueue_script(
            'gs-public-model-gallery',
            get_template_directory_uri() . '/assets/js/public-model-gallery.js',
            ['jquery'],
            '1.0',
            true
        );

        // ✅ Localizamos el objeto correcto (no ajaxurl suelto)
        wp_localize_script('gs-public-model-gallery', 'gs_public_gallery', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('gs_public_gallery_nonce')
        ]);
    }
});



/******************************************************
 * 📸 SISTEMA DE TAMAÑOS DE IMAGEN - LA GRAN SULTANA
 * ----------------------------------------------------
 * Este bloque define tamaños personalizados y calidad
 * de compresión optimizada para las imágenes del sitio.
 * ----------------------------------------------------
 *  - modelo_panel → Galería del modelo (panel privado)
 *  - full → Perfil público (máxima calidad)
 ******************************************************/

add_action('after_setup_theme', function () {
    /**
     * 📷 Tamaño intermedio para las fotos del panel del modelo
     * 600x600 px con recorte exacto (crop = true)
     * Ideal balance entre nitidez y rendimiento (~80–100 KB)
     */
    add_image_size('modelo_panel', 600, 600, true);

    /**
     * 📷 Tamaño de miniatura cuadrado pequeño (si lo necesitas más adelante)
     * 300x300 px (similar a 'medium', pero crop cuadrado exacto)
     */
    add_image_size('modelo_thumb', 300, 300, true);
});


/******************************************************
 * 🧠 FILTRO DE COMPRESIÓN JPEG
 * Aumenta ligeramente la calidad de compresión solo
 * para las imágenes 'modelo_panel' y 'modelo_thumb'.
 * Mantiene la configuración global del resto igual.
 ******************************************************/
add_filter('jpeg_quality', function ($quality, $context) {
    // Mejor calidad para imágenes del panel del modelo
    if (in_array($context, ['modelo_panel', 'modelo_thumb'])) {
        return 90; // valor entre 0 y 100 (default WP ~82)
    }
    return $quality;
}, 10, 2);


/******************************************************
 * 🧰 FUNCIÓN AUXILIAR (opcional)
 * Permite obtener una URL de imagen segura en el tamaño deseado.
 * Si no existe la versión optimizada, devuelve la original.
 ******************************************************/
if (!function_exists('gs_get_model_image')) {
    function gs_get_model_image($post_id, $size = 'modelo_panel') {
        $url = get_the_post_thumbnail_url($post_id, $size);
        if (!$url) {
            $url = get_the_post_thumbnail_url($post_id, 'full');
        }
        return esc_url($url);
    }
}


/**
 * Encolar JS del perfil público del modelo
 */
function lgs_enqueue_public_model_scripts() {
    if (is_page_template('template-parts/model/public-model-profile.php') || is_singular('modelo')) {

        wp_enqueue_script(
            'public-model-profile',
            get_template_directory_uri() . '/assets/js/public-model-profile.js',
            array('jquery'),
            '1.0',
            true
        );

        wp_localize_script('public-model-profile', 'gs_public_profile', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('gs_public_profile_nonce'),
        ]);
    }
}
add_action('wp_enqueue_scripts', 'lgs_enqueue_public_model_scripts');


/////////////////////////////

wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr', [], null, true);
wp_enqueue_style('flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');

wp_enqueue_script('intlTelInput', 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.1/build/js/intlTelInput.min.js', [], null, true);
wp_enqueue_style('intlTelInput-css', 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.1/build/css/intlTelInput.min.css');