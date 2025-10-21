<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ===========================================================
 * 🎯 Sincronización de usuarios con rol "modelo"
 * -----------------------------------------------------------
 *  - Crea un CPT "modelo" cuando un usuario recibe ese rol.
 *  - Mantiene sincronizado el nombre del post con el usuario.
 * ===========================================================
 */

/**
 * 🪄 Crear automáticamente un post tipo "modelo" al asignar el rol
 */
function gran_sultana_crear_cpt_para_modelo( $user_id, $role ) {
    if ( $role !== 'modelo' ) return;

    // 📋 Buscar si ya existe un CPT para este usuario
    $existe = get_posts([
        'post_type'   => 'modelo',
        'meta_key'    => 'user_id',
        'meta_value'  => $user_id,
        'numberposts' => 1,
        'post_status' => ['publish', 'draft', 'pending']
    ]);

    if ( !empty( $existe ) ) return; // ya existe, no crear duplicado

    $user_info = get_userdata( $user_id );

    // 🧾 Nombre del modelo
    $first_name = get_user_meta($user_id, 'first_name', true);
    $display_name = $first_name ?: $user_info->display_name;

    // 🧠 Crear post tipo "modelo"
    $post_id = wp_insert_post([
        'post_type'   => 'modelo',
        'post_title'  => sanitize_text_field($display_name),
        'post_status' => 'publish',
        'post_author' => $user_id,
    ]);

    // 🔗 Guardar la relación
    if ( $post_id ) {
        update_post_meta( $post_id, 'user_id', $user_id );
    }
}
add_action( 'set_user_role', 'gran_sultana_crear_cpt_para_modelo', 10, 2 );


/**
 * 🔄 Mantener sincronizado el nombre del CPT cuando cambia el nombre del modelo
 */
function gran_sultana_actualizar_nombre_modelo( $user_id ) {
    $user_info = get_userdata( $user_id );

    // Buscar post del modelo vinculado
    $posts = get_posts([
        'post_type'   => 'modelo',
        'meta_key'    => 'user_id',
        'meta_value'  => $user_id,
        'numberposts' => 1,
        'post_status' => ['publish', 'draft', 'pending']
    ]);

    if ( empty($posts) ) return;

    $post_id = $posts[0]->ID;
    $first_name = get_user_meta($user_id, 'first_name', true);
    $display_name = $first_name ?: $user_info->display_name;

    wp_update_post([
        'ID'         => $post_id,
        'post_title' => sanitize_text_field($display_name),
    ]);
}
add_action( 'profile_update', 'gran_sultana_actualizar_nombre_modelo' );

