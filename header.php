<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php bloginfo('name'); ?></title>
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="border-b min-h-[64px]" style="border-color: var(--color-borde); background-color: var(--color-blanco-pr);">
  <div class="w-[94%] mx-auto flex items-center justify-between py-4 px-2">

    <!-- Logo -->
    <div class="flex items-center flex-shrink-0">
      <a href="<?php echo esc_url( home_url('/') ); ?>" class="block">
        <img 
          src="<?php echo esc_url( get_site_url() . '/wp-content/uploads/2025/10/Recurso-1-2.png' ); ?>" 
          alt="<?php bloginfo('name'); ?>" 
          class="h-auto w-[110px] transition-opacity hover:opacity-90"
        >
      </a>
    </div>

    <!-- Barra de búsqueda (solo desktop) -->
    <div class="hidden md:flex flex-1 mx-20">
      <form action="<?php echo home_url('/'); ?>" method="get" class="relative w-full">
        <input 
          type="text" 
          name="s"
          placeholder="¿Qué estás buscando?"
          class="w-full py-3 pl-4 pr-10 border rounded-md text-[16px] focus:outline-none"
          style="border-color: var(--color-borde); background-color: var(--color-blanco-bajo); color: var(--color-tx-negro);"
        >
        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2" style="color: var(--color-tx-cafe);">
          <img 
            src="<?php echo esc_url( get_site_url() . '/wp-content/uploads/2025/10/buscar-cafe-icon.png' ); ?>" 
            alt="Buscar" 
            class="h-8 w-8"
          >
        </button>
      </form>
    </div>

    <!-- Botones (solo desktop) -->
    <div class="hidden md:flex items-center space-x-5">
      <?php if ( ! is_user_logged_in() ) : ?>
        <button data-open-login
          class="flex items-center justify-center font-medium px-5 h-[50px] text-[16px] transition"
          style="color: var(--color-tx-cafe);">
          Entrar
        </button>

        <button data-open-register
          class="flex items-center justify-center font-medium px-5 h-[50px] rounded-md transition hover:opacity-90"
          style="background-color: var(--color-amarillo-pr); color: var(--color-tx-blanco);">
          Registrarse
        </button>
      <?php else : ?>
        <a href="<?php echo esc_url( home_url('/mi-cuenta') ); ?>"
          class="flex items-center justify-center font-medium px-6 h-[50px] rounded-md transition hover:opacity-90"
          style="background-color: var(--color-azul-pr); color: var(--color-tx-blanco);">
          Mi cuenta
        </a>
      <?php endif; ?>
    </div>

<!-- Botón Hamburguesa (solo mobile) -->
<button id="gs-menu-toggle"
  class="flex md:hidden flex-col justify-between w-[34px] h-[26px] focus:outline-none transition-all relative z-50"
  aria-label="Abrir menú">
  <span class="hamb-line"></span>
  <span class="hamb-line"></span>
  <span class="hamb-line"></span>
</button>


  </div>

  <!-- Segunda fila: menú de navegación (solo desktop) -->
  <nav class="hidden md:flex border-t" style="border-color: var(--color-borde);">
    <div class="w-[94%] mx-auto px-2 flex items-center justify-between">
      <!-- Menú principal -->
      <div>
        <?php
          wp_nav_menu(array(
            'theme_location' => 'primary',
            'menu_class' => 'flex space-x-8 py-2 text-[15px] font-medium text-tx-cafe',
            'container' => false,
          ));
        ?>
      </div>

      <!-- Ícono carrito -->
      <div class="flex items-center">
        <a href="#" class="relative">
          <img 
            src="<?php echo esc_url( get_site_url() . '/wp-content/uploads/2025/10/carrito-de-compras-cafe.png' ); ?>" 
            alt="Carrito de compras"
            class="h-6 w-auto hover:opacity-80 transition"
          />
        </a>
      </div>
    </div>
  </nav>
</header>
<?php get_template_part('template-parts/header/mobile-menu'); ?>



