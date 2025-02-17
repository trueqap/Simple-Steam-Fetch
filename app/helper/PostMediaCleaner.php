<?php
/**
 * Post Media Cleaner functionality.
 *
 * Handles the cleanup of media attachments when posts are deleted.
 *
 * @package HW_Steam_Fetch
 * @since 1.0.0
 */

namespace HelloWP\HWSteamMain\App\Helper;

use HelloWP\HWSteamMain\App\Helper\SettingsConfig;
use HelloWP\HWSteamMain\App\Helper\GeneralSettingsConfig;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PostMediaCleaner
 *
 * Manages the cleanup of media attachments associated with posts.
 *
 * @package HelloWP\HWSteamMain\App\Helper
 */
class PostMediaCleaner {

	/**
	 * Initialize the media cleaner functionality.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function init() {
		add_action( 'before_delete_post', array( __CLASS__, 'clean_media_on_post_delete' ) );
	}

	/**
	 * Delete attached images from the media library when the post is deleted,
	 * if the general setting is enabled.
	 *
	 * @since 1.0.0
	 * @param int $post_id The ID of the post being deleted.
	 * @return void
	 */
	public static function clean_media_on_post_delete( $post_id ) {
		if ( ! GeneralSettingsConfig::delete_imported_images() ) {
			return;
		}

		$selected_cpt = SettingsConfig::get_selected_cpt();
		$post         = get_post( $post_id );

		if ( ! $post || $selected_cpt !== $post->post_type ) {
			return;
		}

		// Search for images attached to the post.
		$attachment_ids = self::get_attached_images( $post_id );

		// Delete found images.
		if ( ! empty( $attachment_ids ) ) {
			foreach ( $attachment_ids as $attachment_id ) {
				wp_delete_attachment( $attachment_id, true );
			}
		}
	}

	/**
	 * Get IDs of images attached to the post that may originate from the Fetch system.
	 *
	 * @since 1.0.0
	 * @param int $post_id The post ID to check for attachments.
	 * @return array Array of attachment IDs.
	 */
	private static function get_attached_images( $post_id ) {
		$meta_keys = array(
			SettingsConfig::get_capsule_meta(),
			SettingsConfig::get_gallery_meta(),
			SettingsConfig::get_movie_meta(),
		);

		$attachment_ids = array();

		// Get the Featured Image ID if exists.
		$thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
		if ( $thumbnail_id ) {
			$attachment_ids[] = absint( $thumbnail_id );
		}

		foreach ( $meta_keys as $meta_key ) {
			if ( empty( $meta_key ) ) {
				continue;
			}

			$meta_value = get_post_meta( $post_id, $meta_key, true );

			if ( is_array( $meta_value ) ) {
				// Gallery or capsule image.
				foreach ( $meta_value as $maybe_value ) {
					// If array structure is ['id' => 123, 'url' => '...'].
					if ( isset( $maybe_value['id'] ) ) {
						$attachment_ids[] = absint( $maybe_value['id'] );
					} elseif ( is_array( $maybe_value ) ) {
						// If nested array.
						foreach ( $maybe_value as $sub_value ) {
							if ( isset( $sub_value['id'] ) ) {
								$attachment_ids[] = absint( $sub_value['id'] );
							}
						}
					}
				}
			} elseif ( is_numeric( $meta_value ) ) {
				// If direct ID is stored.
				$attachment_ids[] = absint( $meta_value );
			}
		}

		return array_unique( $attachment_ids );
	}
}
