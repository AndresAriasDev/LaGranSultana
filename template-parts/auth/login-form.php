<?php if ( ! defined('ABSPATH') ) exit; ?>

<form id="gs-login-form" class="space-y-6 font-inter">
  <?php wp_nonce_field('gs_login_nonce', 'gs_login_nonce'); ?>

<!-- Campo: Correo -->
<div class="relative">
  <input id="gs_log_email" name="email" type="email" autocomplete="email" required
    class="peer w-full rounded-md border border-[var(--color-borde)] bg-[var(--color-blanco-pr)] px-3 pt-5 pb-2 text-sm text-[var(--color-tx-negro)] focus:border-[var(--color-azul-pr)] focus:ring-2 focus:ring-[var(--color-azul-pr)] focus:outline-none transition-all"
    placeholder=" " /> <!-- 👈 placeholder vacío pero con espacio -->
  <label for="gs_log_email"
    class="absolute left-3 top-[-10px] text-[13px] text-[var(--color-tx-azul)] transition-all 
           peer-placeholder-shown:top-3.5 peer-placeholder-shown:text-sm 
           peer-placeholder-shown:text-[var(--color-tx-azul)] 
           peer-focus:top-1 peer-focus:text-xs peer-focus:text-[var(--color-azul-pr)]">
    Correo electrónico
  </label>
</div>

<!-- Campo: Contraseña -->
<div class="relative">
  <input id="gs_log_pass" name="password" type="password" autocomplete="current-password" required
    class="peer w-full rounded-md border border-[var(--color-borde)] bg-[var(--color-blanco-pr)] px-3 pt-5 pb-2 text-sm text-[var(--color-tx-negro)] focus:border-[var(--color-azul-pr)] focus:ring-2 focus:ring-[var(--color-azul-pr)] focus:outline-none transition-all"
    placeholder=" " /> <!-- 👈 igual aquí -->
  <label for="gs_log_pass"
    class="absolute left-3 top-[-10px] text-[13px] text-[var(--color-tx-azul)] transition-all 
           peer-placeholder-shown:top-3.5 peer-placeholder-shown:text-sm 
           peer-placeholder-shown:text-[var(--color-tx-azul)] 
           peer-focus:top-1 peer-focus:text-xs peer-focus:text-[var(--color-azul-pr)]">
    Contraseña
  </label>
</div>


  <!-- Botón -->
  <button type="submit"
    class="w-full mt-1 rounded-md bg-[var(--color-azul-pr)] px-4 py-3 text-sm font-semibold text-[var(--color-tx-blanco)] hover:opacity-95 transition-all">
    Iniciar sesión
  </button>

  <!-- Enlaces inferiores -->
  <div class="flex flex-col items-center mt-4 space-y-2 text-xs text-[var(--color-tx-azul)]">
    <p>
      ¿Aún no tienes cuenta?
      <a href="#" data-switch-register
         class="underline hover:text-[var(--color-amarillo-pr)] transition-colors">
        Regístrate aquí
      </a>
    </p>
    <a href="/recuperar-password"
       class="underline text-[var(--color-tx-negro)] hover:text-[var(--color-amarillo-pr)] transition-colors">
      ¿Olvidaste tu contraseña?
    </a>
  </div>

</form>
