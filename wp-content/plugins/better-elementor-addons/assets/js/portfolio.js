(function($) {
    "use strict";

	
	function beaPortfolioFilter($scope,$) {
		$scope.find('.better-portfolio').each(function () {
			var galleryobj= $(this).find('.gallery');
			galleryobj.isotope({
				itemSelector: '.items'
			});
			var gallery = galleryobj.isotope();
			$(this).find('.filtering span').on('click', function () {
				var filterValue = $(this).attr('data-filter');
				gallery.isotope({ filter: filterValue });
			});

			$(this).find('.filtering span').on('click', function () {
				$(this).addClass('active').siblings().removeClass('active');
			});
			
		});
		
	}
	function beaPortfolioHoverImages($scope,$) {
		$scope.find('.better-portfolio.style-1').each(function () {
			var item = $(this).find('.item');
			var img = $(this).find('.tab-img');
			var widget = $(this);
			$(this).find('.item').on('mouseenter', function () {
				var tab_id = $(this).attr('data-tab');
				// remove item class
				item.removeClass('current');
				$(this).addClass('current');
				// remove image class
				img.removeClass('current');
				widget.find("#" + tab_id).addClass('current'); 	

				if ($(this).hasClass('current')) {
					return false;
				}	
			}); 
			
		});
	}
	function betterportfolio($scope, $) {

		/* ===============================  Swiper slider  =============================== */

        var portfoliostyle8 = new Swiper(".better-portfolio.style-8 .work-curs", {
            slidesPerView: "auto",
            speed: 1000,
            loop: true,
            spaceBetween: 30,
            pagination: {
                el: ".better-portfolio.style-8 .swiper-pagination",
                clickable: true,
            },

            navigation: {
                nextEl: '.better-portfolio.style-8 .next-ctrl',
                prevEl: '.better-portfolio.style-8 .prev-ctrl'
            },
            breakpoints: {
                500: {
                    slidesPerView: 1
                },
                700: {
                    slidesPerView: 1.5
                }
            }
        });

		var portfoliostyle5 = new Swiper('.better-portfolio.style-5 .swiper-container', {
			spaceBetween: 0,
			speed: 1000,
			loop: true,
	
			breakpoints: {
				320: {
					slidesPerView: 1,
					spaceBetween: 0
				},
				767: {
					slidesPerView: 1,
					spaceBetween: 0
				},
				991: {
					slidesPerView: 2,
					spaceBetween: 0
				},
				1024: {
					slidesPerView: 3,
					spaceBetween: 0
				}
			},
	
			navigation: {
				nextEl: '.better-portfolio.style-5 .next-ctrl',
				prevEl: '.better-portfolio.style-5 .prev-ctrl'
			},
		});

		var parallaxSlider;
		var parallaxSliderOptions = {
			speed: 1000,
			autoplay: true,
			parallax: true,
			loop: true,
	
			on: {
				init: function () {
					var swiper = this;
					for (var i = 0; i < swiper.slides.length; i++) {
						$(swiper.slides[i])
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
				el: '.better-slider.style-2 .parallax-slider .swiper-pagination',
				dynamicBullets: true,
				clickable: true
			},
	
			navigation: {
				nextEl: '.better-slider.style-2 .parallax-slider .next-ctrl',
				prevEl: '.better-slider.style-2 .parallax-slider .prev-ctrl'
			}
		};
		parallaxSlider = new Swiper('.better-slider.style-2 .parallax-slider', parallaxSliderOptions);
	
		var swiperWorkSlider = new Swiper('.better-portfolio.style-3.slider-scroll .swiper-container', {
			slidesPerView: 2,
			spaceBetween: 100,
			mousewheel: true,
			centeredSlides: true,
			speed: 1000,
			loop: true,
	
			breakpoints: {
				320: {
					slidesPerView: 1
				},
				480: {
					slidesPerView: 1
				},
				640: {
					slidesPerView: 2
				},
				991: {
					slidesPerView: 2
				}
			},
	
			pagination: {
				el: '.better-portfolio.style-3.slider-scroll .swiper-pagination',
			},
	
			navigation: {
				nextEl: '.better-portfolio.style-3.slider-scroll .next-ctrl',
				prevEl: '.better-portfolio.style-3.slider-scroll .prev-ctrl'
			}
		});
	
		var swiperWorkMetro = new Swiper('.better-portfolio.style-2.metro .swiper-container', {
			slidesPerView: 2,
			spaceBetween: 0,
			speed: 1000,
			loop: true,
			centeredSlides: true,
	
			breakpoints: {
				320: {
					slidesPerView: 1,
					spaceBetween: 0
				},
				640: {
					slidesPerView: 1,
					spaceBetween: 0
				},
				767: {
					slidesPerView: 2,
					spaceBetween: 0
				}
				,
				991: {
					slidesPerView: 2,
					spaceBetween: 0
				}
			},
	
			pagination: {
				el: '.better-portfolio.style-2.metro .swiper-pagination',
				type: 'progressbar',
			},
	
			navigation: {
				nextEl: '.better-portfolio.style-2.metro .swiper-button-next',
				prevEl: '.better-portfolio.style-2.metro .swiper-button-prev'
			},
		});
	
		swiperWorkMetro.on('slideChange', function () {
			var activeslide = swiperWorkMetro.realIndex;
			$(".activeslide").html("0" + (activeslide + 1));
		});

		/* ===============================  Var Background image  =============================== */

		var pageSection = $(".better-bg-img, section");
		pageSection.each(function (indx) {

			if ($(this).attr("data-background")) {
				$(this).css("background-image", "url(" + $(this).data("background") + ")");
			}
		});

		/* ===============================  SPLITTING TEXT  =============================== */

		$(window).load(function () {
            Splitting();
        });

	}
	
    /**
 	 * @param $scope The Widget wrapper element as a jQuery element
	 * @param $ The jQuery alias
	 */ 
	var WidgetHelloWorldHandler = function( $scope, $ ) {
		console.log( $scope );
	};

   
	function betterportfolio7($scope, $) {
        $('.better-portfolio.style-7 .gallery').imagesLoaded( function() {$('.better-portfolio.style-7 .gallery').isotope()});

        // filter items when filter link is clicked
        $('.better-portfolio.style-7 .filter span').on('click', function() {
            var selector = $(this).attr('data-filter');
            $('.gallery').isotope({
                itemSelector: '.items',
                filter: selector,
            });
            $(".better-portfolio.style-7 .filter span").removeClass("active");
            $(this).addClass("active");
            return false;
        });
    };
	



 	// Make sure you run this code under Elementor.
	$( window ).on( 'elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/hello-world.default', WidgetHelloWorldHandler );
		elementorFrontend.hooks.addAction('frontend/element_ready/better-portfolio.default', beaPortfolioFilter);
        elementorFrontend.hooks.addAction('frontend/element_ready/better-portfolio.default', betterportfolio);
        elementorFrontend.hooks.addAction('frontend/element_ready/better-portfolio.default', beaPortfolioHoverImages);
        elementorFrontend.hooks.addAction('frontend/element_ready/better-portfolio.default', betterportfolio7);
	});

})(jQuery);