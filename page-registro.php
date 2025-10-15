<?php
/* Template Name: Página de Registro */
if ( ! defined('ABSPATH') ) exit;

get_header();
?>

<main class="min-h-screen flex flex-col items-center justify-center bg-[var(--color-blanco-bajo)] px-4 py-16 text-center">

    <?php if ( is_user_logged_in() ) : ?>
        <!-- 🟢 Usuario YA registrado -->
        <h1 class="text-2xl font-semibold text-[var(--color-tx-azul)] mb-4">
            ¡Ya formas parte de La Gran Sultana! 🎉
        </h1>
        <p class="text-[var(--color-tx-negro)] mb-6 max-w-md">
            Gracias por ser parte de nuestra comunidad.  
            Accede a tu panel personal para gestionar tu perfil y explorar nuevas oportunidades.
        </p>
        <a href="<?php echo esc_url( home_url('/mi-cuenta') ); ?>"
           class="px-5 py-2 rounded-md bg-[var(--color-azul-pr)] text-white font-medium hover:opacity-90">
            Ir a mi cuenta
        </a>

    <?php else : ?>
        <!-- 🔵 Usuario NO logueado -->
        <h1 class="text-2xl font-semibold text-[var(--color-tx-azul)] mb-4">
            Forma parte de La Gran Sultana ✨
        </h1>
        <p class="text-[var(--color-tx-negro)] mb-6 max-w-md">
            Crea tu cuenta y descubre todos los beneficios de ser parte de nuestra comunidad.  
            Conéctate, explora y muestra tu talento al mundo.
        </p>
        <button data-open-register
                class="px-5 py-2 rounded-md bg-[var(--color-amarillo-pr)] text-white font-medium hover:opacity-90">
            Crear mi cuenta
        </button>
    <?php endif; ?>

</main>

<?php get_footer(); ?>

