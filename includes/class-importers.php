<?php
/**
 * Gallery Importers
 *
 * @package MMP_Photo_Minder_Pro
 */

if (!defined('ABSPATH')) exit;

class MMP_Importers {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_import_page'));
        add_action('admin_post_mmp_import_gallery', array($this, 'handle_import'));
        add_action('wp_ajax_mmp_check_import_status', array($this, 'check_import_status'));
    }

    public function add_import_page() {
        add_submenu_page(
            'edit.php?post_type=mmp_gallery',
            __('Import Gallery', 'mmp-photo'),
            __('Import Gallery', 'mmp-photo'),
            'manage_options',
            'mmp-import-gallery',
            array($this, 'render_import_page')
        );
    }

    private function import_from_google_photos($album_url) {
        // Validate URL
        if (!filter_var($album_url, FILTER_VALIDATE_URL)) {
            return array('error' => __('Invalid Google Photos URL', 'mmp-photo'));
        }

        // Initialize return data
        $import_data = array(
            'title' => '',
            'images' => array(),
            'error' => null
        );

        try {
            // Extract album ID from URL
            $album_id = $this->extract_google_album_id($album_url);
            if (!$album_id) {
                throw new Exception(__('Could not extract album ID from URL', 'mmp-photo'));
            }

            // Use Google Photos API to fetch album data
            $photos = $this->fetch_google_photos($album_id);
            if (empty($photos)) {
                throw new Exception(__('No photos found in album', 'mmp-photo'));
            }

            $import_data['title'] = sanitize_text_field($photos['title']);
            $import_data['images'] = $this->process_google_photos($photos['items']);

        } catch (Exception $e) {
            $import_data['error'] = $e->getMessage();
        }

        return $import_data;
    }

    private function import_from_apple_photos($file) {
        $import_data = array(
            'title' => '',
            'images' => array(),
            'error' => null
        );

        try {
            if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
                throw new Exception(__('No file uploaded', 'mmp-photo'));
            }

            // Create temp directory
            $temp_dir = wp_temp_dir() . '/mmp_import_' . uniqid();
            wp_mkdir_p($temp_dir);

            // Extract zip
            $zip = new ZipArchive;
            if ($zip->open($file['tmp_name']) !== TRUE) {
                throw new Exception(__('Could not open zip file', 'mmp-photo'));
            }
            $zip->extractTo($temp_dir);
            $zip->close();

            // Process extracted files
            $import_data['images'] = $this->process_apple_photos_export($temp_dir);
            $import_data['title'] = basename($file['name'], '.zip');

            // Cleanup
            $this->recursive_rmdir($temp_dir);

        } catch (Exception $e) {
            $import_data['error'] = $e->getMessage();
        }

        return $import_data;
    }

    private function import_from_rss($feed_url) {
        $import_data = array(
            'title' => '',
            'images' => array(),
            'error' => null
        );

        try {
            if (!class_exists('SimplePie')) {
                require_once(ABSPATH . WPINC . '/class-feed.php');
            }

            $feed = new SimplePie();
            $feed->set_feed_url($feed_url);
            $feed->enable_cache(false);
            $feed->init();

            if ($feed->error()) {
                throw new Exception($feed->error());
            }

            $import_data['title'] = $feed->get_title();
            $import_data['images'] = $this->process_rss_feed($feed);

        } catch (Exception $e) {
            $import_data['error'] = $e->getMessage();
        }

        return $import_data;
    }

    public function handle_import() {
        if (!isset($_POST['mmp_import_nonce']) || 
            !wp_verify_nonce($_POST['mmp_import_nonce'], 'mmp_import_gallery')) {
            wp_die(__('Security check failed', 'mmp-photo'));
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'mmp-photo'));
        }

        $source = sanitize_text_field($_POST['source']);
        $import_data = array();

        switch ($source) {
            case 'google_photos':
                $import_data = $this->import_from_google_photos($_POST['google_album_url']);
                break;
            
            case 'apple_photos':
                $import_data = $this->import_from_apple_photos($_FILES['apple_photos_export']);
                break;
            
            case 'rss_feed':
                $import_data = $this->import_from_rss($_POST['rss_feed_url']);
                break;
        }

        if (!empty($import_data['error'])) {
            wp_redirect(add_query_arg(
                array(
                    'page' => 'mmp-import-gallery',
                    'error' => urlencode($import_data['error'])
                ),
                admin_url('edit.php?post_type=mmp_gallery')
            ));
            exit;
        }

        // Create new gallery
        $gallery_id = wp_insert_post(array(
            'post_title' => !empty($import_data['title']) ? 
                          $import_data['title'] : 
                          __('Imported Gallery', 'mmp-photo'),
            'post_type' => 'mmp_gallery',
            'post_status' => 'publish'
        ));

        if (is_wp_error($gallery_id)) {
            wp_redirect(add_query_arg(
                array(
                    'page' => 'mmp-import-gallery',
                    'error' => urlencode($gallery_id->get_error_message())
                ),
                admin_url('edit.php?post_type=mmp_gallery')
            ));
            exit;
        }

        // Update gallery settings
        update_field('gallery_type', 'grid', $gallery_id);
        update_field('gallery_images', $import_data['images'], $gallery_id);

        wp_redirect(add_query_arg(
            array(
                'post' => $gallery_id,
                'action' => 'edit',
                'import' => 'success'
            ),
            admin_url('post.php')
        ));
        exit;
    }

    private function process_google_photos($items) {
        $processed_images = array();
        
        foreach ($items as $item) {
            // Download and process each image
            $image_id = media_sideload_image($item['url'], 0, $item['title'], 'id');
            
            if (!is_wp_error($image_id)) {
                $processed_images[] = array(
                    'ID' => $image_id,
                    'alt' => $item['title'],
                    'caption' => $item['description'] ?? '',
                    'title' => $item['title']
                );
            }
        }
        
        return $processed_images;
    }

    private function process_apple_photos_export($dir) {
        $processed_images = array();
        $allowed_types = array('jpg', 'jpeg', 'png');
        
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir)
        );
        
        foreach ($files as $file) {
            if ($file->isFile()) {
                $ext = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
                if (in_array($ext, $allowed_types)) {
                    $image_id = media_sideload_image($file->getPathname(), 0, '', 'id');
                    
                    if (!is_wp_error($image_id)) {
                        $processed_images[] = array(
                            'ID' => $image_id,
                            'alt' => pathinfo($file->getFilename(), PATHINFO_FILENAME),
                            'caption' => '',
                            'title' => pathinfo($file->getFilename(), PATHINFO_FILENAME)
                        );
                    }
                }
            }
        }
        
        return $processed_images;
    }

    private function process_rss_feed($feed) {
        $processed_images = array();
        
        foreach ($feed->get_items() as $item) {
            // Look for enclosures or media content
            $enclosure = $item->get_enclosure();
            if ($enclosure && in_array($enclosure->get_type(), array('image/jpeg', 'image/png'))) {
                $image_id = media_sideload_image($enclosure->get_link(), 0, $item->get_title(), 'id');
                
                if (!is_wp_error($image_id)) {
                    $processed_images[] = array(
                        'ID' => $image_id,
                        'alt' => $item->get_title(),
                        'caption' => $item->get_description(),
                        'title' => $item->get_title()
                    );
                }
            }
        }
        
        return $processed_images;
    }

    private function recursive_rmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) {
                        $this->recursive_rmdir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }

    public function render_import_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Import Gallery', 'mmp-photo'); ?></h1>

            <?php 
            if (isset($_GET['error'])) {
                echo '<div class="notice notice-error"><p>' . esc_html(urldecode($_GET['error'])) . '</p></div>';
            }
            ?>

            <div class="mmp-import-container">
                <!-- Google Photos Import -->
                <div class="mmp-import-section">
                    <h2><?php esc_html_e('Import from Google Photos', 'mmp-photo'); ?></h2>
                    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                        <?php wp_nonce_field('mmp_import_gallery', 'mmp_import_nonce'); ?>
                        <input type="hidden" name="action" value="mmp_import_gallery">
                        <input type="hidden" name="source" value="google_photos">
                        
                        <p>
                            <label for="google_album_url">
                                <?php esc_html_e('Google Photos Album URL:', 'mmp-photo'); ?>
                            </label>
                            <input type="url" 
                                   id="google_album_url" 
                                   name="google_album_url" 
                                   class="regular-text" 
                                   required>
                        </p>
                        <button type="submit" class="button button-primary">
                            <?php esc_html_e('Import from Google Photos', 'mmp-photo'); ?>
                        </button>
                    </form>
                </div>

                <!-- Apple Photos Import -->
                <div class="mmp-import-section">
                    <h2><?php esc_html_e('Import from Apple Photos', 'mmp-photo'); ?></h2>
                    <form method="post" 
                          action="<?php echo admin_url('admin-post.php'); ?>" 
                          enctype="multipart/form-data">
                        <?php wp_nonce_field('mmp_import_gallery', 'mmp_import_nonce'); ?>
                        <input type="hidden" name="action" value="mmp_import_gallery">
                        <input type="hidden" name="source" value="apple_photos">
                        
                        <p>
                            <label for="apple_photos_export">
                                <?php esc_html_e('Upload Apple Photos Export (.zip):', 'mmp-photo'); ?>
                            </label>
                            <input type="file" 
                                   id="apple_photos_export" 
                                   name="apple_photos_export" 
                                   accept=".zip" 
                                   required>
                        </p>
                        <button type="submit" class="button button-primary">
                            <?php esc_html_e('Import from Apple Photos', 'mmp-photo'); ?>
                        </button>
                    </form>
                </div>

                <!-- RSS Feed Import -->
                <div class="mmp-import-section">
                    <h2><?php esc_html_e('Import from RSS Feed', 'mmp-photo'); ?></h2>
                    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                        <?php wp_nonce_field('mmp_import_gallery', 'mmp_import_nonce'); ?>
                        <input type="hidden" name="action" value="mmp_import_gallery">
                        <input type="hidden" name="source" value="rss_feed">
                        
                        <p>
                            <label for="rss_feed_url">
                                <?php esc_html_e('RSS Feed URL:', 'mmp-photo'); ?>
                            </label>
                            <input type="url" 
                                   id="rss_feed_url" 
                                   name="rss_feed_url" 
                                   class="regular-text" 
                                   required>
                        </p>
                        <button type="submit" class="button button-primary">
                            <?php esc_html_e('Import from RSS Feed', 'mmp-photo'); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
}