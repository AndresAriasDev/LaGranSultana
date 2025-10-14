(function(){
  const modal = document.getElementById('gs-auth-modal');
  const overlay = document.getElementById('gs-auth-overlay');
  const closeBtns = document.querySelectorAll('.gs-auth-close');

  function openModal() {
    if (!modal || !overlay) return;
    overlay.classList.remove('hidden');
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
  }
  function closeModal() {
    if (!modal || !overlay) return;
    overlay.classList.add('hidden');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
  }

  document.addEventListener('click', function(e){
    const t = e.target.closest('[data-open-register]');
    if (t) { e.preventDefault(); openModal(); }
  });

  overlay && overlay.addEventListener('click', closeModal);
  closeBtns.forEach(b => b.addEventListener('click', closeModal));
})();


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

    // Prepara los datos del formulario
    const formData = new FormData(form);
    formData.append('action', 'gs_handle_user_registration'); // Nombre del hook AJAX

    try {
      const response = await fetch(gsAuth.ajaxUrl, {
        method: 'POST',
        body: formData
      });
      const result = await response.json();

      if (result.success) {
        // Mostrar mensaje de éxito y redirigir
        if (feedback) {
          feedback.className = 'rounded-md border border-green-300 bg-green-50 px-3 py-2 text-sm text-green-800';
          feedback.innerHTML = result.data.message;
        }
        setTimeout(() => window.location.href = result.data.redirect, 1000);
      } else {
        // Mostrar errores
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

/**************/

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
    formData.append('action', 'gs_handle_user_login'); // Hook definido en PHP

    try {
      const response = await fetch(gsAuth.ajaxUrl, {
        method: 'POST',
        body: formData
      });
      const result = await response.json();

      if (result.success) {
        if (feedback) {
          feedback.className = 'rounded-md border border-green-300 bg-green-50 px-3 py-2 text-sm text-green-800';
          feedback.innerHTML = result.data.message;
        }
        setTimeout(() => window.location.href = result.data.redirect, 800);
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
