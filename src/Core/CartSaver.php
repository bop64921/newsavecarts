<?php

namespace SaveCarts\Core;

if (!defined('ABSPATH')) {
    exit;
}

class CartSaver
{
    public function __construct()
    {
        // Mostrar formulario en el carrito o donde se use el shortcode
        add_action('woocommerce_before_cart', [$this, 'render_save_cart_form']);
        add_shortcode('save_cart_form', [$this, 'render_save_cart_form']);
        error_log('🚨 CartSaver se ha instanciado correctamente');




        // Acción AJAX para guardar carrito
        add_action('wp_ajax_save_cart_ajax', [$this, 'handle_ajax_cart_save']);
        add_action('wp_ajax_nopriv_save_cart_ajax', [$this, 'handle_ajax_cart_save']);

        // Cargar JS externo
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function enqueue_scripts()
    {

        if (!is_cart()) return;
        wp_enqueue_script(
            'save-cart-js',
            SAVE_CARTS_PLUGIN_URL . 'assets/js/save-cart.js',
            ['jquery'],
            '1.0',
            true
        );
        wp_enqueue_style(
            'save-cart-style',
            SAVE_CARTS_PLUGIN_URL . 'assets/css/save-cart.css',
            [],
            filemtime(SAVE_CARTS_PLUGIN_URL . 'assets/css/save-cart.css')
        );
        $shop_url = wc_get_page_permalink('shop');
        if (defined('SAVE_CARTS_PRO') && SAVE_CARTS_PRO) {
            $shop_url = apply_filters('save_carts_continue_shopping_url', $shop_url);
        }
        wp_localize_script('save-cart-js', 'save_cart_ajax_obj', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('save_cart_nonce'),
            'success_msg' => __('Cart saved successfully!', 'save-carts'),
            'shop_url'    => esc_url($shop_url),
        ]);
    }

    public function render_save_cart_form()
    {
        if (!function_exists('WC') || WC()->cart->is_empty()) {
            return ''; // No mostrar el formulario si el carrito está vacío
        }
        ob_start();
?>
        <div class="woocommerce">
            <div class="woocommerce-form-coupon-toggle">
                <h3><?php esc_html_e('Save this cart', 'save-carts'); ?></h3>

                <form method="post" class="save-cart-form">
                    <p>
                        <label for="cart_name"><?php esc_html_e('Cart name:', 'save-carts'); ?></label><br>
                        <input type="text" name="cart_name" id="cart_name" required>
                    </p>
                    <p>
                        <button type="submit" class="button"><?php esc_html_e('Save cart', 'save-carts'); ?></button>
                    </p>
                </form>
            </div>
        </div>
<?php
        return ob_get_clean();
    }

    public function handle_ajax_cart_save()
    {

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => __('You must be logged in to save a cart.', 'save-carts')]);
        }
        check_ajax_referer('save_cart_nonce', 'nonce');
        $cart_name = sanitize_text_field($_POST['cart_name'] ?? '');
        $user_id = get_current_user_id();

        if (empty($cart_name)) {
            wp_send_json_error(['message' => __('Cart name is required.', 'save-carts')]);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'savedcarts';

        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE user_id = %d AND name = %s",
            $user_id,
            $cart_name
        ));

        if ($exists > 0) {
            wp_send_json_error(['message' => __('You already have a cart saved with that name.', 'save-carts')]);
        }

        $cart = WC()->cart;
        if (!$cart || $cart->is_empty()) {
            wp_send_json_error(['message' => __('Your cart is empty.', 'save-carts')]);
        }

        if (!SAVE_CARTS_PRO) {
            $count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE user_id = %d",
                $user_id
            ));
            if ($count >= 5) {
                wp_send_json_error(['message' => __('You can only save up to 5 carts.', 'save-carts')]);
            }
        }

        $wpdb->insert($table, [
            'user_id' => $user_id,
            'name' => $cart_name,
            'data' => maybe_serialize($cart->get_cart_contents()),
            'total_taxes' => $cart->get_taxes_total(),
            'total_no_taxes' => $cart->get_cart_contents_total(),
            'quantity_articles' => $cart->get_cart_contents_count(),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql'),
        ]);

        $message = __('Your cart has been saved successfully.', 'save-carts');

        if (defined('SAVE_CARTS_PRO') && SAVE_CARTS_PRO) {
            $custom = get_option('save_carts_success_message');
            if (!empty($custom)) {
                $message = $custom;
            }
        }

        wp_send_json_success([
            'message' => $message,
            'show_actions' => true
        ]);

        
    }
}
