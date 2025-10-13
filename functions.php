<?php
/**
 * Funciones principales del tema Gran Sultana
 */

function gran_sultana_setup() {
    // Soporte para el título del sitio
    add_theme_support('title-tag');
    // Soporte para imágenes destacadas
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'gran_sultana_setup');

// Encolar estilos y scripts
function gran_sultana_enqueue_scripts() {
    wp_enqueue_style('gran-sultana-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'gran_sultana_enqueue_scripts');
