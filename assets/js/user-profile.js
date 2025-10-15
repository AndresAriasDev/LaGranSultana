/******************************************************
 * 👤 PERFIL DE USUARIO – La Gran Sultana
 * Guardado, progreso dinámico y mensajes UX refinados
 ******************************************************/

document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("#gs-user-profile-form");
  if (!form) return;

  const progressBar = document.querySelector("#gs-profile-progress-bar");
  const completionText = document.querySelector("#gs-profile-completion-text");
  const pointsText = document.querySelector("#gs-profile-points");
  const progressModule = document.querySelector("#gs-profile-progress-module");
  const saveBtn = form.querySelector("button[type='submit']");

  let hasChanges = false;

  /******************************************************
   * 🟡 Detectar cambios en los campos
   ******************************************************/
  form.querySelectorAll("input, select, textarea").forEach((field) => {
    field.addEventListener("input", () => {
      hasChanges = true;
      saveBtn.disabled = false;
      saveBtn.classList.remove("opacity-60", "cursor-not-allowed");
    });
  });

  // Desactivar botón por defecto
  saveBtn.disabled = true;
  saveBtn.classList.add("opacity-60", "cursor-not-allowed");

  /******************************************************
   * 🟢 Guardar cambios del perfil
   ******************************************************/
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    if (!hasChanges) return; // no envíes si nada cambió

    const originalText = saveBtn.textContent;
    saveBtn.textContent = "Guardando...";
    saveBtn.disabled = true;

    const formData = new FormData(form);
    formData.append("action", "gs_save_user_profile");
    formData.append("nonce", gsProfile.nonce);

    try {
      const response = await fetch(gsProfile.ajaxUrl, { method: "POST", body: formData });
      const result = await response.json();

      if (result.success) {
        const data = result.data;
        const completion = data.completion || 0;

        // ✅ Mostrar mensaje base de guardado
        gsToast("Cambios guardados correctamente.", "success");

        // 🔄 Actualizar barra
        if (progressBar && completionText) {
          progressBar.style.width = `${completion}%`;
          completionText.textContent = `${completion}%`;

          progressBar.className =
            "h-3 transition-all duration-500 " +
            (completion < 50
              ? "bg-red-400"
              : completion < 80
              ? "bg-yellow-400"
              : "bg-green-500");
        }

        // 💰 Actualizar puntos
        if (pointsText) {
          pointsText.textContent = `${data.points} pts`;
        }

        // 🎯 Si completó el perfil por primera vez
        if (completion >= 100 && data.bonus_just_awarded) {
          queueToasts([
            "🎉 ¡Has completado tu perfil al 100%!",
            "Has ganado 20 puntos por completar tu perfil 👏",
          ]);

          // 🕊️ Animar ocultado suave del módulo de progreso
          if (progressModule) {
            progressModule.style.transition = "opacity 0.8s ease, transform 0.8s ease, margin 0.8s ease";
            progressModule.style.opacity = "0";
            progressModule.style.transform = "translateY(-20px)";
            progressModule.style.marginBottom = "0";
            setTimeout(() => progressModule.remove(), 900);
          }
        }
      } else {
        gsToast(result.data.message || "Error al guardar los datos.", "error");
      }
    } catch (err) {
      console.error("❌ Error en AJAX:", err);
      gsToast("Error de conexión. Intenta nuevamente.", "error");
    } finally {
      // Restaurar botón
      saveBtn.textContent = originalText;
      hasChanges = false;
      saveBtn.disabled = true;
      saveBtn.classList.add("opacity-60", "cursor-not-allowed");
    }
  });
});

/******************************************************
 * ✨ Sistema de mensajes secuenciales
 ******************************************************/
function queueToasts(messages, delay = 800) {
  messages.forEach((msg, index) => {
    setTimeout(() => gsToast(msg, "success"), index * delay);
  });
}
