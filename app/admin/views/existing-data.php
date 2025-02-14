<?php
use HelloWP\HWSteamMain\App\Helper\Querys;

if (!defined('ABSPATH')) {
    exit;
}

$post_types   = Querys::get_public_custom_post_types();
$taxonomies   = Querys::get_public_taxonomies();
$post_statuses = Querys::get_predefined_post_statuses();

/**
 * We load the fetch settings contained in the 'hw_steam_game_fetch_cfg' option array.
 */
$options = get_option('hw_steam_game_fetch_cfg', []);


$selected_cpt               = $options['selected_cpt']               ?? '';
$selected_post_status       = $options['post_status']                ?? 'publish';
$save_description           = (int)($options['save_description']     ?? 0);
$detailed_description_meta  = $options['detailed_description_meta']  ?? '';
$save_short_description     = $options['save_short_description']     ?? 'excerpt';

$selected_genre_taxonomy    = $options['genre_taxonomy']             ?? '';
$selected_category_taxonomy = $options['category_taxonomy']          ?? '';
$selected_developer_taxonomy= $options['developer_taxonomy']         ?? '';
$selected_publisher_taxonomy= $options['publisher_taxonomy']         ?? '';
$selected_platform_taxonomy = $options['platform_taxonomy']          ?? '';

$save_featured_image        = (int)($options['save_featured_image']  ?? 0);
$capsule_meta               = $options['capsule_meta']               ?? '';
$gallery_meta               = $options['gallery_meta']               ?? '';
$disable_inline_images      = (int)($options['disable_inline_images'] ?? 0);
$movie_meta                 = $options['movie_meta']                 ?? '';

$release_date_meta          = $options['release_date_meta']          ?? '';
$release_date_format        = $options['release_date_format']        ?? 'string';
$is_free_meta               = $options['is_free_meta']               ?? '';
$is_free_true_value         = $options['is_free_true_value']         ?? 'yes';
$is_free_false_value        = $options['is_free_false_value']        ?? 'no';
$price_meta                 = $options['price_meta']                 ?? '';
$remove_currency            = (int)($options['remove_currency']      ?? 0);

?>

<div class="wrap">
    <h1><?php _e('Existing Data Settings', 'hw-steAM-fetch-games'); ?></h1>
    <p><?php _e('Configure how existing data or further details are handled or mapped.', 'hw-steAM-fetch-games'); ?></p>

    <form method="post" action="options.php">
        <?php 
            settings_fields('hw_steam_settings_group'); 
        ?>

        <div class="metabox-holder has-columns">
            
            <!-- Core (CPT / Post Status / Descriptions) -->
            <div class="postbox">
                <h2 class="hndle"><?php _e('Core Settings', 'hw-steAM-fetch-games'); ?></h2>
                <div class="inside">

                    <!-- Post Type -->
                    <div class="hw-form-group">
                        <label for="selected_cpt"><?php _e('Select Custom Post Type', 'hw-steAM-fetch-games'); ?></label>
                        <select id="selected_cpt" 
                                name="hw_steam_game_fetch_cfg[selected_cpt]" 
                                class="select2-field" 
                                style="width: 100%;">
                            <option value="">
                                <?php _e('Select a custom post type', 'hw-steAM-fetch-games'); ?>
                            </option>
                            <?php foreach ($post_types as $pt): ?>
                                <option value="<?php echo esc_attr($pt['slug']); ?>"
                                    <?php selected($selected_cpt, $pt['slug']); ?>>
                                    <?php 
                                        echo esc_html($pt['label']) . ' (' . esc_html($pt['slug']) . ')';
                                    ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">
                            <?php _e('Which post type to import games into.', 'hw-steAM-fetch-games'); ?>
                        </p>
                    </div>

                    <!-- Post Status -->
                    <div class="hw-form-group">
                        <label for="post_status"><?php _e('Select Post Status', 'hw-steAM-fetch-games'); ?></label>
                        <select id="post_status" 
                                name="hw_steam_game_fetch_cfg[post_status]" 
                                class="select2-field" 
                                style="width: 100%;">
                            <?php foreach ($post_statuses as $status): ?>
                                <option value="<?php echo esc_attr($status['slug']); ?>"
                                    <?php selected($selected_post_status, $status['slug']); ?>>
                                    <?php 
                                        echo esc_html($status['label']); 
                                    ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">
                            <?php _e('Status for the created post. On re-fetch, it won’t be overwritten.', 'hw-steAM-fetch-games'); ?>
                        </p>
                    </div>

                    <!-- Detailed Description -->
                    <fieldset style="border:1px solid #ddd; padding:10px; margin-top:20px;">
                        <legend>
                            <strong><?php _e('Game Descriptions', 'hw-steAM-fetch-games'); ?></strong>
                        </legend>
                        <div class="hw-form-group">
                            <label for="save_description">
                                <input type="checkbox"
                                       id="save_description"
                                       name="hw_steam_game_fetch_cfg[save_description]"
                                       value="1"
                                       <?php checked($save_description, 1); ?>>
                                <?php _e('Save Detailed Description to Content', 'hw-steAM-fetch-games'); ?>
                            </label>
                        </div>
                        <div class="hw-form-group">
                            <label for="detailed_description_meta">
                                <?php _e('Detailed Description Meta ID', 'hw-steAM-fetch-games'); ?>
                            </label>
                            <input type="text"
                                   id="detailed_description_meta"
                                   name="hw_steam_game_fetch_cfg[detailed_description_meta]"
                                   value="<?php echo esc_attr($detailed_description_meta); ?>"
                                   class="regular-text">
                                   <p class="description">
                                <?php _e('You can also save the data to a custom meta if you want (note that HTML will also be included).', 'hw-steAM-fetch-games'); ?>
                                </p>
                        </div>

                        <!-- Save Short Description (radio) -->
                        <div class="hw-form-group hw-radio-group">
                            <label><?php _e('Save Short Description', 'hw-steAM-fetch-games'); ?></label>
                            <p class="description">
                                <?php _e('Where to save the short description.', 'hw-steAM-fetch-games'); ?>
                            </p>
                            <label>
                                <input type="radio"
                                       name="hw_steam_game_fetch_cfg[save_short_description]"
                                       value="content"
                                       <?php checked($save_short_description, 'content'); ?>>
                                <?php _e('Save to Content', 'hw-steAM-fetch-games'); ?>
                            </label>
                            <label>
                                <input type="radio"
                                       name="hw_steam_game_fetch_cfg[save_short_description]"
                                       value="excerpt"
                                       <?php checked($save_short_description, 'excerpt'); ?>>
                                <?php _e('Save to Excerpt', 'hw-steAM-fetch-games'); ?>
                            </label>
                            <label>
                                <input type="radio"
                                       name="hw_steam_game_fetch_cfg[save_short_description]"
                                       value="none"
                                       <?php checked($save_short_description, 'none'); ?>>
                                <?php _e('No Import', 'hw-steAM-fetch-games'); ?>
                            </label>
                        </div>
                    </fieldset>
                </div>
            </div>

            <!-- Taxonomy Settings -->
            <div class="postbox">
                <h2 class="hndle"><?php _e('Taxonomy Settings', 'hw-steAM-fetch-games'); ?></h2>
                <div class="inside">

                    <!-- Genre Taxonomy -->
                    <div class="hw-form-group">
                        <label for="genre_taxonomy">
                            <?php _e('Select Genre Taxonomy', 'hw-steAM-fetch-games'); ?>
                        </label>
                        <select id="genre_taxonomy"
                                name="hw_steam_game_fetch_cfg[genre_taxonomy]"
                                class="select2-field"
                                style="width: 100%;">
                            <option value="">
                                <?php _e('Select a taxonomy', 'hw-steAM-fetch-games'); ?>
                            </option>
                            <?php foreach ($taxonomies as $tax): ?>
                                <option value="<?php echo esc_attr($tax['slug']); ?>"
                                    <?php selected($selected_genre_taxonomy, $tax['slug']); ?>>
                                    <?php 
                                        echo esc_html($tax['label']) . ' (' . esc_html($tax['slug']) . ')';
                                    ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Category Taxonomy -->
                    <div class="hw-form-group">
                        <label for="category_taxonomy">
                            <?php _e('Select Category Taxonomy', 'hw-steAM-fetch-games'); ?>
                        </label>
                        <select id="category_taxonomy"
                                name="hw_steam_game_fetch_cfg[category_taxonomy]"
                                class="select2-field"
                                style="width: 100%;">
                            <option value="">
                                <?php _e('Select a taxonomy for categories', 'hw-steAM-fetch-games'); ?>
                            </option>
                            <?php foreach ($taxonomies as $tax): ?>
                                <option value="<?php echo esc_attr($tax['slug']); ?>"
                                    <?php selected($selected_category_taxonomy, $tax['slug']); ?>>
                                    <?php 
                                        echo esc_html($tax['label']) . ' (' . esc_html($tax['slug']) . ')';
                                    ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Developer Taxonomy -->
                    <div class="hw-form-group">
                        <label for="developer_taxonomy">
                            <?php _e('Select Developer Taxonomy', 'hw-steAM-fetch-games'); ?>
                        </label>
                        <select id="developer_taxonomy"
                                name="hw_steam_game_fetch_cfg[developer_taxonomy]"
                                class="select2-field"
                                style="width: 100%;">
                            <option value="">
                                <?php _e('Select a taxonomy for developers', 'hw-steAM-fetch-games'); ?>
                            </option>
                            <?php foreach ($taxonomies as $tax): ?>
                                <option value="<?php echo esc_attr($tax['slug']); ?>"
                                    <?php selected($selected_developer_taxonomy, $tax['slug']); ?>>
                                    <?php 
                                        echo esc_html($tax['label']) . ' (' . esc_html($tax['slug']) . ')';
                                    ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Publisher Taxonomy -->
                    <div class="hw-form-group">
                        <label for="publisher_taxonomy">
                            <?php _e('Select Publisher Taxonomy', 'hw-steAM-fetch-games'); ?>
                        </label>
                        <select id="publisher_taxonomy"
                                name="hw_steam_game_fetch_cfg[publisher_taxonomy]"
                                class="select2-field"
                                style="width: 100%;">
                            <option value="">
                                <?php _e('Select a taxonomy for publishers', 'hw-steAM-fetch-games'); ?>
                            </option>
                            <?php foreach ($taxonomies as $tax): ?>
                                <option value="<?php echo esc_attr($tax['slug']); ?>"
                                    <?php selected($selected_publisher_taxonomy, $tax['slug']); ?>>
                                    <?php 
                                        echo esc_html($tax['label']) . ' (' . esc_html($tax['slug']) . ')';
                                    ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Platform Taxonomy -->
                    <div class="hw-form-group">
                        <label for="platform_taxonomy">
                            <?php _e('Select Platform Taxonomy', 'hw-steAM-fetch-games'); ?>
                        </label>
                        <select id="platform_taxonomy"
                                name="hw_steam_game_fetch_cfg[platform_taxonomy]"
                                class="select2-field"
                                style="width: 100%;">
                            <option value="">
                                <?php _e('Select a taxonomy for platforms', 'hw-steAM-fetch-games'); ?>
                            </option>
                            <?php foreach ($taxonomies as $tax): ?>
                                <option value="<?php echo esc_attr($tax['slug']); ?>"
                                    <?php selected($selected_platform_taxonomy, $tax['slug']); ?>>
                                    <?php 
                                        echo esc_html($tax['label']) . ' (' . esc_html($tax['slug']) . ')';
                                    ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">
                            <?php _e('Pre-create terms with slugs: windows, mac, linux. The label can differ, but slug must match.', 'hw-steAM-fetch-games'); ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Image & Video Settings -->
            <div class="postbox">
                <h2 class="hndle"><?php _e('Image & Video Settings', 'hw-steAM-fetch-games'); ?></h2>
                <div class="inside">
                    <div class="hw-form-group">
                        <label for="save_featured_image">
                            <input type="checkbox"
                                   id="save_featured_image"
                                   name="hw_steam_game_fetch_cfg[save_featured_image]"
                                   value="1"
                                   <?php checked($save_featured_image, 1); ?>>
                            <?php _e('Header Image as Featured Image', 'hw-steAM-fetch-games'); ?>
                        </label>
                    </div>
                    <div class="hw-form-group">
                        <label for="disable_inline_images">
                            <input type="checkbox"
                                   id="disable_inline_images"
                                   name="hw_steam_game_fetch_cfg[disable_inline_images]"
                                   value="1"
                                   <?php checked($disable_inline_images, 1); ?>>
                            <?php _e('Disable Inline Images in Description', 'hw-steAM-fetch-games'); ?>
                        </label>
                    </div>

                    <div class="hw-form-group">
                        <label for="capsule_meta">
                            <?php _e('Capsule Image Meta ID', 'hw-steAM-fetch-games'); ?>
                        </label>
                        <input type="text"
                               id="capsule_meta"
                               name="hw_steam_game_fetch_cfg[capsule_meta]"
                               value="<?php echo esc_attr($capsule_meta); ?>"
                               class="regular-text">
                    </div>
                    <div class="hw-form-group">
                        <label for="gallery_meta">
                            <?php _e('Game Gallery Meta ID', 'hw-steAM-fetch-games'); ?>
                        </label>
                        <input type="text"
                               id="gallery_meta"
                               name="hw_steam_game_fetch_cfg[gallery_meta]"
                               value="<?php echo esc_attr($gallery_meta); ?>"
                               class="regular-text">
                    </div>
                    <div class="hw-form-group">
                        <label for="movie_meta">
                            <?php _e('Movie Meta ID', 'hw-steAM-fetch-games'); ?>
                        </label>
                        <input type="text"
                               id="movie_meta"
                               name="hw_steam_game_fetch_cfg[movie_meta]"
                               value="<?php echo esc_attr($movie_meta); ?>"
                               class="regular-text">
                    </div>
                </div>
            </div>

            <!-- Meta Information Settings (Release date, Price, etc.) -->
            <div class="postbox">
                <h2 class="hndle"><?php _e('Meta Information Settings', 'hw-steAM-fetch-games'); ?></h2>
                <div class="inside">
                    <!-- Release Date -->
                    <fieldset style="border:1px solid #ddd; padding:10px; margin-top:20px;">
                        <legend>
                            <strong><?php _e('Release Date Settings', 'hw-steAM-fetch-games'); ?></strong>
                        </legend>
                        <div class="hw-form-group">
                            <label for="release_date_meta">
                                <?php _e('Release Date Meta ID', 'hw-steAM-fetch-games'); ?>
                            </label>
                            <input type="text"
                                   id="release_date_meta"
                                   name="hw_steam_game_fetch_cfg[release_date_meta]"
                                   value="<?php echo esc_attr($release_date_meta); ?>"
                                   class="regular-text">
                        </div>
                        <div class="hw-form-group hw-radio-group">
                            <label><?php _e('Save Format', 'hw-steAM-fetch-games'); ?></label>
                            <label>
                                <input type="radio"
                                       name="hw_steam_game_fetch_cfg[release_date_format]"
                                       value="string"
                                       <?php checked($release_date_format, 'string'); ?>>
                                <?php _e('String', 'hw-steAM-fetch-games'); ?>
                            </label>
                            <label>
                                <input type="radio"
                                       name="hw_steam_game_fetch_cfg[release_date_format]"
                                       value="unix"
                                       <?php checked($release_date_format, 'unix'); ?>>
                                <?php _e('Unix', 'hw-steAM-fetch-games'); ?>
                            </label>
                            <label>
                                <input type="radio"
                                       name="hw_steam_game_fetch_cfg[release_date_format]"
                                       value="timestamp"
                                       <?php checked($release_date_format, 'timestamp'); ?>>
                                <?php _e('Timestamp', 'hw-steAM-fetch-games'); ?>
                            </label>
                        </div>
                    </fieldset>

                    <!-- Price / Is Free -->
                    <fieldset style="border:1px solid #ddd; padding:10px; margin-top:20px;">
                        <legend>
                            <strong><?php _e('Game Price settings', 'hw-steAM-fetch-games'); ?></strong>
                        </legend>
                        <div class="hw-form-group">
                            <label for="is_free_meta">
                                <?php _e('Is Free Meta ID', 'hw-steAM-fetch-games'); ?>
                            </label>
                            <input type="text"
                                   id="is_free_meta"
                                   name="hw_steam_game_fetch_cfg[is_free_meta]"
                                   value="<?php echo esc_attr($is_free_meta); ?>"
                                   class="regular-text">
                        </div>
                        <div class="hw-form-group">
                            <label for="is_free_true_value">
                                <?php _e('Value for "Free" (True)', 'hw-steAM-fetch-games'); ?>
                            </label>
                            <input type="text"
                                   id="is_free_true_value"
                                   name="hw_steam_game_fetch_cfg[is_free_true_value]"
                                   value="<?php echo esc_attr($is_free_true_value); ?>"
                                   class="regular-text">
                        </div>
                        <div class="hw-form-group">
                            <label for="is_free_false_value">
                                <?php _e('Value for "Not Free" (False)', 'hw-steAM-fetch-games'); ?>
                            </label>
                            <input type="text"
                                   id="is_free_false_value"
                                   name="hw_steam_game_fetch_cfg[is_free_false_value]"
                                   value="<?php echo esc_attr($is_free_false_value); ?>"
                                   class="regular-text">
                        </div>
                        <div class="hw-form-group">
                            <label for="price_meta">
                                <?php _e('Price Meta ID', 'hw-steAM-fetch-games'); ?>
                            </label>
                            <input type="text"
                                   id="price_meta"
                                   name="hw_steam_game_fetch_cfg[price_meta]"
                                   value="<?php echo esc_attr($price_meta); ?>"
                                   class="regular-text">
                        </div>
                        <div class="hw-form-group">
                            <label for="remove_currency">
                                <input type="checkbox"
                                       id="remove_currency"
                                       name="hw_steam_game_fetch_cfg[remove_currency]"
                                       value="1"
                                       <?php checked($remove_currency, 1); ?>>
                                <?php _e('Remove Currency Symbol', 'hw-steAM-fetch-games'); ?>
                            </label>
                        </div>
                    </fieldset>
                </div>
            </div>

        </div><!-- /.metabox-holder -->

        <?php submit_button(); ?>
    </form>
</div>
