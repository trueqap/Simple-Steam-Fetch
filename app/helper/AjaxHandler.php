<?php

namespace HelloWP\HWSteamMain\App\Helper;

if (!defined('ABSPATH')) {
    exit;
}

class AjaxHandler {
    

    public static function init() {
        add_action('wp_ajax_hw_search_steam_games', [__CLASS__, 'search_steam_games']);
    }
    
    

    public static function search_steam_games() {
        $query = sanitize_text_field($_POST['query']);

        if (strlen($query) < 3) {
            wp_send_json_error(['message' => __('Please enter at least 3 characters.', 'hw-steam-fetch-games')]);
        }

        // Steam API to get all apps and filter by the search query
        $response = wp_remote_get('https://api.steampowered.com/ISteamApps/GetAppList/v2/?format=json');
        if (is_wp_error($response)) {
            wp_send_json_error(['message' => __('Failed to fetch data from Steam API. Try reload the page', 'hw-steam-fetch-games')]);
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        $filtered_games = [];
        foreach ($data['applist']['apps'] as $app) {
            if (stripos($app['name'], $query) !== false) {
                $filtered_games[] = [
                    'id'   => $app['appid'],
                    'text' => $app['name'] . ' (' . $app['appid'] . ')'
                ];
                if (count($filtered_games) >= 20) break; // Limit the results to 20
            }
        }

        wp_send_json_success($filtered_games);
    }
}
