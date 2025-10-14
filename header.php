<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="border-b" style="border-color: var(--color-borde); background-color: var(--color-blanco-pr);">
  <!-- Primera fila -->
  <div class="w-[94%] mx-auto flex items-center justify-between py-4 px-2">
    
    <!-- Logo -->
    <div class="flex items-center flex-shrink-0">
      <img 
        src="./wp-content/uploads/2025/10/Recurso-1-2.png" 
        alt="Logo La Gran Sultana" 
        class="h-auto w-[130px]"
      >
    </div>

    <!-- Barra de búsqueda -->
    <div class="flex-1 mx-30">
      <form action="<?php echo home_url('/'); ?>" method="get" class="relative">
        <input 
          type="text" 
          name="s"
          placeholder="¿Qué estás buscando?"
          class="w-full py-3 pl-4 pr-10 border rounded-md text-[16px] focus:outline-none" style="border-color: var(--color-borde); background-color: var(--color-blanco-bajo); color: var(--color-tx-negro);"
        >
        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2" style="color: var(--color-tx-cafe);">
          <img src="./wp-content/uploads/2025/10/buscar-cafe-icon.png" alt="Buscar" class="h-8 w-8">
        </button>
      </form>
    </div>

    <!-- Botones -->
    <div class="flex items-center space-x-5">
      <a href="#" class="font-medium text-[16px] transition" style="color: var(--color-tx-cafe);">Entrar</a>
      <a href="#" class="font-medium px-5 py-2.5 rounded-md" style="background-color: var(--color-amarillo-pr); color: var(--color-tx-blanco);">
        Registrarse
      </a>
    </div>

  </div>

<!-- Segunda fila: menú de navegación -->
<nav class="border-t" style="border-color: var(--color-borde);">
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
          src="/wp-content/uploads/2025/10/carrito-de-compras-cafe.png" 
          alt="Carrito de compras" 
          class="h-6 w-auto hover:opacity-80 transition"
        >
      </a>
    </div>
  </div>
</nav>

</header>


