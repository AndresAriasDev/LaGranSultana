<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * Imprime el contenedor del modal de autenticación en todo el sitio.
 * (Se mantiene oculto hasta que se dispare con JS)
 */
add_action('wp_footer', function () {
    ?>
    <div id="gs-auth-overlay" class="fixed inset-0 z-[9998] hidden bg-black/60"></div>

    <div id="gs-auth-modal"
         class="fixed inset-0 z-[9999] hidden flex items-center justify-center p-4">
        <div class="w-full max-w-md rounded-2xl bg-[var(--color-blanco-pr)] shadow-xl">
            <!-- Header -->
            <div class="flex items-center justify-between px-5 py-4 border-b border-[var(--color-borde)]">
                <h3 class="text-base font-semibold text-[var(--color-tx-azul)]">Autenticación</h3>
                <button type="button" class="gs-auth-close p-2 rounded-md hover:bg-[var(--color-blanco-bajo)]">
                    ✕
                </button>
            </div>

            <!-- Contenido (lo insertaremos en el siguiente paso) -->
            <div id="gs-auth-modal-content" class="p-5">
                <!-- Aquí cargaremos el formulario de registro/login -->
                <p class="text-sm text-[var(--color-tx-negro)]">Cargando…</p>
            </div>
        </div>
    </div>
    <?php
});
