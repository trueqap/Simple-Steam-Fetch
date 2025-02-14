<?php
if (!defined('ABSPATH')) {
    exit;
}

// Ellenőrizzük az URL paramétereket
$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : null;
$success = isset($_GET['success']) && $_GET['success'] === 'true';
$error = isset($_GET['error']) ? sanitize_text_field($_GET['error']) : null;

if ($success && $post_id) {
    $post = get_post($post_id);
    $edit_link = get_edit_post_link($post_id);
    $view_link = get_permalink($post_id);
    
    if ($post) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <?php if ($post->post_date_gmt === $post->post_modified_gmt) : ?>
                    <strong><?php _e('Successfully created the following post:', 'hw-steam-fetch-games'); ?></strong>
                <?php else : ?>
                    <strong><?php _e('Successfully updated the following post:', 'hw-steam-fetch-games'); ?></strong>
                <?php endif; ?>
                <br>
                <a href="<?php echo esc_url($edit_link); ?>" target="_blank"><?php echo esc_html($post->post_title); ?></a> 
                <span> | </span>
                <a href="<?php echo esc_url($view_link); ?>" target="_blank"><?php _e('View on frontend', 'hw-steam-fetch-games'); ?></a>
            </p>
        </div>
        <?php
    }
} elseif ($error) {
    ?>
    <div class="notice notice-error is-dismissible">
        <p><?php _e('Unfortunately, we couldn’t create the post. Please try again.', 'hw-steam-fetch-games'); ?></p>
    </div>
    <?php
}
?>
