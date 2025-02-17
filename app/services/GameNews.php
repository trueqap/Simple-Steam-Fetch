<?php
/**
 * Game News functionality.
 *
 * Handles the fetching and processing of Steam game news through AJAX requests.
 * Provides both authenticated and non-authenticated endpoints for news retrieval.
 *
 * @package HW_Steam_Fetch
 * @since 1.0.0
 */

namespace HelloWP\HWSteamMain\App\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class GameNews
 *
 * Manages the retrieval and processing of Steam game news through AJAX endpoints.
 * Supports both logged-in and non-logged-in users.
 *
 * @package HelloWP\HWSteamMain\App\Services
 */
class GameNews {

	/**
	 * Initialize the game news functionality.
	 *
	 * Sets up AJAX hooks for both authenticated and non-authenticated users
	 * to fetch game news data.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_ajax_hw_fetch_game_news', array( __CLASS__, 'fetch_news' ) );
		add_action( 'wp_ajax_nopriv_hw_fetch_game_news', array( __CLASS__, 'fetch_news' ) );
	}

	/**
	 * Fetch and return news items for a specific game.
	 *
	 * Retrieves news items from the Steam API for a specific game,
	 * validates the request, and returns the formatted news data.
	 *
	 * @since 1.0.0
	 * @return void Sends JSON response and exits.
	 */
	public static function fetch_news() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'hw_steam_news_nonce' ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Invalid nonce.', 'hw-steam-fetch-games' ),
				)
			);
		}

		if ( empty( $_POST['post_id'] ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Post ID not provided.', 'hw-steam-fetch-games' ),
				)
			);
		}

		$post_id    = absint( $_POST['post_id'] );
		$feednumber = isset( $_POST['feednumber'] ) ? absint( $_POST['feednumber'] ) : 3;

		$app_id = get_post_meta( $post_id, '_hw_steam_app_id', true );
		$app_id = absint( $app_id );

		if ( empty( $app_id ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'App ID not found for this post.', 'hw-steam-fetch-games' ),
				)
			);
		}

		$api_url = add_query_arg(
			array(
				'appid'     => $app_id,
				'count'     => $feednumber,
				'maxlength' => 550,
				'format'    => 'json',
			),
			'https://api.steampowered.com/ISteamNews/GetNewsForApp/v2/'
		);

		$response = wp_remote_get(
			$api_url,
			array(
				'timeout'     => 15,
				'user-agent'  => 'WordPress/' . get_bloginfo( 'version' ),
			)
		);

		if ( is_wp_error( $response ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Failed to fetch news from Steam.', 'hw-steam-fetch-games' ),
				)
			);
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $data['appnews']['newsitems'] ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'No news found for this game.', 'hw-steam-fetch-games' ),
				)
			);
		}

		wp_send_json_success( $data['appnews']['newsitems'] );
	}
}
