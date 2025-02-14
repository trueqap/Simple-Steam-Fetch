<?php

namespace HelloWP\HWSteamMain\App\Services;

use HelloWP\HWSteamMain\App\Helper\SettingsConfig;
use HelloWP\HWSteamMain\App\Helper\FetchHelpers;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ProcessFetch
 *
 * Handles the main fetch-and-process workflow for a Steam game,
 * 
 */
class ProcessFetch {

    /**
     * Fetch data from Steam, create or update the WP post, handle images, taxonomies, meta, etc.
     *
     * @param string $app_id    The Steam App ID.
     * @param string $language  Language code (e.g., 'en', 'de', 'hu').
     * @return array            Contains 'success' (bool) and additional info like 'post_id'.
     */
    public static function fetch_and_process_game($app_id, $language = 'en') {
        // 1) Preliminary checks and post retrieval/creation
        $postInfo = self::setup_post($app_id);
        if (!$postInfo['success']) {
            return $postInfo;
        }

        $post_id       = $postInfo['post_id'];
        $current_status= $postInfo['current_status'];
        $is_update     = $postInfo['is_update'];

        // 2) Fetch Steam API data
        if (isset($_POST['steam_language'])) {
            $language = sanitize_text_field($_POST['steam_language']);
        }
        $api_result = FetchHelpers::fetch_game_data_from_steam($app_id, $language);
        if (!$api_result['success']) {
            error_log("Steam API fetch failed: " . $api_result['message']);
            return [
                'success' => false,
                'message' => $api_result['message'],
            ];
        }
        $game_data = $api_result['data'];

        // 3) Process core content (title, content, excerpt)
        self::process_core_content($post_id, $game_data, $current_status);

        // 4) Process taxonomy assignments (genre, category, dev, pub, platform)
        self::process_taxonomies($post_id, $game_data);

        // 5) Process images (featured, capsule, gallery) & movie
        self::process_images_and_movie($post_id, $game_data);

        // 6) Process meta information (is_free, price, release_date, etc.)
        self::process_meta_information($post_id, $game_data);

        /**
         * Action fired after the entire fetch_and_process_game flow finishes.
         *
         * @param int    $post_id
         * @param string $app_id
         * @param bool   $is_update
         */
        do_action('hw_steam_after_full_fetch', $post_id, $app_id, $is_update);

        return [
            'success'   => true,
            'post_id'   => $post_id,
            'is_update' => $is_update,
        ];
    }

    /**
     * Step 1: Setup post (create or retrieve existing).
     * Also returns the post ID and current post status.
     *
     * @param string $app_id
     * @return array ['success'=>bool,'post_id'=>int,'is_update'=>bool,'current_status'=>string]
     */
    private static function setup_post($app_id) {
        $cpt         = SettingsConfig::get_selected_cpt();
        $post_status = SettingsConfig::get_post_status();

        if (empty($cpt)) {
            return [
                'success' => false,
                'message' => __('No Custom Post Type selected.', 'hw-steAM-fetch-games'),
            ];
        }

        $existing_post = get_posts([
            'post_type'   => $cpt,
            'meta_key'    => '_hw_steam_app_id',
            'meta_value'  => $app_id,
            'post_status' => 'any',
            'numberposts' => 1,
        ]);

        $is_update      = false;
        $current_status = 'draft';
        if (!empty($existing_post)) {
            $post_id        = $existing_post[0]->ID;
            $is_update      = true;
            $current_status = get_post_status($post_id);

            /**
             * Action fired if we found an existing post for this app.
             *
             * @param int    $post_id
             * @param string $app_id
             */
            do_action('hw_steam_found_existing_post', $post_id, $app_id);

        } else {
            $post_id = wp_insert_post([
                'post_type'   => $cpt,
                'post_title'  => __('Placeholder Title', 'hw-steAM-fetch-games'),
                'post_status' => $post_status,
            ]);

            if (is_wp_error($post_id)) {
                error_log("Failed to create post: " . $post_id->get_error_message());
                return [
                    'success' => false,
                    'message' => __('Failed to create post.', 'hw-steAM-fetch-games'),
                ];
            }

            // Save the app ID
            update_post_meta($post_id, '_hw_steam_app_id', $app_id);
            $current_status = $post_status;

            /**
             * Action fired after creating a brand-new post for this app.
             *
             * @param int    $post_id
             * @param string $app_id
             */
            do_action('hw_steam_created_new_post', $post_id, $app_id);
        }

        return [
            'success'        => true,
            'post_id'        => $post_id,
            'current_status' => $current_status,
            'is_update'      => $is_update,
        ];
    }

    /**
     * Step 2 (part of the main flow): Process the main WP post content.
     * - Sets post title from $game_data['name']
     * - Detailed description -> post_content or a meta field
     * - Short description -> excerpt or appended to content
     *
     * @param int    $post_id
     * @param array  $game_data
     * @param string $current_status
     */
    private static function process_core_content($post_id, array $game_data, string $current_status) {
        $save_description         = SettingsConfig::save_description_to_content();
        $save_short_description   = SettingsConfig::get_save_short_description();
        $detailed_description_meta= SettingsConfig::get_detailed_description_meta();

        // Check if this CPT supports the editor
        $cpt = get_post_type($post_id);
        $supports_content = post_type_supports($cpt, 'editor');

        // Possibly remove inline images from the detailed description if setting is enabled
        $disable_inline_images = SettingsConfig::disable_inline_images();

        // Post title
        $post_title = $game_data['name'] ?? 'Untitled';

        // Detailed description
        $detailed_desc_raw = $game_data['detailed_description'] ?? '';
        $detailed_desc_dec = html_entity_decode($detailed_desc_raw, ENT_QUOTES | ENT_HTML5);

        // If "disable inline images" is on, remove <img> tags and extra placeholders
        if ($disable_inline_images) {
            // Remove <img ...>
            $detailed_desc_dec = preg_replace('/<img[^>]*>/', '', $detailed_desc_dec);
            // Remove zero-width or special spacing
            $detailed_desc_dec = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}\x{00A0}]+/u', '', $detailed_desc_dec);
            // Remove empty <p> tags
            $detailed_desc_dec = preg_replace('/<p>\s*<\/p>/', '', $detailed_desc_dec);
            // Remove excess line breaks
            $detailed_desc_dec = preg_replace('/(\r?\n){2,}/', "\n", $detailed_desc_dec);
        }

        // Now we handle saving it to content or meta
        $post_content = '';
        if ($save_description && $supports_content) {
            // Save to post_content
            if (!empty($detailed_desc_dec)) {
                $post_content = wp_kses_post($detailed_desc_dec);
                wp_update_post([
                    'ID'           => $post_id,
                    'post_content' => $post_content,
                ]);
            }
        } elseif (!empty($detailed_description_meta)) {
            // Save to a custom meta field
            update_post_meta($post_id, $detailed_description_meta, wp_kses_post($detailed_desc_dec));
        }

        // Short description
        $short_desc_raw = $game_data['short_description'] ?? '';
        $short_desc     = wp_kses_post($short_desc_raw);
        if ($save_short_description !== 'none' && !empty($short_desc)) {
            if ($save_short_description === 'content') {
                $post_content = $short_desc . "\n\n" . $post_content;
                wp_update_post([
                    'ID'           => $post_id,
                    'post_content' => $post_content,
                ]);
            } elseif ($save_short_description === 'excerpt') {
                wp_update_post([
                    'ID'           => $post_id,
                    'post_excerpt' => $short_desc,
                ]);
            }
        }

        wp_update_post([
            'ID'          => $post_id,
            'post_title'  => $post_title,
            'post_status' => $current_status,
        ]);

        /**
         * Action fired after processing core post content, short desc, etc.
         *
         * @param int    $post_id
         * @param array  $game_data
         */
        do_action('hw_steam_after_core_content', $post_id, $game_data);
    }

    /**
     * Step 3: Handle taxonomy assignments (genre, category, developer, publisher, platform).
     *
     * @param int   $post_id
     * @param array $game_data
     */
    private static function process_taxonomies($post_id, array $game_data) {
        $taxonomy_genre      = SettingsConfig::get_selected_genre_taxonomy();
        $taxonomy_category   = SettingsConfig::get_selected_category_taxonomy();
        $taxonomy_developer  = SettingsConfig::get_selected_developer_taxonomy();
        $taxonomy_publisher  = SettingsConfig::get_selected_publisher_taxonomy();
        $taxonomy_platform   = SettingsConfig::get_selected_platform_taxonomy();

        self::assign_terms_to_post($post_id, $taxonomy_genre,     $game_data['genres']     ?? [], 'description');
        self::assign_terms_to_post($post_id, $taxonomy_category,  $game_data['categories'] ?? [], 'description');
        self::assign_terms_to_post($post_id, $taxonomy_developer, $game_data['developers'] ?? []);
        self::assign_terms_to_post($post_id, $taxonomy_publisher, $game_data['publishers'] ?? []);

        if (!empty($game_data['platforms'])) {
            $platform_terms = FetchHelpers::get_platform_terms($game_data['platforms']);
            self::assign_terms_to_post($post_id, $taxonomy_platform, $platform_terms);
        }

        /**
         * Action fired after taxonomy assignments are done.
         *
         * @param int   $post_id
         * @param array $game_data
         */
        do_action('hw_steam_after_taxonomy_assignment', $post_id, $game_data);
    }

    /**
     * Step 4: Handle images (featured, capsule, gallery) and movie/trailer.
     *
     * @param int   $post_id
     * @param array $game_data
     */
    private static function process_images_and_movie($post_id, array $game_data) {
        $save_featured_image   = SettingsConfig::save_featured_image();
        $capsule_meta          = SettingsConfig::get_capsule_meta();
        $gallery_meta          = SettingsConfig::get_gallery_meta();
        $movie_meta            = SettingsConfig::get_movie_meta();

        // Featured image (header_image)
        if ($save_featured_image && !empty($game_data['header_image'])) {
            $header_id = FetchHelpers::upload_or_find_image($game_data['header_image'], $game_data['name'], 'header');
            if (!is_wp_error($header_id)) {
                set_post_thumbnail($post_id, $header_id);
            }
        }

        // Capsule image -> custom meta
        if (!empty($capsule_meta) && !empty($game_data['capsule_image'])) {
            $capsule_image_id = FetchHelpers::upload_or_find_image($game_data['capsule_image'], $game_data['name'], 'logo');
            if (!is_wp_error($capsule_image_id)) {
                $capsule_image_url = wp_get_attachment_url($capsule_image_id);
                $capsule_data = [
                    'id'  => $capsule_image_id,
                    'url' => $capsule_image_url,
                ];
                update_post_meta($post_id, $capsule_meta, $capsule_data);
            }
        }

        // Gallery images
        if (!empty($gallery_meta) && !empty($game_data['screenshots'])) {
            $gallery_images = FetchHelpers::upload_gallery_images($game_data['screenshots'], $game_data['name']);
            if (!empty($gallery_images)) {
                update_post_meta($post_id, $gallery_meta, $gallery_images);
            }
        }

        // Movies
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

        /**
         * Action fired after processing images (featured, capsule, gallery) and movie trailer.
         *
         * @param int   $post_id
         * @param array $game_data
         */
        do_action('hw_steam_after_images_and_movie', $post_id, $game_data);
    }

    /**
     * Step 5: Handle meta information like is_free, price, release date.
     *
     * @param int   $post_id
     * @param array $game_data
     */
    private static function process_meta_information($post_id, array $game_data) {
        // Release date
        $release_date_meta = SettingsConfig::get_release_date_meta();
        if (!empty($release_date_meta) && !empty($game_data['release_date']['date'])) {
            $release_date_format = SettingsConfig::get_release_date_format();
            $formatted_date = FetchHelpers::format_release_date(
                $game_data['release_date']['date'],
                $release_date_format
            );
            if ($formatted_date !== null) {
                update_post_meta($post_id, $release_date_meta, $formatted_date);
            }
        }

        // Is free
        $is_free_meta       = SettingsConfig::get_is_free_meta();
        $is_free_true_value = SettingsConfig::get_is_free_true_value();
        $is_free_false_value= SettingsConfig::get_is_free_false_value();
        if (!empty($is_free_meta) && isset($game_data['is_free'])) {
            $is_free_value = $game_data['is_free'] ? $is_free_true_value : $is_free_false_value;
            update_post_meta($post_id, $is_free_meta, sanitize_text_field($is_free_value));
        }

        // Price
        $price_meta             = SettingsConfig::get_price_meta();
        $remove_currency_symbol = SettingsConfig::remove_currency_symbol();
        if (!empty($price_meta) && !empty($game_data['price_overview']['final_formatted'])) {
            $price = $game_data['price_overview']['final_formatted'];
            if ($remove_currency_symbol) {
                // remove any currency symbol or text, leaving only digits, commas, periods
                $price = preg_replace('/[^0-9,.]/', '', $price);
            }
            update_post_meta($post_id, $price_meta, sanitize_text_field($price));
        }

        /**
         * Action fired after meta info (release date, is_free, price, etc.) has been processed.
         *
         * @param int   $post_id
         * @param array $game_data
         */
        do_action('hw_steam_after_meta_information', $post_id, $game_data);
    }

    /**
     * Assign terms to a post, creating them if necessary.
     *
     * @param int          $post_id
     * @param string       $taxonomy
     * @param array        $terms
     * @param string|null  $key If specified, the term name is taken from $term[$key].
     * @return void
     */
    private static function assign_terms_to_post($post_id, $taxonomy, $terms, $key = null) {
        if (empty($taxonomy) || empty($terms)) {
            return;
        }

        $term_names = array_map(function ($term) use ($key) {
            if ($key && isset($term[$key])) {
                return sanitize_text_field($term[$key]);
            }
            return sanitize_text_field($term);
        }, $terms);

        foreach ($term_names as $term_name) {
            if (!$term_name) {
                continue;
            }
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
