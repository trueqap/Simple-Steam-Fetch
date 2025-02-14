<?php

namespace HelloWP\HWSteamMain\App\Helper;

if (!defined('ABSPATH')) {
    exit;
}

class Querys {

    /**
     * Get all public custom post types with their labels and slugs.
     *
     * @return array List of public custom post types.
     */
    public static function get_public_custom_post_types() {
        $post_types = get_post_types(['public' => true], 'objects');
        $result = [];

        foreach ($post_types as $post_type) {
            $result[] = [
                'label' => $post_type->labels->singular_name,
                'slug'  => $post_type->name
            ];
        }

        return $result;
    }

    /**
     * Get all public taxonomies with their labels and slugs.
     *
     * @return array List of public taxonomies.
     */
    public static function get_public_taxonomies() {
        $taxonomies = get_taxonomies(['public' => true], 'objects');
        $result = [];

        foreach ($taxonomies as $taxonomy) {
            $result[] = [
                'label' => $taxonomy->labels->singular_name,
                'slug'  => $taxonomy->name
            ];
        }

        return $result;
    }

    /**
     * Get predefined post statuses for selection.
     *
     * @return array List of post statuses with labels.
     */
    public static function get_predefined_post_statuses() {
        return [
            ['label' => __('Published', 'hw-steam-fetch-games'), 'slug' => 'publish'],
            ['label' => __('Draft', 'hw-steam-fetch-games'), 'slug' => 'draft'],
            ['label' => __('Pending Review', 'hw-steam-fetch-games'), 'slug' => 'pending'],
        ];
    }

}
