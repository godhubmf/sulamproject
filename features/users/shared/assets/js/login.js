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
