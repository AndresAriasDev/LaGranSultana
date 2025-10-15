<?php
/* Template Name: Página Mi Cuenta */

if ( ! defined( 'ABSPATH' ) ) exit;

// Si el usuario no está logueado, redirigir al login
if ( ! is_user_logged_in() ) {
    wp_redirect( home_url('/login') );
    exit;
}

$current_user = wp_get_current_user();
$user_roles   = $current_user->roles;
?>

<?php get_header(); ?>

<div class="mi-cuenta" style="max-width:800px; margin:50px auto;">
    <h2>Hola, <?php echo esc_html( $current_user->display_name ); ?> 👋</h2>
    <p>Bienvenido a tu cuenta.</p>

    <hr>

    <h3>Tu información básica</h3>
    <ul>
        <li><strong>Nombre de usuario:</strong> <?php echo esc_html( $current_user->user_login ); ?></li>
        <li><strong>Correo electrónico:</strong> <?php echo esc_html( $current_user->user_email ); ?></li>
        <li><strong>Rol actual:</strong> <?php echo esc_html( ucfirst( implode(', ', $user_roles) ) ); ?></li>
    </ul>

    <hr>

    <?php if ( in_array('modelo', $user_roles) ) : ?>
        <h3>Panel de Modelo</h3>
        <p>Aquí más adelante podrás subir fotos, editar tu perfil y gestionar tu galería.</p>
    <?php elseif ( in_array('colaborador', $user_roles) ) : ?>
        <h3>Panel de Colaborador</h3>
        <p>En el futuro aquí verás tus puntos y referidos.</p>
    <?php elseif ( in_array('admin_tienda', $user_roles) ) : ?>
        <h3>Panel de Administrador de Tienda</h3>
        <p>Acceso a la gestión de productos, pedidos, etc.</p>
    <?php else : ?>
        <h3>Panel de Usuario Normal</h3>
        <p>Desde aquí podrás acceder a tus compras, reservas y otros datos.</p>
    <?php endif; ?>

    <hr>

        <form method="POST" action="<?php echo wp_logout_url( home_url('/') ); ?>">
            <button type="submit">Cerrar sesión</button>
        </form>

</div>

<?php get_footer(); ?>
