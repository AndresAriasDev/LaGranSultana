<?php
/**
 * Plantilla principal del tema Gran Sultana
 */
get_header();
?>

<main class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-center bg-red-500">¡Bienvenido a Gran Sultana!</h1>
    <p class="text-center text-gray-500 mt-2">Este es tu tema base personalizado.</p>
<button data-open-login class="px-4 py-2 bg-[var(--color-azul-pr)] text-white rounded-md mr-2">
  Iniciar sesión
</button>

<button data-open-register class="px-4 py-2 bg-[var(--color-amarillo-pr)] text-white rounded-md">
  Registrarse
</button>
<?php get_template_part('template-parts/modals/account-points');?>

</main>

<?php
get_footer();
