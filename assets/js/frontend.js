(function($) {
    'use strict';

    // Initialize Masonry layout if needed
    function initMasonry() {
        $('.mmp-gallery[data-type="masonry"]').each(function() {
            var $gallery = $(this);
            $gallery.masonry({
                itemSelector: '.mmp-gallery-item',
                columnWidth: '.mmp-gallery-item',
                percentPosition: true
            });
        });
    }

    // Initialize Lightbox
    function initLightbox() {
        lightbox.option({
            'resizeDuration': 300,
            'wrapAround': true,
            'disableScrolling': true,
            'fitImagesInViewport': true,
            'maxWidth': 1200,
            'maxHeight': 900,
            'albumLabel': 'Image %1 of %2',
            'alwaysShowNavOnTouchDevices': true
        });
    }

    // Lazy Loading
    function initLazyLoading() {
        if ('loading' in HTMLImageElement.prototype) {
            const images = document.querySelectorAll('img[loading="lazy"]');
            images.forEach(img => {
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                }
            });
        } else {
            // Fallback for browsers that don't support lazy loading
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lozad.js/1.16.0/lozad.min.js';
            script.onload = function() {
                const observer = lozad();
                observer.observe();
            };
            document.body.appendChild(script);
        }
    }

    // Keyboard Navigation
    function initKeyboardNav() {
        $(document).keydown(function(e) {
            if ($('.lightbox').is(':visible')) {
                switch(e.keyCode) {
                    case 37: // Left arrow
                        $('.lb-prev').click();
                        break;
                    case 39: // Right arrow
                        $('.lb-next').click();
                        break;
                    case 27: // Escape
                        $('.lb-close').click();
                        break;
                }
            }
        });
    }

    // Touch Swipe Support
    function initTouchSupport() {
        let touchStartX = 0;
        let touchEndX = 0;

        $(document).on('touchstart', '.lightbox', function(e) {
            touchStartX = e.originalEvent.touches[0].clientX;
        });

        $(document).on('touchend', '.lightbox', function(e) {
            touchEndX = e.originalEvent.changedTouches[0].clientX;
            handleSwipe();
        });

        function handleSwipe() {
            const swipeThreshold = 50;
            const swipeDistance = touchEndX - touchStartX;

            if (Math.abs(swipeDistance) > swipeThreshold) {
                if (swipeDistance > 0) {
                    $('.lb-prev').click();
                } else {
                    $('.lb-next').click();
                }
            }
        }
    }

    // Image Download Button
    function addDownloadButton() {
        $(document).on('click', '.lb-download', function(e) {
            e.preventDefault();
            const imageUrl = $('.lb-image').attr('src');
            const link = document.createElement('a');
            link.href = imageUrl;
            link.download = 'gallery-image.jpg';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    }

    // Initialize everything when document is ready
    $(document).ready(function() {
        initMasonry();
        initLightbox();
        initLazyLoading();
        initKeyboardNav();
        initTouchSupport();
        addDownloadButton();

        // Refresh masonry layout when all images are loaded
        $('.mmp-gallery[data-type="masonry"]').imagesLoaded().done(function() {
            initMasonry();
        });

        // Handle window resize
        let resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                $('.mmp-gallery[data-type="masonry"]').masonry('layout');
            }, 250);
        });
    });

})(jQuery);