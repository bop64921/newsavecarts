<?php

namespace SaveCarts\Core;

if (!defined('ABSPATH')) {
    exit;
}

class CartCleaner
{
    public function __construct()
    {
        add_action('wp_ajax_clear_saved_cart_ajax', [$this, 'handle_clear_cart']);
        add_action('wp_ajax_nopriv_clear_saved_cart_ajax', [$this, 'handle_clear_cart']);
    }

    public function handle_clear_cart()
    {
        if (!check_ajax_referer('save_cart_nonce', 'nonce', false)) {
            wp_send_json_error(['message' => __('Invalid nonce', 'save-carts')]);
        }

        if (function_exists('WC') && WC()->cart) {
            WC()->cart->empty_cart();
        }

        $redirect = wc_get_page_permalink('shop');

        if (defined('SAVE_CARTS_PRO') && SAVE_CARTS_PRO) {
            $requested = $_POST['referrer'] ?? '';
            $custom_redirect = apply_filters('save_carts_pro_redirect_after_clear', $requested);

            if (!empty($custom_redirect)) {
                $redirect = esc_url_raw($custom_redirect);
            }
        }

        wp_send_json_success([
            'message' => __('Cart cleared successfully.', 'save-carts'),
            'redirect_url' => $redirect
        ]);
    }
}
