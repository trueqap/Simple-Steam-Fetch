<?php

namespace HelloWP\HWSteamMain\App\Helper;

if (!defined('ABSPATH')) {
    exit;
}

/**
 *
 * Stores and retrieves plugin settings from a single option array: 'hw_steam_game_fetch_cfg'.
 *
 */
class SettingsConfig
{
    /**
     * Holds all plugin settings once loaded from the database.
     *
     * @var array|null
     */
    private static ?array $options = null;

    /**
     * Loads the hw_steam_game_fetch_cfg option into self::$options if not already loaded.
     *
     * @return void
     */
    private static function load_options(): void
    {
        if (is_null(self::$options)) {
            self::$options = get_option('hw_steam_game_fetch_cfg', []);
        }
    }

    /**
     * Retrieves a specific setting key from the loaded options.
     *
     * @param string $key     The array key to retrieve.
     * @param mixed  $default The default value if the key is not set.
     *
     * @return mixed
     */
    private static function getOption(string $key, $default = null)
    {
        self::load_options();
        return self::$options[$key] ?? $default;
    }

    /**
     * Retrieve the selected custom post type from plugin settings.
     *
     * @return string
     */
    public static function get_selected_cpt(): string
    {
        return (string) self::getOption('selected_cpt', '');
    }

    /**
     * Retrieve the desired post status for created/updated posts.
     *
     * @return string
     */
    public static function get_post_status(): string
    {
        return (string) self::getOption('post_status', 'publish');
    }

    /**
     * Retrieve the taxonomy slug used for genres.
     *
     * @return string
     */
    public static function get_selected_genre_taxonomy(): string
    {
        return (string) self::getOption('genre_taxonomy', '');
    }

    /**
     * Retrieve the taxonomy slug used for categories.
     *
     * @return string
     */
    public static function get_selected_category_taxonomy(): string
    {
        return (string) self::getOption('category_taxonomy', '');
    }

    /**
     * Retrieve the taxonomy slug used for developers.
     *
     * @return string
     */
    public static function get_selected_developer_taxonomy(): string
    {
        return (string) self::getOption('developer_taxonomy', '');
    }

    /**
     * Retrieve the taxonomy slug used for publishers.
     *
     * @return string
     */
    public static function get_selected_publisher_taxonomy(): string
    {
        return (string) self::getOption('publisher_taxonomy', '');
    }

    /**
     * Retrieve the taxonomy slug used for platforms.
     *
     * @return string
     */
    public static function get_selected_platform_taxonomy(): string
    {
        return (string) self::getOption('platform_taxonomy', '');
    }

    /**
     * Check if the featured image should be saved when creating/updating a post.
     *
     * @return bool
     */
    public static function save_featured_image(): bool
    {
        return (bool) self::getOption('save_featured_image', false);
    }

    /**
     * Retrieve the meta key where the game capsule image (logo) will be stored.
     *
     * @return string
     */
    public static function get_capsule_meta(): string
    {
        return (string) self::getOption('capsule_meta', '');
    }

    /**
     * Retrieve the meta key for storing uploaded gallery images.
     *
     * @return string
     */
    public static function get_gallery_meta(): string
    {
        return (string) self::getOption('gallery_meta', '');
    }

    /**
     * Check if the detailed description should be saved to the post_content field.
     *
     * @return bool
     */
    public static function save_description_to_content(): bool
    {
        return (bool) self::getOption('save_description', false);
    }

    /**
     * Retrieve the meta key where the detailed description will be stored
     * (if not saving to post_content).
     *
     * @return string
     */
    public static function get_detailed_description_meta(): string
    {
        return (string) self::getOption('detailed_description_meta', '');
    }

    /**
     * Retrieve how the short description is handled ('excerpt', 'content', or 'none').
     *
     * @return string
     */
    public static function get_save_short_description(): string
    {
        return (string) self::getOption('save_short_description', 'excerpt');
    }

    /**
     * Check if inline images should be removed from the detailed description.
     *
     * @return bool
     */
    public static function disable_inline_images(): bool
    {
        return (bool) self::getOption('disable_inline_images', false);
    }

    /**
     * Retrieve the meta key used for storing movie/trailer URLs.
     *
     * @return string
     */
    public static function get_movie_meta(): string
    {
        return (string) self::getOption('movie_meta', '');
    }

    /**
     * Retrieve the meta key where the release date will be stored.
     *
     * @return string
     */
    public static function get_release_date_meta(): string
    {
        return (string) self::getOption('release_date_meta', '');
    }

    /**
     * Retrieve the format in which the release date should be stored
     * (e.g., 'string', 'unix', 'timestamp').
     *
     * @return string
     */
    public static function get_release_date_format(): string
    {
        return (string) self::getOption('release_date_format', 'string');
    }

    /**
     * Retrieve the meta key for the "is free" indicator.
     *
     * @return string
     */
    public static function get_is_free_meta(): string
    {
        return (string) self::getOption('is_free_meta', '');
    }

    /**
     * Retrieve the saved "true" value for indicating a game is free.
     *
     * @return string
     */
    public static function get_is_free_true_value(): string
    {
        return (string) self::getOption('is_free_true_value', 'yes');
    }

    /**
     * Retrieve the saved "false" value for indicating a game is not free.
     *
     * @return string
     */
    public static function get_is_free_false_value(): string
    {
        return (string) self::getOption('is_free_false_value', 'no');
    }

    /**
     * Retrieve the meta key where the price info will be stored.
     *
     * @return string
     */
    public static function get_price_meta(): string
    {
        return (string) self::getOption('price_meta', '');
    }

    /**
     * Check if the currency symbol should be removed from the price string.
     *
     * @return int Returns 1 if the symbol must be removed, or 0 otherwise.
     */
    public static function remove_currency_symbol(): int
    {
        return (int) self::getOption('remove_currency', 0);
    }
}
