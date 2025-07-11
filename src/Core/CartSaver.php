<?php

namespace SaveCarts\Core;

use WC;
use wpdb;

if (!defined('ABSPATH')) {
    exit;
}

class CartSaver
{
    public function __construct()
    {
        // Hook into cart page
        add_action('woocommerce_cart_collaterals', [$this, 'render_save_cart_form']);
    }

    /**
     * Render the form to save the current cart
     */
    public function render_save_cart_form()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_cart_submit'])) {
            $this->handle_form_submission();
        }

        echo '<div class="save-cart-form">';
        echo '<h3>Save this cart</h3>';
        echo '<form method="post">';
        echo '<label for="cart_name">Cart name:</label><br>';
        echo '<input type="text" name="cart_name" id="cart_name" required><br><br>';
        echo '<button type="submit" name="save_cart_submit" class="button">Save cart</button>';
        echo '</form>';
        echo '</div>';
    }

    /**
     * Save the current cart to the database
     */
    private function handle_form_submission()
    {
        if (!is_user_logged_in()) {
            return;
        }

        $user_id = get_current_user_id();
        $cart_name = sanitize_text_field($_POST['cart_name']);

        $cart = WC()->cart;

        if (!$cart || $cart->is_empty()) {
            return;
        }

        $cart_data = $cart->get_cart_contents();
        $total_taxes = $cart->get_taxes_total();
        $total_no_taxes = $cart->get_cart_contents_total();
        $quantity_articles = $cart->get_cart_contents_count();

        global $wpdb;
        $table = $wpdb->prefix . 'savedcarts';

        $wpdb->insert($table, [
            'user_id' => $user_id,
            'name' => $cart_name,
            'data' => maybe_serialize($cart_data),
            'total_taxes' => $total_taxes,
            'total_no_taxes' => $total_no_taxes,
            'quantity_articles' => $quantity_articles,
        ]);
    }
}