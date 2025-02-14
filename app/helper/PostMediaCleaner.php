<?php
namespace HelloWP\HWSteamMain\App\Helper;

use HelloWP\HWSteamMain\App\Helper\SettingsConfig;
use HelloWP\HWSteamMain\App\Helper\GeneralSettingsConfig;

if (!defined('ABSPATH')) {
    exit;
}

class PostMediaCleaner {
    
    public static function init() {
        add_action('before_delete_post', [__CLASS__, 'clean_media_on_post_delete']);
    }

    /**
     * Delete attached images from the media library when the post is deleted,
     * if the general setting is enabled.
     *
     * @param int $post_id
     */
    public static function clean_media_on_post_delete($post_id) {
        // Instead of a direct get_option, we call our GeneralSettingsConfig:
        if (! GeneralSettingsConfig::delete_imported_images()) {
            return;
        }

        $selected_cpt = SettingsConfig::get_selected_cpt();  // ez maradhat, hiszen ez a fetch CFG-ből jön
        $post         = get_post($post_id);

        if (!$post || $post->post_type !== $selected_cpt) {
            return;
        }

        // Keresés az adott poszthoz kapcsolódó képekre
        $attachment_ids = self::get_attached_images($post_id);

        // Ha találunk képeket, töröljük őket
        if (!empty($attachment_ids)) {
            foreach ($attachment_ids as $attachment_id) {
                wp_delete_attachment($attachment_id, true);
            }
        }
    }

    /**
     * Visszaadja az adott poszthoz kapcsolódó képek ID-jait, amelyek a Fetch rendszerből származhatnak.
     *
     * @param int $post_id
     * @return array
     */
    private static function get_attached_images($post_id): array
    {
        // Eltávolítottad a global $wpdb; ha nem szükséges, ne tartsd ott

        $meta_keys = [
            SettingsConfig::get_capsule_meta(),
            SettingsConfig::get_gallery_meta(),
            SettingsConfig::get_movie_meta(),
        ];
    
        $attachment_ids = [];
    
        // Töröljük a Featured Image-t, ha van
        $thumbnail_id = get_post_meta($post_id, '_thumbnail_id', true);
        if ($thumbnail_id) {
            $attachment_ids[] = (int) $thumbnail_id;
        }
    
        foreach ($meta_keys as $meta_key) {
            if (empty($meta_key)) {
                continue;
            }
    
            $meta_value = get_post_meta($post_id, $meta_key, true);
    
            if (is_array($meta_value)) {
                // Galéria vagy capsule image
                foreach ($meta_value as $maybe_key => $maybe_value) {
                    // Ha pl. ['id' => 123, 'url' => '...']
                    if (isset($maybe_value['id'])) {
                        $attachment_ids[] = (int) $maybe_value['id'];
                    }
                    // Ha esetleg többszintes tömb
                    elseif (is_array($maybe_value)) {
                        foreach ($maybe_value as $sub_value) {
                            if (isset($sub_value['id'])) {
                                $attachment_ids[] = (int) $sub_value['id'];
                            }
                        }
                    }
                }
            } elseif (is_numeric($meta_value)) {
                // Ha esetleg közvetlen ID került ide
                $attachment_ids[] = (int) $meta_value;
            }
        }
    
        return array_unique($attachment_ids);
    }
}
