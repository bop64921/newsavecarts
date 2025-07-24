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
        add_options_page(
            __('Save Carts', 'save-carts'),          // Título en la pestaña del navegador
            __('Save Carts', 'save-carts'),          // Título en el menú
            'manage_options',                        // Capability
            'save-carts-settings',                   // Slug
            [$this, 'render_settings_page']          // Callback que renderiza la página
        );
    }

    public function register_settings() {
        register_setting('save_carts_settings_group', 'save_carts_auto_display');

        add_settings_section(
            'save_carts_main_settings',
            __('Ajustes del formulario', 'save-carts'),
            '__return_null',
            'save-carts-settings'
        );

        add_settings_field(
            'save_carts_auto_display',
            __('Mostrar automáticamente en el carrito', 'save-carts'),
            [$this, 'render_auto_display_field'],
            'save-carts-settings',
            'save_carts_main_settings'
        );
    }

    public function render_auto_display_field() {
        $value = get_option('save_carts_auto_display', '1');
        echo '<input type="checkbox" name="save_carts_auto_display" value="1"' . checked($value, '1', false) . '> ';
        echo '<label>' . __('Mostrar el formulario debajo del carrito de WooCommerce.', 'save-carts') . '</label>';
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Ajustes de Save Carts', 'save-carts'); ?></h1>
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