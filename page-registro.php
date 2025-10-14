<?php
/* Template Name: Página de Registro */

// Evitar acceso directo
if ( ! defined( 'ABSPATH' ) ) exit;

// Manejo del formulario
if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registro_usuario_nonce']) ) {
    if ( wp_verify_nonce( $_POST['registro_usuario_nonce'], 'registro_usuario_action' ) ) {

        $username = sanitize_user( $_POST['username'] );
        $email    = sanitize_email( $_POST['email'] );
        $password = $_POST['password'];

        $errors = array();

        // Validaciones básicas
        if ( username_exists($username) )
            $errors[] = 'El nombre de usuario ya existe.';
        if ( email_exists($email) )
            $errors[] = 'El correo electrónico ya está registrado.';
        if ( empty($username) || empty($email) || empty($password) )
            $errors[] = 'Todos los campos son obligatorios.';

        // Si no hay errores, registrar usuario
        if ( empty($errors) ) {
            $user_id = wp_create_user( $username, $password, $email );

            if ( ! is_wp_error($user_id) ) {
                // Asignar rol "usuario_normal"
                $user = new WP_User($user_id);
                $user->set_role('usuario_normal');

                // Iniciar sesión automáticamente
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id);

                // Redirigir a su cuenta o página de bienvenida
                wp_redirect( home_url('/mi-cuenta') );
                exit;
            } else {
                $errors[] = 'Error al crear el usuario: ' . $user_id->get_error_message();
            }
        }
    }
}
?>

<?php get_header(); ?>

<div class="registro-form" style="max-width:500px; margin:50px auto;">
    <h2>Crear una cuenta</h2>

    <?php if ( ! empty($errors) ) : ?>
        <div class="errores" style="color:red; margin-bottom:10px;">
            <?php foreach ($errors as $error) echo '<p>'.esc_html($error).'</p>'; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <p>
            <label for="username">Nombre de usuario</label><br>
            <input type="text" name="username" id="username" required>
        </p>

        <p>
            <label for="email">Correo electrónico</label><br>
            <input type="email" name="email" id="email" required>
        </p>

        <p>
            <label for="password">Contraseña</label><br>
            <input type="password" name="password" id="password" required>
        </p>

        <?php wp_nonce_field( 'registro_usuario_action', 'registro_usuario_nonce' ); ?>

        <p>
            <button type="submit">Registrarme</button>
        </p>
    </form>
</div>

<?php get_footer(); ?>
