<?php
$delete_imported_images = (int) get_option('hw_delete_imported_images', 0);
?>

<div class="wrap">
    <h1><?php _e('Plugin General Settings', 'hw-steam-fetch-games'); ?></h1>
    <form method="post" action="options.php">
        <?php settings_fields('hw_steam_general_settings_group'); ?>
        <?php do_settings_sections('hw_steam_general_settings_group'); ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Delete imported images if post deleted', 'hw-steam-fetch-games'); ?></th>
                <td>
                    <input type="checkbox" id="hw_delete_imported_images" name="hw_delete_imported_images" value="1" <?php checked($delete_imported_images, 1); ?>>
                    <p class="description"><?php _e('Check this option to automatically delete images imported from Steam when the associated post is deleted.', 'hw-steam-fetch-games'); ?></p>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>
