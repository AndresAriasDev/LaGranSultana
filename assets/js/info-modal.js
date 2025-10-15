document.addEventListener('DOMContentLoaded', () => {
  const overlay = document.getElementById('gs-info-overlay');
  const modal   = document.getElementById('gs-info-modal');
  const inner   = document.getElementById('gs-info-inner');

  if (!overlay || !modal || !inner) {
    console.warn("⚠️ Modal informativo no encontrado en el DOM");
    return;
  }

  console.log("✅ Modal informativo inicializado correctamente");

  // Abrir modal
  document.querySelectorAll('[data-open-info]').forEach(button => {
    button.addEventListener('click', () => {
      const targetId = button.getAttribute('data-open-info');
      const targetContent = document.getElementById(targetId);
      if (!targetContent) return;

inner.querySelectorAll('[id^="gs-info-"]').forEach(el => {
  el.classList.add('hidden', 'opacity-0');
});


      overlay.classList.remove('hidden');
      modal.classList.remove('hidden');
      document.body.classList.add('overflow-hidden');

      requestAnimationFrame(() => {
        overlay.classList.add('opacity-100');
        inner.classList.remove('opacity-0', 'scale-95');
        inner.classList.add('opacity-100', 'scale-100');
        targetContent.classList.remove('hidden', 'opacity-0');
      });
    });
  });

  // Cerrar modal
  function closeInfoModal() {
    overlay.classList.remove('opacity-100');
    inner.classList.add('opacity-0', 'scale-95');
    setTimeout(() => {
      overlay.classList.add('hidden');
      modal.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    }, 250);
  }

  document.querySelectorAll('[data-close-info]').forEach(button => {
    button.addEventListener('click', closeInfoModal);
  });

  modal.addEventListener('click', e => {
    if (e.target === modal || e.target === overlay) closeInfoModal();
  });

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeInfoModal();
  });
});
