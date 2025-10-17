/******************************************************
 * 🌐 SISTEMA GLOBAL DE MODALES – La Gran Sultana
 * Versión unificada (perfil + puntos)
 * Compatible con todos los contenidos dentro de #gs-info-modal
 ******************************************************/

document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("gs-info-modal");
  const overlay = document.getElementById("gs-info-overlay");
  const inner = document.getElementById("gs-info-inner");

  if (!modal || !overlay || !inner) {
    console.warn("⚠️ No se encontró la estructura base del modal global (#gs-info-modal).");
    return;
  }

  console.log("✅ Modal global inicializado correctamente");

  /******************************************************
   * 🧾 ABRIR MODAL GLOBAL
   ******************************************************/
  document.querySelectorAll("[data-open-info]").forEach((button) => {
    button.addEventListener("click", () => {
      const targetId = button.getAttribute("data-open-info");
      const targetContent = document.getElementById(targetId);

      if (!targetContent) {
        console.warn("⚠️ No se encontró el contenido del modal:", targetId);
        return;
      }

      // Ocultar todos los contenidos internos antes de mostrar el nuevo
      inner.querySelectorAll('[id^="gs-info-"]').forEach((el) => {
        el.classList.add("hidden", "opacity-0");
      });

      // Si tiene atributo de recompensa, actualizar texto dinámico
      const rewardName = button.getAttribute("data-reward-name");
      const rewardText = document.getElementById("gs-points-reward-name");
      if (rewardName && rewardText) {
        rewardText.innerHTML = `Has desbloqueado el premio <strong>${rewardName}</strong>. ¿Deseas reclamarlo ahora?`;
      }

      // Mostrar overlay + modal
      overlay.classList.remove("hidden");
      modal.classList.remove("hidden");
      document.body.classList.add("overflow-hidden");

      // Animación
      requestAnimationFrame(() => {
        overlay.classList.add("opacity-100");
        inner.classList.remove("opacity-0", "scale-95");
        inner.classList.add("opacity-100", "scale-100");
        targetContent.classList.remove("hidden", "opacity-0");
      });
    });
  });

  /******************************************************
   * ❌ CERRAR MODAL GLOBAL
   ******************************************************/
  const closeModal = () => {
    overlay.classList.remove("opacity-100");
    inner.classList.add("opacity-0", "scale-95");
    setTimeout(() => {
      overlay.classList.add("hidden");
      modal.classList.add("hidden");
      document.body.classList.remove("overflow-hidden");
    }, 250);
  };

  // Botones genéricos con data-close-info
  document.querySelectorAll("[data-close-info]").forEach((btn) =>
    btn.addEventListener("click", closeModal)
  );

  // Click fuera del contenido o tecla ESC
  modal.addEventListener("click", (e) => {
    if (e.target === modal || e.target === overlay) closeModal();
  });
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeModal();
  });

  /******************************************************
   * 🏆 CONFIRMAR RECLAMO DE RECOMPENSA (simulación)
   ******************************************************/
  const confirmReward = document.getElementById("gs-points-confirm");
  if (confirmReward) {
    confirmReward.addEventListener("click", () => {
      closeModal();
      if (typeof gsToast === "function") {
        gsToast("🎉 Premio reclamado con éxito (simulación)", "success");
      } else {
        alert("🎉 Premio reclamado con éxito (simulación)");
      }
    });
  }
});
