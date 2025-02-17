/**
 * Settings Configuration functionality.
 *
 * Stores and retrieves plugin settings from a single option array: 'hw_steam_game_fetch_cfg'.
 * Provides a centralized way to manage all plugin settings with proper defaults and type handling.
 *
 * @package HW_Steam_Fetch
 * @since 1.0.0
 */

namespace HelloWP\HWSteamMain\App\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SettingsConfig
 *
 * Manages the plugin's settings configuration and provides methods to access
 * individual settings with proper type casting and default values.
 *
 * @package HelloWP\HWSteamMain\App\Helper
 */
class SettingsConfig {

	/**
	 * Holds all plugin settings once loaded from the database.
	 *
	 * @since 1.0.0
	 * @var array|null
	 */
	private static $options = null;

	/**
	 * Loads the hw_steam_game_fetch_cfg option into self::$options if not already loaded.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private static function load_options() {
		if ( null === self::$options ) {
			self::$options = get_option( 'hw_steam_game_fetch_cfg', array() );
		}
	}

	/**
	 * Retrieves a specific setting key from the loaded options.
	 *
	 * @since 1.0.0
	 * @param string $key     The array key to retrieve.
	 * @param mixed  $default The default value if the key is not set.
	 * @return mixed The option value or default if not found.
	 */
	private static function get_option( $key, $default = null ) {
		self::load_options();
		return isset( self::$options[ $key ] ) ? self::$options[ $key ] : $default;
	}

	/**
	 * Retrieve the selected custom post type from plugin settings.
	 *
	 * @since 1.0.0
	 * @return string The selected custom post type or empty string if not set.
	 */
	public static function get_selected_cpt() {
		return (string) self::get_option( 'selected_cpt', '' );
	}

	/**
	 * Retrieve the desired post status for created/updated posts.
	 *
	 * @since 1.0.0
	 * @return string The post status or 'publish' if not set.
	 */
	public static function get_post_status() {
		return (string) self::get_option( 'post_status', 'publish' );
	}

	/**
	 * Retrieve the taxonomy slug used for genres.
	 *
	 * @since 1.0.0
	 * @return string The genre taxonomy slug or empty string if not set.
	 */
	public static function get_selected_genre_taxonomy() {
		return (string) self::get_option( 'genre_taxonomy', '' );
	}

	/**
	 * Retrieve the taxonomy slug used for categories.
	 *
	 * @since 1.0.0
	 * @return string The category taxonomy slug or empty string if not set.
	 */
	public static function get_selected_category_taxonomy() {
		return (string) self::get_option( 'category_taxonomy', '' );
	}

	/**
	 * Retrieve the taxonomy slug used for developers.
	 *
	 * @since 1.0.0
	 * @return string The developer taxonomy slug or empty string if not set.
	 */
	public static function get_selected_developer_taxonomy() {
		return (string) self::get_option( 'developer_taxonomy', '' );
	}

	/**
	 * Retrieve the taxonomy slug used for publishers.
	 *
	 * @since 1.0.0
	 * @return string The publisher taxonomy slug or empty string if not set.
	 */
	public static function get_selected_publisher_taxonomy() {
		return (string) self::get_option( 'publisher_taxonomy', '' );
	}

	/**
	 * Retrieve the taxonomy slug used for platforms.
	 *
	 * @since 1.0.0
	 * @return string The platform taxonomy slug or empty string if not set.
	 */
	public static function get_selected_platform_taxonomy() {
		return (string) self::get_option( 'platform_taxonomy', '' );
	}

	/**
	 * Check if the featured image should be saved when creating/updating a post.
	 *
	 * @since 1.0.0
	 * @return bool True if featured image should be saved, false otherwise.
	 */
	public static function save_featured_image() {
		return (bool) self::get_option( 'save_featured_image', false );
	}

	/**
	 * Retrieve the meta key where the game capsule image (logo) will be stored.
	 *
	 * @since 1.0.0
	 * @return string The capsule meta key or empty string if not set.
	 */
	public static function get_capsule_meta() {
		return (string) self::get_option( 'capsule_meta', '' );
	}

	/**
	 * Retrieve the meta key for storing uploaded gallery images.
	 *
	 * @since 1.0.0
	 * @return string The gallery meta key or empty string if not set.
	 */
	public static function get_gallery_meta() {
		return (string) self::get_option( 'gallery_meta', '' );
	}

	/**
	 * Check if the detailed description should be saved to the post_content field.
	 *
	 * @since 1.0.0
	 * @return bool True if description should be saved to content, false otherwise.
	 */
	public static function save_description_to_content() {
		return (bool) self::get_option( 'save_description', false );
	}

	/**
	 * Retrieve the meta key where the detailed description will be stored
	 * (if not saving to post_content).
	 *
	 * @since 1.0.0
	 * @return string The description meta key or empty string if not set.
	 */
	public static function get_detailed_description_meta() {
		return (string) self::get_option( 'detailed_description_meta', '' );
	}

	/**
	 * Retrieve how the short description is handled ('excerpt', 'content', or 'none').
	 *
	 * @since 1.0.0
	 * @return string The short description handling method or 'excerpt' if not set.
	 */
	public static function get_save_short_description() {
		return (string) self::get_option( 'save_short_description', 'excerpt' );
	}

	/**
	 * Check if inline images should be removed from the detailed description.
	 *
	 * @since 1.0.0
	 * @return bool True if inline images should be disabled, false otherwise.
	 */
	public static function disable_inline_images() {
		return (bool) self::get_option( 'disable_inline_images', false );
	}

	/**
	 * Retrieve the meta key used for storing movie/trailer URLs.
	 *
	 * @since 1.0.0
	 * @return string The movie meta key or empty string if not set.
	 */
	public static function get_movie_meta() {
		return (string) self::get_option( 'movie_meta', '' );
	}

	/**
	 * Retrieve the meta key where the release date will be stored.
	 *
	 * @since 1.0.0
	 * @return string The release date meta key or empty string if not set.
	 */
	public static function get_release_date_meta() {
		return (string) self::get_option( 'release_date_meta', '' );
	}

	/**
	 * Retrieve the format in which the release date should be stored.
	 *
	 * @since 1.0.0
	 * @return string The release date format ('string', 'unix', 'timestamp') or 'string' if not set.
	 */
	public static function get_release_date_format() {
		return (string) self::get_option( 'release_date_format', 'string' );
	}

	/**
	 * Retrieve the meta key for the "is free" indicator.
	 *
	 * @since 1.0.0
	 * @return string The is_free meta key or empty string if not set.
	 */
	public static function get_is_free_meta() {
		return (string) self::get_option( 'is_free_meta', '' );
	}

	/**
	 * Retrieve the saved "true" value for indicating a game is free.
	 *
	 * @since 1.0.0
	 * @return string The value to use when a game is free or 'yes' if not set.
	 */
	public static function get_is_free_true_value() {
		return (string) self::get_option( 'is_free_true_value', 'yes' );
	}

	/**
	 * Retrieve the saved "false" value for indicating a game is not free.
	 *
	 * @since 1.0.0
	 * @return string The value to use when a game is not free or 'no' if not set.
	 */
	public static function get_is_free_false_value() {
		return (string) self::get_option( 'is_free_false_value', 'no' );
	}

	/**
	 * Retrieve the meta key where the price info will be stored.
	 *
	 * @since 1.0.0
	 * @return string The price meta key or empty string if not set.
	 */
	public static function get_price_meta() {
		return (string) self::get_option( 'price_meta', '' );
	}

	/**
	 * Check if the currency symbol should be removed from the price string.
	 *
	 * @since 1.0.0
	 * @return int Returns 1 if the symbol must be removed, or 0 otherwise.
	 */
	public static function remove_currency_symbol() {
		return (int) self::get_option( 'remove_currency', 0 );
	}
}
