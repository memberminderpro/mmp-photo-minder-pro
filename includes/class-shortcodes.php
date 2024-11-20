<?php
/**
 * Shortcode Handler
 *
 * @package MMP_Photo_Minder_Pro
 */

if (!defined('ABSPATH')) exit;

class MMP_Shortcodes {
    public function __construct() {
        add_shortcode('mmp_gallery', array($this, 'render_gallery'));
    }

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
        $images = get_field('gallery_images', $atts['id']);
        if (!$images || !is_array($images)) {
            return '';
        }

        // Get gallery settings
        $settings = get_field('gallery_settings', $atts['id']);
        $type = $atts['type'] ?: get_field('gallery_type', $atts['id']);
        $columns = $atts['columns'] ?: ($settings['columns'] ?? 3);
        $size = $atts['size'] ?: ($settings['image_size'] ?? 'medium');
        $enable_lightbox = $settings['lightbox'] ?? true;

        // Start output buffering
        ob_start();
        ?>
        <div class="mmp-gallery mmp-gallery-<?php echo esc_attr($type); ?>" 
             data-columns="<?php echo esc_attr($columns); ?>">
            <?php
            foreach ($images as $image) {
                $full_img_url = $image['url'];
                $thumb_url = $image['sizes'][$size];
                $caption = $image['caption'];
                $alt = $image['alt'];
                ?>
                <div class="mmp-gallery-item">
                    <?php if ($enable_lightbox): ?>
                    <a href="<?php echo esc_url($full_img_url); ?>" 
                       class="mmp-gallery-link" 
                       data-lightbox="gallery-<?php echo esc_attr($atts['id']); ?>"
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