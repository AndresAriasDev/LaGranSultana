<?php
if ( ! defined('ABSPATH') ) exit;

function gs_register_form_shortcode( $atts = [] ) {
    ob_start();
    get_template_part('template-parts/auth/register-form');
    return ob_get_clean();
}
add_shortcode('gs_register_form', 'gs_register_form_shortcode');
