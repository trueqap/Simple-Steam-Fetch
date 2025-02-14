<?php

namespace HelloWP\HWSteamMain\App\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class AdminSettings {
    private static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']); 
    }

    public function add_admin_menu() {
        add_menu_page(
            __('Game Fetch', 'hw-steam-fetch-games'),  
            __('Game Fetch', 'hw-steam-fetch-games'),  
            'manage_options',                           
            'hw_steam_fetch',                           
            [$this, 'render_fetch_games_page'],         
            'dashicons-download',                       
            60                                         
        );
        
        add_submenu_page(
            'hw_steam_fetch',
            __('Setup', 'hw-steam-fetch-games'),
            __('Setup', 'hw-steam-fetch-games'),
            'manage_options',
            'hw_setup',
            [$this, 'render_setup_page']
        );
        
        add_submenu_page(
            'hw_steam_fetch',
            __('Settings', 'hw-steam-fetch-games'),
            __('Settings', 'hw-steam-fetch-games'),
            'manage_options',
            'hw_steam_settings',
            [$this, 'render_settings_page']
        );

    }

    public function register_settings() {
        // Core settings
        register_setting('hw_steam_settings_group', 'hw_steam_selected_cpt');
        register_setting('hw_steam_settings_group', 'hw_steam_post_status');
        register_setting('hw_steam_settings_group', 'hw_steam_save_short_description');
        register_setting('hw_steam_settings_group', 'hw_steam_save_description');
        register_setting('hw_steam_settings_group', 'hw_steam_detailed_description_meta');


        // taxonomy
        register_setting('hw_steam_settings_group', 'hw_steam_genre_taxonomy');
        register_setting('hw_steam_settings_group', 'hw_steam_category_taxonomy');
        register_setting('hw_steam_settings_group', 'hw_steam_developer_taxonomy');
        register_setting('hw_steam_settings_group', 'hw_steam_publisher_taxonomy');
        register_setting('hw_steam_settings_group', 'hw_steam_platform_taxonomy');

        // image settings
        register_setting('hw_steam_settings_group', 'hw_steam_save_featured_image');
        register_setting('hw_steam_settings_group', 'hw_steam_disable_inline_images');
        register_setting('hw_steam_settings_group', 'hw_steam_capsule_meta');
        register_setting('hw_steam_settings_group', 'hw_steam_gallery_meta');
        register_setting('hw_steam_settings_group', 'hw_steam_movie_meta');

        // meta settings
        register_setting('hw_steam_settings_group', 'hw_steam_release_date_meta');
        register_setting('hw_steam_settings_group', 'hw_steam_release_date_format');

        register_setting('hw_steam_settings_group', 'hw_steam_is_free_meta');
        register_setting('hw_steam_settings_group', 'hw_steam_is_free_true_value');
        register_setting('hw_steam_settings_group', 'hw_steam_is_free_false_value');
        register_setting('hw_steam_settings_group', 'hw_steam_price_meta');
        register_setting('hw_steam_settings_group', 'hw_steam_remove_currency');

       // General settings group
        register_setting('hw_steam_general_settings_group', 'hw_delete_imported_images');


    }

    public function render_fetch_games_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'hw-steam-fetch-games'));
        }
        include_once __DIR__ . '/views/fetch-games.php';
    }

    public function render_setup_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'hw-steam-fetch-games'));
        }

        $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'existing_data'; // Default tab

        ?>
        <div class="wrap">
            <h1><?php _e('Setup Steam Plugin', 'hw-steam-fetch-games'); ?></h1>
            <h2 class="nav-tab-wrapper">
                <a href="?page=hw_setup&tab=existing_data" class="nav-tab <?php echo ($current_tab === 'existing_data') ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Games Fetch mapping', 'hw-steam-fetch-games'); ?>
                </a>
                <a href="?page=hw_setup&tab=create_data" class="nav-tab <?php echo ($current_tab === 'create_data') ? 'nav-tab-active' : ''; ?>">
                    <?php _e('DLC and packages Fetch mapping', 'hw-steam-fetch-games'); ?>
                </a>
            </h2>
            
            <?php
            if ($current_tab === 'existing_data') {
                include_once __DIR__ . '/views/existing-data.php'; 
            } elseif ($current_tab === 'create_data') {
                include_once __DIR__ . '/views/create-data.php';   
            }
            ?>
        </div>
        <?php
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'hw-steam-fetch-games'));
        }
        include_once __DIR__ . '/views/settings-page.php';
    }
}
