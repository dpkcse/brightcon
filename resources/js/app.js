import './bootstrap';
import 'bootstrap';
import '@fortawesome/fontawesome-free/css/all.min.css';
import '../css/app.css';

document.querySelectorAll('[data-partner-carousel]').forEach((carousel) => {
    const slides = [...carousel.querySelectorAll('[data-partner-slide]')];
    const selectors = [...carousel.querySelectorAll('[data-partner-tab]')];
    const indicator = carousel.querySelector('[data-partner-indicator]');

    if (slides.length < 2) return;

    let active = Math.max(slides.findIndex((slide) => slide.classList.contains('is-active') && !slide.hidden), 0);
    const show = (index) => {
        active = (index + slides.length) % slides.length;
        slides.forEach((slide, slideIndex) => {
            const selected = slideIndex === active;
            slide.classList.toggle('is-active', selected);
            slide.toggleAttribute('hidden', !selected);
            slide.setAttribute('aria-hidden', String(!selected));
        });
        selectors.forEach((selector, selectorIndex) => {
            const selected = selectorIndex === active;
            selector.classList.toggle('is-active', selected);
            selector.toggleAttribute('aria-current', selected);
        });
        if (indicator) indicator.textContent = `${String(active + 1).padStart(2, '0')} / ${String(slides.length).padStart(2, '0')}`;
    };

    show(active);
    carousel.classList.add('is-enhanced');
    carousel.querySelector('[data-partner-prev]')?.addEventListener('click', () => show(active - 1));
    carousel.querySelector('[data-partner-next]')?.addEventListener('click', () => show(active + 1));
    selectors.forEach((selector, index) => selector.addEventListener('click', () => {
        const slideIndex = Number.parseInt(selector.dataset.slideIndex, 10);
        show(Number.isInteger(slideIndex) ? slideIndex : index);
    }));
    carousel.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowLeft') { event.preventDefault(); show(active - 1); }
        if (event.key === 'ArrowRight') { event.preventDefault(); show(active + 1); }
    });
});

document.querySelectorAll('[data-gallery-grid]').forEach((grid) => {
    const lightbox = document.querySelector('[data-gallery-lightbox]');
    const items = [...grid.querySelectorAll('[data-gallery-item]')];
    if (!lightbox || !items.length) return;

    const image = lightbox.querySelector('[data-gallery-lightbox-image]');
    const title = lightbox.querySelector('[data-gallery-lightbox-title]');
    const category = lightbox.querySelector('[data-gallery-lightbox-category]');
    const count = lightbox.querySelector('[data-gallery-lightbox-count]');
    const previous = lightbox.querySelector('[data-gallery-prev]');
    const next = lightbox.querySelector('[data-gallery-next]');
    const closeButton = lightbox.querySelector('.gallery-lightbox-close');
    let activeIndex = 0;
    let trigger = null;
    let touchStartX = 0;

    const show = (index) => {
        activeIndex = (index + items.length) % items.length;
        const item = items[activeIndex];
        image.src = item.dataset.fullImage;
        image.alt = item.dataset.alt;
        title.textContent = item.dataset.title;
        category.textContent = item.dataset.category;
        count.textContent = `${activeIndex + 1} / ${items.length}`;
        const hasMultipleItems = items.length > 1;
        previous.hidden = !hasMultipleItems;
        next.hidden = !hasMultipleItems;
    };

    const open = (index, opener) => {
        trigger = opener;
        show(index);
        lightbox.hidden = false;
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.classList.add('gallery-lightbox-open');
        closeButton.focus();
    };

    const close = () => {
        lightbox.hidden = true;
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('gallery-lightbox-open');
        image.removeAttribute('src');
        trigger?.focus();
    };

    items.forEach((item, index) => item.addEventListener('click', () => open(index, item)));
    lightbox.querySelectorAll('[data-gallery-close]').forEach((control) => control.addEventListener('click', close));
    previous.addEventListener('click', () => show(activeIndex - 1));
    next.addEventListener('click', () => show(activeIndex + 1));

    lightbox.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') close();
        if (event.key === 'ArrowLeft') { event.preventDefault(); show(activeIndex - 1); }
        if (event.key === 'ArrowRight') { event.preventDefault(); show(activeIndex + 1); }
        if (event.key === 'Tab') {
            const focusable = [...lightbox.querySelectorAll('button:not([hidden])')];
            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
            if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
        }
    });

    lightbox.addEventListener('touchstart', (event) => { touchStartX = event.changedTouches[0].clientX; }, { passive: true });
    lightbox.addEventListener('touchend', (event) => {
        const distance = event.changedTouches[0].clientX - touchStartX;
        if (Math.abs(distance) < 50 || items.length < 2) return;
        show(activeIndex + (distance < 0 ? 1 : -1));
    }, { passive: true });
});
