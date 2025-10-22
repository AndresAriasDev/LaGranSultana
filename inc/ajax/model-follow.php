<?php
if (!defined('ABSPATH')) exit;

add_action('wp_ajax_model_toggle_follow', 'lgs_model_toggle_follow');
add_action('wp_ajax_nopriv_model_toggle_follow', 'lgs_model_toggle_follow');

function lgs_model_toggle_follow() {
    if (!is_user_logged_in()) {
        wp_send_json(['require_login' => true]);
    }

    $model_id = intval($_POST['model_id'] ?? 0);
    if (!$model_id) wp_send_json_error(['message' => 'ID inválido.']);

    $followers = (array) get_post_meta($model_id, 'followers', true);
    $user_id = get_current_user_id();

    if (in_array($user_id, $followers)) {
        $followers = array_diff($followers, [$user_id]);
        update_post_meta($model_id, 'followers', $followers);
        wp_send_json_success(['following' => false, 'followers' => count($followers)]);
    } else {
        $followers[] = $user_id;
        update_post_meta($model_id, 'followers', $followers);
        wp_send_json_success(['following' => true, 'followers' => count($followers)]);
    }
}
