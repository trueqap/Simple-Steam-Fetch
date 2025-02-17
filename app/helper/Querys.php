<?php
/**
 * Query helper functionality.
 *
 * Provides helper methods for retrieving WordPress post types, taxonomies,
 * and post statuses in a standardized format.
 *
 * @package HW_Steam_Fetch
 * @since 1.0.0
 */

namespace HelloWP\HWSteamMain\App\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Queries
 *
 * Helper class for retrieving WordPress data in a standardized format.
 * Provides methods for getting post types, taxonomies, and post statuses.
 *
 * @package HelloWP\HWSteamMain\App\Helper
 */
class Queries {

	/**
	 * Get all public custom post types with their labels and slugs.
	 *
	 * Retrieves a list of public post types and formats them into a consistent
	 * array structure with labels and slugs.
	 *
	 * @since 1.0.0
	 * @return array {
	 *     Array of post type information.
	 *     @type array {
	 *         @type string $label The singular name of the post type.
	 *         @type string $slug  The registered name of the post type.
	 *     }
	 * }
	 */
	public static function get_public_custom_post_types() {
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		$result     = array();

		foreach ( $post_types as $post_type ) {
			$result[] = array(
				'label' => $post_type->labels->singular_name,
				'slug'  => $post_type->name,
			);
		}

		return $result;
	}

	/**
	 * Get all public taxonomies with their labels and slugs.
	 *
	 * Retrieves a list of public taxonomies and formats them into a consistent
	 * array structure with labels and slugs.
	 *
	 * @since 1.0.0
	 * @return array {
	 *     Array of taxonomy information.
	 *     @type array {
	 *         @type string $label The singular name of the taxonomy.
	 *         @type string $slug  The registered name of the taxonomy.
	 *     }
	 * }
	 */
	public static function get_public_taxonomies() {
		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
		$result     = array();

		foreach ( $taxonomies as $taxonomy ) {
			$result[] = array(
				'label' => $taxonomy->labels->singular_name,
				'slug'  => $taxonomy->name,
			);
		}

		return $result;
	}

	/**
	 * Get predefined post statuses for selection.
	 *
	 * Returns a list of commonly used post statuses with their translated labels
	 * for use in selection interfaces.
	 *
	 * @since 1.0.0
	 * @return array {
	 *     Array of post status information.
	 *     @type array {
	 *         @type string $label The translated label of the post status.
	 *         @type string $slug  The status key (publish, draft, pending).
	 *     }
	 * }
	 */
	public static function get_predefined_post_statuses() {
		return array(
			array(
				'label' => esc_html__( 'Published', 'hw-steam-fetch-games' ),
				'slug'  => 'publish',
			),
			array(
				'label' => esc_html__( 'Draft', 'hw-steam-fetch-games' ),
				'slug'  => 'draft',
			),
			array(
				'label' => esc_html__( 'Pending Review', 'hw-steam-fetch-games' ),
				'slug'  => 'pending',
			),
		);
	}
}
