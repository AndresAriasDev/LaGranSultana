<?php
/* Template Name: Página de Login */

// Evitar acceso directo
if ( ! defined( 'ABSPATH' ) ) exit;

if ( is_user_logged_in() ) {
    wp_redirect( home_url('/mi-cuenta') );
    exit;
}

// Manejo del formulario
if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_usuario_nonce']) ) {
    if ( wp_verify_nonce( $_POST['login_usuario_nonce'], 'login_usuario_action' ) ) {

        $creds = array();
        $creds['user_login']    = sanitize_user( $_POST['username'] );
        $creds['user_password'] = $_POST['password'];
        $creds['remember']      = isset($_POST['remember']) ? true : false;

        $user = wp_signon( $creds, false );

        if ( is_wp_error( $user ) ) {
            $error_message = $user->get_error_message();
        } else {
            wp_redirect( home_url('/mi-cuenta') );
            exit;
        }
    }
}
?>

<?php get_header(); ?>

<div class="login-form" style="max-width:500px; margin:50px auto;">
    <h2>Iniciar sesión</h2>

    <?php if ( isset($error_message) ) : ?>
        <div class="error" style="color:red; margin-bottom:10px;">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <p>
            <label for="username">Nombre de usuario o correo</label><br>
            <input type="text" name="username" id="username" required>
        </p>

        <p>
            <label for="password">Contraseña</label><br>
            <input type="password" name="password" id="password" required>
        </p>

        <p>
            <label>
                <input type="checkbox" name="remember"> Recordarme
            </label>
        </p>

        <?php wp_nonce_field( 'login_usuario_action', 'login_usuario_nonce' ); ?>

        <p>
            <button type="submit">Entrar</button>
        </p>

        <p>¿No tienes cuenta? <a href="<?php echo esc_url( home_url('/registro') ); ?>">Regístrate aquí</a></p>
    </form>
</div>

<?php get_footer(); ?>
