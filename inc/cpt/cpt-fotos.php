<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Registrar CPT Fotos
 */
function gran_sultana_register_cpt_fotos() {

    $labels = array(
        'name'               => 'Fotos',
        'singular_name'      => 'Foto',
        'menu_name'          => 'Fotos',
        'name_admin_bar'     => 'Foto',
        'add_new'            => 'Agregar nueva',
        'add_new_item'       => 'Agregar nueva foto',
        'edit_item'          => 'Editar foto',
        'new_item'           => 'Nueva foto',
        'view_item'          => 'Ver foto',
        'all_items'          => 'Todas las fotos',
        'search_items'       => 'Buscar fotos',
        'not_found'          => 'No se encontraron fotos',
        'not_found_in_trash' => 'No se encontraron fotos en la papelera'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'supports'           => array('title','thumbnail','editor'),
        'show_in_rest'       => true,
        'capability_type'    => 'post',
        'map_meta_cap'       => true,
    );

    register_post_type( 'fotos', $args );
}
add_action( 'init', 'gran_sultana_register_cpt_fotos' );
