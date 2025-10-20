<?php
if (!defined('ABSPATH')) exit;

/**
 * ========================================================
 * 🔄 HOOKS DE USUARIO – La Gran Sultana
 * --------------------------------------------------------
 * Aquí van las acciones que reaccionan a eventos globales
 * como cambios de rol, registro, eliminación, etc.
 * ========================================================
 */

/* ========================================================
 * 🧠 Cuando un usuario cambia de rol
 * ======================================================== */
add_action('set_user_role', 'gs_handle_role_change_points', 10, 3);
function gs_handle_role_change_points($user_id, $new_role, $old_roles) {
    // Solo nos interesa si el nuevo rol es modelo
    if ($new_role !== 'modelo') return;

    // Si tenía el bono del perfil normal, se lo quitamos
    $had_user_bonus = get_user_meta($user_id, 'gs_profile_bonus_awarded', true);

    if ($had_user_bonus) {
        gs_remove_points($user_id, 20, 'Se eliminaron los puntos del perfil normal al cambiar a modelo');
        delete_user_meta($user_id, 'gs_profile_bonus_awarded');
    }

    // Asegurar que no quede marcado el bono de modelo (para reiniciar su proceso)
    delete_user_meta($user_id, 'gs_model_profile_bonus_awarded');
}
