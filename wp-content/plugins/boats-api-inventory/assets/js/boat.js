/**
 * boat (product-detail)
 * Simple image gallery with thumbnail sync
 */
export default function init(root = document) {
    console.log('=== BOAT COMPONENT INIT ===');
    const mainCarouselEl = root.querySelector('#mainCarousel');
    const thumbCarouselEl = root.querySelector('#thumbCarousel');

    if (!mainCarouselEl || !thumbCarouselEl) {
        console.log('Missing carousel elements');
        return;
    }

    // Get all image elements
    const mainTrack = mainCarouselEl.querySelector('.f-carousel__track');
    const mainSlides = mainCarouselEl.querySelectorAll('.f-carousel__slide');
    const thumbSlides = thumbCarouselEl.querySelectorAll('.f-carousel__slide');

    console.log('Found slides - main:', mainSlides.length, 'thumbs:', thumbSlides.length);

    if (!mainTrack || mainSlides.length === 0) {
        console.log('No slides found');
        return;
    }

    // Get thumbnail track for scrolling
    const thumbTrack = thumbCarouselEl.querySelector('.f-carousel__track');
    const thumbViewport = thumbCarouselEl.querySelector('.f-carousel__viewport');

    // Simple manual carousel implementation
    let currentIndex = 0;

    function showSlide(index) {
        if (index < 0 || index >= mainSlides.length) return;

        currentIndex = index;
        console.log('Showing slide:', index);

        // Move the main carousel track using transform
        const slideWidth = mainSlides[0].offsetWidth || mainCarouselEl.offsetWidth;
        const offset = -index * slideWidth;
        mainTrack.style.transform = `translateX(${offset}px)`;
        mainTrack.style.transition = 'transform 0.3s ease';

        // Update thumbnail active state and scroll thumbnails
        thumbSlides.forEach((thumb, i) => {
            thumb.classList.toggle('is-active', i === index);
        });

        // Scroll thumbnails to show active one
        if (thumbTrack && thumbViewport && thumbSlides[index]) {
            const thumbWidth = thumbSlides[0].offsetWidth;
            const thumbGap = 10; // Gap between thumbnails
            const viewportWidth = thumbViewport.offsetWidth;
            const thumbsPerView = Math.floor(viewportWidth / (thumbWidth + thumbGap));

            // Calculate optimal position to center or show the active thumbnail
            let targetOffset = 0;

            if (index > thumbsPerView / 2) {
                // Calculate offset to keep active thumb visible
                const centerPosition = index - Math.floor(thumbsPerView / 2);
                targetOffset = -centerPosition * (thumbWidth + thumbGap);

                // Don't scroll past the last thumbnails
                const maxOffset = -(thumbSlides.length - thumbsPerView) * (thumbWidth + thumbGap);
                targetOffset = Math.max(targetOffset, maxOffset);
            }

            // Apply smooth scroll to thumbnail track
            thumbTrack.style.transform = `translateX(${targetOffset}px)`;
            thumbTrack.style.transition = 'transform 0.3s ease';
        }
    }


    // Add click handlers to thumbnails
    thumbSlides.forEach((thumb, index) => {
        thumb.addEventListener('click', () => {
            console.log('Thumbnail clicked:', index);
            showSlide(index);
        });
    });

    // Initialize showing first slide
    showSlide(0);

    // Add navigation buttons for main carousel
    const prevBtn = mainCarouselEl.querySelector('.f-carousel__nav--prev');
    const nextBtn = mainCarouselEl.querySelector('.f-carousel__nav--next');

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            const newIndex = (currentIndex - 1 + mainSlides.length) % mainSlides.length;
            showSlide(newIndex);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            const newIndex = (currentIndex + 1) % mainSlides.length;
            showSlide(newIndex);
        });
    }

    // Add keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            const newIndex = (currentIndex - 1 + mainSlides.length) % mainSlides.length;
            showSlide(newIndex);
        } else if (e.key === 'ArrowRight') {
            const newIndex = (currentIndex + 1) % mainSlides.length;
            showSlide(newIndex);
        }
    });

    // Add fullscreen lightbox functionality
    function createLightbox() {
        // Create lightbox container
        const lightbox = document.createElement('div');
        lightbox.className = 'boat-lightbox';
        lightbox.innerHTML = `
      <div class="lightbox-container">
        <button class="lightbox-close">&times;</button>
        <button class="lightbox-prev">&#8249;</button>
        <button class="lightbox-next">&#8250;</button>
        <div class="lightbox-track"></div>
        <div class="lightbox-counter"></div>
      </div>
    `;

        // Add all images to lightbox
        const lightboxTrack = lightbox.querySelector('.lightbox-track');
        mainSlides.forEach((slide, index) => {
            const img = slide.querySelector('img');
            if (img) {
                const lightboxSlide = document.createElement('div');
                lightboxSlide.className = 'lightbox-slide';
                lightboxSlide.innerHTML = `<img src="${img.src}" alt="${img.alt || ''}">`;
                lightboxSlide.style.display = index === currentIndex ? 'flex' : 'none';
                lightboxTrack.appendChild(lightboxSlide);
            }
        });

        document.body.appendChild(lightbox);
        document.body.style.overflow = 'hidden'; // Prevent background scrolling

        // Update counter
        const updateCounter = (index) => {
            const counter = lightbox.querySelector('.lightbox-counter');
            counter.textContent = `${index + 1} / ${mainSlides.length}`;
        };
        updateCounter(currentIndex);

        // Navigation in lightbox
        let lightboxIndex = currentIndex;

        const showLightboxSlide = (index) => {
            const slides = lightbox.querySelectorAll('.lightbox-slide');
            slides.forEach((slide, i) => {
                slide.style.display = i === index ? 'flex' : 'none';
            });
            lightboxIndex = index;
            updateCounter(index);
        };

        // Close lightbox
        const closeLightbox = () => {
            lightbox.remove();
            document.body.style.overflow = '';
        };

        // Event handlers
        lightbox.querySelector('.lightbox-close').addEventListener('click', closeLightbox);

        lightbox.querySelector('.lightbox-prev').addEventListener('click', () => {
            const newIndex = (lightboxIndex - 1 + mainSlides.length) % mainSlides.length;
            showLightboxSlide(newIndex);
        });

        lightbox.querySelector('.lightbox-next').addEventListener('click', () => {
            const newIndex = (lightboxIndex + 1) % mainSlides.length;
            showLightboxSlide(newIndex);
        });

        // Close on background click
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox || e.target.classList.contains('lightbox-container')) {
                closeLightbox();
            }
        });

        // Keyboard navigation in lightbox
        const handleLightboxKeys = (e) => {
            if (e.key === 'Escape') {
                closeLightbox();
                document.removeEventListener('keydown', handleLightboxKeys);
            } else if (e.key === 'ArrowLeft') {
                const newIndex = (lightboxIndex - 1 + mainSlides.length) % mainSlides.length;
                showLightboxSlide(newIndex);
            } else if (e.key === 'ArrowRight') {
                const newIndex = (lightboxIndex + 1) % mainSlides.length;
                showLightboxSlide(newIndex);
            }
        };
        document.addEventListener('keydown', handleLightboxKeys);
    }

    // Add click handler to main images to open lightbox
    mainSlides.forEach((slide) => {
        const img = slide.querySelector('img');
        if (img) {
            img.style.cursor = 'zoom-in';
            img.addEventListener('click', createLightbox);
        }
    });

    console.log('Simple carousel initialized with thumbnail scrolling and lightbox');
}
