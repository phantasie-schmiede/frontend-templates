document.addEventListener('DOMContentLoaded', () => {
    const skipLink = document.querySelector('.skip-link[href^="#"]');

    if (null === skipLink) {
        return;
    }

    const targetId = skipLink.getAttribute('href').slice(1);
    const target = document.getElementById(targetId);

    if (null === target) {
        return;
    }

    skipLink.addEventListener('click', () => {
        requestAnimationFrame(() => {
            target.focus({ preventScroll: true });
        });
    });

    skipLink.addEventListener('keydown', (event) => {
        if (' ' === event.key || 'Spacebar' === event.key) {
            event.preventDefault();
            skipLink.click();
        }
    });
});
