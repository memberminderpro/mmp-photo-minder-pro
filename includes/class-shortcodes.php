<?php
/**
 * Shortcode Handler for MMP Photo Minder Pro
 *
 * @package     MMP_Photo_Minder_Pro
 * @author      Rob Moore
 * @copyright   2024 MMP Pro
 * @license     GPL-2.0-or-later
 */

if (!defined('ABSPATH')) exit;

class MMP_Shortcodes {
    /**
     * Initialize the shortcode functionality
     */
    public function __construct() {
        add_shortcode('mmp_gallery', array($this, 'render_gallery'));
    }

    /**
     * Render the gallery output
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output of the gallery
     */
    public function render_gallery($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
            'columns' => null,
            'type' => null,
            'size' => null
        ), $atts);

        if (empty($atts['id'])) {
            return '';
        }

        // Get gallery data
        $gallery_id = absint($atts['id']);
        $images = get_field('gallery_images', $gallery_id);
        if (!$images || !is_array($images)) {
            return '';
        }

        // Get gallery settings from ACF
        $gallery_settings = get_field('gallery_settings', $gallery_id);
        
        // Merge shortcode attributes with gallery settings, shortcode takes precedence
        $type = $atts['type'] ?: (get_field('gallery_type', $gallery_id) ?: 'grid');
        // Force grid if slider is selected (temporary)
        if ($type === 'slider') {
            $type = 'grid';
        }
        $columns = $atts['columns'] ?: ($gallery_settings['columns'] ?? 3);
        $size = $atts['size'] ?: ($gallery_settings['image_size'] ?? 'medium');
        $enable_lightbox = $gallery_settings['lightbox'] ?? true;

        // Start output buffering
        ob_start();
        ?>
        <div class="mmp-gallery" 
             data-type="<?php echo esc_attr($type); ?>"
             data-columns="<?php echo esc_attr($columns); ?>">
            <?php if ($type === 'masonry'): ?>
            <div class="mmp-masonry-sizer"></div>
            <?php endif; ?>
            <?php
            foreach ($images as $image) {
                $thumb_url = $image['sizes'][$size] ?? $image['url'];
                $full_url = $image['url'];
                $caption = $image['caption'];
                $alt = $image['alt'];
                ?>
                <div class="mmp-gallery-item">
                    <?php if ($enable_lightbox): ?>
                    <a href="<?php echo esc_url($full_url); ?>" 
                       class="mmp-gallery-link" 
                       data-lightbox="gallery-<?php echo esc_attr($gallery_id); ?>"
                       data-title="<?php echo esc_attr($caption); ?>">
                    <?php endif; ?>
                        <img src="<?php echo esc_url($thumb_url); ?>"
                             alt="<?php echo esc_attr($alt); ?>"
                             class="mmp-gallery-image"
                             loading="lazy">
                        <?php if ($caption): ?>
                        <div class="mmp-gallery-caption">
                            <?php echo esc_html($caption); ?>
                        </div>
                        <?php endif; ?>
                    <?php if ($enable_lightbox): ?>
                    </a>
                    <?php endif; ?>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
        return ob_get_clean();
    }
}