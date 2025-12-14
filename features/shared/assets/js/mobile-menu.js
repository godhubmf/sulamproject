/**
 * Mobile Menu (Hamburger) Navigation
 * 
 * Handles the mobile sidebar toggle functionality:
 * - Opens/closes sidebar overlay
 * - Manages backdrop
 * - Handles touch/click interactions
 * - Prevents body scroll when menu is open
 */

document.addEventListener('DOMContentLoaded', function() {
  const sidebar = document.querySelector('.sidebar');
  const toggle = document.querySelector('.mobile-menu-toggle');
  let backdrop = document.querySelector('.sidebar-backdrop');
  
  // If elements don't exist, exit
  if (!sidebar) {
    console.warn('Mobile menu: Sidebar not found');
    return;
  }
  
  // Create backdrop if it doesn't exist
  if (!backdrop) {
    backdrop = document.createElement('div');
    backdrop.className = 'sidebar-backdrop';
    document.body.appendChild(backdrop);
  }
  
  // Create toggle button if it doesn't exist
  if (!toggle) {
    const newToggle = document.createElement('button');
    newToggle.className = 'mobile-menu-toggle';
    newToggle.setAttribute('aria-label', 'Toggle menu');
    newToggle.innerHTML = '<i class="fas fa-bars"></i>';
    document.body.appendChild(newToggle);
  }
  
  const menuToggle = toggle || document.querySelector('.mobile-menu-toggle');
  
  // Toggle menu
  function toggleMenu() {
    const isOpen = sidebar.classList.contains('open');
    
    if (isOpen) {
      closeMenu();
    } else {
      openMenu();
    }
  }
  
  // Open menu
  function openMenu() {
    sidebar.classList.add('open');
    backdrop.classList.add('active');
    document.body.style.overflow = 'hidden'; // Prevent body scroll
    menuToggle.innerHTML = '<i class="fas fa-times"></i>'; // Change icon to X
  }
  
  // Close menu
  function closeMenu() {
    sidebar.classList.remove('open');
    backdrop.classList.remove('active');
    document.body.style.overflow = ''; // Restore body scroll
    menuToggle.innerHTML = '<i class="fas fa-bars"></i>'; // Change icon back to hamburger
  }
  
  // Event listeners
  if (menuToggle) {
    menuToggle.addEventListener('click', toggleMenu);
  }
  
  backdrop.addEventListener('click', closeMenu);
  
  // Close menu when clicking a navigation link (for single-page sections)
  const navLinks = sidebar.querySelectorAll('.nav a');
  navLinks.forEach(link => {
    link.addEventListener('click', () => {
      // Only close on mobile
      if (window.innerWidth < 1024) {
        closeMenu();
      }
    });
  });
  
  // Close menu on window resize to desktop
  let resizeTimer;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
      if (window.innerWidth >= 1024) {
        closeMenu();
      }
    }, 250);
  });
  
  // Handle escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && sidebar.classList.contains('open')) {
      closeMenu();
    }
  });
  
  // Touch swipe to close (optional enhancement)
  let touchStartX = 0;
  let touchEndX = 0;
  
  sidebar.addEventListener('touchstart', (e) => {
    touchStartX = e.changedTouches[0].screenX;
  }, { passive: true });
  
  sidebar.addEventListener('touchend', (e) => {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
  }, { passive: true });
  
  function handleSwipe() {
    // Swipe left to close
    if (touchStartX - touchEndX > 50) {
      closeMenu();
    }
  }
  
  console.log('Mobile menu initialized');
});
