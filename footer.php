<?php
/**
 * Footer del tema La Gran Sultana
 *
 * @package LaGranSultana
 */
?>
<!-- Contenedor global de notificaciones -->
<div id="gs-toast-container" class="fixed bottom-2 left-2 z-[10000] flex flex-col gap-2 pointer-events-none"></div>
<footer id="site-footer" class="bg-dark text-light py-6">
    <div class="container mx-auto text-center">
        <p>&copy; <?php echo date('Y'); ?> La Gran Sultana. Todos los derechos reservados.</p>
        <p>Diseñado por tu equipo de desarrollo</p>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
