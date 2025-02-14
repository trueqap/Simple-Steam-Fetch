<?php

namespace HelloWP\HWSteamMain\App\Services;

use HelloWP\HWSteamMain\App\Helper\SettingsConfig;
use HelloWP\HWSteamMain\App\Helper\FetchHelpers;

if (!defined('ABSPATH')) {
    exit;
}

class ProcessFetch {

    public static function fetch_and_process_game($app_id, $language = 'en') {
        $cpt = SettingsConfig::get_selected_cpt();
        $post_status = SettingsConfig::get_post_status(); 
        $save_short_description = SettingsConfig::get_save_short_description();
        $save_description = SettingsConfig::save_description_to_content();
        $detailed_description_meta = SettingsConfig::get_detailed_description_meta();  
        $post_supports_content = post_type_supports($cpt, 'editor');

        $taxonomy_genre = SettingsConfig::get_selected_genre_taxonomy();
        $taxonomy_category = SettingsConfig::get_selected_category_taxonomy();
        $taxonomy_developer = SettingsConfig::get_selected_developer_taxonomy();
        $taxonomy_publisher = SettingsConfig::get_selected_publisher_taxonomy();
        $taxonomy_platform = SettingsConfig::get_selected_platform_taxonomy();

        $save_featured_image = SettingsConfig::save_featured_image();
        $capsule_meta = SettingsConfig::get_capsule_meta();
        $gallery_meta = SettingsConfig::get_gallery_meta(); 
        $disable_inline_images = SettingsConfig::disable_inline_images();
        $movie_meta = SettingsConfig::get_movie_meta();

        $release_date_meta = SettingsConfig::get_release_date_meta();

        $is_free_meta = SettingsConfig::get_is_free_meta();
        $is_free_true_value = SettingsConfig::get_is_free_true_value();
        $is_free_false_value = SettingsConfig::get_is_free_false_value();

        $price_meta = SettingsConfig::get_price_meta();
        $remove_currency_symbol = SettingsConfig::remove_currency_symbol();


        $language = isset($_POST['steam_language']) ? sanitize_text_field($_POST['steam_language']) : 'en';

        if (empty($cpt)) {
            return ['success' => false, 'message' => __('No Custom Post Type selected.', 'hw-steam-fetch-games')];
        }

        $existing_post = get_posts([
            'post_type'  => $cpt,
            'meta_key'   => '_hw_steam_app_id',
            'meta_value' => $app_id,
            'post_status' => 'any',
            'numberposts'=> 1
        ]);

        $is_update = false;
        $current_status = 'draft';
        if (!empty($existing_post)) {
            $post_id = $existing_post[0]->ID;
            $is_update = true;
            $current_status = get_post_status($post_id);
        } else {
            $post_id = wp_insert_post([
                'post_type'   => $cpt,
                'post_title'  => __('Placeholder Title', 'hw-steam-fetch-games'),
                'post_status' => $post_status, 
            ]);
            
            if (is_wp_error($post_id)) {
                error_log("Failed to create post: " . $post_id->get_error_message());
                return ['success' => false, 'message' => __('Failed to create post.', 'hw-steam-fetch-games')];
            }
        
            update_post_meta($post_id, '_hw_steam_app_id', $app_id);
            $current_status = $post_status;  
        }
        

        $api_result = FetchHelpers::fetch_game_data_from_steam($app_id, $language);

        if (!$api_result['success']) {
            error_log("Steam API fetch failed: " . $api_result['message']);
            return ['success' => false, 'message' => $api_result['message']];
        }

        $game_data = $api_result['data'];

        $detailed_description = html_entity_decode($game_data['detailed_description'], ENT_QUOTES | ENT_HTML5);

        if ($disable_inline_images) {
            $detailed_description = preg_replace('/<img[^>]*>/', '', $detailed_description);
            $detailed_description = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}\x{00A0}]+/u', '', $detailed_description);
            $detailed_description = preg_replace('/<p>\s*<\/p>/', '', $detailed_description);
            $detailed_description = preg_replace('/(\r?\n){2,}/', "\n", $detailed_description);
        }
        
        
        
        // --- Detailed Description Handling ---
        $post_content = '';
        if ($save_description && $post_supports_content) {
            if (!empty($detailed_description)) {
                $post_content = wp_kses_post($detailed_description);
                wp_update_post([
                    'ID'          => $post_id,
                    'post_content'=> $post_content,
                ]);
            }
        } elseif (!empty($detailed_description_meta)) {
            update_post_meta($post_id, $detailed_description_meta, wp_kses_post($detailed_description));
        }
        
        // Short Description Handling
        $short_description = isset($game_data['short_description']) ? wp_kses_post($game_data['short_description']) : '';
        if ($save_short_description !== 'none' && !empty($short_description)) {
            if ($save_short_description === 'content') {
                $post_content = $short_description . "\n\n" . $post_content;
                wp_update_post([
                    'ID'          => $post_id,
                    'post_content'=> $post_content,
                ]);
            } elseif ($save_short_description === 'excerpt') {
                wp_update_post([
                    'ID'           => $post_id,
                    'post_excerpt' => $short_description,
                ]);
            }
        }

        // META: Release date 
        if (!empty($release_date_meta) && !empty($game_data['release_date']['date'])) {
            $release_date_format = SettingsConfig::get_release_date_format();  // 'unix', 'timestamp', 'string'
            $formatted_date = FetchHelpers::format_release_date(
                $game_data['release_date']['date'], 
                $release_date_format, 
                $language
            );

            if ($formatted_date !== null) {
                update_post_meta($post_id, $release_date_meta, $formatted_date); 
            }
        }

        if (!empty($gallery_meta) && !empty($game_data['screenshots'])) {
            $gallery_images = FetchHelpers::upload_gallery_images($game_data['screenshots'], $game_data['name']);
            
            if (!empty($gallery_images)) {
                update_post_meta($post_id, $gallery_meta, $gallery_images);
            }
        }
        


        if (!empty($is_free_meta) && isset($game_data['is_free'])) {
            $is_free_value = $game_data['is_free'] ? $is_free_true_value : $is_free_false_value;
            update_post_meta($post_id, $is_free_meta, sanitize_text_field($is_free_value));
        }

        if (!empty($movie_meta) && !empty($game_data['movies'])) {
            $movie_url = '';
            foreach ($game_data['movies'] as $movie) {
                if (!empty($movie['mp4']['max'])) {
                    $movie_url = str_replace('http://', 'https://', sanitize_text_field($movie['mp4']['max']));
                    break;
                }
            }
            if (!empty($movie_url)) {
                update_post_meta($post_id, $movie_meta, $movie_url);
            }
        }

        if (!empty($price_meta) && !empty($game_data['price_overview']['final_formatted'])) {
            $price = $game_data['price_overview']['final_formatted'];
        
            if ($remove_currency_symbol) {
                $price = preg_replace('/[^0-9,.]/', '', $price);
            }
        
            update_post_meta($post_id, $price_meta, sanitize_text_field($price));
        }
        
        wp_update_post([
            'ID'          => $post_id,
            'post_title'  => $game_data['name'],
            'post_content'=> $post_content,
            'post_status' => $current_status,
        ]);

        self::assign_terms_to_post($post_id, $taxonomy_genre, $game_data['genres'], 'description');
        self::assign_terms_to_post($post_id, $taxonomy_category, $game_data['categories'], 'description');
        self::assign_terms_to_post($post_id, $taxonomy_developer, $game_data['developers']);
        self::assign_terms_to_post($post_id, $taxonomy_publisher, $game_data['publishers']);

        if (!empty($game_data['platforms'])) {
            $platform_terms = FetchHelpers::get_platform_terms($game_data['platforms']);
            self::assign_terms_to_post($post_id, $taxonomy_platform, $platform_terms);
        }

       if ($save_featured_image && !empty($game_data['header_image'])) {
            $header_image_id = FetchHelpers::upload_or_find_image($game_data['header_image'], $game_data['name'], 'header');
            if (!is_wp_error($header_image_id)) {
                set_post_thumbnail($post_id, $header_image_id);
            }
        }

        if (!empty($capsule_meta) && !empty($game_data['capsule_image'])) {
            $capsule_image_id = FetchHelpers::upload_or_find_image($game_data['capsule_image'], $game_data['name'], 'logo');
            
            if (!is_wp_error($capsule_image_id)) {
                $capsule_image_url = wp_get_attachment_url($capsule_image_id);
                
                $capsule_image_data = [
                    'id'  => $capsule_image_id,
                    'url' => $capsule_image_url,
                ];

                update_post_meta($post_id, $capsule_meta, $capsule_image_data);
            }
        }
              
        return [
            'success'  => true,
            'post_id'  => $post_id,
            'is_update'=> $is_update,
        ];
    }

  

    private static function assign_terms_to_post($post_id, $taxonomy, $terms, $key = null) {
        if (empty($taxonomy) || empty($terms)) {
            return;
        }

        $term_names = array_map(function ($term) use ($key) {
            return sanitize_text_field($key ? $term[$key] : $term);
        }, $terms);

        foreach ($term_names as $term_name) {
            $term = term_exists($term_name, $taxonomy);
            if (!$term) {
                $term = wp_insert_term($term_name, $taxonomy);
            }
            if (!is_wp_error($term)) {
                wp_set_object_terms($post_id, (int) $term['term_id'], $taxonomy, true);
            }
        }
    }
}
