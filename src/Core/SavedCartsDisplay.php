<?php

namespace SaveCarts\Core;

if (!defined('ABSPATH')) {
    exit;
}

class SavedCartsDisplay
{
    public function __construct()
    {
        add_shortcode('save_carts', [$this, 'render_saved_carts']);
    }

    public function render_saved_carts()
    {
        if (!is_user_logged_in()) {
            return '<p>' . esc_html__('You must be logged in to view your saved carts.', 'save-carts') . '</p>';
        }

        $user_id = get_current_user_id();
        global $wpdb;
        $table = $wpdb->prefix . 'savedcarts';

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table WHERE user_id = %d ORDER BY updated_at DESC",
                $user_id
            )
        );

        if (empty($results)) {
            return '<p>' . esc_html__('You have no saved carts yet.', 'save-carts') . '</p>';
        }

        ob_start(); ?>

        <table class="shop_table shop_table_responsive saved-carts-table">
            <thead>
                <tr>
                    <th><?php esc_html_e('Cart Name', 'save-carts'); ?></th>
                    <th><?php esc_html_e('Date', 'save-carts'); ?></th>
                    <th><?php esc_html_e('Items', 'save-carts'); ?></th>
                    <th><?php esc_html_e('Total', 'save-carts'); ?></th>
                    <th><?php esc_html_e('Actions', 'save-carts'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $cart): ?>
                    <tr>
                        <td><?php echo esc_html($cart->name); ?></td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($cart->updated_at))); ?></td>
                        <td><?php echo intval($cart->quantity_articles); ?></td>
                        <td><?php echo wc_price(floatval($cart->total_no_taxes)); ?></td>
                        <td>
                            <button class="button restore-cart-button" data-cart-id="<?php echo esc_attr($cart->id); ?>">
                                <?php esc_html_e('Restore', 'save-carts'); ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php
        return ob_get_clean();
    }
}