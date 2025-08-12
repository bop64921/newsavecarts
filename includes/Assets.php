<?php

namespace SaveCarts\Includes;

if (!defined('ABSPATH')) {
    exit;
}

class Assets
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
    }

    public function enqueue_frontend_assets()
    {
        // Solo cargar en el carrito o en el endpoint "saved-carts"
        if (!is_cart() && !is_account_page()) {
            return;
        }

        wp_enqueue_style(
            'save-cart-style',
            SAVE_CARTS_PLUGIN_URL . 'assets/css/save-cart.css',
            [],
            filemtime(plugin_dir_path(__DIR__) . '/../assets/css/save-cart.css')
        );

        wp_enqueue_script(
            'save-carts-notifications',
            SAVE_CARTS_PLUGIN_URL . 'assets/js/notifications.js',
            ['jquery'],
            '1.0',
            true
        );

        wp_enqueue_script(
            'save-cart-js',
            SAVE_CARTS_PLUGIN_URL . 'assets/js/save-cart.js',
            ['jquery', 'save-carts-notifications'],
            '1.0',
            true
        );

        wp_localize_script('save-cart-js', 'save_cart_ajax_obj', [
            'ajax_url'    => admin_url('admin-ajax.php'),
            'nonce'       => wp_create_nonce('save_cart_nonce'),
            'success_msg' => __('Cart saved successfully!', 'save-carts'),
            'shop_url'    => esc_url(wc_get_page_permalink('shop')),
        ]);
    }
}
