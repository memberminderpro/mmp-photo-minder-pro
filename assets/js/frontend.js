/**
 * MMP Photo Minder Pro - Frontend Scripts
 * 
 * @package  MMP_Photo_Minder_Pro
 * @author   Rob Moore
 * @version  1.0.5
 */

(function($) {
    'use strict';

    /**
     * Initialize Masonry layout
     */
    function initMasonry() {
        $('.mmp-gallery[data-type="masonry"]').each(function() {
            var $gallery = $(this);
            var columns = $gallery.data('columns') || 3;
            
            $gallery.imagesLoaded(function() {
                $gallery.masonry({
                    itemSelector: '.mmp-gallery-item',
                    percentPosition: true,
                    columnWidth: '.mmp-gallery-item',
                    gutter: 20,
                    horizontalOrder: true
                });
            });
        });
    }

    /**
     * Initialize Lightbox
     */
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

    /**
     * Initialize Layout
     */
    function initLayout() {
        $('.mmp-gallery').each(function() {
            var $gallery = $(this);
            var type = $gallery.data('type') || 'grid';

            if (type === 'masonry') {
                initMasonry();
            }
            // Grid layout is handled by CSS
            // Slider functionality temporarily disabled
        });
    }

    /**
     * Initialize when document is ready
     */
    $(document).ready(function() {
        initLayout();
        initLightbox();

        // Handle window resize for masonry
        let resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                $('.mmp-gallery[data-type="masonry"]').masonry('layout');
            }, 250);
        });
    });

})(jQuery);

/* Slider functionality commented out for future implementation
function initSlider() {
    $('.mmp-gallery[data-type="slider"]').each(function() {
        var $gallery = $(this);
        var autoplay = $gallery.data('autoplay') || false;
        var speed = $gallery.data('speed') || 3000;

        $gallery.imagesLoaded(function() {
            $gallery.slick({
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
*/