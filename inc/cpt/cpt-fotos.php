<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Registrar CPT: Fotos
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
        'not_found_in_trash' => 'No se encontraron fotos en la papelera',
    );

    $args = array(
        'labels'             => $labels,
        'description'        => 'Unidad de contenido para galerías de modelos',
        // ⚠️ No público en frontend
        'public'             => false,
        'publicly_queryable' => false,
        'exclude_from_search'=> true,
        'has_archive'        => false,
        'rewrite'            => false,

        // UI en admin
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_admin_bar'  => true,
        'menu_icon'          => 'dashicons-format-image',

        // Editor
        'supports'           => array( 'title', 'editor', 'thumbnail', 'author' ),
        'hierarchical'       => false,

        // REST (útil para futuros formularios/headless)
        'show_in_rest'       => true,
        'rest_base'          => 'fotos',

        // Capacidades dedicadas (map_meta_cap ON)
        'capability_type'    => array( 'foto', 'fotos' ),
        'map_meta_cap'       => true,
        'capabilities'       => array(
            'edit_post'              => 'edit_foto',
            'read_post'              => 'read_foto',
            'delete_post'            => 'delete_foto',

            'edit_posts'             => 'edit_fotos',
            'edit_others_posts'      => 'edit_others_fotos',
            'publish_posts'          => 'publish_fotos',
            'read_private_posts'     => 'read_private_fotos',

            'delete_posts'           => 'delete_fotos',
            'delete_private_posts'   => 'delete_private_fotos',
            'delete_published_posts' => 'delete_published_fotos',
            'delete_others_posts'    => 'delete_others_fotos',

            'edit_private_posts'     => 'edit_private_fotos',
            'edit_published_posts'   => 'edit_published_fotos',

            // Necesaria desde WP 4.5+ para el botón "Añadir nueva"
            'create_posts'           => 'create_fotos',
        ),

        // Limpieza si se borra el usuario autor (opcional)
        'delete_with_user'   => true,
    );

    register_post_type( 'fotos', $args );
}
add_action( 'init', 'gran_sultana_register_cpt_fotos' );
