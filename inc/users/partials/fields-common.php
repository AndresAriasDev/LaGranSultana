<?php
if (!defined('ABSPATH')) exit;

/**
 * ========================================================
 * 🧩 CAMPOS COMUNES DE PERFIL – La Gran Sultana
 * --------------------------------------------------------
 * Este fragmento se reutiliza entre:
 *  - usuario_normal
 *  - modelo
 *
 * Permite renderizar los campos básicos del perfil
 * evitando duplicación de código.
 * ========================================================
 *
 * Uso:
 * gs_render_common_profile_fields($user_id, ['first_name','phone','department','address','gender']);
 */

function gs_render_common_profile_fields($user_id, $include = []) {
  if (!$user_id) return;

  $fields = [
    'first_name' => [
      'label' => 'Nombre completo',
      'type'  => 'text',
      'placeholder' => 'Tu nombre real',
    ],
    'phone' => [
    'label' => 'Teléfono',
    'type'  => 'tel',
    'id'    => 'gs-phone-input', // 🔹 importante para intlTelInput
    'placeholder' => 'Ej: 88888888',
    ],
    'department' => [
      'label' => 'Departamento',
      'type'  => 'select',
      'options' => [
        '' => 'Seleccionar...',
        'Managua' => 'Managua',
        'Masaya' => 'Masaya',
        'Granada' => 'Granada',
        'León' => 'León',
        'Chinandega' => 'Chinandega',
        'Matagalpa' => 'Matagalpa',
        'Estelí' => 'Estelí',
        'Rivas' => 'Rivas',
        'Carazo' => 'Carazo',
        'Boaco' => 'Boaco',
        'Chontales' => 'Chontales',
        'Nueva Segovia' => 'Nueva Segovia',
        'Madriz' => 'Madriz',
        'RAAN' => 'Región Autónoma Atlántico Norte',
        'RAAS' => 'Región Autónoma Atlántico Sur',
      ],
    ],
    'address' => [
      'label' => 'Dirección',
      'type'  => 'text',
      'placeholder' => 'Tu dirección exacta',
    ],
    'birthdate' => [
      'label' => 'Fecha de nacimiento',
      'type'  => 'date',
      'id'    => 'gs-birthdate',
      'placeholder' => 'Selecciona tu fecha de nacimiento',
    ],
    'gender' => [
      'label' => 'Género',
      'type'  => 'select',
      'options' => [
        '' => 'Seleccionar...',
        'Femenino' => 'Femenino',
        'Masculino' => 'Masculino',
        'Otro' => 'Otro',
        'Prefiero no decirlo' => 'Prefiero no decirlo',
      ],
    ],
  ];

  foreach ($include as $key) {
    if (!isset($fields[$key])) continue;

    $meta_value = get_user_meta($user_id, $key, true);
    $f = $fields[$key];

    echo '<div class="flex flex-col">';
    echo '<label class="text-[15px] font-medium text-gray-700 mb-1.5">' . esc_html($f['label']) . '</label>';

    if ($f['type'] === 'select') {
      echo '<select name="' . esc_attr($key) . '" class="gs-input">';
      foreach ($f['options'] as $val => $label) {
        echo '<option value="' . esc_attr($val) . '" ' . selected($meta_value, $val, false) . '>' . esc_html($label) . '</option>';
      }
      echo '</select>';
    } else {
        // Input tipo texto/tel
        $extra_id = !empty($f['id']) ? 'id="' . esc_attr($f['id']) . '"' : '';
        echo '<input type="' . esc_attr($f['type']) . '" 
                    ' . $extra_id . '
                    name="' . esc_attr($key) . '" 
                    value="' . esc_attr($meta_value) . '" 
                    class="gs-input" 
                    placeholder="' . esc_attr($f['placeholder']) . '">';
    }

    echo '</div>';
  }
}
