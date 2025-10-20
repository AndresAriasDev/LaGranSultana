<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * Modal global para mensajes e información
 * Ubicación: inc/modals/info-modal.php
 */
add_action('wp_body_open', function () {
    ?>
    <!-- 🧩 Overlay general -->
    <div id="gs-info-overlay" 
         class="fixed inset-0 z-[9980] hidden bg-black/60 backdrop-blur-sm transition-opacity duration-300"></div>

    <!-- 🧱 Contenedor principal del modal -->
    <div id="gs-info-modal" 
         class="fixed inset-0 z-[9990] hidden flex items-center justify-center p-4 transition-all duration-300">

        <!-- 📦 Panel interno -->
        <div id="gs-info-inner"
             class="w-full max-w-md rounded-2xl bg-[var(--color-blanco-pr)] shadow-xl overflow-hidden opacity-0 scale-95 transition-all duration-300 ease-out">

            <!-- 🧠 Contenido dinámico (directo) -->
            <?php get_template_part('template-parts/modals/puntos-perfil-info'); ?>
             <!-- 🧠 Contenido dinámico (directo) -->
            <?php get_template_part('template-parts/modals/puntos-recompensa-info'); ?>
            <?php get_template_part('template-parts/modals/subida-foto-info'); ?>

        </div>
    </div>
    <?php
});
