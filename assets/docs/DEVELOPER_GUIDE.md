# MMP Photo Minder Pro Developer Guide

## Overview
This guide provides technical documentation for developers working with MMP Photo Minder Pro.

## Table of Contents
1. [Installation & Setup](#installation--setup)
2. [Troubleshooting Guide](#troubleshooting-guide)
3. [Contributing Guidelines](#contributing-guidelines)
4. [API Documentation](#api-documentation)
5. [Hooks & Filters](#hooks--filters)
6. [Development Environment](#development-environment)

## Installation & Setup

### Requirements
- WordPress 5.0+
- PHP 7.4+
- Advanced Custom Fields Pro
- Composer for dependencies
- Node.js & npm for build tools

### Development Setup
```bash
# Clone repository
git clone https://github.com/your-org/mmp-photo-minder-pro.git

# Install dependencies
composer install
npm install

# Build assets
npm run build
```

### Environment Configuration
```php
// wp-config.php development settings
define('MMPHOTO_DEBUG', true);
define('MMPHOTO_DEV_MODE', true);
define('MMPHOTO_API_DEBUG', true);
```

## Troubleshooting Guide

### Installation Issues

#### ACF Pro Dependency
```php
// Check ACF Pro installation
if (!class_exists('acf')) {
    // ACF Pro not installed
    add_action('admin_notices', function() {
        echo '<div class="error"><p>ACF Pro required</p></div>';
    });
    return;
}
```

#### Memory Limits
```php
// Check memory limits
if (wp_convert_hr_to_bytes(WP_MEMORY_LIMIT) < 67108864) {
    // Memory limit too low
    add_action('admin_notices', function() {
        echo '<div class="error"><p>Insufficient memory</p></div>';
    });
    return;
}
```

#### File Permissions
```bash
# Set correct permissions
chmod 755 wp-content/plugins/mmp-photo-minder-pro
chmod 644 wp-content/plugins/mmp-photo-minder-pro/*.php
```

### Common Problems & Solutions

#### Database Issues
```sql
-- Check table creation
SHOW TABLES LIKE 'wp_mmp_gallery%';

-- Verify indexes
SHOW INDEX FROM wp_mmp_gallery_meta;

-- Check for orphaned data
SELECT * FROM wp_mmp_gallery_images 
WHERE gallery_id NOT IN (
    SELECT ID FROM wp_posts 
    WHERE post_type = 'mmp_gallery'
);
```

#### Image Processing
```php
// Debug image processing
add_filter('mmp_image_process_debug', function($debug, $image_id) {
    error_log('Processing image: ' . $image_id);
    return true;
}, 10, 2);
```

#### Import Failures
```php
// Enable import debugging
add_filter('mmp_import_debug', '__return_true');

// Log import steps
add_action('mmp_import_log', function($message) {
    error_log('[MMP Import] ' . $message);
});
```

## Contributing Guidelines

### Getting Started

1. Fork the repository
2. Create a feature branch
   ```bash
   git checkout -b feature/your-feature-name
   ```
3. Make your changes
4. Submit a pull request

### Code Standards

#### PHP
- Follow WordPress Coding Standards
- Use PHP_CodeSniffer
- Maintain PHP 7.4 compatibility
```bash
# Check code standards
composer run-script phpcs

# Auto-fix standards
composer run-script phpcbf
```

#### JavaScript
- Use ESLint
- Follow Prettier configuration
```bash
# Check JS standards
npm run lint

# Fix JS standards
npm run lint:fix
```

### Testing

#### Unit Tests
```bash
# Run PHP unit tests
composer test

# Run JavaScript tests
npm test
```

#### Integration Tests
```bash
# Run WordPress integration tests
composer test:integration

# Run e2e tests
npm run test:e2e
```

### Documentation

#### Inline Documentation
```php
/**
 * Process gallery images.
 *
 * @since 1.0.0
 * @param int   $gallery_id The gallery ID.
 * @param array $images     Array of image IDs.
 * @return bool|WP_Error    True on success, WP_Error on failure.
 */
function process_gallery_images($gallery_id, $images) {
    // Function implementation
}
```

#### README Updates
- Keep installation instructions current
- Document new features
- Update changelog
- Maintain API documentation

### Pull Request Process

1. Update documentation
2. Add/update tests
3. Follow commit message conventions
4. Request review from maintainers

## API Documentation

### REST API Endpoints

```php
// Register custom endpoints
register_rest_route('mmp/v1', '/galleries', [
    'methods' => 'GET',
    'callback' => 'get_galleries',
    'permission_callback' => 'check_permission'
]);
```

### Hooks & Filters

```php
// Action hooks
do_action('mmp_before_gallery_save', $gallery_id);
do_action('mmp_after_gallery_save', $gallery_id);
do_action('mmp_gallery_imported', $gallery_id);

// Filters
$image_size = apply_filters('mmp_thumbnail_size', 'medium');
$gallery_options = apply_filters('mmp_gallery_options', $defaults);
```

## Development Environment

### Local Development

```bash
# Start development server
npm run start

# Watch for changes
npm run watch

# Build for production
npm run build:prod
```

### Docker Setup

```yaml
# docker-compose.yml
version: '3'
services:
  wordpress:
    image: wordpress:latest
    volumes:
      - ./:/var/www/html/wp-content/plugins/mmp-photo-minder-pro
```

### Debug Mode
```php
// Enable debug logging
if (defined('MMPHOTO_DEBUG') && MMPHOTO_DEBUG) {
    error_log('[MMP Debug] ' . $message);
}
```

Need any specific section expanded or additional topics covered?