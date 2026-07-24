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
