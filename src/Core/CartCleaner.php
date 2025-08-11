<?php

namespace SaveCarts\Core;

use WP_Error;

if (!defined('ABSPATH')) {
    exit;
}

class CartCleaner
{
    /**
     * Clears the WooCommerce cart after validating the request.
     * Returns an array with message and redirect_url on success, or WP_Error on failure.
     *
     * @return array|WP_Error
     */
    public static function clear_cart()
    {
        // Nonce check (do not die on failure; return WP_Error for consistency)
        if (!check_ajax_referer('save_cart_nonce', 'nonce', false)) {
            return new WP_Error('invalid_nonce', __('Invalid nonce.', 'save-carts'));
        }

        // Ensure WooCommerce cart is available
        if (!function_exists('WC') || !WC()->cart) {
            return new WP_Error('no_cart', __('Cart is not available.', 'save-carts'));
        }

        /**
         * Allow Pro or third-parties to run logic before clearing the cart.
         */
        do_action('save_carts_before_clear_cart');

        // Empty the cart
        WC()->cart->empty_cart();

        // Requested referrer (sanitized)
        $requested = isset($_POST['referrer'])
            ? sanitize_text_field(wp_unslash($_POST['referrer']))
            : '';

        // Compute redirect URL (filterable + Pro override)
        $redirect = self::determine_redirect_url($requested);

        /**
         * Allow hooks after clearing the cart.
         */
        do_action('save_carts_after_clear_cart', $redirect, $requested);

        $message = apply_filters(
            'save_carts_clear_cart_message',
            __('Cart cleared successfully.', 'save-carts'),
            $redirect,
            $requested
        );

        $response = [
            'message'      => $message,
            'redirect_url' => $redirect,
        ];

        // Final response filter to let Pro/packages reshape payload
        return apply_filters('save_carts_clear_cart_response', $response, $requested);
    }

    /**
     * Determines the redirect URL after clearing the cart.
     * Pro can override via 'save_carts_pro_redirect_after_clear'.
     *
     * @param string $requested
     * @return string
     */
    protected static function determine_redirect_url(string $requested): string
    {
        $redirect = wc_get_page_permalink('shop');

        if (defined('SAVE_CARTS_PRO') && SAVE_CARTS_PRO) {
            $custom_redirect = apply_filters('save_carts_pro_redirect_after_clear', $requested);
            if (!empty($custom_redirect)) {
                $redirect = esc_url_raw($custom_redirect);
            }
        }

        // Core-level filter for Free & Pro
        $redirect = apply_filters('save_carts_clear_cart_redirect', $redirect, $requested);

        return $redirect;
    }
}
