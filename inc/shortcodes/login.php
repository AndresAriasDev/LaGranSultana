<?php
if ( ! defined('ABSPATH') ) exit;

function gs_login_form_shortcode( $atts = [] ) {
    // Si ya está logueado, lo redirigimos directamente
    if ( is_user_logged_in() ) {
        wp_redirect( home_url('/mi-cuenta') );
        exit;
    }

    ob_start();
    get_template_part('template-parts/auth/login-form');
    return ob_get_clean();
}
add_shortcode('gs_login_form', 'gs_login_form_shortcode');
