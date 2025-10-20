<?php
// 🚀 Identificador para cargar el JS de la galería
define('IS_MODEL_GALLERY_VIEW', true);
$current_user = wp_get_current_user();
$foto_perfil = get_avatar_url($current_user->ID, ['size' => 180]); 
$fotos = get_posts([
    'post_type' => 'fotos',
    'meta_key' => '_modelo_relacionado',
    'meta_value' => $current_user->ID,
    'posts_per_page' => -1
]);
?>

<!-- 🔹 Panel de encabezado del modelo -->
<div class="bg-[#e9f4ff] rounded-2xl p-6 sm:p-8 mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between shadow-sm border border-blue-100">

    <!-- 🖼️ Foto y datos del modelo -->
    <div class="flex items-center gap-6">
        <div class="relative">
            <img src="<?php echo esc_url($foto_perfil); ?>" alt="Foto de perfil" class="w-28 h-28 rounded-full object-cover border-4 border-white shadow-md">
            <button class="absolute bottom-1 right-1 bg-white p-1 rounded-full shadow-sm hover:bg-gray-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 1.104-.896 2-2 2s-2-.896-2-2 .896-2 2-2 2 .896 2 2zM12 11v7m-4-4h8"/>
                </svg>
            </button>
        </div>

        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800"><?php echo esc_html($current_user->display_name); ?></h1>
            <p class="text-blue-600 text-sm font-medium mb-3">Modelo</p>

            <div class="flex items-center gap-6 text-sm text-gray-600">
                <div class="text-center">
                    <p class="font-bold text-gray-800">14</p>
                    <p class="uppercase text-[11px] tracking-wider">Seguidores</p>
                </div>
                <div class="text-center">
                    <p class="font-bold text-gray-800">2.2K</p>
                    <p class="uppercase text-[11px] tracking-wider">Likes</p>
                </div>
                <div class="text-center">
                    <p class="font-bold text-gray-800">15.4K</p>
                    <p class="uppercase text-[11px] tracking-wider">Vistas</p>
                </div>
            </div>

            <p class="mt-3 text-xs text-gray-500">Copia y comparte tu perfil.</p>
        </div>
    </div>

    <!-- ⚙️ Acciones rápidas -->
    <div class="flex items-center gap-3 mt-6 sm:mt-0">
        <a href="<?php echo esc_url(site_url('/modelo/' . $current_user->user_nicename)); ?>" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
            <span>Mi perfil</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
        </a>

        <label class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
            <input type="file" id="btn-subir-foto" accept="image/*" hidden>
            <span>Subir nueva foto</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v16h16V4H4zm8 3v10m-5-5h10"/>
            </svg>
        </label>
    </div>
</div>

<section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 transition-all">
<div id="galeria-fotos" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
  <?php foreach ($fotos as $foto): ?>
    <div class="relative group overflow-hidden rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300">

      <!-- Contenedor cuadrado -->
      <div class="aspect-square w-full h-auto relative overflow-hidden rounded-2xl">
        <?php echo get_the_post_thumbnail($foto->ID, 'medium', [
          'class' => 'w-full h-full object-cover rounded-2xl transform group-hover:scale-105 transition-transform duration-500 ease-out'
        ]); ?>

        <!-- Overlay de Likes -->
        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col items-center justify-center">
          <p class="text-white text-lg font-semibold tracking-wide">
          <?php echo rand(200, 1800); ?> <span class="text-sm font-normal ml-1">Likes</span>
          </p>
        </div>

        <!-- Botón eliminar -->
        <button 
          class="delete-foto absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-all duration-300 transform hover:scale-110 cursor-pointer" 
          data-id="<?php echo $foto->ID; ?>"
          title="Eliminar esta foto"
        >
          <img src="/wp-content/uploads/2025/10/basura-blanco.png" alt="Eliminar" class="w-6 h-6">
        </button>
      </div>

    </div>
  <?php endforeach; ?>
</div>

</section>
