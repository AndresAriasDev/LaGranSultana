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
            
            <div class="flex items-center justify-between px-5 py-4 border-b border-[var(--color-borde)]">
                <h3 id="gs-auth-title" class="text-base font-semibold text-[var(--color-tx-azul)]">
                    Iniciar sesión
                </h3>
                <button type="button" class="gs-auth-close p-2 rounded-md hover:bg-[var(--color-blanco-bajo)]">✕</button>
            </div>

            <!-- Contenedor con altura animada -->
            <div id="gs-auth-container" class="p-5 transition-all duration-300 ease-in-out">
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

