<?php
class MMP_ACF_Fields {
    public function __construct() {
        add_action('acf/init', array($this, 'register_fields'));
    }

    public function register_fields() {
        if (!function_exists('acf_add_local_field_group')) return;

        acf_add_local_field_group(array(
            'key' => 'group_mmp_photo',
            'title' => 'Photo Gallery Settings',
            'fields' => array(
                array(
                    'key' => 'field_gallery_type',
                    'label' => 'Gallery Type',
                    'name' => 'gallery_type',
                    'type' => 'select',
                    'required' => 1,
                    'choices' => array(
                        'grid' => 'Grid Layout',
                        'masonry' => 'Masonry Layout',
                        'slider' => 'Slider Layout'
                    ),
                    'default_value' => 'grid'
                ),
                array(
                    'key' => 'field_gallery_images',
                    'label' => 'Gallery Images',
                    'name' => 'gallery_images',
                    'type' => 'gallery',
                    'required' => 1,
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                    'min' => 1,
                    'max' => '',
                    'mime_types' => 'jpg,jpeg,png'
                ),
                array(
                    'key' => 'field_gallery_settings',
                    'label' => 'Display Settings',
                    'name' => 'gallery_settings',
                    'type' => 'group',
                    'layout' => 'block',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_columns',
                            'label' => 'Number of Columns',
                            'name' => 'columns',
                            'type' => 'number',
                            'default_value' => 3,
                            'min' => 1,
                            'max' => 6
                        ),
                        array(
                            'key' => 'field_image_size',
                            'label' => 'Image Size',
                            'name' => 'image_size',
                            'type' => 'select',
                            'choices' => array(
                                'thumbnail' => 'Thumbnail',
                                'medium' => 'Medium',
                                'large' => 'Large',
                                'full' => 'Full Size'
                            ),
                            'default_value' => 'medium'
                        ),
                        array(
                            'key' => 'field_lightbox',
                            'label' => 'Enable Lightbox',
                            'name' => 'lightbox',
                            'type' => 'true_false',
                            'default_value' => 1,
                            'ui' => 1
                        )
                    )
                )
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'mmp_gallery'
                    )
                )
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => array('permalink', 'the_content', 'excerpt', 'discussion', 'comments', 'slug')
        ));
    }
}