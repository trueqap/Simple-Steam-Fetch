<?php

namespace HelloWP\HWSteamMain\App\Services;

if (!defined('ABSPATH')) {
    exit;
}

class GameNews {

    public static function init() {
        add_action('wp_ajax_hw_fetch_game_news', [__CLASS__, 'fetch_news']);
        add_action('wp_ajax_nopriv_hw_fetch_game_news', [__CLASS__, 'fetch_news']);
    }

    public static function fetch_news() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'hw_steam_news_nonce')) {
            wp_send_json_error(['message' => __('Invalid nonce.', 'hw-steam-fetch-games')]);
        }
    
        if (empty($_POST['post_id'])) {
            wp_send_json_error(['message' => __('Post ID not provided.', 'hw-steam-fetch-games')]);
        }
    
        $post_id = absint($_POST['post_id']);
        $feednumber = isset($_POST['feednumber']) ? absint($_POST['feednumber']) : 3; 
    
        $app_id = get_post_meta($post_id, '_hw_steam_app_id', true);
        $app_id = absint($app_id);
    
        if (empty($app_id)) {
            wp_send_json_error(['message' => __('App ID not found for this post.', 'hw-steam-fetch-games')]);
        }
    
        $api_url = "https://api.steampowered.com/ISteamNews/GetNewsForApp/v2/?appid={$app_id}&count={$feednumber}&maxlength=550&format=json";
        $response = wp_remote_get($api_url);
    
        if (is_wp_error($response)) {
            wp_send_json_error(['message' => __('Failed to fetch news from Steam.', 'hw-steam-fetch-games')]);
        }
    
        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (empty($data['appnews']['newsitems'])) {
            wp_send_json_error(['message' => __('No news found for this game.', 'hw-steam-fetch-games')]);
        }
    
        wp_send_json_success($data['appnews']['newsitems']);
    }
    
    
}
