<?php

namespace SaveCarts\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class Admin {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }

 public function add_menu_page() {
    add_submenu_page(
        'woocommerce',                          // Slug del menú padre (WooCommerce)
        __('Save Carts', 'save-carts'),         // Título de la página
        __('Save Carts', 'save-carts'),         // Título del submenú
        'manage_woocommerce',                   // Capability
        'save-carts-settings',                  // Slug único de la página
        [$this, 'render_settings_page']         // Callback que pinta la página
    );
}

    public function register_settings() {
        register_setting('save_carts_settings_group', 'save_carts_auto_display');

        add_settings_section(
            'save_carts_main_settings',
            __('Form Settings', 'save-carts'),
            '__return_null',
            'save-carts-settings'
        );

        add_settings_field(
            'save_carts_auto_display',
            __('Automatically show the save cart form on the cart page', 'save-carts'),
            [$this, 'render_auto_display_field'],
            'save-carts-settings',
            'save_carts_main_settings'
        );
    }

  public function render_auto_display_field() {
    $value = get_option('save_carts_auto_display', '1');
    echo '<input type="checkbox" name="save_carts_auto_display" value="1"' . checked($value, '1', false) . '> ';
    echo '<label>' . __('Automatically show the save cart form on the WooCommerce cart page.', 'save-carts') . '</label>';
    
    echo '<p style="margin-top:10px;"><code>[save_cart_form]</code> ';
    echo __('Use this shortcode to place the form anywhere manually.', 'save-carts') . '</p>';
}


    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Settings of Save Carts', 'save-carts'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('save_carts_settings_group');
                do_settings_sections('save-carts-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}