<?php

namespace SaveCarts\Includes;

use SaveCarts\Core\CartCleaner;
use SaveCarts\Core\CartSaver; 
use SaveCarts\Core\CartManager;

if (!defined('ABSPATH')) {
    exit;
}

class AjaxHandlers
{
    public function __construct()
    {
        add_action('wp_ajax_save_cart_ajax', [$this, 'handle_cart_save']);
        add_action('wp_ajax_nopriv_save_cart_ajax', [$this, 'handle_cart_save']);

        add_action('wp_ajax_clear_saved_cart_ajax', [$this, 'handle_clear_cart']);
        add_action('wp_ajax_nopriv_clear_saved_cart_ajax', [$this, 'handle_clear_cart']);

        add_action('wp_ajax_delete_saved_cart_ajax', [$this, 'handle_cart_delete']);
        add_action('wp_ajax_nopriv_delete_saved_cart_ajax', [$this, 'handle_cart_delete']);

    }

public function handle_cart_save()
{
    $result = CartSaver::save_cart();

    if (is_wp_error($result)) {
        wp_send_json_error(['message' => $result->get_error_message()]);
    }

    wp_send_json_success([
        'message' => $result,
        'show_actions' => true
    ]);
}

public function handle_clear_cart()
{
    $result = CartCleaner::clear_cart();

    if (is_wp_error($result)) {
        wp_send_json_error(['message' => $result->get_error_message()]);
    }

    wp_send_json_success($result);
}



public function handle_cart_delete()
{
    $result = CartManager::delete_cart();

    if (is_wp_error($result)) {
        wp_send_json_error(['message' => $result->get_error_message()]);
    }

    wp_send_json_success(['message' => $result]);
}
}