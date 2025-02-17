<?php
/**
 * Admin Post Handler functionality.
 *
 * Handles the processing of admin-post.php actions for the Steam Fetch plugin,
 * specifically managing the game data fetching process.
 *
 * @package HW_Steam_Fetch
 * @since 1.0.0
 */

namespace HelloWP\HWSteamMain\App\Services;

use HelloWP\HWSteamMain\App\Services\ProcessFetch;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AdminPostHandler
 *
 * Manages the processing of admin-post.php actions, including security checks
 * and data validation before processing game fetches.
 *
 * @package HelloWP\HWSteamMain\App\Services
 */
class AdminPostHandler {

	/**
	 * Initialize the admin post handler.
	 *
	 * Hooks into WordPress admin_post actions to handle game data fetching.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_post_hw_fetch_game_data', array( __CLASS__, 'handle_fetch_game_data' ) );
	}

	/**
	 * Handle the game data fetch request.
	 *
	 * Processes the POST request for fetching game data, including validation
	 * and security checks before proceeding with the fetch operation.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function handle_fetch_game_data() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'hw-steam-fetch-games' ) );
		}

		check_admin_referer( 'hw_fetch_game_data_action', 'hw_fetch_game_data_nonce' );

		// Validate steam_app_id.
		if ( ! isset( $_POST['steam_app_id'] ) || empty( $_POST['steam_app_id'] ) ) {
			wp_safe_redirect(
				add_query_arg(
					array(
						'error' => 'missing_app_id',
					),
					wp_get_referer()
				)
			);
			exit;
		}

		$app_id = sanitize_text_field( wp_unslash( $_POST['steam_app_id'] ) );
		$app_id = self::extract_app_id( $app_id );

		$language = isset( $_POST['steam_language'] )
			? sanitize_text_field( wp_unslash( $_POST['steam_language'] ) )
			: 'en';

		// Check if $app_id is numeric.
		if ( empty( $app_id ) || ! ctype_digit( $app_id ) ) {
			wp_safe_redirect(
				add_query_arg(
					array(
						'error' => 'invalid_app_id',
					),
					wp_get_referer()
				)
			);
			exit;
		}

		// Call our main fetch.
		$result = ProcessFetch::fetch_and_process_game( $app_id, $language );

		// Redirect based on success/fail.
		if ( $result['success'] ) {
			wp_safe_redirect(
				add_query_arg(
					array(
						'success' => 'true',
						'post_id' => absint( $result['post_id'] ),
					),
					wp_get_referer()
				)
			);
		} else {
			wp_safe_redirect(
				add_query_arg(
					array(
						'error' => 'fetch_failed',
					),
					wp_get_referer()
				)
			);
		}
		exit;
	}

	/**
	 * Extract the App ID from the Steam URL or return the original input if it is not a URL.
	 *
	 * @since 1.0.0
	 * @param string $input The input string that might be a Steam URL or App ID.
	 * @return string The extracted App ID or cleaned input string.
	 */
	private static function extract_app_id( $input ) {
		// Attempt to parse store.steampowered.com/app/<digits>.
		if ( preg_match( '/store\.steampowered\.com\/app\/(\d+)/', $input, $matches ) ) {
			return $matches[1];
		}

		// Otherwise keep only digits.
		return preg_replace( '/\D/', '', $input );
	}
}
