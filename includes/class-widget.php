<?php
/**
 * Gallery Widget
 *
 * @package MMP_Photo_Minder_Pro
 */

if (!defined('ABSPATH')) exit;

class MMP_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'mmp_gallery_widget',
            __('Photo Minder Gallery', 'mmp-photo'),
            array('description' => __('Display a photo gallery in your sidebar', 'mmp-photo'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        if (!empty($instance['gallery_id'])) {
            echo do_shortcode('[mmp_gallery id="' . $instance['gallery_id'] . '" columns="' . $instance['columns'] . '"]');
        } else {
            $this->display_gallery_grid($instance);
        }

        echo $args['after_widget'];
    }

    private function display_gallery_grid($instance) {
        $query_args = array(
            'post_type' => 'mmp_gallery',
            'posts_per_page' => $instance['number'] ?: 4,
            'orderby' => $instance['orderby'] ?: 'date',
            'order' => $instance['order'] ?: 'DESC'
        );

        $galleries = new WP_Query($query_args);

        if ($galleries->have_posts()) {
            echo '<div class="mmp-widget-gallery-grid">';
            while ($galleries->have_posts()) {
                $galleries->the_post();
                $images = get_field('gallery_images', get_the_ID());
                if (!empty($images)) {
                    $thumbnail = $images[0];
                    ?>
                    <div class="mmp-widget-gallery-item">
                        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                            <img src="<?php echo esc_url($thumbnail['sizes']['thumbnail']); ?>" 
                                 alt="<?php echo esc_attr($thumbnail['alt']); ?>"
                                 loading="lazy">
                            <span class="mmp-widget-gallery-title"><?php the_title(); ?></span>
                        </a>
                    </div>
                    <?php
                }
            }
            echo '</div>';
            wp_reset_postdata();
        }
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $gallery_id = !empty($instance['gallery_id']) ? $instance['gallery_id'] : '';
        $display_mode = !empty($instance['display_mode']) ? $instance['display_mode'] : 'single';
        $number = !empty($instance['number']) ? $instance['number'] : 4;
        $columns = !empty($instance['columns']) ? $instance['columns'] : 2;
        $orderby = !empty($instance['orderby']) ? $instance['orderby'] : 'date';
        $order = !empty($instance['order']) ? $instance['order'] : 'DESC';

        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_html_e('Title:', 'mmp-photo'); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('display_mode')); ?>">
                <?php esc_html_e('Display Mode:', 'mmp-photo'); ?>
            </label>
            <select class="widefat" 
                    id="<?php echo esc_attr($this->get_field_id('display_mode')); ?>" 
                    name="<?php echo esc_attr($this->get_field_name('display_mode')); ?>">
                <option value="single" <?php selected($display_mode, 'single'); ?>>
                    <?php esc_html_e('Single Gallery', 'mmp-photo'); ?>
                </option>
                <option value="grid" <?php selected($display_mode, 'grid'); ?>>
                    <?php esc_html_e('Gallery Grid', 'mmp-photo'); ?>
                </option>
            </select>
        </p>

        <p class="mmp-single-gallery-options" <?php echo $display_mode === 'grid' ? 'style="display:none;"' : ''; ?>>
            <label for="<?php echo esc_attr($this->get_field_id('gallery_id')); ?>">
                <?php esc_html_e('Select Gallery:', 'mmp-photo'); ?>
            </label>
            <select class="widefat" 
                    id="<?php echo esc_attr($this->get_field_id('gallery_id')); ?>" 
                    name="<?php echo esc_attr($this->get_field_name('gallery_id')); ?>">
                <option value=""><?php esc_html_e('-- Select Gallery --', 'mmp-photo'); ?></option>
                <?php
                $galleries = get_posts(array('post_type' => 'mmp_gallery', 'posts_per_page' => -1));
                foreach ($galleries as $gallery) {
                    echo sprintf(
                        '<option value="%s" %s>%s</option>',
                        esc_attr($gallery->ID),
                        selected($gallery_id, $gallery->ID, false),
                        esc_html($gallery->post_title)
                    );
                }
                ?>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('columns')); ?>">
                <?php esc_html_e('Columns:', 'mmp-photo'); ?>
            </label>
            <input class="tiny-text" 
                   id="<?php echo esc_attr($this->get_field_id('columns')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('columns')); ?>" 
                   type="number" 
                   step="1" 
                   min="1" 
                   max="4" 
                   value="<?php echo esc_attr($columns); ?>">
        </p>

        <p class="mmp-grid-options" <?php echo $display_mode === 'single' ? 'style="display:none;"' : ''; ?>>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>">
                <?php esc_html_e('Number of galleries to show:', 'mmp-photo'); ?>
            </label>
            <input class="tiny-text" 
                   id="<?php echo esc_attr($this->get_field_id('number')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('number')); ?>" 
                   type="number" 
                   step="1" 
                   min="1" 
                   value="<?php echo esc_attr($number); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = !empty($new_instance['title']) ? strip_tags($new_instance['title']) : '';
        $instance['display_mode'] = !empty($new_instance['display_mode']) ? $new_instance['display_mode'] : 'single';
        $instance['gallery_id'] = !empty($new_instance['gallery_id']) ? (int) $new_instance['gallery_id'] : 0;
        $instance['number'] = !empty($new_instance['number']) ? (int) $new_instance['number'] : 4;
        $instance['columns'] = !empty($new_instance['columns']) ? (int) $new_instance['columns'] : 2;
        $instance['orderby'] = !empty($new_instance['orderby']) ? $new_instance['orderby'] : 'date';
        $instance['order'] = !empty($new_instance['order']) ? $new_instance['order'] : 'DESC';

        return $instance;
    }
}

// Register Widget
function mmp_register_widget() {
    register_widget('MMP_Widget');
}
add_action('widgets_init', 'mmp_register_widget');