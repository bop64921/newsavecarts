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

  public static function save_cart($user_id, $cart_name)
{
    global $wpdb;

    $table = $wpdb->prefix . 'savedcarts';

    $cart = WC()->cart;

    if (!$cart || $cart->is_empty()) {
        return new \WP_Error('empty_cart', __('Your cart is empty.', 'save-carts'));
    }

    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE user_id = %d AND name = %s",
        $user_id,
        $cart_name
    ));

    if ($exists > 0) {
        return new \WP_Error('name_exists', __('You already have a cart saved with that name.', 'save-carts'));
    }

    if (!defined('SAVE_CARTS_PRO') || !SAVE_CARTS_PRO) {
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE user_id = %d",
            $user_id
        ));
        if ($count >= 5) {
            return new \WP_Error('limit_reached', __('You can only save up to 5 carts.', 'save-carts'));
        }
    }

    $result = $wpdb->insert($table, [
        'user_id'           => $user_id,
        'name'              => $cart_name,
        'data'              => maybe_serialize($cart->get_cart_contents()),
        'total_taxes'       => $cart->get_taxes_total(),
        'total_no_taxes'    => $cart->get_cart_contents_total(),
        'quantity_articles' => $cart->get_cart_contents_count(),
        'created_at'        => current_time('mysql'),
        'updated_at'        => current_time('mysql'),
    ]);

    if ($result === false) {
        return new \WP_Error('insert_failed', __('Could not save the cart.', 'save-carts'));
    }

    $message = __('Your cart has been saved successfully.', 'save-carts');

    if (defined('SAVE_CARTS_PRO') && SAVE_CARTS_PRO) {
        $custom = get_option('save_carts_success_message');
        if (!empty($custom)) {
            $message = $custom;
        }
    }

    return $message;
}
}
