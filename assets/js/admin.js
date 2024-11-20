(function($) {
    'use strict';

    // Gallery Import Handling
    function initImportHandlers() {
        const $importForms = $('.mmp-import-section form');
        const $importProgress = $('.mmp-import-progress');

        $importForms.on('submit', function(e) {
            const $form = $(this);
            const $submitButton = $form.find('button[type="submit"]');
            const formData = new FormData(this);

            e.preventDefault();

            // Show progress container
            $importProgress.slideDown();
            
            // Disable submit button
            $submitButton.prop('disabled', true)
                        .text(MMP_Admin.i18n.importing);

            // Perform the import
            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.data.redirect;
                    } else {
                        showError(response.data.message);
                    }
                },
                error: function() {
                    showError(MMP_Admin.i18n.import_error);
                },
                complete: function() {
                    $submitButton.prop('disabled', false)
                                .text(MMP_Admin.i18n.import);
                }
            });
        });
    }

    // Gallery Settings Panel
    function initGallerySettings() {
        const $galleryType = $('#field_gallery_type');
        const $settings = $('.gallery-settings-group');

        // Show/hide settings based on gallery type
        $galleryType.on('change', function() {
            const type = $(this).val();
            
            $settings.find('[data-gallery-type]').each(function() {
                const $setting = $(this);
                const supportedTypes = $setting.data('gallery-type').split(',');
                
                if (supportedTypes.includes(type) || supportedTypes.includes('all')) {
                    $setting.show();
                } else {
                    $setting.hide();
                }
            });
        }).trigger('change');
    }

    // Image Manager
    function initImageManager() {
        const $imageContainer = $('.mmp-gallery-images');
        let selectedImages = [];

        // Enable sorting
        if ($imageContainer.length) {
            $imageContainer.sortable({
                items: '.mmp-gallery-image',
                handle: '.mmp-image-handle',
                update: function() {
                    updateImageOrder();
                }
            });
        }

        // Bulk selection
        $('.mmp-select-all').on('change', function() {
            const isChecked = $(this).prop('checked');
            $('.mmp-image-select').prop('checked', isChecked);
            updateBulkActions();
        });

        // Individual selection
        $(document).on('change', '.mmp-image-select', function() {
            updateBulkActions();
        });

        // Bulk actions
        $('.mmp-bulk-action-apply').on('click', function() {
            const action = $('.mmp-bulk-action-select').val();
            const selectedImages = getSelectedImages();

            if (!action || !selectedImages.length) return;

            switch (action) {
                case 'delete':
                    deleteImages(selectedImages);
                    break;
                case 'download':
                    downloadImages(selectedImages);
                    break;
                case 'optimize':
                    optimizeImages(selectedImages);
                    break;
            }
        });

        // Image editing
        $(document).on('click', '.mmp-image-edit', function() {
            const $image = $(this).closest('.mmp-gallery-image');
            const imageId = $image.data('id');
            openImageEditor(imageId);
        });
    }

    // Helper Functions
    function updateImageOrder() {
        const imageOrder = $('.mmp-gallery-image').map(function() {
            return $(this).data('id');
        }).get();

        $.post(ajaxurl, {
            action: 'mmp_update_image_order',
            order: imageOrder,
            nonce: MMP_Admin.nonce
        });
    }

    function getSelectedImages() {
        return $('.mmp-image-select:checked').map(function() {
            return $(this).closest('.mmp-gallery-image').data('id');
        }).get();
    }

    function updateBulkActions() {
        const selectedCount = $('.mmp-image-select:checked').length;
        $('.mmp-bulk-action-apply').prop('disabled', !selectedCount);
        $('.mmp-selected-count').text(selectedCount);
    }

    function openImageEditor(imageId) {
        const editor = wp.media({
            title: MMP_Admin.i18n.edit_image,
            frame: 'image',
            id: imageId,
            button: {
                text: MMP_Admin.i18n.update
            }
        });

        editor.on('update', function(attachment) {
            updateImageData(imageId, attachment);
        });

        editor.open();
    }

    function showError(message) {
        const $notice = $('<div class="notice notice-error is-dismissible"><p></p></div>')
            .find('p').text(message).end();
            
        $('.wrap h1').after($notice);
        
        // Enable WordPress dismissible notices
        if (typeof wp !== 'undefined' && wp.notices) {
            wp.notices.removeDismissible($notice);
        }
    }

    // Initialize everything
    $(document).ready(function() {
        initImportHandlers();
        initGallerySettings();
        initImageManager();

        // Initialize tooltips
        $('.mmp-tooltip').tooltip();

        // Handle media button
        $('.mmp-add-media').on('click', function(e) {
            e.preventDefault();
            
            const mediaFrame = wp.media({
                title: MMP_Admin.i18n.select_images,
                multiple: true,
                library: {
                    type: 'image'
                },
                button: {
                    text: MMP_Admin.i18n.add_to_gallery
                }
            });

            mediaFrame.on('select', function() {
                const attachments = mediaFrame.state().get('selection').toJSON();
                addImagesToGallery(attachments);
            });

            mediaFrame.open();
        });

        // Toggle Advanced Settings
        $('.mmp-toggle-advanced').on('click', function(e) {
            e.preventDefault();
            $('.mmp-advanced-settings').slideToggle();
            $(this).find('.dashicons').toggleClass('dashicons-arrow-down dashicons-arrow-up');
        });

        // Save alert
        let formChanged = false;
        
        $('#post').on('change', 'input, select, textarea', function() {
            formChanged = true;
        });

        $(window).on('beforeunload', function() {
            if (formChanged) {
                return MMP_Admin.i18n.unsaved_changes;
            }
        });
    });

})(jQuery);