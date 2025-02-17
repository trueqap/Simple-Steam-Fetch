<?php
/**
 * Fetch Helpers functionality.
 *
 * Helper functions for fetching and processing Steam game data.
 *
 * @package HW_Steam_Fetch
 * @since 1.0.0
 */

namespace HelloWP\HWSteamMain\App\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class FetchHelpers
 *
 * Provides helper methods for fetching and processing Steam game data.
 *
 * @package HelloWP\HWSteamMain\App\Helper
 */
class FetchHelpers {

	/**
	 * Check if an attachment already exists in the media library by its file name.
	 *
	 * @since 1.0.0
	 * @param string $filename The file name to check.
	 * @return int|null The attachment ID if found, otherwise null.
	 */
	public static function find_attachment_by_title( $filename ) {
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta}
			WHERE meta_key = '_wp_attached_file'
			AND meta_value LIKE %s
			LIMIT 1",
			'%' . $wpdb->esc_like( $filename ) . '%'
		);

		$attachment_id = $wpdb->get_var( $query );

		return $attachment_id ? absint( $attachment_id ) : null;
	}

	/**
	 * Upload an image to the media library or return its existing attachment ID if it already exists.
	 *
	 * @since 1.0.0
	 * @param string $image_url  The URL of the image.
	 * @param string $game_name  The name of the game (used for naming the image).
	 * @param string $image_type The type of the image (default: 'header').
	 * @return int|\WP_Error The attachment ID or WP_Error on failure.
	 */
	public static function upload_or_find_image( $image_url, $game_name, $image_type = 'header' ) {
		$sanitized_game_name = sanitize_title_with_dashes( $game_name );
		$filename           = "{$sanitized_game_name}-{$image_type}";

		$existing_id = self::find_attachment_by_title( $filename );
		if ( $existing_id ) {
			return $existing_id;
		}

		$image_url = str_replace( 'http://', 'https://', $image_url );

		$temp_file = download_url( $image_url );
		if ( is_wp_error( $temp_file ) ) {
			return $temp_file;
		}

		if ( filesize( $temp_file ) < 500 ) {
			unlink( $temp_file );
			return new \WP_Error( 'small_file', esc_html__( 'File is too small or empty.', 'hw-steam-fetch-games' ) );
		}

		$file = array(
			'name'     => "{$filename}.jpg",
			'type'     => mime_content_type( $temp_file ),
			'tmp_name' => $temp_file,
			'error'    => 0,
			'size'     => filesize( $temp_file ),
		);

		$overrides = array( 'test_form' => false );
		$file_id   = media_handle_sideload( $file, 0, "{$game_name} {$image_type}" );

		unlink( $temp_file );

		return $file_id;
	}

	/**
	 * Upload multiple images to the media library and return their attachment IDs and URLs.
	 *
	 * @since 1.0.0
	 * @param array  $screenshots List of screenshots.
	 * @param string $game_name   The name of the game.
	 * @return array List of uploaded images with their IDs and URLs.
	 */
	public static function upload_gallery_images( $screenshots, $game_name ) {
		if ( empty( $screenshots ) || ! is_array( $screenshots ) ) {
			return array();
		}

		$gallery_images = array();
		foreach ( $screenshots as $index => $screenshot ) {
			$image_url = $screenshot['path_full'];
			$image_id  = self::upload_or_find_image( $image_url, $game_name, "galleryimg{$index}" );

			if ( ! is_wp_error( $image_id ) ) {
				$gallery_images[] = array(
					'id'  => $image_id,
					'url' => wp_get_attachment_url( $image_id ),
				);
			}
		}

		return $gallery_images;
	}

	/**
	 * Fetch game data from the Steam API.
	 *
	 * @since 1.0.0
	 * @param string $app_id   The Steam App ID.
	 * @param string $language The language for the API request (default: 'en').
	 * @return array The response with success status and data.
	 */
	public static function fetch_game_data_from_steam( $app_id, $language = 'en' ) {
		$url = add_query_arg(
			array(
				'appids' => $app_id,
				'l'      => $language,
			),
			'https://store.steampowered.com/api/appdetails'
		);

		$args = array(
			'headers'    => array(
				'Accept-Language' => $language,
			),
			'timeout'    => 15,
			'user-agent' => 'WordPress/' . get_bloginfo( 'version' ),
		);

		$response = wp_remote_get( $url, $args );

		if ( is_wp_error( $response ) ) {
			return array(
				'success' => false,
				'message' => esc_html__( 'Failed to fetch data from Steam API.', 'hw-steam-fetch-games' ),
			);
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! $data[ $app_id ]['success'] ) {
			$fallback_url = add_query_arg(
				array(
					'appids' => $app_id,
					'l'      => 'en',
				),
				'https://store.steampowered.com/api/appdetails'
			);

			$response = wp_remote_get( $fallback_url, $args );
			if ( is_wp_error( $response ) ) {
				return array(
					'success' => false,
					'message' => esc_html__( 'Fallback to English failed.', 'hw-steam-fetch-games' ),
				);
			}

			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body, true );
		}

		if ( ! $data[ $app_id ]['success'] ) {
			return array(
				'success' => false,
				'message' => esc_html__( 'Invalid App ID or data not found.', 'hw-steam-fetch-games' ),
			);
		}

		return array(
			'success' => true,
			'data'    => $data[ $app_id ]['data'],
		);
	}

	/**
	 * Convert platform data from the Steam API to term names.
	 *
	 * @since 1.0.0
	 * @param array $platforms List of available platforms from the Steam API.
	 * @return array List of term names.
	 */
	public static function get_platform_terms( $platforms ) {
		$available_platforms = array(
			'windows' => 'Windows',
			'mac'     => 'Mac',
			'linux'   => 'Linux',
		);

		$terms = array();
		foreach ( $available_platforms as $key => $term_name ) {
			if ( ! empty( $platforms[ $key ] ) && true === $platforms[ $key ] ) {
				$terms[] = sanitize_text_field( $term_name );
			}
		}

		return $terms;
	}

	/**
	 * Format the release date based on the given format.
	 *
	 * @since 1.0.0
	 * @param string $date     The release date in 'j M, Y' format.
	 * @param string $format   The desired output format ('string', 'unix', 'timestamp').
	 * @param string $language The language for the date (default: 'en').
	 * @return mixed The formatted date or null if the date is invalid.
	 */
	public static function format_release_date( $date, $format = 'string', $language = 'en' ) {
		if ( empty( $date ) ) {
			return null;
		}

		$datetime = \DateTime::createFromFormat( 'j M, Y', $date );

		if ( ! $datetime ) {
			return null;
		}

		switch ( $format ) {
			case 'unix':
				return $datetime->format( 'Y-m-d' );
			case 'timestamp':
				return $datetime->getTimestamp();
			case 'string':
			default:
				return $date;
		}
	}
}
