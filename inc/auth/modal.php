<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * Modal de autenticación unificado (Login / Registro)
 */
add_action('wp_footer', function () {
    ?>
    <div id="gs-auth-overlay" class="fixed inset-0 z-[9998] hidden bg-black/60"></div>

        <div id="gs-auth-modal"
            class="fixed inset-0 z-[9999] hidden flex items-center justify-center p-4">
        <div class="w-full max-w-md rounded-2xl bg-[var(--color-blanco-pr)] shadow-xl overflow-hidden transition-all duration-300 ease-in-out">
            
        <div class="px-5 pt-6 pb-1 flex justify-center">
        <h3 id="gs-auth-title"
            class="font-inter text-xl font-semibold text-[var(--color-tx-azul)] tracking-wide text-center">
            Iniciar sesión
        </h3>
        </div>


            <!-- Contenedor con altura animada -->
            <div id="gs-auth-container"
     class="px-5 py-0 transition-[height,min-height,opacity] duration-300 ease-in-out min-h-[250px]">

                <div id="gs-auth-login-content"
                    class="p-5 transition-opacity duration-300 ease-in-out opacity-100">
                <?php get_template_part('template-parts/auth/login-form'); ?>
                </div>

                <div id="gs-auth-register-content"
                    class="p-5 transition-opacity duration-300 ease-in-out opacity-0 hidden">
                <?php get_template_part('template-parts/auth/register-form'); ?>
                </div>
            </div>
        </div>
        </div>
    <?php
});

