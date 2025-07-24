<?php
namespace SaveCarts\Core;

class HookFallback {
    public static function init() {
        add_action('woocommerce_after_cart', [self::class, 'render_if_enabled']);
    }

    public static function render_if_enabled() {
        $auto_display = get_option('save_carts_auto_display', '1');
        if ($auto_display === '1') {
            echo do_shortcode('[save_cart_form]');
        }
    }
}