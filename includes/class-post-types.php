<?php
/**
 * Post Types Registration
 *
 * @package MMP_Photo_Minder_Pro
 */

if (!defined('ABSPATH')) exit;

class MMP_Post_Types {
    public function __construct() {
        add_action('init', array($this, 'register_post_types'));
        add_filter('manage_mmp_gallery_posts_columns', array($this, 'add_admin_columns'));
        add_action('manage_mmp_gallery_posts_custom_column', array($this, 'manage_admin_columns'), 10, 2);
    }

    public function register_post_types() {
        $labels = array(
            'name'                  => _x('Photo Galleries', 'Post Type General Name', 'mmp-photo'),
            'singular_name'         => _x('Photo Gallery', 'Post Type Singular Name', 'mmp-photo'),
            'menu_name'            => __('Photo Minder', 'mmp-photo'),
            'name_admin_bar'       => __('Photo Gallery', 'mmp-photo'),
            'add_new'              => __('Add New Gallery', 'mmp-photo'),
            'add_new_item'         => __('Add New Photo Gallery', 'mmp-photo'),
            'edit_item'            => __('Edit Gallery', 'mmp-photo'),
            'new_item'             => __('New Gallery', 'mmp-photo'),
            'view_item'            => __('View Gallery', 'mmp-photo'),
            'search_items'         => __('Search Galleries', 'mmp-photo'),
            'not_found'            => __('No galleries found', 'mmp-photo'),
            'not_found_in_trash'   => __('No galleries found in Trash', 'mmp-photo'),
        );

        $args = array(
            'label'               => __('Photo Galleries', 'mmp-photo'),
            'labels'              => $labels,
            'supports'            => array('title'),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 20,
            'menu_icon'           => 'dashicons-format-gallery',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
            'show_in_rest'        => true,
        );

        register_post_type('mmp_gallery', $args);
    }

    public function add_admin_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = $columns['title'];
        $new_columns['shortcode'] = __('Shortcode', 'mmp-photo');
        $new_columns['images'] = __('Images', 'mmp-photo');
        $new_columns['type'] = __('Gallery Type', 'mmp-photo');
        $new_columns['date'] = $columns['date'];
        
        return $new_columns;
    }

    public function manage_admin_columns($column, $post_id) {
        switch ($column) {
            case 'shortcode':
                echo '<code>[mmp_gallery id="' . $post_id . '"]</code>';
                break;
                
            case 'images':
                $images = get_field('gallery_images', $post_id);
                echo is_array($images) ? count($images) : '0';
                break;
                
            case 'type':
                $type = get_field('gallery_type', $post_id);
                echo ucfirst($type ?: 'grid');
                break;
        }
    }
}