<?php

namespace HelloWP\HWSteamMain\App\Helper;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class GeneralSettingsConfig
 *
 * Manages general plugin settings (not specifically related to the fetch process),
 * all stored in a single option array: 'hw_steam_general_cfg'.
 */
class GeneralSettingsConfig
{
    /**
     * Holds all plugin general settings once loaded from the database.
     *
     * @var array|null
     */
    private static ?array $options = null;

    /**
     * Loads the hw_steam_general_cfg option into self::$options if not already loaded.
     *
     * @return void
     */
    private static function load_options(): void
    {
        if (is_null(self::$options)) {
            self::$options = get_option('hw_steam_general_cfg', []);
        }
    }

    /**
     * Retrieves a specific setting key from the loaded options.
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    private static function getOption(string $key, $default = null)
    {
        self::load_options();
        return self::$options[$key] ?? $default;
    }

    /**
     * Whether to delete imported images automatically when a post is deleted.
     *
     * @return bool True if deletion is enabled, false otherwise.
     */
    public static function delete_imported_images(): bool
    {

        return (bool) self::getOption('delete_imported_images', false);
    }

  
}
