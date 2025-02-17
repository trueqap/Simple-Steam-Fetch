<?php
/**
 * Simple Steam Fetch
 *
 * Fetch and import game data from the Steam API. Automatically create custom posts
 * with detailed descriptions, screenshots, and pricing information.
 *
 * @package HW_Steam_Fetch
 * @author Soczó Kristóf
 * @copyright 2024 Soczó Kristóf
 * @license GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: Simple Steam Fetch
 * Description: Fetch and import game data from the Steam API. Automatically create custom posts with detailed descriptions, screenshots, and pricing information. Compatible with JetEngine.
 * Version: 0.2
 * Author: Soczó Kristóf
 * Author URI: https://github.com/Lonsdale201?tab=repositories
 * Plugin URI: https://github.com/Lonsdale201/Simple-Steam-Fetch
 * Text Domain: hw-steam-fetch-games
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace HelloWP\HWSteamMain;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'HW_STEAM_MAIN', true );
define( 'HW_STEAM_MAIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'HW_STEAM_MAIN_URL', plugin_dir_url( __FILE__ ) );
define( 'HW_STEAM_MAIN_ASSETS', HW_STEAM_MAIN_URL . 'app/admin/assets/' );
define( 'HW_STEAM_FRONTEND_TEMPLATES', HW_STEAM_MAIN_PATH . 'app/frontend/templates/' );
define( 'HW_STEAM_FRONTEND_ASSETS', HW_STEAM_MAIN_URL . 'app/frontend/assets/' );

require_once __DIR__ . '/vendor/autoload.php';
require_once dirname( __FILE__ ) . '/plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5p0\PucFactory;

/**
 * Main plugin class.
 *
 * Handles the initialization and core functionality of the plugin.
 *
 * @since 0.2
 */
final class HW_Steam_Main {

	/**
	 * Minimum required WordPress version.
	 *
	 * @since 0.2
	 * @var string
	 */
	const MINIMUM_WORDPRESS_VERSION = '6.0';

	/**
	 * Minimum required PHP version.
	 *
	 * @since 0.2
	 * @var string
	 */
	const MINIMUM_PHP_VERSION = '8.0';

	/**
	 * Instance of the plugin.
	 *
	 * @since 0.2
	 * @var HW_Steam_Main|null
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance of the plugin.
	 *
	 * @since 0.2
	 * @return HW_Steam_Main Instance of the plugin.
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * Sets up plugin hooks and initializes components.
	 *
	 * @since 0.2
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init_on_plugins_loaded' ) );
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_settings_link' ) );
	}

	/**
	 * Load plugin text domain.
	 *
	 * @since 0.2
	 * @return void
	 */
	public function load_plugin_textdomain() {
		if ( version_compare( $GLOBALS['wp_version'], '6.7', '<' ) ) {
			load_plugin_textdomain( 'hw-steam-fetch-games', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		} else {
			load_textdomain( 'hw-steam-fetch-games', HW_STEAM_MAIN_PATH . 'languages/hw-steam-fetch-games-' . determine_locale() . '.mo' );
		}
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @since 0.2
	 * @return void
	 */
	public function enqueue_admin_assets() {
		$screen = get_current_screen();

		if ( isset( $screen->id ) && (
			false !== strpos( $screen->id, 'hw_steam_fetch' ) ||
			false !== strpos( $screen->id, 'hw_setup' ) ||
			false !== strpos( $screen->id, 'hw_steam_settings' )
		) ) {
			wp_enqueue_style(
				'hw_steam_admin_css',
				HW_STEAM_MAIN_ASSETS . 'admin-style.css',
				array(),
				'1.0.0'
			);

			wp_enqueue_script(
				'hw_steam_admin_js',
				HW_STEAM_MAIN_ASSETS . 'admin-script.js',
				array( 'jquery' ),
				'1.0.0',
				true
			);

			// Select2 library (CDN).
			wp_enqueue_style(
				'select2_css',
				'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
				array(),
				'4.1.0'
			);

			wp_enqueue_script(
				'select2_js',
				'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
				array( 'jquery' ),
				'4.1.0',
				true
			);
		}
	}

	/**
	 * Initialize plugin components.
	 *
	 * @since 0.2
	 * @return void
	 */
	public function init_on_plugins_loaded() {
		if ( ! $this->is_compatible() ) {
			return;
		}

		if ( is_admin() ) {
			\HelloWP\HWSteamMain\App\Admin\AdminSettings::instance();
			\HelloWP\HWSteamMain\App\Services\AdminPostHandler::init();
			\HelloWP\HWSteamMain\App\Helper\AjaxHandler::init();
			\HelloWP\HWSteamMain\App\Helper\PostMediaCleaner::init();
		}

		\HelloWP\HWSteamMain\App\Frontend\Shortcode\SteamNewsShortcode::register();
		\HelloWP\HWSteamMain\App\Services\GameNews::init();

		$update_checker = PucFactory::buildUpdateChecker(
			'https://plugin-uodater.alex.hellodevs.dev/plugins/hw-steam-fetch-games.json',
			__FILE__,
			'hw-steam-fetch-games'
		);
	}

	/**
	 * Check if the environment meets the plugin requirements.
	 *
	 * @since 0.2
	 * @return bool True if compatible, false otherwise.
	 */
	public function is_compatible() {
		if ( version_compare( get_bloginfo( 'version' ), self::MINIMUM_WORDPRESS_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_wordpress_version' ) );
			return false;
		}

		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php_version' ) );
			return false;
		}

		return true;
	}

	/**
	 * Add settings links to the plugin page.
	 *
	 * @since 0.2
	 * @param array $links Existing plugin action links.
	 * @return array Modified plugin action links.
	 */
	public function add_settings_link( $links ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'admin.php?page=hw_setup' ) ),
			esc_html__( 'Setup', 'hw-steam-fetch-games' )
		);

		$report_link = sprintf(
			'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
			'https://github.com/Lonsdale201/Simple-Steam-Fetch/issues',
			esc_html__( 'Report issue, or feature request', 'hw-steam-fetch-games' )
		);

		array_unshift( $links, $settings_link, $report_link );
		return $links;
	}

	/**
	 * Display admin notice for minimum WordPress version requirement.
	 *
	 * @since 0.2
	 * @return void
	 */
	public function admin_notice_minimum_wordpress_version() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$message = sprintf(
			/* translators: %s: Required WordPress version */
			esc_html__( 'Simple Steam games plugin requires WordPress version %s or greater. Please update WordPress to use this plugin.', 'hw-steam-fetch-games' ),
			self::MINIMUM_WORDPRESS_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', $message );
	}

	/**
	 * Display admin notice for minimum PHP version requirement.
	 *
	 * @since 0.2
	 * @return void
	 */
	public function admin_notice_minimum_php_version() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$message = sprintf(
			/* translators: %s: Required PHP version */
			esc_html__( 'Simple Steam games plugin requires PHP version %s or greater. Please update PHP to use this plugin.', 'hw-steam-fetch-games' ),
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', $message );
	}
}

HW_Steam_Main::instance();
