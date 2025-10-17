<?php
if (!defined('ABSPATH')) exit;

$current_user = wp_get_current_user();
$user_points  = gs_get_user_points($current_user->ID);

$rewards = [
  [
    'name'  => 'Café Sultana Gratis ☕',
    'points_required' => 20,
    'description' => 'Canjea un café gratis en nuestras sucursales aliadas.',
    'image' => get_site_url() . '/wp-content/uploads/2025/10/cafe.png'
  ],
  [
    'name'  => 'Descuento del 10% 🎟️',
    'points_required' => 50,
    'description' => 'Usa este cupón para obtener un 10% de descuento en tu próxima compra.',
    'image' => get_site_url() . '/wp-content/uploads/2025/10/discount.png'
  ],
  [
    'name'  => 'Gift Card C$100 🎁',
    'points_required' => 120,
    'description' => 'Canjeable en productos o accesorios exclusivos.',
    'image' => get_site_url() . '/wp-content/uploads/2025/10/gift.png'
  ],
];
?>

<!-- 🎯 SECCIÓN DE PUNTOS (solo el contenido principal) -->
<section class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 transition-all">
  <!-- 🔹 Encabezado -->
  <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
    <h2 class="text-2xl font-bold text-gray-800">Puntos acumulados</h2>

    <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 px-5 py-2 rounded-lg shadow-sm">
      <span class="text-3xl font-extrabold text-gray-800"><?php echo intval($user_points); ?></span>
      <span class="text-2xl">🎉</span>
    </div>
  </div>

  <p class="text-sm text-gray-600 mt-3">
    Canjea tus puntos por recompensas disponibles según tu progreso.
  </p>

  <!-- 🧱 Grid de recompensas -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
    <?php foreach ($rewards as $reward): 
      $is_unlocked = $user_points >= $reward['points_required'];
    ?>
      <div class="flex flex-col items-center justify-center border border-gray-200 rounded-xl py-6 px-4 text-center transition-all duration-300 
        <?php echo $is_unlocked ? 'cursor-pointer hover:shadow-md hover:border-[var(--color-amarillo-pr)]' : 'bg-gray-50 text-gray-400 select-none'; ?>"
        <?php if ($is_unlocked): ?> data-open-info="gs-info-reward" data-reward-name="<?php echo esc_attr($reward['name']); ?>" <?php endif; ?>
      >
        <img src="<?php echo esc_url($reward['image']); ?>" alt="<?php echo esc_attr($reward['name']); ?>" class="w-12 h-12 mb-3">
        <span class="text-lg font-semibold <?php echo $is_unlocked ? 'text-gray-800' : 'text-gray-500'; ?>">
          <?php echo esc_html($reward['name']); ?>
        </span>
        <span class="text-[13px] mt-1 <?php echo $is_unlocked ? 'text-gray-500' : 'text-gray-400'; ?>">
          <?php echo $is_unlocked ? 'Toca para obtener' : 'Necesitas ' . $reward['points_required'] . ' puntos'; ?>
        </span>
        <?php if (!$is_unlocked): ?>
          <div class="mt-2 text-gray-400 text-sm flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11V17M12 7H12.01M5.07 21h13.86A2.07 2.07 0 0021 18.93V5.07A2.07 2.07 0 0018.93 3H5.07A2.07 2.07 0 003 5.07v13.86A2.07 2.07 0 005.07 21z" />
            </svg>
            Bloqueado
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</section>
