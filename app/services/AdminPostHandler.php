<?php

namespace HelloWP\HWSteamMain\App\Services;

use HelloWP\HWSteamMain\App\Services\ProcessFetch;

if (!defined('ABSPATH')) {
    exit;
}

class AdminPostHandler {

    public static function init() {
        // Hook into admin_post for logged-in users
        add_action('admin_post_hw_fetch_game_data', [__CLASS__, 'handle_fetch_game_data']);
    }

    public static function handle_fetch_game_data() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to access this page.', 'hw-steAM-fetch-games'));
        }

        if (!isset($_POST['hw_fetch_game_data_nonce']) || 
            !wp_verify_nonce($_POST['hw_fetch_game_data_nonce'], 'hw_fetch_game_data_action')) {
            wp_die(__('Nonce verification failed.', 'hw-steAM-fetch-games'));
        }

        // Validate steam_app_id
        if (!isset($_POST['steam_app_id']) || empty($_POST['steam_app_id'])) {
            wp_redirect(add_query_arg('error', 'missing_app_id', wp_get_referer()));
            exit;
        }

        $app_id = sanitize_text_field($_POST['steam_app_id']);
        $app_id = self::extract_app_id($app_id);

        $language = isset($_POST['steam_language']) 
            ? sanitize_text_field($_POST['steam_language']) 
            : 'en'; 
    
        // Check if $app_id is numeric
        if (empty($app_id) || !ctype_digit($app_id)) {
            wp_redirect(add_query_arg('error', 'invalid_app_id', wp_get_referer()));
            exit;
        }

        // Call our main fetch
        $result = ProcessFetch::fetch_and_process_game($app_id, $language);

        // Redirect based on success/fail
        if ($result['success']) {
            wp_redirect(add_query_arg(['success' => 'true', 'post_id' => $result['post_id']], wp_get_referer()));
        } else {
            wp_redirect(add_query_arg('error', 'fetch_failed', wp_get_referer()));
        }
        exit;
    }

    /**
     * Extract the App ID from the Steam URL or return the original input if it is not a URL.
     *
     * @param string $input
     * @return string
     */
    private static function extract_app_id($input) {
        // Attempt to parse store.steampowered.com/app/<digits>
        if (preg_match('/store\.steampowered\.com\/app\/(\d+)/', $input, $matches)) {
            return $matches[1];
        }
        // Otherwise keep only digits
        return preg_replace('/\D/', '', $input);
    }
}
