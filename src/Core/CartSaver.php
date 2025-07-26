<?php

namespace SaveCarts\Core;



if (!defined('ABSPATH')) {
    exit;
}

class CartSaver
{
    public function __construct()
    {
        // Hook into cart page
        error_log('✅ CartSaver constructor ejecutado');
        add_action('woocommerce_cart_collaterals', [$this, 'render_save_cart_form']);
        add_shortcode('save_cart_form', [$this, 'render_save_cart_form']);
    }

    /**
     * Render the form to save the current cart
     */
   public function render_save_cart_form()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_cart_submit'])) {
        $this->handle_form_submission();
    }

    ob_start();
    ?>
    <div class="save-cart-form">
        <h3>Save this cart</h3>
        <form method="post">
            <label for="cart_name">Cart name:</label><br>
            <input type="text" name="cart_name" id="cart_name" required><br><br>
            <button type="submit" name="save_cart_submit" class="button">Save cart</button>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
    /**
     * Save the current cart to the database
     */
    private function handle_form_submission()
{
    if (!is_user_logged_in()) {
        wc_add_notice(__('You must be logged in to save a cart.', 'save-carts'), 'error');
        return;
    }

    $user_id = get_current_user_id();
    $cart_name = sanitize_text_field($_POST['cart_name']);

    if (empty($cart_name)) {
        wc_add_notice(__('Cart name is required.', 'save-carts'), 'error');
        return;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'savedcarts';

    // Verificar duplicado
    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE user_id = %d AND name = %s",
        $user_id,
        $cart_name
    ));

    if ($exists > 0) {
        wc_add_notice(__('You already have a cart saved with that name.', 'save-carts'), 'error');
        return;
    }

    $cart = WC()->cart;

    if (!$cart || $cart->is_empty()) {
        wc_add_notice(__('Your cart is empty.', 'save-carts'), 'error');
        return;
    }

    $cart_data = $cart->get_cart_contents();
    $total_taxes = $cart->get_taxes_total();
    $total_no_taxes = $cart->get_cart_contents_total();
    $quantity_articles = $cart->get_cart_contents_count();

    $wpdb->insert($table, [
        'user_id' => $user_id,
        'name' => $cart_name,
        'data' => maybe_serialize($cart_data),
        'total_taxes' => $total_taxes,
        'total_no_taxes' => $total_no_taxes,
        'quantity_articles' => $quantity_articles,
    ]);

    wc_add_notice(__('Cart saved successfully!', 'save-carts'), 'success');
}
}