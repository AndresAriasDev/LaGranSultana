/****************************************************
 * 🧱 SECCIÓN 1: CONTROL GENERAL DEL MODAL
 ****************************************************/
(function(){
  // Elementos principales del modal
  const modal = document.getElementById('gs-auth-modal');
  const overlay = document.getElementById('gs-auth-overlay');
  const closeBtns = document.querySelectorAll('.gs-auth-close');
  const title = document.getElementById('gs-auth-title');
  const loginContent = document.getElementById('gs-auth-login-content');
  const registerContent = document.getElementById('gs-auth-register-content');

  /**
   * Abre el modal y muestra el modo indicado (login o registro)
   */
function openModal(mode = 'login') {
  if (!modal || !overlay) return;

  // Ocultar ambos contenidos ANTES de mostrar el modal
  loginContent.classList.add('hidden', 'opacity-0');
  registerContent.classList.add('hidden', 'opacity-0');

  // Mostrar modal y overlay
  overlay.classList.remove('hidden');
  modal.classList.remove('hidden');
  document.body.classList.add('overflow-hidden');

  const inner = modal.querySelector('.w-full.max-w-md');
if (inner) {
  inner.classList.add('opacity-0', 'scale-95');
  setTimeout(() => {
    inner.classList.remove('opacity-0', 'scale-95');
    inner.classList.add('opacity-100', 'scale-100');
  }, 50);
}

  // Asegurar que el contenido correcto aparece con un pequeño delay visual
  setTimeout(() => {
    switchMode(mode);
  }, 50);
}

  /**
   * Cierra el modal completamente
   */
  function closeModal() {
    if (!modal || !overlay) return;
    overlay.classList.add('hidden');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
  }

  // 🔁 Hacemos ambas funciones globales para usarlas desde otros módulos
  window.openModal = openModal;
  window.closeModal = closeModal;

  /**
   * Cambia entre login y registro dentro del modal (con fade suave)
   */
  function switchMode(mode) {
    if (mode === 'register') {
      title.textContent = 'Crear cuenta';
      loginContent.classList.add('opacity-0');
      setTimeout(() => {
        loginContent.classList.add('hidden');
        registerContent.classList.remove('hidden');
        setTimeout(() => registerContent.classList.remove('opacity-0'), 20);
      }, 150);
    } else {
      title.textContent = 'Iniciar sesión';
      registerContent.classList.add('opacity-0');
      setTimeout(() => {
        registerContent.classList.add('hidden');
        loginContent.classList.remove('hidden');
        setTimeout(() => loginContent.classList.remove('opacity-0'), 20);
      }, 150);
    }
  }

  /****************************************************
   * 🎯 EVENTOS GLOBALES
   ****************************************************/
  document.addEventListener('click', function(e){
    const tLogin = e.target.closest('[data-open-login]');
    const tRegister = e.target.closest('[data-open-register]');
    const tToRegister = e.target.closest('[data-switch-register]');
    const tToLogin = e.target.closest('[data-switch-login]');

    // Abrir modal según origen
    if (tLogin) { e.preventDefault(); openModal('login'); }
    if (tRegister) { e.preventDefault(); openModal('register'); }

    // Cambiar entre formularios internos
    if (tToRegister) { e.preventDefault(); switchMode('register'); }
    if (tToLogin) { e.preventDefault(); switchMode('login'); }
  });

  // Cerrar si se hace clic fuera del contenido
  overlay && overlay.addEventListener('click', closeModal);
  modal && modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
  closeBtns.forEach(btn => btn.addEventListener('click', closeModal));

  // Cerrar modal con tecla ESC (opcional y moderno)
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModal();
  });
})();

/****************************************************
 * 🧱 SECCIÓN 2: MANEJADOR DE REGISTRO (AJAX)
 ****************************************************/
/****************************************************
 * 🧱 SECCIÓN 2: MANEJADOR DE REGISTRO (AJAX)
 ****************************************************/
(function(){
  const form = document.getElementById('gs-register-form');
  if (!form) return;

  form.addEventListener('submit', async function(e){
    e.preventDefault();

    const formData = new FormData(form);
    formData.append('action', 'gs_handle_user_registration');

    try {
      const response = await fetch(gsAuth.ajaxUrl, { method: 'POST', body: formData });
      const result = await response.json();

      if (result.success) {
        gsToast(result.data.message || 'Cuenta creada exitosamente.', 'success');
        closeModal();
        // 🔄 Recargar después de un pequeño delay para actualizar sesión visual
        setTimeout(() => window.location.reload(), 1200);
      } else {
        gsToast(result.data.message || 'Ocurrió un error durante el registro.', 'error');
      }
    } catch (error) {
      gsToast('Error de conexión. Intenta de nuevo.', 'error');
    }
  });
})();


/****************************************************
 * 🧱 SECCIÓN 3: MANEJADOR DE LOGIN (AJAX)
 ****************************************************/
(function(){
  const form = document.getElementById('gs-login-form');
  if (!form) return;

  form.addEventListener('submit', async function(e){
    e.preventDefault();

    const feedback = document.getElementById('gs-login-feedback');
    if (feedback) {
      feedback.classList.remove('hidden');
      feedback.className = 'rounded-md border px-3 py-2 text-sm bg-[var(--color-blanco-bajo)] text-[var(--color-tx-azul)]';
      feedback.innerHTML = 'Verificando credenciales...';
    }

    const formData = new FormData(form);
    formData.append('action', 'gs_handle_user_login');

    try {
      const response = await fetch(gsAuth.ajaxUrl, { method: 'POST', body: formData });
      const result = await response.json();

     if (result.success) {
  gsToast(result.data.message, 'success');
  closeModal();
  setTimeout(() => window.location.reload(), 1200);
} else {
  gsToast(result.data.message || 'Correo o contraseña incorrectos.', 'error');
}

    } catch (error) {
      if (feedback) {
        feedback.className = 'rounded-md border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-800';
        feedback.innerHTML = 'Error de conexión. Intenta nuevamente.';
      }
    }
  });
})();

/****************************************************
 * 🧱 SECCIÓN 4: SISTEMA DE NOTIFICACIONES (TOAST)
 ****************************************************/
window.gsToast = function(message, type = 'info') {
  const container = document.getElementById('gs-toast-container');
  if (!container) return;

  const toast = document.createElement('div');
const bgColor =
  type === 'success'
    ? 'var(--color-verde-pr)'
    : type === 'error'
    ? 'var(--color-rojo-pr)'
    : 'var(--color-azul-pr)';

toast.className = `
  gs-toast fixed bottom-5 left-5 z-[99999]
  px-4 py-2 text-sm font-inter font-medium rounded-lg shadow-lg text-white
  transform translate-x-[-120%] opacity-0 transition-all duration-300 ease-in-out pointer-events-auto
`;

toast.style.backgroundColor = bgColor;

  toast.textContent = message;
  container.appendChild(toast);

  // Animación de entrada
  setTimeout(() => {
    toast.classList.remove('translate-x-[-120%]', 'opacity-0');
    toast.classList.add('translate-x-0', 'opacity-100');
  }, 50);

  // Desaparición automática
  setTimeout(() => {
    toast.classList.add('translate-x-[-120%]', 'opacity-0');
    setTimeout(() => toast.remove(), 300);
  }, 3000);
};

/*********/

window.togglePasswordVisibility = function (inputId, buttonEl) {
  const input = document.getElementById(inputId);
  if (!input) return;

  const isHidden = input.type === 'password';
  input.type = isHidden ? 'text' : 'password';
  buttonEl.textContent = isHidden ? 'Ocultar' : 'Mostrar';
};

/*********/

document.addEventListener('DOMContentLoaded', () => {
  const toggleBtn = document.getElementById('gs-menu-toggle');
  if (!toggleBtn) return;

  toggleBtn.addEventListener('click', () => {
    toggleBtn.classList.toggle('active');
    // Aquí más adelante vas a mostrar/ocultar el menú móvil
  });
});
