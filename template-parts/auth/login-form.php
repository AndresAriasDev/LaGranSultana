<?php if ( ! defined('ABSPATH') ) exit; ?>

<form id="gs-login-form" class="space-y-4 font-sans">
  <?php wp_nonce_field('gs_login_nonce', 'gs_login_nonce'); ?>

  <div class="space-y-1">
    <label for="gs_log_email" class="text-sm font-medium text-[var(--color-tx-azul)]">Correo</label>
    <input id="gs_log_email" name="email" type="email" autocomplete="email" required
           class="w-full rounded-md border border-[var(--color-borde)] bg-[var(--color-blanco-pr)] px-3 py-2 text-sm text-[var(--color-tx-negro)] focus:outline-none focus:ring-2 focus:ring-[var(--color-azul-pr)]" />
  </div>

  <div class="space-y-1">
    <label for="gs_log_pass" class="text-sm font-medium text-[var(--color-tx-azul)]">Contraseña</label>
    <input id="gs_log_pass" name="password" type="password" autocomplete="current-password" required
           class="w-full rounded-md border border-[var(--color-borde)] bg-[var(--color-blanco-pr)] px-3 py-2 text-sm text-[var(--color-tx-negro)] focus:outline-none focus:ring-2 focus:ring-[var(--color-azul-pr)]" />
  </div>

  <div class="flex items-center justify-between">
    <label class="flex items-center gap-2 text-xs text-[var(--color-tx-negro)]">
      <input type="checkbox" name="remember" class="h-4 w-4 rounded border-[var(--color-borde)] text-[var(--color-amarillo-pr)]" />
      Recordarme
    </label>
    <a href="/recuperar-password" class="text-xs text-[var(--color-azul-pr)] underline">¿Olvidaste tu contraseña?</a>
  </div>

  <button type="submit"
          class="w-full rounded-md bg-[var(--color-azul-pr)] px-4 py-2 text-sm font-semibold text-[var(--color-tx-blanco)] hover:opacity-95">
    Iniciar sesión
  </button>

<p class="text-xs text-center text-[var(--color-tx-azul)]">
  ¿Aún no tienes cuenta?
  <a href="#" data-switch-register class="underline hover:text-[var(--color-amarillo-pr)]">Regístrate aquí</a>
</p>


  <div id="gs-login-feedback" class="hidden rounded-md border px-3 py-2 text-sm"></div>
</form>
