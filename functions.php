<?php
/**
 * Funciones principales del tema Gran Sultana
 */

function gran_sultana_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'gran_sultana_setup');

function gran_sultana_enqueue_scripts() {
    wp_enqueue_style(
        'gran-sultana-style', 
        get_template_directory_uri() . '/assets/css/style.css'
    );
}
add_action('wp_enqueue_scripts', 'gran_sultana_enqueue_scripts');

/***********************************************************/

function gran_sultana_register_menus() {
    register_nav_menus(array(
        'primary' => __('Menú Principal', 'lagransultana'),
    ));
}
add_action('after_setup_theme', 'gran_sultana_register_menus');


require_once get_template_directory() . '/inc/roles.php';


/**********REGISTRO DE CPT PERSONALIZADOS ************/
require_once get_template_directory() . '/inc/cpt/cpt-modelos.php';
require_once get_template_directory() . '/inc/cpt/cpt-fotos.php';
require_once get_template_directory() . '/inc/roles/rol-modelo.php';
