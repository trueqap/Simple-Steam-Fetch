<?php
if (file_exists(__DIR__ . '/message-fetched.php')) {
    include_once __DIR__ . '/message-fetched.php';
}
?>

<div class="wrap">
    <h1><?php _e('Fetch Steam Games', 'hw-steam-fetch-games'); ?></h1>
    <p><?php _e('Use the form below to fetch Steam game data.', 'hw-steam-fetch-games'); ?></p>

    <form id="fetch-game-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="hw_fetch_game_data">
        
        <!-- Steam Game Search -->
        <div class="hw-form-group">
            <label for="steam-game-search"><?php _e('Search for a Steam Game', 'hw-steam-fetch-games'); ?></label>
            <select id="steam-game-search" style="width: 100%;"></select>
            <p class="description"><?php _e('Start typing the name of a game to search for it on Steam. Sometimes a page reload is required after fetching, as it might get stuck. Use the Steam App ID or URL field for manual fetching.', 'hw-steam-fetch-games'); ?></p>
        </div>
        
        <!-- App ID Input -->
        <div class="hw-form-group">
            <label for="steam-app-id"><?php _e('Steam App ID or Steam URL', 'hw-steam-fetch-games'); ?></label>
            <input type="text" id="steam-app-id" name="steam_app_id" class="regular-text" placeholder="<?php _e('Enter App ID or URL manually or it will be auto-filled from the search above.', 'hw-steam-fetch-games'); ?>">
            <p class="description"><?php _e('You can manually enter an App ID or Steam URL, or it will be filled automatically from the search above. This value is automatically saved as a programmed meta to the post: <code>_hw_steam_app_id</code>.', 'hw-steam-fetch-games'); ?></p>
        </div>

        <!-- Language Input -->
        <div class="hw-form-group">
            <label for="steam-language"><?php _e('Language Code', 'hw-steam-fetch-games'); ?></label>
            <input type="text" id="steam-language" name="steam_language" class="regular-text" value="en" placeholder="<?php _e('Enter language code (e.g., en, de, fr, hu)', 'hw-steam-fetch-games'); ?>">
            <p class="description"><?php _e('Specify the language code for fetching game data. Default is "en" (English).', 'hw-steam-fetch-games'); ?></p>
        </div>


        <button type="submit" class="button button-primary"><?php _e('Fetch Game Data', 'hw-steam-fetch-games'); ?></button>
    </form>
</div>
