<?php

namespace HelloWP\HWSteamMain\App\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class AdminSettings {
    private static $_instance = null;

    /**
     * Singleton instance.
     *
     * @return AdminSettings
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor: add menu + register settings hooks.
     */
    private function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    /**
     * Add main menu and submenus.
     */
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            __('Game Fetch', 'hw-steam-fetch-games'),
            __('Game Fetch', 'hw-steAM-fetch-games'),
            'manage_options',
            'hw_steam_fetch',
            [$this, 'render_fetch_games_page'],
            'dashicons-download',
            60
        );

        // Submenu: Setup
        add_submenu_page(
            'hw_steam_fetch',
            __('Setup', 'hw-steAM-fetch-games'),
            __('Setup', 'hw-steAM-fetch-games'),
            'manage_options',
            'hw_setup',
            [$this, 'render_setup_page']
        );

        // Submenu: Settings
        add_submenu_page(
            'hw_steam_fetch',
            __('Settings', 'hw-steAM-fetch-games'),
            __('Settings', 'hw-steAM-fetch-games'),
            'manage_options',
            'hw_steam_settings',
            [$this, 'render_settings_page']
        );
    }

    /**
     * Register both "fetch" settings (hw_steam_game_fetch_cfg)
     * and "general" settings (hw_steam_general_cfg).
     *
     * Each uses a different group name, so you can manage them in separate forms
     * (if you like) or the same form – whichever you prefer.
     */
    public function register_settings() {

        // 1) Fetch settings group + single array option
        register_setting(
            'hw_steam_settings_group',    // group name for FETCH
            'hw_steam_game_fetch_cfg',    // single array option name
            [
                'sanitize_callback' => [$this, 'sanitize_main_settings']
            ]
        );

        // 2) General settings group + single array option
        register_setting(
            'hw_steam_general_settings_group', // group name for GENERAL
            'hw_steam_general_cfg',            // single array option name
            [
                'sanitize_callback' => [$this, 'sanitize_general_settings']
            ]
        );
    }

    /**
     * Sanitize callback for the fetch-related settings array (hw_steam_game_fetch_cfg).
     *
     * @param array $input
     * @return array
     */
    public function sanitize_main_settings($input) {
        // $input is an associative array, e.g.:
        // [
        //   'selected_cpt' => 'games',
        //   'post_status'  => 'publish',
        //   ...
        // ]

        $output = [];

        // Example: selected_cpt
        $output['selected_cpt'] = isset($input['selected_cpt'])
            ? sanitize_text_field($input['selected_cpt'])
            : '';

        // Example: post_status
        $output['post_status'] = isset($input['post_status'])
            ? sanitize_text_field($input['post_status'])
            : 'publish';

        // Example: checkbox
        $output['save_description'] = !empty($input['save_description']) ? 1 : 0;

        // Example: radio
        $allowedShortDescValues = ['content', 'excerpt', 'none'];
        if (isset($input['save_short_description']) && in_array($input['save_short_description'], $allowedShortDescValues, true)) {
            $output['save_short_description'] = $input['save_short_description'];
        } else {
            $output['save_short_description'] = 'excerpt';
        }

        // Detailed desc meta
        $output['detailed_description_meta'] = isset($input['detailed_description_meta'])
            ? sanitize_text_field($input['detailed_description_meta'])
            : '';

        // ...stb. – a többi mezőt is hasonlóan tisztítsd
        $output['genre_taxonomy'] = isset($input['genre_taxonomy'])
            ? sanitize_text_field($input['genre_taxonomy'])
            : '';

        $output['category_taxonomy'] = isset($input['category_taxonomy'])
            ? sanitize_text_field($input['category_taxonomy'])
            : '';

        $output['developer_taxonomy'] = isset($input['developer_taxonomy'])
            ? sanitize_text_field($input['developer_taxonomy'])
            : '';

        $output['publisher_taxonomy'] = isset($input['publisher_taxonomy'])
            ? sanitize_text_field($input['publisher_taxonomy'])
            : '';

        $output['platform_taxonomy'] = isset($input['platform_taxonomy'])
            ? sanitize_text_field($input['platform_taxonomy'])
            : '';

        $output['save_featured_image']    = !empty($input['save_featured_image']) ? 1 : 0;
        $output['disable_inline_images']  = !empty($input['disable_inline_images']) ? 1 : 0;
        $output['capsule_meta']           = isset($input['capsule_meta']) ? sanitize_text_field($input['capsule_meta']) : '';
        $output['movie_meta']             = isset($input['movie_meta'])   ? sanitize_text_field($input['movie_meta'])   : '';
        $output['gallery_meta']           = isset($input['gallery_meta']) ? sanitize_text_field($input['gallery_meta']) : '';

        $output['release_date_meta'] = isset($input['release_date_meta'])
            ? sanitize_text_field($input['release_date_meta'])
            : '';

        $allowedDateFormats = ['string', 'unix', 'timestamp'];
        if (isset($input['release_date_format']) && in_array($input['release_date_format'], $allowedDateFormats, true)) {
            $output['release_date_format'] = $input['release_date_format'];
        } else {
            $output['release_date_format'] = 'string';
        }

        $output['is_free_meta']         = isset($input['is_free_meta'])         ? sanitize_text_field($input['is_free_meta'])         : '';
        $output['is_free_true_value']   = isset($input['is_free_true_value'])   ? sanitize_text_field($input['is_free_true_value'])   : 'yes';
        $output['is_free_false_value']  = isset($input['is_free_false_value'])  ? sanitize_text_field($input['is_free_false_value'])  : 'no';
        $output['price_meta']           = isset($input['price_meta'])           ? sanitize_text_field($input['price_meta'])           : '';
        $output['remove_currency']      = !empty($input['remove_currency']) ? 1 : 0;

        return $output;
    }

    /**
     * Sanitize callback for the general plugin settings (hw_steam_general_cfg).
     *
     * @param array $input
     * @return array
     */
    public function sanitize_general_settings($input) {
        $output = [];

        // Példa: 'delete_imported_images' (checkbox)
        $output['delete_imported_images'] = !empty($input['delete_imported_images']) ? 1 : 0;

        // Ha később lesznek további "general" beállítások, azokat is itt tisztítod meg
        return $output;
    }

    /**
     * Fetch Games page content
     */
    public function render_fetch_games_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'hw-steam-fetch-games'));
        }
        include_once __DIR__ . '/views/fetch-games.php';
    }

    /**
     * Setup page content
     */
    public function render_setup_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'hw-steAM-fetch-games'));
        }

        $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'existing_data';
        ?>
        <div class="wrap">
            <h1><?php _e('Setup Steam Plugin', 'hw-steAM-fetch-games'); ?></h1>
            <h2 class="nav-tab-wrapper">
                <a href="?page=hw_setup&tab=existing_data" 
                   class="nav-tab <?php echo ($current_tab === 'existing_data') ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Games Fetch mapping', 'hw-steAM-fetch-games'); ?>
                </a>
                <a href="?page=hw_setup&tab=create_data" 
                   class="nav-tab <?php echo ($current_tab === 'create_data') ? 'nav-tab-active' : ''; ?>">
                    <?php _e('DLC and packages Fetch mapping', 'hw-steAM-fetch-games'); ?>
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

    /**
     * "Settings" page content
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'hw-steAM-fetch-games'));
        }
        include_once __DIR__ . '/views/settings-page.php';
    }
}
