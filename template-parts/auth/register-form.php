<?php if ( ! defined('ABSPATH') ) exit; ?>
<form id="gs-register-form" class="space-y-4 font-sans">
  <?php wp_nonce_field('gs_register_nonce', 'gs_register_nonce'); ?>

  <div class="space-y-1">
    <label for="gs_reg_name" class="text-sm font-medium text-[var(--color-tx-azul)]">Nombre completo</label>
    <input id="gs_reg_name" name="name" type="text" required
           class="w-full rounded-md border border-[var(--color-borde)] bg-[var(--color-blanco-pr)] px-3 py-2 text-sm text-[var(--color-tx-negro)] focus:outline-none focus:ring-2 focus:ring-[var(--color-azul-pr)]" />
  </div>

  <div class="space-y-1">
    <label for="gs_reg_email" class="text-sm font-medium text-[var(--color-tx-azul)]">Correo</label>
    <input id="gs_reg_email" name="email" type="email" autocomplete="email" required
           class="w-full rounded-md border border-[var(--color-borde)] bg-[var(--color-blanco-pr)] px-3 py-2 text-sm text-[var(--color-tx-negro)] focus:outline-none focus:ring-2 focus:ring-[var(--color-azul-pr)]" />
  </div>

  <div class="space-y-1">
    <label for="gs_reg_pass" class="text-sm font-medium text-[var(--color-tx-azul)]">Contraaseña</label>
    <input id="gs_reg_pass" name="password" type="password" autocomplete="new-password" required
           class="w-full rounded-md border border-[var(--color-borde)] bg-[var(--color-blanco-pr)] px-3 py-2 text-sm text-[var(--color-tx-negro)] focus:outline-none focus:ring-2 focus:ring-[var(--color-azul-pr)]" />
  </div>

  <div class="flex items-start gap-2">
    <input id="gs_reg_terms" name="terms" type="checkbox" required
           class="mt-1 h-4 w-4 rounded border-[var(--color-borde)] text-[var(--color-amarillo-pr)] focus:ring-[var(--color-amarillo-pr)]" />
    <label for="gs_reg_terms" class="text-xs text-[var(--color-tx-negro)]">
      Acepto los <a href="/terminos" class="underline">Términos</a> y <a href="/privacidad" class="underline">Política de Privacidad</a>.
    </label>
  </div>

  <input type="hidden" name="intent" id="gs_reg_intent" value="" />

  <button type="submit"
          class="w-full rounded-md bg-[var(--color-amarillo-pr)] px-4 py-2 text-sm font-semibold text-[var(--color-tx-blanco)] hover:opacity-95">
    Crear cuenta
  </button>

  <p class="text-xs text-center text-[var(--color-tx-azul)]">
    ¿Ya tienes cuenta? <a href="/login" class="underline">Inicia sesión</a>
  </p>

  <div id="gs-reg-feedback" class="hidden rounded-md border px-3 py-2 text-sm"></div>
</form>
