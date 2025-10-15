<?php
function gran_sultana_create_roles() {
    if ( ! get_role('usuario_normal') ) {
        add_role('usuario_normal', 'Usuario Normal', array(
            'read' => true,
            'level_0' => true,
            'can_like' => true,
            'can_comprar' => true,
            'can_reservar' => true,
        ));
    }

    if ( ! get_role('modelo') ) {
        add_role('modelo', 'Modelo', array(
            'read' => true,
            'level_0' => true,
            'can_subir_fotos' => true,
            'can_editar_perfil' => true,
            'codigo_asociado' => true,
        ));
    }

    if ( ! get_role('colaborador') ) {
        add_role('colaborador', 'Colaborador', array(
            'read' => true,
            'level_0' => true,
            'codigo_asociado' => true,
            'ganar_puntos' => true,
        ));
    }

    if ( ! get_role('admin_tienda') ) {
        add_role('admin_tienda', 'Administrador de Tienda', array(
            'read' => true,
            'level_1' => true,
            'edit_posts' => true,
            'publish_posts' => true,
            'manage_products' => true,
        ));
    }
}
add_action('after_setup_theme', 'gran_sultana_create_roles');
