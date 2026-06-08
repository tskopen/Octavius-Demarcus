document.addEventListener('DOMContentLoaded', () => {
    const lightbox = document.getElementById('lightbox');
    if (!lightbox) return;

    const img = lightbox.querySelector('img');
    const titleEl = lightbox.querySelector('.lightbox-title');
    const captionEl = lightbox.querySelector('.lightbox-caption');
    const closeBtn = lightbox.querySelector('.lightbox-close');

    function open(card) {
        img.src = card.dataset.lightboxSrc;
        img.alt = card.dataset.lightboxTitle || '';
        titleEl.textContent = card.dataset.lightboxTitle || '';
        captionEl.textContent = card.dataset.lightboxCaption || '';
        lightbox.classList.remove('hidden');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.classList.add('lightbox-open');
    }

    function close() {
        lightbox.classList.add('hidden');
        lightbox.setAttribute('aria-hidden', 'true');
        img.removeAttribute('src');
        document.body.classList.remove('lightbox-open');
    }

    document.querySelectorAll('.travel-card-clickable, .gallery-item-clickable').forEach((trigger) => {
        trigger.addEventListener('click', () => open(trigger));
        trigger.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                open(trigger);
            }
        });
    });

    closeBtn.addEventListener('click', close);
    lightbox.addEventListener('click', (event) => {
        if (event.target === lightbox) close();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !lightbox.classList.contains('hidden')) {
            close();
        }
    });
});
