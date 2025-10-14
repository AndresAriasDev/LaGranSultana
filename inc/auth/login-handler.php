<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * Maneja el inicio de sesión de usuarios vía AJAX.
 */
function gs_handle_user_login() {
    ob_clean(); // 💥 Limpia cualquier salida previa (espacios, notices, etc.)

    // Verificar nonce
    if ( ! isset($_POST['gs_login_nonce']) || ! wp_verify_nonce($_POST['gs_login_nonce'], 'gs_login_nonce') ) {
        wp_send_json_error(['message' => 'Sesión no válida. Recarga la página e inténtalo de nuevo.']);
    }

    $email    = sanitize_email($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = ! empty($_POST['remember']);

    if ( empty($email) || empty($password) ) {
        wp_send_json_error(['message' => 'Debes ingresar tu correo y contraseña.']);
    }

    $user = get_user_by('email', $email);
    if ( ! $user ) {
        wp_send_json_error(['message' => 'No existe una cuenta con ese correo.']);
    }

    $creds = [
        'user_login'    => $user->user_login,
        'user_password' => $password,
        'remember'      => $remember,
    ];

    // 🔒 Evita redirección
    remove_all_actions('wp_login_failed');
    remove_all_actions('wp_login');
    $login = wp_signon($creds, false);

    if ( is_wp_error($login) ) {
        wp_send_json_error(['message' => 'Credenciales incorrectas. Verifica tus datos.']);
    }

    // Configura sesión
    wp_set_current_user($login->ID);
    wp_set_auth_cookie($login->ID, $remember);

    // 🔥 Respuesta JSON limpia
    wp_send_json_success(['message' => 'Inicio de sesión exitoso.']);

    wp_die();
}
add_action('wp_ajax_nopriv_gs_handle_user_login', 'gs_handle_user_login');
add_action('wp_ajax_gs_handle_user_login', 'gs_handle_user_login');
