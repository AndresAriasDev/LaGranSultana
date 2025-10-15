<?php if ( ! defined('ABSPATH') ) exit; ?>

<form id="gs-register-form" class="space-y-6 font-inter">
  <?php wp_nonce_field('gs_register_nonce', 'gs_register_nonce'); ?>

  <!-- Campo: Nombre completo -->
  <div class="relative">
    <input id="gs_reg_name" name="name" type="text" required
      class="peer w-full rounded-md border border-[var(--color-borde)] bg-[var(--color-blanco-pr)] 
             px-3 pt-5 pb-2 text-sm text-[var(--color-tx-negro)] focus:border-[var(--color-amarillo-pr)] 
             focus:ring-2 focus:ring-[var(--color-amarillo-pr)] focus:outline-none transition-all" 
      placeholder=" " />
    <label for="gs_reg_name"
      class="absolute left-3 top-[-10px] text-[13px] text-[var(--color-tx-azul)] transition-all 
             peer-placeholder-shown:top-3.5 peer-placeholder-shown:text-sm 
             peer-placeholder-shown:text-[var(--color-tx-azul)] 
             peer-focus:top-1 peer-focus:text-xs peer-focus:text-[var(--color-amarillo-pr)]">
      Nombre completo
    </label>
  </div>

  <!-- Campo: Correo -->
  <div class="relative">
    <input id="gs_reg_email" name="email" type="email" autocomplete="email" required
      class="peer w-full rounded-md border border-[var(--color-borde)] bg-[var(--color-blanco-pr)] 
             px-3 pt-5 pb-2 text-sm text-[var(--color-tx-negro)] focus:border-[var(--color-amarillo-pr)] 
             focus:ring-2 focus:ring-[var(--color-amarillo-pr)] focus:outline-none transition-all" 
      placeholder=" " />
    <label for="gs_reg_email"
      class="absolute left-3 top-[-10px] text-[13px] text-[var(--color-tx-azul)] transition-all 
             peer-placeholder-shown:top-3.5 peer-placeholder-shown:text-sm 
             peer-placeholder-shown:text-[var(--color-tx-azul)] 
             peer-focus:top-1 peer-focus:text-xs peer-focus:text-[var(--color-amarillo-pr)]">
      Correo electrónico
    </label>
  </div>

  <!-- Campo: Contraseña -->
  <div class="relative">
    <input id="gs_reg_pass" name="password" type="password" autocomplete="new-password" required
      class="peer w-full rounded-md border border-[var(--color-borde)] bg-[var(--color-blanco-pr)] 
             px-3 pt-5 pb-2 text-sm text-[var(--color-tx-negro)] focus:border-[var(--color-amarillo-pr)] 
             focus:ring-2 focus:ring-[var(--color-amarillo-pr)] focus:outline-none transition-all" 
      placeholder=" " />
    <label for="gs_reg_pass"
      class="absolute left-3 top-[-10px] text-[13px] text-[var(--color-tx-azul)] transition-all 
             peer-placeholder-shown:top-3.5 peer-placeholder-shown:text-sm 
             peer-placeholder-shown:text-[var(--color-tx-azul)] 
             peer-focus:top-1 peer-focus:text-xs peer-focus:text-[var(--color-amarillo-pr)]">
      Contraseña
    </label>
  </div>

  <!-- Aceptación de términos -->
  <div class="flex items-start gap-2">
    <input id="gs_reg_terms" name="terms" type="checkbox" required
      class="mt-1 h-4 w-4 rounded border-[var(--color-borde)] text-[var(--color-amarillo-pr)] 
             focus:ring-[var(--color-amarillo-pr)] focus:ring-offset-0" />
    <label for="gs_reg_terms" class="text-xs text-[var(--color-tx-negro)] leading-snug">
      Acepto los 
      <a href="/terminos" class="underline hover:text-[var(--color-amarillo-pr)] transition-colors">Términos</a> 
      y la 
      <a href="/privacidad" class="underline hover:text-[var(--color-amarillo-pr)] transition-colors">Política de Privacidad</a>.
    </label>
  </div>

  <!-- Botón de envío -->
  <button type="submit"
    class="w-full mt-1 rounded-md bg-[var(--color-amarillo-pr)] px-4 py-3 text-sm font-semibold 
           text-[var(--color-tx-blanco)] hover:opacity-95 transition-all">
    Crear cuenta
  </button>

  <!-- Enlace inferior -->
  <div class="flex flex-col items-center mt-3 text-xs text-[var(--color-tx-azul)]">
    <p>
      ¿Ya tienes cuenta?
      <a href="#" data-switch-login
         class="underline hover:text-[var(--color-amarillo-pr)] transition-colors">
        Inicia sesión
      </a>
    </p>
  </div>
</form>
