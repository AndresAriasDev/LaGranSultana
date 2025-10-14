<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * Maneja el registro de nuevos usuarios vía AJAX o envío tradicional.
 */
function gs_handle_user_registration() {
    // Verificar nonce de seguridad
    if ( ! isset($_POST['gs_register_nonce']) || ! wp_verify_nonce($_POST['gs_register_nonce'], 'gs_register_nonce') ) {
        wp_send_json_error(['message' => 'Sesión no válida. Actualiza la página e inténtalo de nuevo.']);
    }

    // Sanitizar entradas
    $name     = sanitize_text_field($_POST['name'] ?? '');
    $email    = sanitize_email($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validaciones básicas
    $errors = [];

    if ( empty($name) || empty($email) || empty($password) ) {
        $errors[] = 'Todos los campos son obligatorios.';
    }
    if ( ! is_email($email) ) {
        $errors[] = 'El correo electrónico no es válido.';
    }
    if ( email_exists($email) ) {
        $errors[] = 'El correo ya está registrado.';
    }

    // Si hay errores, devolverlos
    if ( ! empty($errors) ) {
        wp_send_json_error(['message' => implode('<br>', $errors)]);
    }

    // Crear nombre de usuario a partir del correo
    $username = sanitize_user( current( explode('@', $email) ), true );
    if ( username_exists($username) ) {
        $username .= rand(100, 999);
    }

    // Crear usuario
    $user_id = wp_create_user($username, $password, $email);
    if ( is_wp_error($user_id) ) {
        wp_send_json_error(['message' => 'Error al crear el usuario: ' . $user_id->get_error_message()]);
    }

    // Asignar rol base
    $user = new WP_User($user_id);
    $user->set_role('usuario_normal');

    // Guardar nombre completo
    update_user_meta($user_id, 'first_name', $name);

    // Iniciar sesión automáticamente
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);

    // Responder éxito
    wp_send_json_success([
        'message' => 'Cuenta creada correctamente.',
        'redirect' => home_url('/mi-cuenta')
    ]);
}
add_action('wp_ajax_nopriv_gs_handle_user_registration', 'gs_handle_user_registration');
add_action('wp_ajax_gs_handle_user_registration', 'gs_handle_user_registration'); // por si ya está logueado
