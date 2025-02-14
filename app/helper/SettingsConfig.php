<?php

namespace HelloWP\HWSteamMain\App\Helper;

if (!defined('ABSPATH')) {
    exit;
}

class SettingsConfig {
    public static function get_selected_cpt() {
        return get_option('hw_steam_selected_cpt', '');
    }

    public static function get_post_status() {
        return get_option('hw_steam_post_status', 'publish');
    }    

    public static function get_selected_genre_taxonomy() {
        return get_option('hw_steam_genre_taxonomy', '');
    }

    public static function get_selected_category_taxonomy() {
        return get_option('hw_steam_category_taxonomy', '');
    }

    public static function get_selected_developer_taxonomy() {
        return get_option('hw_steam_developer_taxonomy', '');
    }

    public static function get_selected_publisher_taxonomy() {
        return get_option('hw_steam_publisher_taxonomy', '');
    }

    public static function get_selected_platform_taxonomy() {
        return get_option('hw_steam_platform_taxonomy', '');
    }

    public static function save_featured_image() {
        return (bool) get_option('hw_steam_save_featured_image', false);
    }

    public static function get_capsule_meta() {
        return get_option('hw_steam_capsule_meta', '');
    }       
    public static function get_gallery_meta() {
        return get_option('hw_steam_gallery_meta', '');
    }    

    public static function save_description_to_content() {
        return (bool) get_option('hw_steam_save_description', false);
    }

    public static function get_detailed_description_meta() {
        return get_option('hw_steam_detailed_description_meta', '');
    }    

    public static function get_save_short_description() {
        return get_option('hw_steam_save_short_description', 'excerpt');
    }    

    public static function disable_inline_images() {
        return (bool) get_option('hw_steam_disable_inline_images', false);
    }

    public static function get_movie_meta() {
        return get_option('hw_steam_movie_meta', '');
    }
    

    public static function get_release_date_meta() {
        return get_option('hw_steam_release_date_meta', '');
    }

    public static function get_release_date_format() {
        return get_option('hw_steam_release_date_format', 'string'); 
    }

    public static function get_is_free_meta() {
        return get_option('hw_steam_is_free_meta', '');
    }

    public static function get_is_free_true_value() {
        return get_option('hw_steam_is_free_true_value', 'yes');
    }

    public static function get_is_free_false_value() {
        return get_option('hw_steam_is_free_false_value', 'no'); 
    }
    
    public static function get_price_meta() {
        return get_option('hw_steam_price_meta', '');
    }
    
    public static function remove_currency_symbol() {
        return (int) get_option('hw_steam_remove_currency', 0);
    }
    
}
