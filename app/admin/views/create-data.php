<?php
/**
 * Admin view for creating data configuration.
 *
 * @package HW_Steam_Fetch_Games
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap">
    <h2><?php esc_html_e( 'Data Configuration', 'hw-steam-fetch-games' ); ?></h2>
    <p>
        <?php
        esc_html_e(
            'Configure where and how to store and manage fetched dlc and packages data. (JetEngine compatible).',
            'hw-steam-fetch-games'
        );
        ?>
        <strong><?php esc_html_e( 'This section is under development.', 'hw-steam-fetch-games' ); ?></strong>
    </p>
</div>
