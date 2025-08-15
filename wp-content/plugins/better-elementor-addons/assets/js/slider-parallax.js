!(function ($) {
    "use strict";

    /* ===============================  Slider-parallax  =============================== */

    function betterSliderparallaxy($scope, $) {
        var sliderparallax;
        var sliderparallaxOptions = {
            speed: 1300,
            autoplay: true,
            parallax: true,
            mousewheel: true,
            loop: true,

            on: {
                init: function () {
                    var swiper = this;
                    for (var i = 0; i < swiper.slides.length; i++) {
                        jQuery(swiper.slides[i])
                            .find('.better-bg-img')
                            .attr({
                                'data-swiper-parallax': 0.75 * swiper.width
                            });
                    }
                },
                resize: function () {
                    this.update();
                }
            },

            pagination: {
                el: '.showcase-full .parallax-slider .swiper-pagination',
                type: 'fraction',
                clickable: true,
                type: 'bullets', // Set the pagination type to 'bullets'
            },

            navigation: {
                nextEl: '.showcase-full .parallax-slider .next-ctrl',
                prevEl: '.showcase-full .parallax-slider .prev-ctrl'
            }
        };

        sliderparallax = new Swiper('.showcase-full .parallax-slider', sliderparallaxOptions);

    }

    function betterSliderExtras($scope, $) {

        /* ===============================  Var Background image  =============================== */

        var pageSection = $(".better-bg-img, section");
        pageSection.each(function (indx) {

            if ($(this).attr("data-background")) {
                $(this).css("background-image", "url(" + $(this).data("background") + ")");
            }
        });

        /* ===============================  SPLITTING TEXT  =============================== */

        if (typeof Splitting === 'function') {
            Splitting();
        }

    }

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bea-slider-parallax.default', betterSliderparallaxy);
        elementorFrontend.hooks.addAction('frontend/element_ready/bea-slider-parallax.default', betterSliderExtras);

    });

})(jQuery);