# MMP Photo Minder Pro

![License](https://img.shields.io/badge/license-GPL--2.0%2B-blue.svg)
![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-8892bf.svg)
![WordPress Version](https://img.shields.io/badge/wordpress-%3E%3D5.0-blue.svg)
![ACF Pro Required](https://img.shields.io/badge/requires-ACF%20Pro-00a0d2.svg)

A professional WordPress gallery management system designed for accessibility and ease of use. Perfect for photographers, organizations, and anyone needing to manage and display photo collections.

## 🎯 Features

- **Intuitive Gallery Management**: Simple drag-and-drop interface for organizing photos
- **Accessibility-First Design**: WCAG 2.1 compliant with screen reader support
- **Multiple Import Options**: Import from Google Photos, Apple Photos, and RSS feeds
- **Advanced Display Options**: Grid, masonry, and slider layouts with responsive design
- **Performance Optimized**: Automatic image optimization and lazy loading
- **Widget Support**: Easily add galleries to sidebars and widget areas
- **Bulk Operations**: Efficiently manage large photo collections
- **Shortcode Support**: Simple integration with posts and pages

## 📋 Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher
- Advanced Custom Fields Pro
- 64MB+ WordPress memory limit
- ModRewrite enabled

## 🚀 Installation

1. Download the latest release
2. Upload to your WordPress site
3. Activate the plugin
4. Navigate to Photo Minder → Settings
5. Enter your license key
6. Configure your preferences

```php
// Optional: Add to wp-config.php for custom limits
define('MMPHOTO_MAX_UPLOAD_SIZE', '10M');
define('MMPHOTO_MEMORY_LIMIT', '256M');
```

## 📖 Documentation

- [User Guide](docs/USER_GUIDE.md) - For end users managing galleries
- [Site Owner Guide](docs/SITE_OWNER_GUIDE.md) - For website administrators
- [Developer Guide](docs/DEVELOPER_GUIDE.md) - For developers extending the plugin
- [Translation Guide](docs/TRANSLATIONS.md) - For translators

## 🔧 Development

### Setup Development Environment

```bash
# Clone the repository
git clone https://github.com/your-org/mmp-photo-minder-pro.git

# Install dependencies
composer install
npm install

# Build assets
npm run build
```

### Running Tests

```bash
# PHP Unit Tests
composer test

# JavaScript Tests
npm test

# End-to-end Tests
npm run test:e2e
```

### Coding Standards

```bash
# PHP Code Standards
composer run-script phpcs

# JavaScript Linting
npm run lint
```

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) for details on:

- Submitting bug reports
- Development workflow
- Making pull requests
- Code review process

## 📝 License

MMP Photo Minder Pro is licensed under the GPL v2 or later.

See [LICENSE](LICENSE) for the complete license text.

## 🔒 Security

### Reporting Security Issues

Please report security issues privately to security@example.com. Please do not create public issues for security vulnerabilities.

See our [Security Policy](.github/SECURITY.md) for more details.

## 🌟 Credits

MMP Photo Minder Pro is developed and maintained by [Your Organization](https://example.com).

### Contributors

Thank you to all our contributors:
- [Contributor Name](https://github.com/username) - Feature implementation
- [Contributor Name](https://github.com/username) - Bug fixes
- [Contributor Name](https://github.com/username) - Documentation

See the full list of [contributors](https://github.com/your-org/mmp-photo-minder-pro/graphs/contributors).

## 📢 Support

- 🌐 [Official Website](https://example.com)
- 📚 [Documentation](https://example.com/docs)
- 💬 [Community Forum](https://example.com/forum)
- 📧 [Email Support](mailto:support@example.com)

## 🗺️ Roadmap

See our [project roadmap](https://github.com/your-org/mmp-photo-minder-pro/projects/1) for planned features and improvements.

## 📜 Changelog

See [CHANGELOG.md](CHANGELOG.md) for a list of changes and release notes.

---

<div align="center">
Made with ❤️ by [Your Organization]
</div>