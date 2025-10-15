<?php
/* Template Name: Mi Cuenta */

if ( ! is_user_logged_in() ) {
    wp_redirect( home_url('/') );
    exit;
}

get_header();

$current_user = wp_get_current_user();
$roles = (array) $current_user->roles;

// Panel dinámico según el rol
if (in_array('modelo', $roles)) {
    get_template_part('template-parts/account/account', 'model');
} elseif (in_array('colaborador', $roles)) {
    get_template_part('template-parts/account/account', 'colaborator');
} else {
    get_template_part('template-parts/account/account', 'user');
}

get_footer();
