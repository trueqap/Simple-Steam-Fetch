<?php
namespace HelloWP\HWSteamMain\App\Frontend\Shortcode;

if (!defined('ABSPATH')) {
    exit;
}

class SteamNewsShortcode {

    public static function register() {
        add_shortcode('steam_game_related_news', [__CLASS__, 'render_shortcode']);
    }

    public static function render_shortcode($atts) {
        $atts = shortcode_atts([
            'feednumber' => 3,
            'label'      => '', 
        ], $atts);
    
        $feednumber = intval($atts['feednumber']);
        $label = sanitize_text_field($atts['label']);
    
        wp_enqueue_style('hw-steam-news-css', HW_STEAM_FRONTEND_ASSETS . 'steam-news.css', [], '1.0');
        wp_enqueue_script('hw-steam-news-js', HW_STEAM_FRONTEND_ASSETS . 'steam-news.js', ['jquery'], '1.0', true);
    
        wp_localize_script('hw-steam-news-js', 'hwSteamNewsAjax', [
            'ajaxurl'      => admin_url('admin-ajax.php'),
            'post_id'      => get_the_ID(),
            'nonce'        => wp_create_nonce('hw_steam_news_nonce'),
            'feednumber'   => $feednumber,
            'readMoreText' => __('Read more', 'hw-steam-fetch-games'),
            'noNewsText'   => __('No related news found.', 'hw-steam-fetch-games'),
            'errorText'    => __('Failed to load news.', 'hw-steam-fetch-games'),
        ]);
    
        ob_start();
        ?>
        <div id="hw_steam_news_container">
            <?php if (!empty($label)) : ?>
                <h3><?php echo esc_html($label); ?></h3>
            <?php endif; ?>
            <div class="hw_steam_news_list">
                <p class="hw_steam_loading_message"><?php _e('Loading related news...', 'hw-steam-fetch-games'); ?></p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
