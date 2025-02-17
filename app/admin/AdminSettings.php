<?php
/**
 * Admin settings management class.
 *
 * This class handles all admin-related settings, menus, and options
 * for the Steam Fetch plugin.
 *
 * @package HW_Steam_Fetch
 * @since 1.0.0
 */

namespace HelloWP\HWSteamMain\App\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AdminSettings
 *
 * Handles the admin settings interface and functionality.
 *
 * @package HelloWP\HWSteamMain\App\Admin
 */
class AdminSettings {

	/**
	 * Instance of the class.
	 *
	 * @var AdminSettings|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @since 1.0.0
	 * @return AdminSettings Instance of the class.
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
	 * Add menu and register settings hooks.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Add main menu and submenus.
	 *
	 * @since 1.0.0
	 */
	public function add_admin_menu() {
		// Main menu.
		add_menu_page(
			esc_html__( 'Game Fetch', 'hw-steam-fetch-games' ),
			esc_html__( 'Game Fetch', 'hw-steam-fetch-games' ),
			'manage_options',
			'hw_steam_fetch',
			array( $this, 'render_fetch_games_page' ),
			'dashicons-download',
			60
		);

		// Submenu: Setup.
		add_submenu_page(
			'hw_steam_fetch',
			esc_html__( 'Setup', 'hw-steam-fetch-games' ),
			esc_html__( 'Setup', 'hw-steam-fetch-games' ),
			'manage_options',
			'hw_setup',
			array( $this, 'render_setup_page' )
		);

		// Submenu: Settings.
		add_submenu_page(
			'hw_steam_fetch',
			esc_html__( 'Settings', 'hw-steam-fetch-games' ),
			esc_html__( 'Settings', 'hw-steam-fetch-games' ),
			'manage_options',
			'hw_steam_settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register both "fetch" settings and "general" settings.
	 *
	 * Each uses a different group name, so you can manage them in separate forms
	 * or the same form.
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {
		// 1) Fetch settings group + single array option.
		register_setting(
			'hw_steam_settings_group',
			'hw_steam_game_fetch_cfg',
			array(
				'sanitize_callback' => array( $this, 'sanitize_main_settings' ),
			)
		);

		// 2) General settings group + single array option.
		register_setting(
			'hw_steam_general_settings_group',
			'hw_steam_general_cfg',
			array(
				'sanitize_callback' => array( $this, 'sanitize_general_settings' ),
			)
		);
	}

	/**
	 * Sanitize callback for the fetch-related settings array.
	 *
	 * @since 1.0.0
	 * @param array $input Raw input array.
	 * @return array Sanitized output array.
	 */
	public function sanitize_main_settings( $input ) {
		$output = array();

		// Basic text fields.
		$text_fields = array(
			'selected_cpt',
			'detailed_description_meta',
			'genre_taxonomy',
			'category_taxonomy',
			'developer_taxonomy',
			'publisher_taxonomy',
			'platform_taxonomy',
			'capsule_meta',
			'movie_meta',
			'gallery_meta',
			'release_date_meta',
			'is_free_meta',
			'is_free_true_value',
			'is_free_false_value',
			'price_meta',
		);

		foreach ( $text_fields as $field ) {
			$output[ $field ] = isset( $input[ $field ] )
				? sanitize_text_field( $input[ $field ] )
				: '';
		}

		// Post status.
		$output['post_status'] = isset( $input['post_status'] )
			? sanitize_text_field( $input['post_status'] )
			: 'publish';

		// Checkboxes.
		$checkboxes = array(
			'save_description',
			'save_featured_image',
			'disable_inline_images',
			'remove_currency',
		);

		foreach ( $checkboxes as $checkbox ) {
			$output[ $checkbox ] = ! empty( $input[ $checkbox ] ) ? 1 : 0;
		}

		// Short description radio.
		$allowed_short_desc_values = array( 'content', 'excerpt', 'none' );
		$output['save_short_description'] = isset( $input['save_short_description'] ) &&
			in_array( $input['save_short_description'], $allowed_short_desc_values, true )
			? $input['save_short_description']
			: 'excerpt';

		// Release date format.
		$allowed_date_formats = array( 'string', 'unix', 'timestamp' );
		$output['release_date_format'] = isset( $input['release_date_format'] ) &&
			in_array( $input['release_date_format'], $allowed_date_formats, true )
			? $input['release_date_format']
			: 'string';

		return $output;
	}

	/**
	 * Sanitize callback for the general plugin settings.
	 *
	 * @since 1.0.0
	 * @param array $input Raw input array.
	 * @return array Sanitized output array.
	 */
	public function sanitize_general_settings( $input ) {
		$output = array();
		$output['delete_imported_images'] = ! empty( $input['delete_imported_images'] ) ? 1 : 0;
		return $output;
	}

	/**
	 * Render Fetch Games page content.
	 *
	 * @since 1.0.0
	 */
	public function render_fetch_games_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'hw-steam-fetch-games' ) );
		}
		include_once __DIR__ . '/views/fetch-games.php';
	}

	/**
	 * Render Setup page content.
	 *
	 * @since 1.0.0
	 */
	public function render_setup_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'hw-steam-fetch-games' ) );
		}

		$current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'existing_data';
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Setup Steam Plugin', 'hw-steam-fetch-games' ); ?></h1>
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'hw_setup', 'tab' => 'existing_data' ), admin_url( 'admin.php' ) ) ); ?>" 
					class="nav-tab <?php echo esc_attr( 'existing_data' === $current_tab ? 'nav-tab-active' : '' ); ?>">
					<?php esc_html_e( 'Games Fetch mapping', 'hw-steam-fetch-games' ); ?>
				</a>
				<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'hw_setup', 'tab' => 'create_data' ), admin_url( 'admin.php' ) ) ); ?>" 
					class="nav-tab <?php echo esc_attr( 'create_data' === $current_tab ? 'nav-tab-active' : '' ); ?>">
					<?php esc_html_e( 'DLC and packages Fetch mapping', 'hw-steam-fetch-games' ); ?>
				</a>
			</h2>
			<?php
			if ( 'existing_data' === $current_tab ) {
				include_once __DIR__ . '/views/existing-data.php';
			} elseif ( 'create_data' === $current_tab ) {
				include_once __DIR__ . '/views/create-data.php';
			}
			?>
		</div>
		<?php
	}

	/**
	 * Render Settings page content.
	 *
	 * @since 1.0.0
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'hw-steam-fetch-games' ) );
		}
		include_once __DIR__ . '/views/settings-page.php';
	}
}
