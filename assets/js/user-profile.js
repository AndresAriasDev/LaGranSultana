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

if (!result.success) {
  // 🚫 Si el backend devolvió un error (como teléfono duplicado)
  const errorMsg = result.data?.message || "Error al guardar los datos.";
  gsToast(errorMsg, "error");

  // 🧭 Marcar visualmente el campo si aplica
  if (result.data?.field === "phone") {
    const phoneField = form.querySelector("input[name='phone']");
    if (phoneField) {
      phoneField.focus();
      phoneField.classList.add("border-red-400");
      setTimeout(() => phoneField.classList.remove("border-red-400"), 2500);
    }
  }

  // 🚫 No continuar con el flujo de éxito
  return;
}

// ✅ Si el resultado fue exitoso
const data = result.data;
const completion = data.completion || 0;

// ✅ Mostrar mensaje base solo si no completó el perfil al 100 %
if (!(completion >= 100 && data.bonus_just_awarded)) {
  gsToast("Cambios guardados correctamente.", "success");
}

// 🔄 Actualizar barra de progreso
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

// 🔢 Actualizar puntos
if (pointsText) {
  pointsText.textContent = `${data.points} pts`;
}

// 🎯 Si completó el perfil por primera vez
if (completion >= 100 && data.bonus_just_awarded) {
  queueToasts([
    "Has ganado 20 puntos por completar tu perfil 👏",
  ]);

  if (progressModule) {
    progressModule.style.transition =
      "opacity 0.8s ease, transform 0.8s ease, margin 0.8s ease";
    progressModule.style.opacity = "0";
    progressModule.style.transform = "translateY(-20px)";
    progressModule.style.marginBottom = "0";
    setTimeout(() => progressModule.remove(), 900);
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
 * 📸 CAMBIO DE FOTO DE PERFIL (actualizado)
 ******************************************************/
document.addEventListener("DOMContentLoaded", () => {
  const changeBtn = document.querySelector("#gs-change-avatar-btn");
  const inputFile = document.querySelector("#gs-avatar-input");
  const avatarImg = document.querySelector("#gs-user-avatar");
  const progressBar = document.querySelector("#gs-profile-progress-bar");
  const completionText = document.querySelector("#gs-profile-completion-text");
  const pointsText = document.querySelector("#gs-profile-points");
  const progressModule = document.querySelector("#gs-profile-progress-module");

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
        const data = result.data;

        // ✅ Mostrar mensaje de éxito principal
        gsToast(data.message, "success");

        // 🔄 Actualizar barra de progreso si viene el porcentaje
        if (progressBar && data.completion !== undefined) {
          const completion = data.completion;
          progressBar.style.width = `${completion}%`;
          completionText.textContent = `${completion}%`;

          progressBar.className =
            "h-3 transition-all duration-500 " +
            (completion < 50
              ? "bg-red-400"
              : completion < 80
              ? "bg-yellow-400"
              : "bg-green-500");

          // 🎯 Si completó el perfil al 100 %, mostrar toasts y ocultar barra
          if (completion >= 100 && data.bonus_just_awarded) {
            queueToasts([
              "🎉 ¡Has completado tu perfil al 100%! ",
              "Has ganado 20 puntos por completar tu perfil 👏",
            ]);

            if (progressModule) {
              progressModule.style.transition =
                "opacity 0.8s ease, transform 0.8s ease, margin 0.8s ease";
              progressModule.style.opacity = "0";
              progressModule.style.transform = "translateY(-20px)";
              progressModule.style.marginBottom = "0";
              setTimeout(() => progressModule.remove(), 900);
            }
          }
        }

        // 🔢 Actualizar puntos si viene incluido
        if (pointsText && data.points !== undefined) {
          pointsText.textContent = `${data.points} pts`;
        }
      } else {
        gsToast(result.data?.message || "Error al actualizar la foto.", "error");
      }
    } catch (err) {
      console.error("❌ Error al subir foto:", err);
      gsToast("Error de conexión. Intenta nuevamente.", "error");
    }
  });
});


/******************************************************
 * 👠 PERFIL DE MODELO – GUARDADO AJAX
 * (Similar al usuario normal pero con sus propios campos)
 ******************************************************/
document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("#gs-model-profile-form");
  if (!form) {
    console.log("⚠️ Formulario de modelo no encontrado");
    return;
  }
  console.log("✅ Script del perfil de modelo activo");
  const saveBtn = form.querySelector("button[type='submit']");
  let hasChanges = false;

  // 🔸 Detectar cambios
  form.querySelectorAll("input, select, textarea").forEach((field) => {
    field.addEventListener("input", () => {
      hasChanges = true;
      saveBtn.disabled = false;
      saveBtn.classList.remove("opacity-60", "cursor-not-allowed");
    });
  });

  // Desactivar por defecto
  saveBtn.disabled = true;
  saveBtn.classList.add("opacity-60", "cursor-not-allowed");

  // 🔹 Guardar datos por AJAX
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    if (!hasChanges) return;

    const originalText = saveBtn.textContent;
    saveBtn.textContent = "Guardando...";
    saveBtn.disabled = true;

    const formData = new FormData(form);
    formData.append("action", "gs_save_model_profile");
    formData.append("nonce", gsProfile.nonce);

    try {
      const response = await fetch(gsProfile.ajaxUrl, { method: "POST", body: formData });
      const result = await response.json();

      if (result.success) {
        const data = result.data;
        gsToast(data.message, "success");

        // 🎯 Si completó el perfil por primera vez
        if (data.bonus_just_awarded) {
          queueToasts([
          "🎉 ¡Has completado tu perfil al 100%! ",
          "Has ganado 20 puntos por completar tu perfil 👏",
        ]);
        }

      } else {
        const msg = result.data?.message || "Error al guardar los datos.";
        gsToast(msg, "error");

        // Si fue error de teléfono
        if (result.data?.field === "phone") {
          const phoneField = form.querySelector("input[name='phone']");
          if (phoneField) {
            phoneField.focus();
            phoneField.classList.add("border-red-400");

            // 🔹 Reactivar el botón de guardar para permitir corregir y reenviar
            saveBtn.disabled = false;
            saveBtn.classList.remove("opacity-60", "cursor-not-allowed");

            // 🔹 Escuchar cuando el usuario empiece a corregir el número
            phoneField.addEventListener(
              "input",
              () => {
                phoneField.classList.remove("border-red-400");
              },
              { once: true }
            );
          }
        }
      }

    } catch (err) {
      console.error("❌ Error en AJAX:", err);
      gsToast("Error de conexión. Intenta nuevamente.", "error");
    } finally {
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
