/*!
 * boat.js — product-detail gallery (classic script)
 * - Fancyapps if available; manual fallback otherwise
 * - Desktop: nav arrows only
 * - Mobile: thumb swipe on main carousel
 */
(function () {
    'use strict';

    function init(root) {
        root = root || document;

        var hasFancy =
            typeof window !== 'undefined' &&
            typeof window.Carousel !== 'undefined' &&
            typeof window.Fancybox !== 'undefined';

        if (hasFancy) initFancy(root);
        else initManual(root);
    }

    /* ---------- Fancyapps ---------- */
    function initFancy(root) {
        var mainEl  = root.querySelector('#mainCarousel');
        var thumbEl = root.querySelector('#thumbCarousel');
        if (!mainEl) return;

        var main = new window.Carousel(mainEl, {
            Dots: false,
            infinite: false,
            Navigation: true // CSS hides this on mobile
        });

        if (thumbEl) {
            new window.Carousel(thumbEl, {
                Dots: false,
                infinite: false,
                Navigation: false,
                slidesPerPage: 'auto',
                center: false,
                Sync: { target: main, friction: 0.2 }
            });

            // click thumb → jump main
            thumbEl.addEventListener('click', function (e) {
                var slide = e.target.closest('.f-carousel__slide');
                if (!slide) return;
                var nodes = Array.prototype.slice.call(thumbEl.querySelectorAll('.f-carousel__slide'));
                var idx = nodes.indexOf(slide);
                if (idx > -1) main.slideTo(idx);
            });
        }

        window.Fancybox.bind('#mainCarousel .f-carousel__slide[data-fancybox="gallery"]', {
            groupAll: true,
            Images: { zoom: false },
            Toolbar: { display: ['counter','zoom','slideshow','fullscreen','download','close'] }
        });

        ensureActiveThumbStyles();
    }

    /* ---------- Manual fallback ---------- */
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
                var thumbW   = thumbSlides[0].offsetWidth;
                var gap      = 10;
                var vpW      = thumbViewport.offsetWidth;
                var perView  = Math.max(1, Math.floor(vpW / (thumbW + gap)));
                var target   = 0;

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

        // Click thumbs
        Array.prototype.forEach.call(thumbSlides, function (thumb, i) {
            thumb.addEventListener('click', function () { showSlide(i); });
        });

        // Init
        showSlide(0);

        // --- NAV BUTTONS (desktop-only via CSS) ---
        addDesktopNavButtons(mainEl, function prev() {
            showSlide((currentIndex - 1 + mainSlides.length) % mainSlides.length);
        }, function next() {
            showSlide((currentIndex + 1) % mainSlides.length);
        });

        // --- SWIPE ON MOBILE (thumb swipe on main area) ---
        enableSwipe(mainEl, {
            onLeft: function () { showSlide((currentIndex + 1) % mainSlides.length); },
            onRight: function () { showSlide((currentIndex - 1 + mainSlides.length) % mainSlides.length); }
        });

        // Better touch behavior
        Array.prototype.forEach.call(mainEl.querySelectorAll('img'), function (img) {
            img.draggable = false;
        });

        ensureActiveThumbStyles();
    }

    /* ---------- helpers ---------- */
    function addDesktopNavButtons(container, onPrev, onNext) {
        // If markup already includes nav buttons, wire them up
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
        var startX = 0;
        var dx = 0;
        var active = false;
        var threshold = 40; // px to trigger a slide
        var locked = false;

        el.addEventListener('pointerdown', function (e) {
            if (e.pointerType !== 'touch' && !('ontouchstart' in window)) return; // prefer touch
            active = true;
            locked = false;
            startX = e.clientX;
            dx = 0;
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

    // Auto-run
    if (document.readyState !== 'loading') init(document);
    else document.addEventListener('DOMContentLoaded', function () { init(document); });
})();
