<?php
/**
 * Display the Steam game fetch form in the admin area.
 *
 * This template handles the display of the form used to fetch Steam game data,
 * including game search functionality and manual input options.
 *
 * @package HW_Steam_Fetch
 * @since 1.0.0
 */

// Include message template if it exists.
if ( file_exists( __DIR__ . '/message-fetched.php' ) ) {
	include_once __DIR__ . '/message-fetched.php';
}
?>

<div class="wrap">
	<h1><?php esc_html_e( 'Fetch Steam Games', 'hw-steam-fetch-games' ); ?></h1>
	<p><?php esc_html_e( 'Use the form below to fetch Steam game data.', 'hw-steam-fetch-games' ); ?></p>

	<form id="fetch-game-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<?php wp_nonce_field( 'hw_fetch_game_data_action', 'hw_fetch_game_data_nonce' ); ?>

		<input type="hidden" name="action" value="hw_fetch_game_data">
		
		<!-- Steam Game Search -->
		<div class="hw-form-group">
			<label for="steam-game-search"><?php esc_html_e( 'Search for a Steam Game', 'hw-steam-fetch-games' ); ?></label>
			<select id="steam-game-search" style="width: 100%;"></select>
			<p class="description">
				<?php
				esc_html_e(
					'Start typing the name of a game to search for it on Steam. Sometimes a page reload is required after fetching, as it might get stuck. Use the Steam App ID or URL field for manual fetching.',
					'hw-steam-fetch-games'
				);
				?>
			</p>
		</div>
		
		<!-- App ID Input -->
		<div class="hw-form-group">
			<label for="steam-app-id"><?php esc_html_e( 'Steam App ID or Steam URL', 'hw-steam-fetch-games' ); ?></label>
			<input 
				type="text" 
				id="steam-app-id" 
				name="steam_app_id" 
				class="regular-text" 
				placeholder="<?php esc_attr_e( 'Enter App ID or URL manually or it will be auto-filled from the search above.', 'hw-steam-fetch-games' ); ?>"
			>
			<p class="description">
				<?php
				printf(
					/* translators: %s: Meta key name */
					esc_html__( 'You can manually enter an App ID or Steam URL, or it will be filled automatically from the search above. This value is automatically saved as a programmed meta to the post: %s', 'hw-steam-fetch-games' ),
					'<code>_hw_steam_app_id</code>'
				);
				?>
			</p>
		</div>

		<!-- Language Input -->
		<div class="hw-form-group">
			<label for="steam-language"><?php esc_html_e( 'Language Code', 'hw-steam-fetch-games' ); ?></label>
			<input 
				type="text" 
				id="steam-language" 
				name="steam_language" 
				class="regular-text" 
				value="en" 
				placeholder="<?php esc_attr_e( 'Enter language code (e.g., en, de, fr, hu)', 'hw-steam-fetch-games' ); ?>"
			>
			<p class="description">
				<?php esc_html_e( 'Specify the language code for fetching game data. Default is "en" (English).', 'hw-steam-fetch-games' ); ?>
			</p>
		</div>

		<button type="submit" class="button button-primary">
			<?php esc_html_e( 'Fetch Game Data', 'hw-steam-fetch-games' ); ?>
		</button>
	</form>
</div>
