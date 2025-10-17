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
require_once get_template_directory() . '/inc/cpt/cpt-modelos.php';     // CPT Modelos
require_once get_template_directory() . '/inc/cpt/cpt-fotos.php';       // CPT Fotos
require_once get_template_directory() . '/inc/roles/rol-modelo.php';    // Lógica específica del rol Modelo
require_once get_template_directory() . '/inc/auth/modal.php';
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


wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr', [], null, true);
wp_enqueue_style('flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');

wp_enqueue_script('intlTelInput', 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.1/build/js/intlTelInput.min.js', [], null, true);
wp_enqueue_style('intlTelInput-css', 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.1/build/css/intlTelInput.min.css');
