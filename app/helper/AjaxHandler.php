<?php
/**
 * Ajax Handler functionality.
 *
 * Handles all AJAX related functionality for the Steam Fetch plugin.
 *
 * @package HW_Steam_Fetch
 * @since 1.0.0
 */

namespace HelloWP\HWSteamMain\App\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AjaxHandler
 *
 * Manages AJAX requests and responses.
 *
 * @package HelloWP\HWSteamMain\App\Helper
 */
class AjaxHandler {

	/**
	 * Initialize AJAX hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_ajax_hw_search_steam_games', array( __CLASS__, 'search_steam_games' ) );
	}

	/**
	 * Handle Steam games search AJAX request.
	 *
	 * Fetches and filters games from Steam API based on search query.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function search_steam_games() {
		check_ajax_referer( 'hw_steam_search_nonce', 'nonce' );

		if ( ! isset( $_POST['query'] ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Search query is missing.', 'hw-steam-fetch-games' ),
				)
			);
		}

		$query = sanitize_text_field( wp_unslash( $_POST['query'] ) );

		if ( strlen( $query ) < 3 ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Please enter at least 3 characters.', 'hw-steam-fetch-games' ),
				)
			);
		}

		// Steam API to get all apps and filter by the search query.
		$response = wp_remote_get(
			'https://api.steampowered.com/ISteamApps/GetAppList/v2/?format=json',
			array(
				'timeout'     => 15,
				'user-agent'  => 'WordPress/' . get_bloginfo( 'version' ),
			)
		);

		if ( is_wp_error( $response ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Failed to fetch data from Steam API. Try reload the page', 'hw-steam-fetch-games' ),
				)
			);
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! isset( $data['applist']['apps'] ) || ! is_array( $data['applist']['apps'] ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Invalid response from Steam API.', 'hw-steam-fetch-games' ),
				)
			);
		}

		$filtered_games = array();
		foreach ( $data['applist']['apps'] as $app ) {
			if ( stripos( $app['name'], $query ) !== false ) {
				$filtered_games[] = array(
					'id'   => absint( $app['appid'] ),
					'text' => esc_html( $app['name'] ) . ' (' . absint( $app['appid'] ) . ')',
				);

				if ( count( $filtered_games ) >= 20 ) {
					break; // Limit the results to 20.
				}
			}
		}

		wp_send_json_success( $filtered_games );
	}
}
