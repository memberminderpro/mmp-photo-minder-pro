<?php
/**
 * Plugin Name: MMP Photo Minder Pro
 * Plugin URI: https://mmpro.dev/wp/plugins/photo-minder-pro
 * Description: Professional photo gallery management system with ACF Pro integration
 * Version: 1.0.2
 * Author: Rob Moore
 * Author URI: https://mmpro.dev
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Plugin constants
define('MMPHOTO_VERSION', '1.0.0');
define('MMPHOTO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MMPHOTO_PLUGIN_URL', plugin_dir_url(__FILE__));

class MMPhotoMinderPro {
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_init', array($this, 'check_dependencies'));
        add_action('plugins_loaded', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    public function check_dependencies() {
        if (!class_exists('acf')) {
            add_action('admin_notices', function() {
                ?>
                <div class="error">
                    <p>MMP Photo Minder Pro requires Advanced Custom Fields Pro to be installed and activated.</p>
                </div>
                <?php
            });
            return false;
        }
        return true;
    }

    public function init() {
        load_plugin_textdomain('mmp-photo', false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        require_once MMPHOTO_PLUGIN_DIR . 'includes/class-post-types.php';
        require_once MMPHOTO_PLUGIN_DIR . 'includes/class-acf-fields.php';
        require_once MMPHOTO_PLUGIN_DIR . 'includes/class-shortcodes.php';
        require_once MMPHOTO_PLUGIN_DIR . 'includes/class-widget.php';
        require_once MMPHOTO_PLUGIN_DIR . 'includes/class-importers.php';
        
        new MMP_Post_Types();
        new MMP_ACF_Fields();
        new MMP_Shortcodes();
        new MMP_Widget();
        new MMP_Importers();
        
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    public function activate() {
        flush_rewrite_rules();
    }

    public function deactivate() {
        flush_rewrite_rules();
    }

    public function enqueue_frontend_assets() {
        // Enqueue imagesLoaded library
        wp_enqueue_script(
            'images-loaded',
            'https://cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/5.0.0/imagesloaded.pkgd.min.js',
            array('jquery'),
            '5.0.0',
            true
        );
    
        // Enqueue masonry library
        wp_enqueue_script(
            'masonry',
            'https://cdnjs.cloudflare.com/ajax/libs/masonry/4.2.2/masonry.pkgd.min.js',
            array('jquery', 'images-loaded'),
            '4.2.2',
            true
        );
    
        // Enqueue Slick Carousel
        wp_enqueue_script(
            'slick-carousel',
            'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js',
            array('jquery'),
            '1.8.1',
            true
        );
    
        wp_enqueue_style(
            'slick-carousel-css',
            'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css',
            array(),
            '1.8.1'
        );
    
        wp_enqueue_style(
            'slick-carousel-theme',
            'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css',
            array('slick-carousel-css'),
            '1.8.1'
        );
    
        // Enqueue main styles
        wp_enqueue_style(
            'mmp-photo-styles', 
            MMPHOTO_PLUGIN_URL . 'assets/css/frontend.css',
            array('slick-carousel-theme'),
            MMPHOTO_VERSION
        );
        
        // Enqueue lightbox
        wp_enqueue_script(
            'mmp-photo-lightbox',
            'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js',
            array('jquery'),
            '2.11.3',
            true
        );
        
        wp_enqueue_style(
            'mmp-photo-lightbox',
            'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css',
            array(),
            '2.11.3'
        );
        
        // Enqueue our main script with all dependencies
        wp_enqueue_script(
            'mmp-photo-frontend',
            MMPHOTO_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery', 'images-loaded', 'masonry', 'slick-carousel', 'mmp-photo-lightbox'),
            MMPHOTO_VERSION,
            true
        );
    
        // Add localization for our script
        wp_localize_script(
            'mmp-photo-frontend',
            'MMPhotoSettings',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('mmp_photo_nonce'),
                'debug' => defined('WP_DEBUG') && WP_DEBUG,
                'i18n' => array(
                    'loading' => __('Loading...', 'mmp-photo'),
                    'error' => __('Error loading gallery', 'mmp-photo')
                )
            )
        );
    }

    public function enqueue_admin_assets($hook) {
        if ('post.php' !== $hook && 'post-new.php' !== $hook) {
            return;
        }
        
        wp_enqueue_style(
            'mmp-photo-admin',
            MMPHOTO_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            MMPHOTO_VERSION
        );
        
        wp_enqueue_script(
            'mmp-photo-admin',
            MMPHOTO_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            MMPHOTO_VERSION,
            true
        );
    }
}

// Initialize plugin
function MMPhotoMinderPro() {
    return MMPhotoMinderPro::get_instance();
}

MMPhotoMinderPro();