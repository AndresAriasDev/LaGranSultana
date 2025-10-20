<?php
// 🔹 CPT: Fotos de Modelos
function gs_register_cpt_fotos() {
    $labels = array(
        'name'          => 'Fotos',
        'singular_name' => 'Foto',
        'menu_name'     => 'Fotos',
        'add_new'       => 'Subir Nueva',
        'add_new_item'  => 'Subir Nueva Foto',
    );

    $args = array(
        'labels'        => $labels,
        'public'        => true,
        'has_archive'   => false,
        'menu_icon'     => 'dashicons-format-image',
        'supports'      => array('title', 'thumbnail', 'author'),
        'rewrite'       => array('slug' => 'fotos', 'with_front' => false),
        'show_in_rest'  => true,
    );

    register_post_type('fotos', $args);
}
add_action('init', 'gs_register_cpt_fotos');
