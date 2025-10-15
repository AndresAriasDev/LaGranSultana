<?php
/* Template Name: Página de Login */
if ( ! defined('ABSPATH') ) exit;

get_header();
?>

<main class="min-h-screen flex flex-col items-center justify-center bg-[var(--color-blanco-bajo)] px-4 py-16 text-center">
    <?php if ( is_user_logged_in() ) : ?>
        <h1 class="text-2xl font-semibold text-[var(--color-tx-azul)] mb-4">¡Ya estás conectado!</h1>
        <p class="text-[var(--color-tx-negro)] mb-6">Accede a tu panel personal desde aquí.</p>
        <a href="<?php echo home_url('/mi-cuenta'); ?>"
           class="px-5 py-2 rounded-md bg-[var(--color-azul-pr)] text-white font-medium hover:opacity-90">
           Ir a mi cuenta
        </a>
    <?php else : ?>
        <h1 class="text-2xl font-semibold text-[var(--color-tx-azul)] mb-4">¿Ya tienes cuenta?</h1>
        <p class="text-[var(--color-tx-negro)] mb-6">Inicia sesión para acceder a tu panel, tus fotos o tus reservas.</p>
        <button data-open-login
                class="px-5 py-2 rounded-md bg-[var(--color-azul-pr)] text-white font-medium hover:opacity-90">
            Iniciar sesión
        </button>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
