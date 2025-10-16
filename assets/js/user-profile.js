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

  // 🔄 Actualizar barra y puntos solo si todo salió bien
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

  if (pointsText) {
    pointsText.textContent = `${data.points} pts`;
  }

  // 🎯 Si completó el perfil por primera vez
  if (completion >= 100 && data.bonus_just_awarded) {
    queueToasts([
      "🎉 ¡Has completado tu perfil al 100%!",
      "Has ganado 20 puntos por completar tu perfil 👏",
    ]);
    if (progressModule) {
      progressModule.style.transition = "opacity 0.8s ease, transform 0.8s ease, margin 0.8s ease";
      progressModule.style.opacity = "0";
      progressModule.style.transform = "translateY(-20px)";
      progressModule.style.marginBottom = "0";
      setTimeout(() => progressModule.remove(), 900);
    }
  }
} else {
  // ⚠️ Mostrar error
  const errorMsg = result.data.message || "Error al guardar los datos.";
  gsToast(errorMsg, "error");

  // 🚫 Si es error de campo (ej. teléfono duplicado)
  if (result.data.field === "phone") {
    const phoneField = form.querySelector("input[name='phone']");
    if (phoneField) {
      phoneField.value = ""; // limpiar campo
      phoneField.focus();
      phoneField.classList.add("border-red-400");
      setTimeout(() => phoneField.classList.remove("border-red-400"), 2500);
    }

    // ❌ No actualizar progreso ni puntos
    return;
  }
}

        // 🔹 ACTUALIZAR DEPARTAMENTO EN PERFIL SIN REFRESCAR
  const deptField = form.querySelector('[name="department"]');
  const deptDisplay = document.getElementById('user-department');

  if (deptField && deptDisplay) {
    const newDept = deptField.options[deptField.selectedIndex].text || "";
    deptDisplay.textContent = "Departamento: " + newDept;
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
 * 📅 FECHA DE NACIMIENTO Y TELÉFONO – UX MODERNO
 ******************************************************/
document.addEventListener("DOMContentLoaded", () => {
  // Flatpickr (fecha moderna)
  if (typeof flatpickr !== "undefined") {
    flatpickr("#gs-birthdate", {
      dateFormat: "Y-m-d",
      maxDate: "today",
      altInput: true,
      altFormat: "d \\de F \\de Y",
      locale: "es",
    });
  }

  // intlTelInput (teléfono con bandera y código)
  if (typeof window.intlTelInput !== "undefined") {
    const input = document.querySelector("#gs-phone-input");
    if (input) {
      const iti = window.intlTelInput(input, {
        initialCountry: "ni",
        separateDialCode: true,
        preferredCountries: ["ni", "co", "us", "mx"],
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.1/build/js/utils.js",
      });

      // Antes de enviar, asegura el número completo
      const form = document.querySelector("#gs-user-profile-form");
      form?.addEventListener("submit", () => {
        input.value = iti.getNumber();
      });
    }
  }
});

/******************************************************
 * 📸 CAMBIO DE FOTO DE PERFIL
 ******************************************************/
document.addEventListener("DOMContentLoaded", () => {
  const changeBtn = document.querySelector("#gs-change-avatar-btn");
  const inputFile = document.querySelector("#gs-avatar-input");
  const avatarImg = document.querySelector("#gs-user-avatar");

  if (!changeBtn || !inputFile || !avatarImg) return;

  changeBtn.addEventListener("click", () => inputFile.click());

  inputFile.addEventListener("change", async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    // Previsualización inmediata
    const previewURL = URL.createObjectURL(file);
    avatarImg.src = previewURL;

    const formData = new FormData();
    formData.append("action", "gs_upload_profile_picture");
    formData.append("nonce", gsProfile.nonce);
    formData.append("avatar", file);

    try {
      const res = await fetch(gsProfile.ajaxUrl, { method: "POST", body: formData });
      const result = await res.json();

      if (result.success) {
        gsToast(result.data.message, "success");
        if (result.data.bonus_just_awarded) {
          setTimeout(() => gsToast("🎉 Has ganado +5 puntos por cambiar tu foto 👏", "success"), 1000);
        }
      } else {
        gsToast(result.data.message || "Error al actualizar la foto.", "error");
      }
    } catch (err) {
      console.error("❌ Error al subir foto:", err);
      gsToast("Error de conexión. Intenta nuevamente.", "error");
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
