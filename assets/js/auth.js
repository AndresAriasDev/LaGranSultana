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
    overlay.classList.remove('hidden');
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    switchMode(mode);
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
(function(){
  const form = document.getElementById('gs-register-form');
  if (!form) return;

  form.addEventListener('submit', async function(e){
    e.preventDefault();

    const feedback = document.getElementById('gs-reg-feedback');
    if (feedback) {
      feedback.classList.remove('hidden');
      feedback.className = 'rounded-md border px-3 py-2 text-sm bg-[var(--color-blanco-bajo)] text-[var(--color-tx-azul)]';
      feedback.innerHTML = 'Procesando...';
    }

    const formData = new FormData(form);
    formData.append('action', 'gs_handle_user_registration');

    try {
      const response = await fetch(gsAuth.ajaxUrl, { method: 'POST', body: formData });
      const result = await response.json();

      if (result.success) {
        gsToast(result.data.message, 'success');
        closeModal(); // ✅ cierra el modal correctamente
      } else {
        if (feedback) {
          feedback.className = 'rounded-md border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-800';
          feedback.innerHTML = result.data.message || 'Ocurrió un error.';
        }
      }
    } catch (error) {
      if (feedback) {
        feedback.className = 'rounded-md border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-800';
        feedback.innerHTML = 'Error de conexión. Intenta de nuevo.';
      }
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
        closeModal(); // ✅ ya está global
      } else {
        if (feedback) {
          feedback.className = 'rounded-md border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-800';
          feedback.innerHTML = result.data.message || 'Correo o contraseña incorrectos.';
        }
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
  toast.className = `
    gs-toast px-4 py-2 text-sm font-medium rounded-md shadow-md text-white transform translate-x-[-120%] opacity-0
    transition-all duration-300 ease-in-out pointer-events-auto
    ${type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-gray-700'}
  `;
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
