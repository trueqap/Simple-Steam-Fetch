<?php
/**
 * General Settings Configuration functionality.
 *
 * Manages general plugin settings that are not specifically related to the fetch process.
 * All settings are stored in a single option array: 'hw_steam_general_cfg'.
 *
 * @package HW_Steam_Fetch
 * @since 1.0.0
 */

namespace HelloWP\HWSteamMain\App\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class GeneralSettingsConfig
 *
 * Manages general plugin settings (not specifically related to the fetch process),
 * all stored in a single option array: 'hw_steam_general_cfg'.
 *
 * @package HelloWP\HWSteamMain\App\Helper
 */
class GeneralSettingsConfig {

	/**
	 * Holds all plugin general settings once loaded from the database.
	 *
	 * @since 1.0.0
	 * @var array|null
	 */
	private static $options = null;

	/**
	 * Loads the hw_steam_general_cfg option into self::$options if not already loaded.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private static function load_options() {
		if ( null === self::$options ) {
			self::$options = get_option( 'hw_steam_general_cfg', array() );
		}
	}

	/**
	 * Retrieves a specific setting key from the loaded options.
	 *
	 * @since 1.0.0
	 * @param string $key     The option key to retrieve.
	 * @param mixed  $default The default value if the key doesn't exist.
	 * @return mixed The option value or default if not found.
	 */
	private static function get_option( $key, $default = null ) {
		self::load_options();
		return isset( self::$options[ $key ] ) ? self::$options[ $key ] : $default;
	}

	/**
	 * Whether to delete imported images automatically when a post is deleted.
	 *
	 * @since 1.0.0
	 * @return bool True if deletion is enabled, false otherwise.
	 */
	public static function delete_imported_images() {
		return (bool) self::get_option( 'delete_imported_images', false );
	}
}
