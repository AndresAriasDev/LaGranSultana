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

  <!-- 🔹 PORTADA -->
  <section class="relative w-full h-72 sm:h-80 md:h-96 overflow-hidden bg-[var(--color-blanco-bajo)]">
    <!-- Imagen de fondo -->
    <img src="<?php echo esc_url($profile_photo); ?>"
         alt="<?php echo esc_attr($display_name); ?>"
         class="absolute inset-0 w-full h-full object-cover">
    <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/30 to-transparent"></div>

    <!-- Foto circular centrada -->
    <div class="absolute bottom-[-64px] left-1/2 transform -translate-x-1/2">
      <img src="<?php echo esc_url($profile_photo); ?>"
           alt="<?php echo esc_attr($display_name); ?>"
           class="w-32 h-32 rounded-full border-4 border-[var(--color-blanco-pr)] shadow-xl object-cover">
    </div>
  </section>

  <div class="h-24"></div>

  <!-- 👤 Información principal -->
  <section class="flex flex-col items-center text-center">
    <h1 class="text-2xl font-semibold text-[var(--color-tx-azul)]"><?php echo esc_html($display_name); ?></h1>
    <p class="text-[var(--color-tx-cafe)] text-sm capitalize"><?php echo esc_html($gender ?: 'Modelo'); ?></p>

    <?php if (!empty($department)): ?>
      <p class="text-[13px] text-[var(--color-tx-negro)] mt-1">Departamento: <?php echo esc_html($department); ?></p>
    <?php endif; ?>

    <!-- Botón seguir -->
    <button id="followBtn"
            data-model-id="<?php echo esc_attr($model_id); ?>"
            class="mt-3 bg-[var(--color-rojo-pr)] hover:bg-[#ff5858] transition text-white font-semibold px-6 py-2 rounded-full shadow-sm">
      + Seguir
    </button>

    <!-- Estadísticas -->
    <div class="flex justify-center gap-10 sm:gap-20 mt-8 border-t border-[var(--color-borde)] pt-6 pb-8 w-full max-w-2xl">
      <div class="text-center">
        <p id="followersCount" class="text-xl font-bold text-[var(--color-tx-negro)]"><?php echo $followers; ?></p>
        <p class="text-xs text-[var(--color-tx-cafe)] uppercase tracking-wide">Seguidores</p>
      </div>
      <div class="text-center">
        <p id="likesCount" class="text-xl font-bold text-[var(--color-tx-negro)]"><?php echo $total_likes; ?></p>
        <p class="text-xs text-[var(--color-tx-cafe)] uppercase tracking-wide">Likes</p>
      </div>
      <div class="text-center">
        <p id="viewsCount" class="text-xl font-bold text-[var(--color-tx-negro)]"><?php echo $total_views; ?></p>
        <p class="text-xs text-[var(--color-tx-cafe)] uppercase tracking-wide">Vistas</p>
      </div>
    </div>
  </section>

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
