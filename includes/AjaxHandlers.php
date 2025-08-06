<?php

namespace SaveCarts\Includes;

use SaveCarts\Core\CartSaver; // 

if (!defined('ABSPATH')) {
    exit;
}

class AjaxHandlers
{
    public function __construct()
    {
        add_action('wp_ajax_save_cart_ajax', [$this, 'handle_cart_save']);
        add_action('wp_ajax_nopriv_save_cart_ajax', [$this, 'handle_cart_save']);
    }

    public function handle_cart_save()
    {
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => __('You must be logged in to save a cart.', 'save-carts')]);
        }

        check_ajax_referer('save_cart_nonce', 'nonce');

        $cart_name = sanitize_text_field($_POST['cart_name'] ?? '');
        $user_id   = get_current_user_id();

        if (empty($cart_name)) {
            wp_send_json_error(['message' => __('Cart name is required.', 'save-carts')]);
        }

        $result = CartSaver::save_cart($user_id, $cart_name);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        wp_send_json_success([
            'message' => $result,
            'show_actions' => true
        ]);
    }
}