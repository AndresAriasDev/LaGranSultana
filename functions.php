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
 * Incluir archivos modulares
 */
require_once get_template_directory() . '/inc/roles.php';               // Roles personalizados base
require_once get_template_directory() . '/inc/cpt/cpt-modelos.php';     // CPT Modelos
require_once get_template_directory() . '/inc/cpt/cpt-fotos.php';       // CPT Fotos
require_once get_template_directory() . '/inc/roles/rol-modelo.php';    // Lógica específica del rol Modelo
require_once get_template_directory() . '/inc/auth/modal.php';

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
