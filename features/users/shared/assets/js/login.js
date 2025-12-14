/**
 * Login/Register Page Interactions
 */

function toggleAuth(view) {
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const mainCard = document.querySelector('.login-card');
    const wrapper = document.querySelector('.login-wrapper');
    
    // Get URLs from data attributes
    const loginUrl = mainCard.dataset.loginUrl;
    const registerUrl = mainCard.dataset.registerUrl;

    // Get the site name part (after the dash) or default
    const parts = document.title.split('—');
    const siteName = parts.length > 1 ? parts[1].trim() : 'SulamProject';
    
    // Clear any existing timeouts to prevent race conditions
    if (window.authTransitionTimeout) {
        clearTimeout(window.authTransitionTimeout);
    }

    if (view === 'register') {
        wrapper.classList.add('register-mode');
        loginForm.style.display = 'none';
        registerForm.style.display = 'block';
        document.title = 'Register — ' + siteName;
        // Update URL without reloading
        if (registerUrl) {
            window.history.pushState({path: registerUrl}, '', registerUrl);
        }
    } else {
        wrapper.classList.remove('register-mode');
        registerForm.style.display = 'none';
        loginForm.style.display = 'block';
        document.title = 'Login — ' + siteName;
        // Update URL without reloading
        if (loginUrl) {
            window.history.pushState({path: loginUrl}, '', loginUrl);
        }
    }
}

function initPasswordToggles() {
    const toggleButtons = document.querySelectorAll('[data-toggle-password]');
    if (!toggleButtons.length) return;

    toggleButtons.forEach((button) => {
        const targetId = button.getAttribute('data-toggle-password');
        const input = targetId ? document.getElementById(targetId) : null;
        if (!input) return;

        button.addEventListener('click', () => {
            const showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';

            button.setAttribute('aria-pressed', showing ? 'false' : 'true');
            button.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');

            const icon = button.querySelector('i');
            if (icon) {
                if (showing) {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                } else {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            }

            // Keep focus on the input for smoother typing
            input.focus();
        });
    });
}

function initLoginEventsCarousel() {
    const container = document.querySelector('[data-login-events-carousel]');
    const dataEl = document.getElementById('loginEventsCarouselData');
    if (!container || !dataEl) return;

    let events;
    try {
        events = JSON.parse(dataEl.textContent || '[]');
    } catch (e) {
        events = [];
    }

    if (!Array.isArray(events) || events.length === 0) return;

    const dayEl = container.querySelector('[data-event-day]');
    const monthEl = container.querySelector('[data-event-month]');
    const titleEl = container.querySelector('[data-event-title]');
    const metaEl = container.querySelector('[data-event-meta]');
    const imageEl = container.querySelector('[data-event-image]');
    const fallbackIconEl = container.querySelector('[data-event-fallback-icon]');
    const dotsWrap = container.querySelector('[data-carousel-dots]');

    function render(index) {
        const evt = events[index];
        if (!evt) return;

        if (dayEl) dayEl.textContent = evt.day || '';
        if (monthEl) monthEl.textContent = evt.month || '';
        if (titleEl) titleEl.textContent = evt.title || '';
        if (metaEl) metaEl.textContent = evt.meta || '';

        const hasImage = !!(evt.image && String(evt.image).trim() !== '');
        if (imageEl) {
            if (hasImage) {
                imageEl.setAttribute('src', evt.image);
                imageEl.style.display = '';
            } else {
                imageEl.removeAttribute('src');
                imageEl.style.display = 'none';
            }
        }
        if (fallbackIconEl) {
            fallbackIconEl.style.display = hasImage ? 'none' : '';
        }

        if (dotsWrap) {
            const dots = dotsWrap.querySelectorAll('[data-carousel-index]');
            dots.forEach((d) => {
                const isActive = String(index) === d.getAttribute('data-carousel-index');
                d.classList.toggle('active', isActive);
            });
        }
    }

    if (dotsWrap) {
        dotsWrap.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-carousel-index]');
            if (!btn) return;
            const index = parseInt(btn.getAttribute('data-carousel-index'), 10);
            if (Number.isNaN(index)) return;
            render(index);
        });
    }

    render(0);
}

document.addEventListener('DOMContentLoaded', () => {
    initPasswordToggles();
    initLoginEventsCarousel();
});
