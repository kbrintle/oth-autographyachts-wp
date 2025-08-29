/*!
 * boat.js — product-detail gallery
 * - Fancyapps if available (v5 or v3); manual fallback otherwise
 * - Desktop: nav arrows only (CSS controls visibility/size/hover)
 * - Mobile: swipe on main area
 * - Main image click ⇒ lightbox (navigate, fullscreen, close)
 */
(function () {

    'use strict';
    // ---------- bootstrap ----------
    function init(root) {
        root = root || document;

        var hasCarousel = typeof window !== 'undefined' && typeof window.Carousel !== 'undefined';
        var hasFancybox = typeof window !== 'undefined' && (typeof window.Fancybox !== 'undefined' || (window.jQuery && window.jQuery.fancybox));

        if (hasCarousel && hasFancybox) initFancy(root);
        else initManual(root);
    }

    // ============================================================
    // Fancyapps path (Carousel + Fancybox v5 or v3)
    // ============================================================
    function initFancy(root) {
        var mainEl  = root.querySelector('#mainCarousel');
        var thumbEl = root.querySelector('#thumbCarousel');
        if (!mainEl) return;

        // Main carousel
        var main = new window.Carousel(mainEl, {
            Dots: false,
            infinite: false,
            Navigation: true // visibility handled by CSS
        });

        // Thumbs + sync
        if (thumbEl) {
            new window.Carousel(thumbEl, {
                Dots: false,
                infinite: false,
                Navigation: false,
                slidesPerPage: 'auto',
                center: false,
                Sync: { target: main, friction: 0.2 }
            });

            thumbEl.addEventListener('click', function (e) {
                var slide = e.target.closest('.f-carousel__slide');
                if (!slide) return;
                var nodes = Array.prototype.slice.call(thumbEl.querySelectorAll('.f-carousel__slide'));
                var idx = nodes.indexOf(slide);
                if (idx > -1) main.slideTo(idx);
            });
        }

        // Build slides (with captions)
        var slides = Array.prototype.map.call(
            mainEl.querySelectorAll('.f-carousel__slide img'),
            function (img) { return { src: img.currentSrc || img.src, type: 'image', caption: img.alt || '' }; }
        );

        // Guard against drag/click on nav
        var dragged = false;
        mainEl.addEventListener('pointerdown', function () { dragged = false; }, { passive: true });
        mainEl.addEventListener('pointermove', function () { dragged = true; },  { passive: true });

        // Open lightbox on main image click
        mainEl.addEventListener('click', function (e) {
            if (dragged) return;
            if (e.target.closest('.f-carousel__nav')) return;

            var slide = e.target.closest('.f-carousel__slide');
            if (!slide) return;

            var nodes = Array.prototype.slice.call(mainEl.querySelectorAll('.f-carousel__slide'));
            var idx = nodes.indexOf(slide);
            if (idx < 0) return;

            openAtIndex(slides, idx); // v5/v3 aware
        });

        // Cursor hint + no drag ghost
        Array.prototype.forEach.call(mainEl.querySelectorAll('img'), function (img) {
            img.style.cursor = 'zoom-in';
            img.draggable = false;
        });

        ensureActiveThumbStyles();
    }

    // Use Fancybox v5 if present; else try v3; else noop (manual fallback handles)
    function openAtIndex(slides, index) {
        if (window.Fancybox && typeof window.Fancybox.show === 'function') {
            window.Fancybox.show(slides, {
                Toolbar: { display: ['counter', 'slideshow', 'zoom', 'fullscreen', 'close'] },
                Thumbs: false,
                infinite: false,
                dragToClose: false,
                startIndex: index
            });
            return;
        }
        if (window.jQuery && window.jQuery.fancybox && typeof window.jQuery.fancybox.open === 'function') {
            window.jQuery.fancybox.open(
                slides.map(function (s) { return { src: s.src, type: 'image', caption: s.caption || '' }; }),
                {
                    infobar: true,
                    buttons: ['zoom', 'fullScreen', 'close'],
                    loop: false,
                    animationEffect: 'zoom-in-out',
                    transitionEffect: 'fade',
                    index: index
                }
            );
            return;
        }
    }

    // ============================================================
    // Manual fallback path
    // ============================================================
    function initManual(root) {
        var mainEl    = root.querySelector('#mainCarousel');
        var thumbEl   = root.querySelector('#thumbCarousel');
        if (!mainEl || !thumbEl) return;

        var mainTrack   = mainEl.querySelector('.f-carousel__track');
        var mainSlides  = mainEl.querySelectorAll('.f-carousel__slide');
        var thumbSlides = thumbEl.querySelectorAll('.f-carousel__slide');
        if (!mainTrack || !mainSlides.length) return;

        var thumbTrack    = thumbEl.querySelector('.f-carousel__track');
        var thumbViewport = thumbEl.querySelector('.f-carousel__viewport');

        var currentIndex = 0;

        function showSlide(index) {
            if (index < 0 || index >= mainSlides.length) return;
            currentIndex = index;

            var slideW = (mainSlides[0].offsetWidth || mainEl.offsetWidth);
            var offset = -index * slideW;
            mainTrack.style.transform  = 'translateX(' + offset + 'px)';
            mainTrack.style.transition = 'transform 0.3s ease';

            Array.prototype.forEach.call(thumbSlides, function (t, i) {
                t.classList.toggle('is-active', i === index);
            });

            if (thumbTrack && thumbViewport && thumbSlides[index]) {
                var thumbW  = thumbSlides[0].offsetWidth;
                var gap     = 10;
                var vpW     = thumbViewport.offsetWidth;
                var perView = Math.max(1, Math.floor(vpW / (thumbW + gap)));
                var target  = 0;

                if (index > perView / 2) {
                    var center = index - Math.floor(perView / 2);
                    target = -center * (thumbW + gap);
                    var max = -(thumbSlides.length - perView) * (thumbW + gap);
                    target = Math.max(target, max);
                }

                thumbTrack.style.transform  = 'translateX(' + target + 'px)';
                thumbTrack.style.transition = 'transform 0.3s ease';
            }
        }

        // Thumbs click
        Array.prototype.forEach.call(thumbSlides, function (thumb, i) {
            thumb.addEventListener('click', function () { showSlide(i); });
        });

        showSlide(0);

        // Desktop nav buttons (visibility via CSS)
        addDesktopNavButtons(
            mainEl,
            function () { showSlide((currentIndex - 1 + mainSlides.length) % mainSlides.length); },
            function () { showSlide((currentIndex + 1) % mainSlides.length); }
        );

        // Swipe on mobile
        enableSwipe(mainEl, {
            onLeft:  function () { showSlide((currentIndex + 1) % mainSlides.length); },
            onRight: function () { showSlide((currentIndex - 1 + mainSlides.length) % mainSlides.length); }
        });

        // Manual lightbox on click
        var images = Array.prototype.map.call(
            mainEl.querySelectorAll('.f-carousel__slide img'),
            function (img) { return img.currentSrc || img.src; }
        );

        var dragged = false;
        mainEl.addEventListener('pointerdown', function () { dragged = false; }, { passive: true });
        mainEl.addEventListener('pointermove', function () { dragged = true; },  { passive: true });

        mainEl.addEventListener('click', function (e) {
            if (dragged) return;
            if (e.target.closest('.f-carousel__nav')) return;

            var slide = e.target.closest('.f-carousel__slide');
            if (!slide) return;

            var nodes = Array.prototype.slice.call(mainEl.querySelectorAll('.f-carousel__slide'));
            var idx = nodes.indexOf(slide);
            if (idx < 0) return;

            openFallbackLightbox(images, idx);
        });

        Array.prototype.forEach.call(mainEl.querySelectorAll('img'), function (img) {
            img.style.cursor = 'zoom-in';
            img.draggable = false;
        });

        ensureActiveThumbStyles();
    }

    // ============================================================
    // Helpers
    // ============================================================
    function addDesktopNavButtons(container, onPrev, onNext) {
        var prevBtn = container.querySelector('.f-carousel__nav--prev');
        var nextBtn = container.querySelector('.f-carousel__nav--next');

        if (!prevBtn || !nextBtn) {
            prevBtn = document.createElement('button');
            nextBtn = document.createElement('button');
            prevBtn.type = nextBtn.type = 'button';
            prevBtn.className = 'f-carousel__nav f-carousel__nav--prev';
            nextBtn.className = 'f-carousel__nav f-carousel__nav--next';
            prevBtn.setAttribute('aria-label', 'Previous image');
            nextBtn.setAttribute('aria-label', 'Next image');
            prevBtn.innerHTML = '&#8249;';
            nextBtn.innerHTML = '&#8250;';
            container.appendChild(prevBtn);
            container.appendChild(nextBtn);
        }
        prevBtn.addEventListener('click', onPrev);
        nextBtn.addEventListener('click', onNext);
    }

    function enableSwipe(el, handlers) {
        var startX = 0, dx = 0, active = false, locked = false;
        var threshold = 40;

        el.addEventListener('pointerdown', function (e) {
            if (e.pointerType !== 'touch' && !('ontouchstart' in window)) return;
            active = true; locked = false; startX = e.clientX; dx = 0;
            try { el.setPointerCapture(e.pointerId); } catch (_) {}
        });

        el.addEventListener('pointermove', function (e) {
            if (!active) return;
            dx = e.clientX - startX;
            if (!locked && Math.abs(dx) > threshold) {
                locked = true;
                if (dx < 0 && handlers.onLeft)  handlers.onLeft();
                if (dx > 0 && handlers.onRight) handlers.onRight();
            }
        });

        ['pointerup','pointercancel','pointerleave'].forEach(function (type) {
            el.addEventListener(type, function () { active = false; dx = 0; locked = false; });
        });
    }

    function ensureActiveThumbStyles() {
        if (document.getElementById('boat-thumb-active-style')) return;
        var style = document.createElement('style');
        style.id = 'boat-thumb-active-style';
        style.textContent =
            '#thumbCarousel .f-carousel__slide.is-nav-selected img,' +
            '#thumbCarousel .f-carousel__slide.is-active img{' +
            '  border:2px solid rgba(53,149,209,1);opacity:1;' +
            '}';
        document.head.appendChild(style);
    }

    // ---------- Fallback lightbox ----------
    function openFallbackLightbox(srcList, startIndex) {
        var idx = startIndex || 0;

        var overlay = document.createElement('div');
        overlay.className = 'boat-lightbox';
        overlay.innerHTML = [
            '<div class="lightbox-container">',
            '<button class="lightbox-close" aria-label="Close">&times;</button>',
            '<button class="lightbox-prev" aria-label="Previous">&#8249;</button>',
            '<button class="lightbox-next" aria-label="Next">&#8250;</button>',
            '<button class="lightbox-full" aria-label="Fullscreen" title="Fullscreen">⤢</button>',
            '<div class="lightbox-track"></div>',
            '<div class="lightbox-counter"></div>',
            '</div>'
        ].join('');

        var track   = overlay.querySelector('.lightbox-track');
        var counter = overlay.querySelector('.lightbox-counter');

        function render() {
            track.innerHTML = '';
            var img = document.createElement('img');
            img.src = srcList[idx];
            img.alt = '';
            img.style.maxWidth  = '90vw';
            img.style.maxHeight = '90vh';
            img.style.objectFit = 'contain';
            track.appendChild(img);
            counter.textContent = (idx + 1) + ' / ' + srcList.length;
        }

        function close() {
            document.removeEventListener('keydown', onKeys);
            overlay.remove();
            document.body.style.overflow = '';
        }

        function onKeys(e) {
            if (e.key === 'Escape') close();
            else if (e.key === 'ArrowRight') { idx = (idx + 1) % srcList.length; render(); }
            else if (e.key === 'ArrowLeft')  { idx = (idx - 1 + srcList.length) % srcList.length; render(); }
        }

        overlay.querySelector('.lightbox-close').addEventListener('click', close);
        overlay.querySelector('.lightbox-prev').addEventListener('click', function () {
            idx = (idx - 1 + srcList.length) % srcList.length; render();
        });
        overlay.querySelector('.lightbox-next').addEventListener('click', function () {
            idx = (idx + 1) % srcList.length; render();
        });
        overlay.querySelector('.lightbox-full').addEventListener('click', function () {
            var el = overlay;
            if (document.fullscreenElement) { document.exitFullscreen && document.exitFullscreen(); }
            else {
                (el.requestFullscreen || el.webkitRequestFullscreen || el.msRequestFullscreen || function(){})
                    .call(el);
            }
        });

        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) close(); // click outside image closes
        });

        document.addEventListener('keydown', onKeys);
        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';
        render();
    }

    // Auto-run
    if (document.readyState !== 'loading') init(document);
    else document.addEventListener('DOMContentLoaded', function () { init(document); });
})();
