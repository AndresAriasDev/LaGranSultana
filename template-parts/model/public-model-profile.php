<?php
/**
 * Template: Perfil Público del Modelo (con datos reales del modelo)
 */

if (!defined('ABSPATH')) exit;

get_header();

global $post;
$model_id = $post->ID;

// 🧩 Buscar el usuario modelo vinculado
$author_id = get_post_field('post_author', $model_id);

// Validar que sea un modelo
$user = get_userdata($author_id);
if (!$user || !in_array('modelo', (array)$user->roles)) {
  echo '<p class="text-center mt-20 text-gray-600">⚠️ Este perfil no pertenece a un modelo válido.</p>';
  get_footer();
  exit;
}

// 📸 Datos reales del modelo
$profile_photo = get_user_meta($author_id, 'gs_profile_picture', true);
if (empty($profile_photo)) {
  $profile_photo = get_avatar_url($author_id, ['size' => 256]);
}

// 🧾 Datos del modelo
$full_name  = trim(get_user_meta($author_id, 'first_name', true));
$department = get_user_meta($author_id, 'department', true);
$gender     = get_user_meta($author_id, 'gender', true);

// 👤 Formato de nombre (nombre + apellido)
$name_parts = preg_split('/\s+/', $full_name);
if (count($name_parts) >= 3) {
  $display_name = $name_parts[0] . ' ' . $name_parts[2];
} elseif (count($name_parts) >= 2) {
  $display_name = $name_parts[0] . ' ' . $name_parts[1];
} else {
  $display_name = $name_parts[0];
}

// 📈 Métricas visibles
$followers   = (int) get_post_meta($model_id, 'model_followers', true);
$total_likes = (int) get_post_meta($model_id, 'model_total_likes', true);
$total_views = (int) get_post_meta($model_id, 'model_total_views', true);
?>

<main id="public-model-profile" class="bg-[var(--color-blanco-pr)] font-[var(--font-sans)]">

  <!-- 🔹 PORTADA final ajustada (100% ancho, max 1200px contenido) -->
  <section class="relative w-full h-[220px] sm:h-[200px]">
    <!-- Imagen de fondo -->
    <img src="<?php echo esc_url($profile_photo); ?>"
         alt="<?php echo esc_attr($display_name); ?>"
         class="absolute inset-0 w-full h-full object-cover brightness-90">
    <div class="absolute inset-0 bg-gradient-to-b from-black/70 via-black/40 to-transparent"></div>

    <!-- Contenedor interno limitado -->
    <div class="relative h-full max-w-[1200px] mx-auto">

      <!-- 📊 Stats centradas en la imagen (con bordes sutiles) -->
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 flex items-center justify-center gap-12 sm:gap-20 text-center text-white">
        <div class="px-4 border-y border-[var(--color-borde)] py-2">
          <p class="text-[22px] font-semibold leading-none"><?php echo $followers ?: 487; ?></p>
          <p class="uppercase text-[11px] tracking-wider text-gray-300 mt-1">Seguidores</p>
        </div>
        <div class="px-4 border-y border-[var(--color-borde)] py-2">
          <p class="text-[22px] font-semibold leading-none"><?php echo $total_likes ?: 455; ?></p>
          <p class="uppercase text-[11px] tracking-wider text-gray-300 mt-1">Likes</p>
        </div>
        <div class="px-4 border-y border-[var(--color-borde)] py-2">
          <p class="text-[22px] font-semibold leading-none"><?php echo $total_views ?: 15; ?></p>
          <p class="uppercase text-[11px] tracking-wider text-gray-300 mt-1">Vistas</p>
        </div>
      </div>


      <!-- 🧍 Info del modelo alineada a la izquierda -->
      <div class="absolute bottom-[-70px] left-0 flex items-start gap-5 px-6">
        <img src="<?php echo esc_url($profile_photo); ?>"
             alt="<?php echo esc_attr($display_name); ?>"
             class="w-[150px] h-[150px] rounded-full border-4 border-[var(--color-blanco-pr)] shadow-lg object-cover mr-2">

        <div class="flex flex-col sm:flex-row sm:items-center gap-3 text-left translate-y-26">
          <div>
            <h1 class="text-2xl font-semibold text-[var(--color-tx-azul)] leading-tight">
              <?php echo esc_html($display_name); ?>
            </h1>
            <p class="text-[15px] text-[var(--color-tx-cafe)] mt-0.5">
              Modelo
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
<!-- 🔘 Barra de acciones (debajo de la portada) -->
<section class="max-w-[1200px] mx-auto flex items-end justify-end gap-3 px-6 mt-6 mb-4">
  <!-- Botón Lista de deseos -->
  <button id="wishlistBtn"
          class=" text-[var(--color-tx-negro)] border-1 border-[var(--color-borde)] px-6 py-2 rounded-[4px] font-semibold">
    💛 Mi lista de deseos
  </button>
  <!-- Botón Seguir -->
  <button id="followBtn"
          data-model-id="<?php echo esc_attr($model_id); ?>"
          class="bg-[var(--color-azul-pr)] text-[var(--color-blanco-pr)] px-6 py-2 rounded-[4px] font-semibold shadow-sm transition">
    + Seguir
  </button>
</section>

  <div class="h-50"></div>

  <!-- GALERÍA -->
  <section class="max-w-6xl mx-auto px-6 pb-12">
    <div id="modelGallery" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
      <?php for ($i = 1; $i <= 20; $i++) : ?>
        <div class="relative group overflow-hidden rounded-lg cursor-pointer shadow-sm border border-[var(--color-borde)]" data-photo-id="<?php echo $i; ?>">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/placeholder-<?php echo ($i % 5) + 1; ?>.jpg" 
               alt="Foto <?php echo $i; ?>" 
               class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300 ease-out">
          <div class="absolute bottom-2 right-2 bg-black/40 px-2 py-1 rounded text-white text-xs flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[var(--color-rojo-pr)]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
            <span class="font-medium">856</span>
          </div>
        </div>
      <?php endfor; ?>
    </div>

    <div class="flex justify-center mt-10">
      <button id="loadMoreBtn"
              class="px-5 py-2 bg-[var(--color-azul-pr)] text-white rounded hover:bg-[#6f84b3] transition">
        Cargar más
      </button>
    </div>
  </section>

</main>

<?php get_footer(); ?>
