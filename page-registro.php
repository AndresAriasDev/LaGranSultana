<?php
/* Template Name: Página de Registro */
if ( ! defined('ABSPATH') ) exit;

get_header();
?>

<main class="min-h-screen flex flex-col items-center justify-center bg-[var(--color-blanco-bajo)] px-4 py-16 text-center">
    <h1 class="text-2xl font-semibold text-[var(--color-tx-azul)] mb-4">Forma parte de La Gran Sultana ✨</h1>
    <p class="text-[var(--color-tx-negro)] mb-6 max-w-md">
        Crea tu cuenta y descubre todos los beneficios de ser parte de nuestra comunidad. 
        Conéctate, explora y muestra tu talento al mundo.
    </p>
    <button data-open-register
            class="px-5 py-2 rounded-md bg-[var(--color-amarillo-pr)] text-white font-medium hover:opacity-90">
        Crear mi cuenta
    </button>
</main>

<?php get_footer(); ?>
