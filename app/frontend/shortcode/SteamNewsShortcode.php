<?php
/**
 * Steam News Shortcode functionality.
 *
 * Handles the registration and rendering of the Steam news shortcode.
 *
 * @package HW_Steam_Fetch
 * @since 1.0.0
 */

namespace HelloWP\HWSteamMain\App\Frontend\Shortcode;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SteamNewsShortcode
 *
 * Manages the Steam news shortcode functionality.
 *
 * @package HelloWP\HWSteamMain\App\Frontend\Shortcode
 */
class SteamNewsShortcode {

	/**
	 * Register the shortcode.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function register() {
		add_shortcode( 'steam_game_related_news', array( __CLASS__, 'render_shortcode' ) );
	}

	/**
	 * Render the shortcode content.
	 *
	 * @since 1.0.0
	 * @param array $atts Shortcode attributes.
	 * @return string Rendered shortcode content.
	 */
	public static function render_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'feednumber' => 3,
				'label'      => '',
			),
			$atts
		);

		$feednumber = absint( $atts['feednumber'] );
		$label      = sanitize_text_field( $atts['label'] );

		wp_enqueue_style(
			'hw-steam-news-css',
			HW_STEAM_FRONTEND_ASSETS . 'steam-news.css',
			array(),
			'1.0'
		);

		wp_enqueue_script(
			'hw-steam-news-js',
			HW_STEAM_FRONTEND_ASSETS . 'steam-news.js',
			array( 'jquery' ),
			'1.0',
			true
		);

		wp_localize_script(
			'hw-steam-news-js',
			'hwSteamNewsAjax',
			array(
				'ajaxurl'      => admin_url( 'admin-ajax.php' ),
				'post_id'      => get_the_ID(),
				'nonce'        => wp_create_nonce( 'hw_steam_news_nonce' ),
				'feednumber'   => $feednumber,
				'readMoreText' => esc_html__( 'Read more', 'hw-steam-fetch-games' ),
				'noNewsText'   => esc_html__( 'No related news found.', 'hw-steam-fetch-games' ),
				'errorText'    => esc_html__( 'Failed to load news.', 'hw-steam-fetch-games' ),
			)
		);

		ob_start();
		?>
		<div id="hw_steam_news_container">
			<?php if ( ! empty( $label ) ) : ?>
				<h3><?php echo esc_html( $label ); ?></h3>
			<?php endif; ?>
			<div class="hw_steam_news_list">
				<p class="hw_steam_loading_message">
					<?php esc_html_e( 'Loading related news...', 'hw-steam-fetch-games' ); ?>
				</p>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
