document.addEventListener('DOMContentLoaded', () => {
  const btnToggle = document.getElementById('gs-menu-toggle');
  const menuOverlay = document.getElementById('gs-mobile-menu');
  const menuPanel = document.getElementById('gs-mobile-panel');
  const btnClose = document.getElementById('gs-menu-close');

  if (!btnToggle || !menuOverlay || !menuPanel) return;

  const openMenu = () => {
    btnToggle.classList.add('active'); // ✅ activa la animación
    menuOverlay.classList.remove('hidden');
    setTimeout(() => {
      menuOverlay.classList.add('opacity-100');
      menuOverlay.classList.remove('opacity-0');
      menuPanel.classList.remove('-translate-x-full');
      menuPanel.classList.add('translate-x-0');
    }, 10);
    document.body.classList.add('overflow-hidden');
  };

  const closeMenu = () => {
    btnToggle.classList.remove('active'); // ✅ quita la animación
    menuOverlay.classList.add('opacity-0');
    menuOverlay.classList.remove('opacity-100');
    menuPanel.classList.add('-translate-x-full');
    menuPanel.classList.remove('translate-x-0');
    document.body.classList.remove('overflow-hidden');
    setTimeout(() => {
      menuOverlay.classList.add('hidden');
    }, 300);
  };

  btnToggle.addEventListener('click', () => {
    const isOpen = btnToggle.classList.contains('active');
    if (isOpen) closeMenu();
    else openMenu();
  });

  if (btnClose) btnClose.addEventListener('click', closeMenu);
  menuOverlay.addEventListener('click', (e) => {
    if (e.target === menuOverlay) closeMenu();
  });
});
