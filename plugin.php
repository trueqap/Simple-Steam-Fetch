<?php
/**
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

define('HW_Steam_Main', true);
define('HW_STEAM_MAIN_PATH', plugin_dir_path(__FILE__)); 
define('HW_STEAM_MAIN_URL', plugin_dir_url(__FILE__));  
define('HW_STEAM_MAIN_ASSETS', HW_STEAM_MAIN_URL . 'app/admin/assets/'); 
define('HW_STEAM_FRONTEND_TEMPLATES', HW_STEAM_MAIN_PATH . 'app/frontend/templates/');
define('HW_STEAM_FRONTEND_ASSETS', HW_STEAM_MAIN_URL . 'app/frontend/assets/');


require_once __DIR__ . '/vendor/autoload.php';
require dirname(__FILE__) . '/plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5p0\PucFactory;

final class HW_Steam_Main {

	const MINIMUM_WORDPRESS_VERSION = '6.0';
	const MINIMUM_PHP_VERSION = '8.0';

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
        add_action('plugins_loaded', [$this, 'init_on_plugins_loaded']);
        add_action('init', [$this, 'load_plugin_textdomain']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'add_settings_link']);
    }
    
    public function load_plugin_textdomain() {
        if ( version_compare( $GLOBALS['wp_version'], '6.7', '<' ) ) {
            load_plugin_textdomain( 'hw-steam-fetch-games', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        } else {
            load_textdomain( 'hw-steam-fetch-games', HW_STEAM_MAIN_PATH . 'languages/hw-steam-fetch-games-' . determine_locale() . '.mo' );
        }
    }

    public function enqueue_admin_assets() {
        $screen = get_current_screen();

        if (isset($screen->id) && (strpos($screen->id, 'hw_steam_fetch') !== false || strpos($screen->id, 'hw_setup') !== false || strpos($screen->id, 'hw_steam_settings') !== false)) {
            wp_enqueue_style(
                'hw_steam_admin_css',
                HW_STEAM_MAIN_ASSETS . 'admin-style.css',
                [],
                '1.0.0',
                'all'
            );
    
            wp_enqueue_script(
                'hw_steam_admin_js',
                HW_STEAM_MAIN_ASSETS . 'admin-script.js',
                ['jquery'],
                '1.0.0',
                true
            );
    
            // Select2 lib  (CDN)
            wp_enqueue_style(
                'select2_css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                [],
                '4.1.0'
            );
    
            wp_enqueue_script(
                'select2_js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                ['jquery'],
                '4.1.0',
                true
            );
        }
    }
    
    public function init_on_plugins_loaded() {

        if ( ! $this->is_compatible() ) {
            return;
        }

        if (is_admin()) {
            \HelloWP\HWSteamMain\App\Admin\AdminSettings::instance();
            \HelloWP\HWSteamMain\App\Services\AdminPostHandler::init();
            \HelloWP\HWSteamMain\App\Helper\AjaxHandler::init();
            \HelloWP\HWSteamMain\App\Helper\PostMediaCleaner::init();
        }

        \HelloWP\HWSteamMain\App\Frontend\Shortcode\SteamNewsShortcode::register();
        \HelloWP\HWSteamMain\App\Services\GameNews::init();

        $myUpdateChecker = PucFactory::buildUpdateChecker(
            'https://plugin-uodater.alex.hellodevs.dev/plugins/hw-steam-fetch-games.json',
            __FILE__,
            'hw-steam-fetch-games'
        );

    }
     
	public function is_compatible() {
		if ( version_compare( get_bloginfo( 'version' ), self::MINIMUM_WORDPRESS_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_wordpress_version' ] );
			return false;
		}

		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return false;
		}

		return true;
	}

    
    public function add_settings_link($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=hw_setup') . '">' . __('Setup', 'hw-steam-fetch-games') . '</a>';
        $report_link = '<a href="https://github.com/Lonsdale201/Simple-Steam-Fetch/issues" target="_blank" rel="noopener noreferrer">' . __('Report issue, or feature request', 'hw-steam-fetch-games') . '</a>';
        
        array_unshift($links, $settings_link, $report_link);
        return $links;
    }
    
   
    public function admin_notice_minimum_wordpress_version() {
        if (!current_user_can('manage_options')) {
            return;
        }
        echo '<div class="notice notice-warning is-dismissible"><p>';
        echo sprintf(__('Simple Steam games plugin requires WordPress version %s or greater. Please update WordPress to use this plugin.', 'hw-steam-fetch-games'), self::MINIMUM_WORDPRESS_VERSION);
        echo '</p></div>';
    }
    
    public function admin_notice_minimum_php_version() {
        if (!current_user_can('manage_options')) {
            return;
        }
        echo '<div class="notice notice-warning is-dismissible"><p>';
        echo sprintf(__('Simple Steam games plugin requires PHP version %s or greater. Please update PHP to use this plugin.', 'hw-steam-fetch-games'), self::MINIMUM_PHP_VERSION);
        echo '</p></div>';
    }

}

HW_Steam_Main::instance();
