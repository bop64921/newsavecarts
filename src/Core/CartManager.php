<?php

namespace SaveCarts\Core;

if (!defined('ABSPATH')) {
    exit;
}

class CartManager
{
    public static function delete_cart()
    {
        if (!is_user_logged_in()) {
            return new \WP_Error('not_logged_in', __('You must be logged in to delete a cart.', 'save-carts'));
        }

        if (!check_ajax_referer('save_cart_nonce', 'nonce', false)) {
            return new \WP_Error('invalid_nonce', __('Invalid nonce.', 'save-carts'));
        }

        $cart_id = intval($_POST['cart_id'] ?? 0);
        $user_id = get_current_user_id();

        if ($cart_id <= 0) {
            return new \WP_Error('invalid_id', __('Invalid cart ID.', 'save-carts'));
        }

        global $wpdb;
        $table = $wpdb->prefix . 'savedcarts';

        $deleted = $wpdb->delete($table, [
            'id'      => $cart_id,
            'user_id' => $user_id
        ], ['%d', '%d']);

        if ($deleted === false) {
            return new \WP_Error('delete_failed', __('Could not delete the cart.', 'save-carts'));
        }

        return __('Cart deleted successfully.', 'save-carts');
    }

    // Aquí se pueden añadir más acciones:
    // public static function restore_cart()
    // public static function rename_cart()
    // public static function convert_to_list()
    // public static function categorize_cart()
}