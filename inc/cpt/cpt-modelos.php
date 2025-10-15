<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function gran_sultana_register_cpt_modelos() {

    $labels = array(
        'name'               => 'Modelos',
        'singular_name'      => 'Modelo',
        'menu_name'          => 'Modelos',
        'name_admin_bar'     => 'Modelo',
        'add_new'            => 'Agregar nuevo',
        'add_new_item'       => 'Agregar nuevo modelo',
        'edit_item'          => 'Editar modelo',
        'new_item'           => 'Nuevo modelo',
        'view_item'          => 'Ver modelo',
        'all_items'          => 'Todos los modelos',
        'search_items'       => 'Buscar modelos',
        'not_found'          => 'No se encontraron modelos',
        'not_found_in_trash' => 'No se encontraron modelos en la papelera'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'modelo'),
        'menu_icon'          => 'dashicons-admin-users',
        'supports'           => array('title','editor','thumbnail'),
        'show_in_rest'       => true,
        'capability_type'    => 'post',
        'map_meta_cap'       => true,
    );

    register_post_type( 'modelos', $args );
}
add_action( 'init', 'gran_sultana_register_cpt_modelos' );
