<?php
/**
 * Template for displaying Steam news shortcode content.
 *
 * @package HW_Steam_Fetch
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="hw_steam_news_container">
	<h3><?php esc_html_e( 'Related News', 'hw-steam-fetch-games' ); ?></h3>
	<div class="hw_steam_news_list">
		<p class="hw_steam_loading_message">
			<?php esc_html_e( 'Loading related news...', 'hw-steam-fetch-games' ); ?>
		</p>
	</div>
</div>
