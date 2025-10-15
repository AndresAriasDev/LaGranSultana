<!-- Menú móvil a pantalla completa -->
<div id="gs-mobile-menu"
  class="fixed inset-0 z-40 hidden bg-black/70 backdrop-blur-sm transition-opacity duration-300 opacity-0">

  <div id="gs-mobile-panel"
    class="absolute inset-0 w-full h-full bg-white transform -translate-x-full transition-transform duration-300 flex flex-col justify-between"
    style="background-color: var(--color-blanco-pr);">

    <!-- Sección superior -->
    <div class="flex flex-col flex-1 px-4 pt-6 overflow-y-auto">

<!-- Encabezado -->
<div class="flex items-center min-h-[44px] justify-between mb-5 px-0 pt-0">
  <?php if ( is_user_logged_in() ) : ?>
    <a href="<?php echo esc_url( home_url('/mi-cuenta') ); ?>"
       class="flex items-center space-x-2 px-4 h-[40px] rounded-md font-medium transition hover:opacity-90"
       style="background-color: var(--color-azul-pr); color: var(--color-tx-blanco);">
      <img src="<?php echo esc_url( get_site_url() . '/wp-content/uploads/2025/10/usuario-blanco.png' ); ?>"
           alt="Mi cuenta"
           class="h-5 w-5 opacity-90">
      <span>Mi cuenta</span>
    </a>
  <?php else : ?>
    <span></span>
  <?php endif; ?>
  <span></span>
</div>


      <!-- Barra de búsqueda -->
      <form action="<?php echo home_url('/'); ?>" method="get" class="relative mb-8">
        <input
          type="text"
          name="s"
          placeholder="Buscar productos..."
          class="w-full py-3 pl-4 pr-12 rounded-md text-[16px] focus:outline-none border"
          style="border-color: var(--color-borde); background-color: var(--color-blanco-bajo); color: var(--color-tx-negro);">
        <button type="submit"
          class="absolute right-3 top-1/2 -translate-y-1/2">
          <img
            src="<?php echo esc_url( get_site_url() . '/wp-content/uploads/2025/10/buscar-cafe-icon.png' ); ?>"
            alt="Buscar"
            class="h-7 w-7 opacity-80 hover:opacity-100 transition">
        </button>
      </form>

      <!-- Menú principal -->
      <nav class="flex flex-col items-center space-y-5 text-[18px] font-medium">
        <?php
          wp_nav_menu(array(
            'theme_location' => 'primary',
            'menu_class' => 'flex flex-col items-center space-y-5',
            'container' => false,
          ));
        ?>
      </nav>
    </div>

    <!-- Sección inferior -->
    <div class="px-6 pb-8 border-t" style="border-color: var(--color-borde);">
      <?php if ( ! is_user_logged_in() ) : ?>
        <div class="flex items-center justify-center space-x-3 mt-6">
          <button data-open-login
            class="flex-1 py-3 rounded-md font-medium transition hover:opacity-90 text-center"
            style="background-color: var(--color-amarillo-pr); color: var(--color-tx-blanco);">
            Entrar
          </button>
          <button data-open-register
            class="flex-1 py-3 rounded-md font-medium border transition hover:bg-gray-50 text-center"
            style="border-color: var(--color-borde); color: var(--color-tx-cafe);">
            Registrarse
          </button>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
