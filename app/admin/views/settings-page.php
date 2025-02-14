<?php
// Kiolvassuk a jelenlegi "general" beállításokat (hw_steam_general_cfg)
$general_options = get_option('hw_steam_general_cfg', []);
$delete_imported_images = !empty($general_options['delete_imported_images']) ? 1 : 0;
?>

<div class="wrap">
    <h1><?php _e('Plugin General Settings', 'hw-steam-fetch-games'); ?></h1>
    
    <form method="post" action="options.php">
        <?php 
            // Ez a csoport: 'hw_steam_general_settings_group'
            settings_fields('hw_steam_general_settings_group');
        ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <?php _e('Delete imported images if post deleted', 'hw-steam-fetch-games'); ?>
                </th>
                <td>
                    <input type="checkbox"
                           id="delete_imported_images"
                           name="hw_steam_general_cfg[delete_imported_images]"
                           value="1"
                           <?php checked($delete_imported_images, 1); ?>>
                    <p class="description">
                        <?php _e('Check this option to automatically delete images imported from Steam when the associated post is deleted.', 'hw-steam-fetch-games'); ?>
                    </p>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>
