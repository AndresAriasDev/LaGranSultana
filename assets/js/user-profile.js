document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("#gs-user-profile-form");
  if (!form) return;

  const progressBar = document.querySelector("#gs-profile-progress-bar");
  const completionText = document.querySelector("#gs-profile-completion-text");
  const pointsText = document.querySelector("#gs-profile-points");
  const progressModule = document.querySelector("#gs-profile-progress-module");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const btn = form.querySelector("button[type='submit']");
    const originalText = btn.textContent;
    btn.textContent = "Guardando...";
    btn.disabled = true;

    const formData = new FormData(form);
    formData.append("action", "gs_save_user_profile");
    formData.append("nonce", gsProfile.nonce);

    try {
      const response = await fetch(gsProfile.ajaxUrl, { method: "POST", body: formData });
      const result = await response.json();

      if (result.success) {
        const data = result.data;
        const completion = data.completion || 0;

        // Mostrar mensaje solo si completó el 100%
        if (completion >= 100 && !data.has_bonus) {
          gsToast(data.message, "success");
        }

        // Actualizar barra y puntos
        if (progressBar) {
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

        if (pointsText) {
          pointsText.textContent = `${data.points} pts`;
        }

        // Ocultar módulo si ya tiene los puntos
        if (data.has_bonus && progressModule) {
          progressModule.classList.add("hidden");
        }
      } else {
        gsToast(result.data.message || "Error al guardar los datos.", "error");
      }
    } catch (error) {
      console.error(error);
      gsToast("Error de conexión. Intenta nuevamente.", "error");
    } finally {
      btn.textContent = originalText;
      btn.disabled = false;
    }
  });
});
