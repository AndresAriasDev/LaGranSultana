/**
 * JS: Perfil Público del Modelo
 * Maneja seguir, vistas, likes y modal de galería
 * Autor: Jeykel Arias (La Gran Sultana)
 */

jQuery(document).ready(function ($) {

  // ==============================
  // 🔹 1. VARIABLES GLOBALES
  // ==============================
  const modelId = $("#followBtn").data("model-id");
  let currentPhotoIndex = null;
  let tapActive = false;
  let tapCooldownUntil = null;
  let galleryPhotos = []; // Se llenará desde HTML o AJAX

  // ==============================
  // 🔹 2. FUNCIONES UTILITARIAS
  // ==============================

  // Mostrar modal de login si el usuario no está autenticado
  function showLoginModal() {
    if (typeof openModal === "function") {
      openModal("login");
    } else {
      alert("Por favor inicia sesión para continuar.");
    }
  }

  // Controla el tiempo local del tap tap
  function isTapAvailable() {
    const now = Date.now();
    return !tapCooldownUntil || now > tapCooldownUntil;
  }

  // Simula guardar tiempo de bloqueo (1 hora)
  function setTapCooldown() {
    tapCooldownUntil = Date.now() + 60 * 60 * 1000; // 1 hora
    localStorage.setItem(`tapCooldown_${modelId}`, tapCooldownUntil);
  }

  function checkTapCooldown() {
    const saved = localStorage.getItem(`tapCooldown_${modelId}`);
    if (saved) tapCooldownUntil = parseInt(saved);
  }

  // ==============================
  // 🔹 3. REGISTRAR VISTA (cada 30s)
  // ==============================
  function registerView() {
    $.post(ajaxurl, {
      action: "model_register_view",
      model_id: modelId,
    });
  }

  // 1ra vista inmediata + cada 30s
  registerView();
  setInterval(registerView, 30000);

  // ==============================
  // 🔹 4. SEGUIR / DEJAR DE SEGUIR
  // ==============================
  $("#followBtn").on("click", function () {
    const $btn = $(this);

    $.post(ajaxurl, {
      action: "model_toggle_follow",
      model_id: modelId,
    }, function (res) {
      if (res?.require_login) {
        showLoginModal();
        return;
      }

      if (res.success) {
        if (res.following) {
          $btn.text("Siguiendo").removeClass("bg-pink-600").addClass("bg-gray-700");
        } else {
          $btn.text("+ Seguir").removeClass("bg-gray-700").addClass("bg-pink-600");
        }
        $("#followersCount").text(res.followers || 0);
      }
    }, "json");
  });

  // ==============================
  // 🔹 5. MODAL DE FOTO
  // ==============================
  const $photoModal = $("#photoModal");
  const $modalImg = $("#modalPhoto");
  const $modalLikes = $("#modalLikes");
  const $likeBtn = $("#likeBtn");

  // Abrir modal
  $("#modelGallery").on("click", "img", function () {
    const index = $(this).closest("div").index();
    currentPhotoIndex = index;
    openPhotoModal(index);
  });

  function openPhotoModal(index) {
    const $photo = $("#modelGallery img").eq(index);
    const src = $photo.attr("src");
    const likes = $photo.closest("div").find("span.font-medium").text();

    $modalImg.attr("src", src);
    $modalLikes.text(`${likes} Likes`);
    $photoModal.removeClass("hidden flex").addClass("flex");
    checkTapCooldown();
  }

  $("#closeModal").on("click", function () {
    $photoModal.addClass("hidden");
  });

  $("#prevPhoto").on("click", function () {
    if (currentPhotoIndex > 0) {
      currentPhotoIndex--;
      openPhotoModal(currentPhotoIndex);
    }
  });

  $("#nextPhoto").on("click", function () {
    if (currentPhotoIndex < $("#modelGallery img").length - 1) {
      currentPhotoIndex++;
      openPhotoModal(currentPhotoIndex);
    }
  });

  // ==============================
  // 🔹 6. SISTEMA DE LIKES (TapTap)
  // ==============================
  let tapSessionStart = null;

  $likeBtn.on("click", function () {
    const photoId = $("#modelGallery div").eq(currentPhotoIndex).data("photo-id");

    // Si no logueado, abrir modal
    $.post(ajaxurl, { action: "model_check_login" }, function (res) {
      if (!res.logged_in) {
        showLoginModal();
        return;
      }

      // Revisar cooldown
      if (!isTapAvailable()) {
        const unlock = new Date(tapCooldownUntil).toLocaleTimeString();
        alert(`Podrás volver a dar likes a esta foto a las ${unlock}.`);
        return;
      }

      const now = Date.now();

      // Si la sesión de taps no ha empezado o ya pasó 15min, iniciar nueva
      if (!tapSessionStart || now - tapSessionStart > 15 * 60 * 1000) {
        tapSessionStart = now;
        setTimeout(() => setTapCooldown(), 15 * 60 * 1000); // Bloquear después de 15min
      }

      // Enviar like via AJAX
      $.post(ajaxurl, {
        action: "model_like_photo",
        photo_id: photoId,
      }, function (res) {
        if (res.success) {
          $modalLikes.text(`${res.total_likes} Likes`);
          // Actualizar contador en miniatura
          $("#modelGallery div").eq(currentPhotoIndex).find("span.font-medium").text(res.total_likes);
          animateHeart($likeBtn);
        } else if (res.require_login) {
          showLoginModal();
        } else if (res.locked) {
          alert("Has llegado al límite de likes por ahora. Inténtalo más tarde ❤️");
        }
      }, "json");
    }, "json");
  });

  // Animación del corazón ❤️
  function animateHeart($el) {
    $el.addClass("scale-125");
    setTimeout(() => $el.removeClass("scale-125"), 200);
  }

  // ==============================
  // 🔹 7. CARGAR MÁS FOTOS (Paginación)
  // ==============================
  $("#loadMoreBtn").on("click", function () {
    const page = $(this).data("page") || 1;

    $.post(ajaxurl, {
      action: "model_load_gallery",
      model_id: modelId,
      page: page + 1,
    }, function (res) {
      if (res.success && res.html) {
        $("#modelGallery").append(res.html);
        $("#loadMoreBtn").data("page", page + 1);
      } else {
        $("#loadMoreBtn").prop("disabled", true).text("No hay más fotos");
      }
    }, "json");
  });

});
