<?php

namespace SaveCarts\Setup;

use wpdb;

if (!defined('ABSPATH')) {
    exit;
}

class Installer
{
    public static function create_saved_carts_table()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'savedcarts';

        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name) {
            error_log("The table {$table_name} already exists. Skipping creation.");
            return;
        }

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
            user_id MEDIUMINT(9) NOT NULL,
            name VARCHAR(255) NOT NULL DEFAULT '',
            data LONGTEXT NOT NULL,
            total_taxes DECIMAL(10,2) NOT NULL DEFAULT 0,
            total_no_taxes DECIMAL(10,2) NOT NULL DEFAULT 0,
            quantity_articles INT NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    public static function create_saved_carts_page()
    {
        $existing = get_page_by_path('saved-carts');

        if ($existing) {
            return; // Page already exists
        }

        $page_data = [
            'post_title'    => 'Saved Carts',
            'post_name'     => 'saved-carts',
            'post_content'  => '[save_carts]', // Aquí irá el shortcode del plugin
            'post_status'   => 'publish',
            'post_type'     => 'page',
        ];

        wp_insert_post($page_data);
    }
}