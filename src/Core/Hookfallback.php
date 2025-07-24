<?php
namespace SaveCarts\Core;

class HookFallback {
    public static function init() {
        add_action('woocommerce_after_cart', [self::class, 'render_if_enabled']);
    }

    public static function render_if_enabled() {
    error_log('🧩 HookFallback ejecutado');

    $auto_display = get_option('save_carts_auto_display', '1');
    error_log('🛠️ Opción save_carts_auto_display: ' . $auto_display);

    if ($auto_display === '1') {
        error_log('🎯 Mostrando formulario desde hook');
        echo do_shortcode('[save_cart_form]');
    } else {
        error_log('🚫 No se muestra porque está desactivado en ajustes');
    }
}
}