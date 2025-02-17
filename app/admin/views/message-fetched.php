<?php
/**
 * Display success or error messages after Steam game data fetch.
 *
 * This template handles the display of success or error messages after attempting
 * to fetch and save Steam game data. It shows different messages based on whether
 * the post was created or updated.
 *
 * @package HW_Steam_Fetch
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check URL parameters.
$post_id = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : null;
$success = isset( $_GET['success'] ) && 'true' === $_GET['success'];
$error   = isset( $_GET['error'] ) ? sanitize_text_field( wp_unslash( $_GET['error'] ) ) : null;

if ( $success && $post_id ) {
	$post      = get_post( $post_id );
	$edit_link = get_edit_post_link( $post_id );
	$view_link = get_permalink( $post_id );
	
	if ( $post ) {
		?>
		<div class="notice notice-success is-dismissible">
			<p>
				<?php if ( $post->post_date_gmt === $post->post_modified_gmt ) : ?>
					<strong><?php esc_html_e( 'Successfully created the following post:', 'hw-steam-fetch-games' ); ?></strong>
				<?php else : ?>
					<strong><?php esc_html_e( 'Successfully updated the following post:', 'hw-steam-fetch-games' ); ?></strong>
				<?php endif; ?>
				<br>
				<a href="<?php echo esc_url( $edit_link ); ?>" target="_blank"><?php echo esc_html( $post->post_title ); ?></a> 
				<span> | </span>
				<a href="<?php echo esc_url( $view_link ); ?>" target="_blank"><?php esc_html_e( 'View on frontend', 'hw-steam-fetch-games' ); ?></a>
			</p>
		</div>
		<?php
	}
} elseif ( $error ) {
	?>
	<div class="notice notice-error is-dismissible">
		<p><?php esc_html_e( 'Unfortunately, we could not create the post. Please try again.', 'hw-steam-fetch-games' ); ?></p>
	</div>
	<?php
}
?>
