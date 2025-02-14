<?php
use HelloWP\HWSteamMain\App\Helper\Querys;

$post_types = Querys::get_public_custom_post_types();
$taxonomies = Querys::get_public_taxonomies();
$post_statuses = Querys::get_predefined_post_statuses();

$selected_cpt = get_option('hw_steam_selected_cpt', '');
$save_short_description = get_option('hw_steam_save_short_description', 'excerpt');
$save_description = (int) get_option('hw_steam_save_description', 0);
$detailed_description_meta = get_option('hw_steam_detailed_description_meta', ''); 
$selected_post_status = get_option('hw_steam_post_status', 'publish');

$selected_genre_taxonomy = get_option('hw_steam_genre_taxonomy', '');
$selected_category_taxonomy = get_option('hw_steam_category_taxonomy', '');
$selected_developer_taxonomy = get_option('hw_steam_developer_taxonomy', '');
$selected_publisher_taxonomy = get_option('hw_steam_publisher_taxonomy', '');
$selected_platform_taxonomy = get_option('hw_steam_platform_taxonomy', '');

$save_featured_image = (int) get_option('hw_steam_save_featured_image', 0);
$capsule_meta = get_option('hw_steam_capsule_meta', '');
$gallery_meta = get_option('hw_steam_gallery_meta', '');
$disable_inline_images = (int) get_option('hw_steam_disable_inline_images', 0);
$movie_meta = get_option('hw_steam_movie_meta', '');

$release_date_meta = get_option('hw_steam_release_date_meta', '');
$release_date_format = get_option('hw_steam_release_date_format', 'string');
$is_free_meta = get_option('hw_steam_is_free_meta', '');
$is_free_true_value = get_option('hw_steam_is_free_true_value', 'yes');
$is_free_false_value = get_option('hw_steam_is_free_false_value', 'no');
$price_meta = get_option('hw_steam_price_meta', '');
$remove_currency = (int) get_option('hw_steam_remove_currency', 0);



?>

<div class="wrap">
    <h2><?php _e('Steam game fetch settings', 'hw-steam-fetch-games'); ?></h2>
    <p><?php _e('Configure where and how to store and manage fetched game data. (JetEngine compatible)', 'hw-steam-fetch-games'); ?></p>
    
    <form method="post" action="options.php">
        <?php settings_fields('hw_steam_settings_group'); ?>

        <div class="metabox-holder has-columns">
            <!-- Core Settings -->
            <div class="postbox">
                <h3 class="hndle"><?php _e('Core Settings', 'hw-steam-fetch-games'); ?></h3>
                <p class="description"><?php _e('Here you can configure the essential fetch settings.', 'hw-steam-fetch-games'); ?></p>
                <div class="inside">
                    <!-- CPT Selection -->
                    <div class="hw-form-group">
                        <label for="hw_steam_selected_cpt"><?php _e('Select Custom Post Type', 'hw-steam-fetch-games'); ?></label>
                        <select id="hw_steam_selected_cpt" name="hw_steam_selected_cpt" class="select2-field" style="width: 100%;">
                            <option value=""><?php _e('Select a custom post type', 'hw-steam-fetch-games'); ?></option>
                            <?php foreach ($post_types as $post_type): ?>
                                <option value="<?php echo esc_attr($post_type['slug']); ?>" <?php selected($selected_cpt, $post_type['slug']); ?>>
                                    <?php echo esc_html($post_type['label']) . ' (' . esc_html($post_type['slug']) . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php _e('Here you can set which post type the system will use to import the games.', 'hw-steam-fetch-games'); ?></p>
                    </div>
                    <!-- Post Status Selection -->
                    <div class="hw-form-group">
                        <label for="hw_steam_post_status"><?php _e('Select Post Status', 'hw-steam-fetch-games'); ?></label>
                        <select id="hw_steam_post_status" name="hw_steam_post_status" class="select2-field" style="width: 100%;">
                            <?php foreach ($post_statuses as $status): ?>
                                <option value="<?php echo esc_attr($status['slug']); ?>" <?php selected($selected_post_status, $status['slug']); ?>>
                                    <?php echo esc_html($status['label']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php _e('Here you can set the status for the created post. (In case of re-fetch, it will not overwrite but keep the current status.)', 'hw-steam-fetch-games'); ?></p>
                    </div>

                    
                  <!-- Game Descriptions Fieldset -->
        <fieldset style="border: 1px solid #ddd; padding: 10px; margin-top: 20px;">
            <legend><strong><?php _e('Game Descriptions', 'hw-steam-fetch-games'); ?></strong></legend>
            <p class="description"><?php _e('Configure how game descriptions should be saved.', 'hw-steam-fetch-games'); ?></p>
            
            <!-- Save Detailed Description to Content -->
            <div class="hw-form-group">
                <label for="hw_steam_save_description">
                    <input type="checkbox" id="hw_steam_save_description" name="hw_steam_save_description" value="1" <?php checked($save_description, 1); ?>>
                    <?php _e('Save Detailed Description to Content', 'hw-steam-fetch-games'); ?>
                </label>
                <p class="description"><?php _e('Check this option to save the detailed description to the post content.', 'hw-steam-fetch-games'); ?></p>
            </div>

            <!-- Detailed Description Meta ID -->
            <div class="hw-form-group">
                <label for="hw_steam_detailed_description_meta"><?php _e('Detailed Description Meta ID', 'hw-steam-fetch-games'); ?></label>
                <input type="text" id="hw_steam_detailed_description_meta" name="hw_steam_detailed_description_meta" value="<?php echo esc_attr($detailed_description_meta); ?>" class="regular-text">
                <p class="description"><?php _e('Enter the meta ID where the detailed description should be saved.', 'hw-steam-fetch-games'); ?></p>
            </div>

            <!-- Save Short Description Radio Options -->
            <div class="hw-form-group hw-radio-group">
                <label><?php _e('Save Short Description', 'hw-steam-fetch-games'); ?></label>
                <p class="description"><?php _e('Choose where to save the short description.', 'hw-steam-fetch-games'); ?></p>
                <label>
                    <input type="radio" name="hw_steam_save_short_description" value="content" <?php checked($save_short_description, 'content'); ?>>
                    <?php _e('Save to Content', 'hw-steam-fetch-games'); ?>
                </label>
                <label>
                    <input type="radio" name="hw_steam_save_short_description" value="excerpt" <?php checked($save_short_description, 'excerpt'); ?>>
                    <?php _e('Save to Excerpt', 'hw-steam-fetch-games'); ?>
                </label>
                <label>
                    <input type="radio" name="hw_steam_save_short_description" value="none" <?php checked($save_short_description, 'none'); ?>>
                    <?php _e('No Import', 'hw-steam-fetch-games'); ?>
                </label>
            </div>
        </fieldset>

     
                
                </div>
            </div>


            <!-- Taxonomy Settings -->
            <div class="postbox">
                <h3 class="hndle"><?php _e('Taxonomy Settings', 'hw-steam-fetch-games'); ?></h3>
                <p class="description"><?php _e('Configure genre, category, developer, and publisher taxonomies.', 'hw-steam-fetch-games'); ?></p>
                <div class="inside">
                    <!-- Genre Taxonomy Selection -->
                    <div class="hw-form-group">
                        <label for="hw_steam_genre_taxonomy"><?php _e('Select Genre Taxonomy', 'hw-steam-fetch-games'); ?></label>
                        <select id="hw_steam_genre_taxonomy" name="hw_steam_genre_taxonomy" class="select2-field" style="width: 100%;">
                            <option value=""><?php _e('Select a taxonomy', 'hw-steam-fetch-games'); ?></option>
                            <?php foreach ($taxonomies as $taxonomy): ?>
                                <option value="<?php echo esc_attr($taxonomy['slug']); ?>" <?php selected($selected_genre_taxonomy, $taxonomy['slug']); ?>>
                                    <?php echo esc_html($taxonomy['label']) . ' (' . esc_html($taxonomy['slug']) . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Category Taxonomy Selection -->
                    <div class="hw-form-group">
                        <label for="hw_steam_category_taxonomy"><?php _e('Select Category Taxonomy', 'hw-steam-fetch-games'); ?></label>
                        <select id="hw_steam_category_taxonomy" name="hw_steam_category_taxonomy" class="select2-field" style="width: 100%;">
                            <option value=""><?php _e('Select a taxonomy for categories', 'hw-steam-fetch-games'); ?></option>
                            <?php foreach ($taxonomies as $taxonomy): ?>
                                <option value="<?php echo esc_attr($taxonomy['slug']); ?>" <?php selected($selected_category_taxonomy, $taxonomy['slug']); ?>>
                                    <?php echo esc_html($taxonomy['label']) . ' (' . esc_html($taxonomy['slug']) . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Developers Taxonomy Selection -->
                    <div class="hw-form-group">
                        <label for="hw_steam_developer_taxonomy"><?php _e('Select Developer Taxonomy', 'hw-steam-fetch-games'); ?></label>
                        <select id="hw_steam_developer_taxonomy" name="hw_steam_developer_taxonomy" class="select2-field" style="width: 100%;">
                            <option value=""><?php _e('Select a taxonomy for developers', 'hw-steam-fetch-games'); ?></option>
                            <?php foreach ($taxonomies as $taxonomy): ?>
                                <option value="<?php echo esc_attr($taxonomy['slug']); ?>" <?php selected($selected_developer_taxonomy, $taxonomy['slug']); ?>>
                                    <?php echo esc_html($taxonomy['label']) . ' (' . esc_html($taxonomy['slug']) . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Publishers Taxonomy Selection -->
                    <div class="hw-form-group">
                        <label for="hw_steam_publisher_taxonomy"><?php _e('Select Publisher Taxonomy', 'hw-steam-fetch-games'); ?></label>
                        <select id="hw_steam_publisher_taxonomy" name="hw_steam_publisher_taxonomy" class="select2-field" style="width: 100%;">
                            <option value=""><?php _e('Select a taxonomy for publishers', 'hw-steam-fetch-games'); ?></option>
                            <?php foreach ($taxonomies as $taxonomy): ?>
                                <option value="<?php echo esc_attr($taxonomy['slug']); ?>" <?php selected($selected_publisher_taxonomy, $taxonomy['slug']); ?>>
                                    <?php echo esc_html($taxonomy['label']) . ' (' . esc_html($taxonomy['slug']) . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Platform Taxonomy Selection -->
                    <div class="hw-form-group">
                        <label for="hw_steam_platform_taxonomy"><?php _e('Select Platform Taxonomy', 'hw-steam-fetch-games'); ?></label>
                        <select id="hw_steam_platform_taxonomy" name="hw_steam_platform_taxonomy" class="select2-field" style="width: 100%;">
                            <option value=""><?php _e('Select a taxonomy for platforms', 'hw-steam-fetch-games'); ?></option>
                            <?php foreach ($taxonomies as $taxonomy): ?>
                                <option value="<?php echo esc_attr($taxonomy['slug']); ?>" <?php selected($selected_platform_taxonomy, $taxonomy['slug']); ?>>
                                    <?php echo esc_html($taxonomy['label']) . ' (' . esc_html($taxonomy['slug']) . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php _e('In this taxonomy, you must pre-create exactly 3 terms with the following slugs: windows, mac, linux . <small>Steam returns a true/false value for these in every language. The label can be different, but the slug must match exactly.</small>', 'hw-steam-fetch-games'); ?></p>
                    </div>
                </div>
            </div>

           <!-- Image & Video Settings -->
            <div class="postbox">
                <h3 class="hndle"><?php _e('Image & Video Settings', 'hw-steam-fetch-games'); ?></h3>
                <p class="description"><?php _e('Configure image-related settings.', 'hw-steam-fetch-games'); ?></p>
                <div class="inside">
                    <div class="hw-form-group">
                        <label for="hw_steam_save_featured_image">
                            <input type="checkbox" id="hw_steam_save_featured_image" name="hw_steam_save_featured_image" value="1" <?php checked($save_featured_image, 1); ?>>
                            <?php _e('Header Image as Featured Image', 'hw-steam-fetch-games'); ?>
                        </label>
                    </div>
                    <div class="hw-form-group">
                        <label for="hw_steam_disable_inline_images">
                            <input type="checkbox" id="hw_steam_disable_inline_images" name="hw_steam_disable_inline_images" value="1" <?php checked($disable_inline_images, 1); ?>>
                            <?php _e('Disable Inline Images in Description', 'hw-steam-fetch-games'); ?>
                        </label>
                        <p class="description"><?php _e('In the game description, developers often include multiple images. If you prefer, you can remove these images. If you leave them, they will be embedded from an external source and will not be added to your media library in any way.
<small>If you skip this, the content section (Gutenberg) will have a lot of empty spaces (whitespace)—make sure to remove them manually later.</small>', 'hw-steam-fetch-games'); ?></p>
                    </div>
                    <div class="hw-form-group">
                        <label for="hw_steam_capsule_meta"><?php _e('Capsule Image Meta ID', 'hw-steam-fetch-games'); ?></label>
                        <input type="text" id="hw_steam_capsule_meta" name="hw_steam_capsule_meta" value="<?php echo esc_attr($capsule_meta); ?>" class="regular-text">
                        <p class="description">
                            <?php _e('Provide the JetEngine-generated media meta ID. This will retrieve the capsule image, which essentially serves as the logo.', 'hw-steam-fetch-games'); ?>
                            <small><?php _e('Set the value format to: Array with media ID and URL.', 'hw-steam-fetch-games'); ?></small>
                        </p>
                    </div>
                    <div class="hw-form-group">
                        <label for="hw_steam_movie_meta"><?php _e('Movie Meta ID', 'hw-steam-fetch-games'); ?></label>
                        <input type="text" id="hw_steam_movie_meta" name="hw_steam_movie_meta" value="<?php echo esc_attr($movie_meta); ?>" class="regular-text">
                        <p class="description"><?php _e('Enter the meta key for storing the movie URL.', 'hw-steam-fetch-games'); ?></p>
                    </div>
                    <div class="hw-form-group">
                        <label for="hw_steam_gallery_meta"><?php _e('Game Gallery Meta ID', 'hw-steam-fetch-games'); ?></label>
                        <input type="text" id="hw_steam_gallery_meta" name="hw_steam_gallery_meta" value="<?php echo esc_attr($gallery_meta); ?>" class="regular-text">
                        <p class="description">
                            <?php _e('Provide the JetEngine-generated gallery meta ID. This will retrieve the screenshots images.', 'hw-steam-fetch-games'); ?>
                            <small><?php _e('Set the value format to: <strong>Array with media ID and URL</strong>.', 'hw-steam-fetch-games'); ?></small>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Meta Information Settings -->
            <div class="postbox">
                <h3 class="hndle"><?php _e('Meta Information Settings', 'hw-steam-fetch-games'); ?></h3>
                <p class="description"><?php _e('Configure meta information-related settings.', 'hw-steam-fetch-games'); ?></p>
                <div class="inside">
                    <!-- Release Date Settings -->
                    <fieldset style="border: 1px solid #ddd; padding: 10px; margin-top: 20px;">
                        <legend><strong><?php _e('Release Date Settings', 'hw-steam-fetch-games'); ?></strong></legend>
                        <p class="description"><?php _e('Configure how the release date should be saved.', 'hw-steam-fetch-games'); ?></p>
                        
                        <!-- Release Date Meta ID -->
                        <div class="hw-form-group">
                            <label for="hw_steam_release_date_meta"><?php _e('Release Date Meta ID', 'hw-steam-fetch-games'); ?></label>
                            <input type="text" id="hw_steam_release_date_meta" name="hw_steam_release_date_meta" value="<?php echo esc_attr($release_date_meta); ?>" class="regular-text">
                            <p class="description"><?php _e('Enter the meta key for storing the release date.', 'hw-steam-fetch-games'); ?></p>
                        </div>
                        
                        <!-- Save Format Radio Buttons -->
                        <div class="hw-form-group hw-radio-group">
                            <label><?php _e('Save Format', 'hw-steam-fetch-games'); ?></label>
                            <p class="description"><?php _e('Choose the format for saving the release date. <strong>Timestamp and Unix only work with english(en) request. Both option work with JetEngine Date field</strong> ', 'hw-steam-fetch-games'); ?></p>
                            <label>
                                <input type="radio" name="hw_steam_release_date_format" value="string" <?php checked($release_date_format, 'string'); ?>>
                                <?php _e('String', 'hw-steam-fetch-games'); ?>
                            </label>
                            <label>
                                <input type="radio" name="hw_steam_release_date_format" value="unix" <?php checked($release_date_format, 'unix'); ?>>
                                <?php _e('Unix', 'hw-steam-fetch-games'); ?>
                            </label>
                            <label>
                                <input type="radio" name="hw_steam_release_date_format" value="timestamp" <?php checked($release_date_format, 'timestamp'); ?>>
                                <?php _e('Timestamp', 'hw-steam-fetch-games'); ?>
                            </label>
                        </div>
                    </fieldset>

                    <!-- Is Free Meta Settings -->
                    <fieldset style="border: 1px solid #ddd; padding: 10px; margin-top: 20px;">
                        <legend><strong><?php _e('Game Price settings', 'hw-steam-fetch-games'); ?></strong></legend>
                        <p class="description"><?php _e('Configure the meta key and values to store whether the game is free or not and the price, how much it costs.', 'hw-steam-fetch-games'); ?></p>
                        
                        <!-- Is Free Meta ID -->
                        <div class="hw-form-group">
                            <label for="hw_steam_is_free_meta"><?php _e('Is Free Meta ID', 'hw-steam-fetch-games'); ?></label>
                            <input type="text" id="hw_steam_is_free_meta" name="hw_steam_is_free_meta" value="<?php echo esc_attr($is_free_meta); ?>" class="regular-text">
                            <p class="description"><?php _e('Enter the meta key for storing the "is free" status.', 'hw-steam-fetch-games'); ?></p>
                        </div>
                        
                        <!-- Value for True -->
                        <div class="hw-form-group">
                            <label for="hw_steam_is_free_true_value"><?php _e('Value for "Free" (True)', 'hw-steam-fetch-games'); ?></label>
                            <input type="text" id="hw_steam_is_free_true_value" name="hw_steam_is_free_true_value" value="<?php echo esc_attr($is_free_true_value); ?>" class="regular-text">
                            <p class="description"><?php _e('Enter the value to store when the game is free (e.g., "yes").', 'hw-steam-fetch-games'); ?></p>
                        </div>
                        
                        <!-- Value for False -->
                        <div class="hw-form-group">
                            <label for="hw_steam_is_free_false_value"><?php _e('Value for "Not Free" (False)', 'hw-steam-fetch-games'); ?></label>
                            <input type="text" id="hw_steam_is_free_false_value" name="hw_steam_is_free_false_value" value="<?php echo esc_attr($is_free_false_value); ?>" class="regular-text">
                            <p class="description"><?php _e('Enter the value to store when the game is not free (e.g., "no").', 'hw-steam-fetch-games'); ?></p>
                        </div>
                        <!-- Price Meta ID -->
                        <div class="hw-form-group">
                            <label for="hw_steam_price_meta"><?php _e('Price Meta ID', 'hw-steam-fetch-games'); ?></label>
                            <input type="text" id="hw_steam_price_meta" name="hw_steam_price_meta" value="<?php echo esc_attr($price_meta); ?>" class="regular-text">
                            <p class="description"><?php _e('Enter the meta key for storing the game price.', 'hw-steam-fetch-games'); ?></p>
                        </div>
                        
                        <!-- Remove Currency Symbol -->
                        <div class="hw-form-group">
                            <label for="hw_steam_remove_currency">
                                <input type="checkbox" id="hw_steam_remove_currency" name="hw_steam_remove_currency" value="1" <?php checked($remove_currency, 1); ?>>
                                <?php _e('Remove Currency Symbol', 'hw-steam-fetch-games'); ?>
                            </label>
                            <p class="description"><?php _e('Check this box if you want to store only the numeric value of the price without the currency symbol.', 'hw-steam-fetch-games'); ?></p>
                        </div>
                    </fieldset>
                </div>
            </div>


        </div>
        
        <?php submit_button(); ?>
    </form>
</div>
