<?php
// Evitar acceso directo
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Crear automáticamente un CPT "modelo" cuando un usuario obtiene el rol
 */
function gran_sultana_crear_cpt_para_modelo( $user_id, $role ) {
    if ( $role === 'modelo' ) {
        // Verificar si ya existe un post de este modelo
        $existe = get_posts(array(
            'post_type'  => 'modelos',
            'meta_key'   => 'user_id',
            'meta_value' => $user_id,
            'numberposts' => 1
        ));

        if ( empty( $existe ) ) {
            $user_info = get_userdata( $user_id );

            // Crear el post
            $post_id = wp_insert_post(array(
                'post_type'   => 'modelos',
                'post_title'  => $user_info->display_name,
                'post_status' => 'publish'
            ));

            // Guardar relación con el usuario
            update_post_meta( $post_id, 'user_id', $user_id );
        }
    }
}
add_action( 'set_user_role', 'gran_sultana_crear_cpt_para_modelo', 10, 2 );

/**
 * Sincronizar cambios de nombre del usuario con su CPT modelo
 */
function gran_sultana_actualizar_nombre_modelo( $user_id ) {
    $user_info = get_userdata( $user_id );

    $posts = get_posts(array(
        'post_type'  => 'modelos',
        'meta_key'   => 'user_id',
        'meta_value' => $user_id,
        'numberposts' => 1
    ));

    if ( !empty( $posts ) ) {
        $post_id = $posts[0]->ID;
        wp_update_post(array(
            'ID' => $post_id,
            'post_title' => $user_info->display_name
        ));
    }
}
add_action( 'profile_update', 'gran_sultana_actualizar_nombre_modelo' );
