<?php

namespace SaveCarts\Frontend;

if (!defined('ABSPATH')) {
    exit;
}

class PublicView
{
    public function __construct()
    {
        // Register hooks on init
        add_action('init', [$this, 'register_endpoints']);
        add_filter('woocommerce_account_menu_items', [$this, 'add_account_menu_item']);
        add_action('woocommerce_account_saved-carts_endpoint', [$this, 'render_saved_carts_endpoint']);
    }

    /**
     * Register custom endpoints for the "My Account" area.
     */
    public function register_endpoints()
    {
        add_rewrite_endpoint('saved-carts', EP_ROOT | EP_PAGES);
    }

    /**
     * Add custom menu item to the "My Account" sidebar.
     */
    public function add_account_menu_item($items)
    {
        $items['saved-carts'] = __('Saved Carts', 'save-carts');
        return $items;
    }

    /**
     * Output the content for the "Saved Carts" endpoint.
     */
    public function render_saved_carts_endpoint()
    {
        echo do_shortcode('[save_carts]');
    }
}