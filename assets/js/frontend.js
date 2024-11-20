(function($) {
    'use strict';

    // Initialize Masonry layout if needed
    function initMasonry() {
        $('.mmp-gallery[data-type="masonry"]').each(function() {
            var $gallery = $(this);
            // Initialize Masonry after images are loaded
            $gallery.imagesLoaded(function() {
                $gallery.masonry({
                    itemSelector: '.mmp-gallery-item',
                    columnWidth: '.mmp-gallery-item',
                    percentPosition: true
                }).addClass('is-initialized');
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

    // Initialize grid layout
    function initGrid() {
        $('.mmp-gallery[data-type="grid"]').each(function() {
            var $gallery = $(this);
            var columns = $gallery.data('columns') || 3;
            
            $gallery.imagesLoaded(function() {
                $gallery.addClass('is-initialized');
            });
        });
    }

    // Handle Slideshow Layout
    function initSlideshow() {
        $('.mmp-gallery[data-type="slider"]').each(function() {
            var $gallery = $(this);
            var autoplay = $gallery.data('autoplay') || false;
            var speed = $gallery.data('speed') || 3000;

            $gallery.imagesLoaded(function() {
                $gallery.addClass('is-initialized').slick({
                    dots: true,
                    arrows: true,
                    infinite: true,
                    speed: 500,
                    fade: true,
                    cssEase: 'linear',
                    autoplay: autoplay,
                    autoplaySpeed: speed,
                    adaptiveHeight: true
                });
            });
        });
    }

    // Accessibility Enhancements
    function initAccessibility() {
        // Add aria labels
        $('.mmp-gallery-item a').each(function() {
            const $link = $(this);
            const caption = $link.find('.mmp-gallery-caption').text();
            if (caption) {
                $link.attr('aria-label', caption);
            }
        });

        // Add focus styles
        $('.mmp-gallery-item a').on('focus', function() {
            $(this).parent().addClass('is-focused');
        }).on('blur', function() {
            $(this).parent().removeClass('is-focused');
        });

        // Make lightbox keyboard accessible
        $('.lb-nav a').attr('role', 'button').attr('tabindex', '0');
    }

    // Performance Monitoring
    function monitorPerformance() {
        const galleryLoadTime = window.performance.now();
        console.log('Gallery load time:', galleryLoadTime + 'ms');

        // Monitor image loading
        $('.mmp-gallery img').each(function() {
            const img = this;
            const startTime = window.performance.now();
            
            if (img.complete) {
                const loadTime = window.performance.now() - startTime;
                console.log('Image loaded from cache:', loadTime + 'ms');
            } else {
                img.addEventListener('load', function() {
                    const loadTime = window.performance.now() - startTime;
                    console.log('Image loaded:', loadTime + 'ms');
                });
            }
        });
    }

    // Initialize everything when document is ready
    $(document).ready(function() {
        // Initialize layouts based on gallery type
        $('.mmp-gallery').each(function() {
            var $gallery = $(this);
            var type = $gallery.data('type') || 'grid';

            switch(type) {
                case 'masonry':
                    initMasonry();
                    break;
                case 'slider':
                    initSlideshow();
                    break;
                default:
                    initGrid();
            }
        });

        initLightbox();
        initLazyLoading();
        initKeyboardNav();
        initTouchSupport();
        initAccessibility();
        addDownloadButton();

        if (MMPhotoSettings.debug) {
            monitorPerformance();
        }

        // Handle window resize
        let resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                $('.mmp-gallery[data-type="masonry"]').masonry('layout');
                $('.mmp-gallery[data-type="slider"]').slick('setPosition');
            }, 250);
        });
    });

})(jQuery);