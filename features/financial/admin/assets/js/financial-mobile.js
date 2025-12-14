/**
 * Financial Module - Mobile Enhancements
 * 
 * Provides mobile-specific enhancements for financial tables:
 * - Scroll hint removal after user interacts
 * - Touch-friendly table navigation
 * - Category entry management
 */

document.addEventListener('DOMContentLoaded', function() {
  
  // ===== TABLE SCROLL HINTS =====
  const tableWrappers = document.querySelectorAll('.table-responsive, .table-responsive--wide');
  
  tableWrappers.forEach(wrapper => {
    let hasScrolled = false;
    
    wrapper.addEventListener('scroll', function() {
      if (!hasScrolled && this.scrollLeft > 10) {
        hasScrolled = true;
        this.classList.add('scrolled');
      }
    }, { passive: true });
    
    // Also check if table is wider than wrapper
    const table = wrapper.querySelector('table');
    if (table) {
      const wrapperWidth = wrapper.clientWidth;
      const tableWidth = table.scrollWidth;
      
      // If table doesn't need scrolling, hide hint
      if (tableWidth <= wrapperWidth) {
        wrapper.classList.add('scrolled');
      }
    }
  });
  
  
  // ===== STICKY COLUMN SHADOW ON SCROLL =====
  tableWrappers.forEach(wrapper => {
    const stickyColumns = wrapper.querySelectorAll('.sticky-col-left');
    
    if (stickyColumns.length > 0) {
      wrapper.addEventListener('scroll', function() {
        const scrolled = this.scrollLeft > 0;
        
        stickyColumns.forEach(col => {
          if (scrolled) {
            col.style.boxShadow = '2px 0 8px rgba(0, 0, 0, 0.15)';
          } else {
            col.style.boxShadow = '2px 0 5px rgba(0, 0, 0, 0.05)';
          }
        });
      }, { passive: true });
    }
  });
  
  
  // ===== CATEGORY ENTRY MANAGEMENT (for add/edit forms) =====
  
  // Add category button handler
  window.addCategory = function() {
    const container = document.getElementById('category-entries');
    if (!container) return;
    
    const firstEntry = container.querySelector('.category-entry');
    if (!firstEntry) return;
    
    const newEntry = firstEntry.cloneNode(true);
    
    // Clear values in cloned entry
    const selects = newEntry.querySelectorAll('select');
    const inputs = newEntry.querySelectorAll('input');
    
    selects.forEach(select => select.value = '');
    inputs.forEach(input => input.value = '');
    
    // Add remove button if it doesn't exist
    let removeBtn = newEntry.querySelector('.remove-category-btn');
    if (!removeBtn) {
      removeBtn = document.createElement('button');
      removeBtn.type = 'button';
      removeBtn.className = 'btn btn-sm btn-danger remove-category-btn';
      removeBtn.innerHTML = '<i class="fas fa-times"></i> Remove';
      removeBtn.onclick = function() {
        this.closest('.category-entry').remove();
        updateCategoryCount();
      };
      newEntry.appendChild(removeBtn);
    }
    
    container.appendChild(newEntry);
    updateCategoryCount();
  };
  
  // Remove category handler
  window.removeCategory = function(button) {
    const entry = button.closest('.category-entry');
    const container = document.getElementById('category-entries');
    
    // Don't remove if it's the only entry
    if (container && container.querySelectorAll('.category-entry').length > 1) {
      entry.remove();
      updateCategoryCount();
    } else {
      alert('At least one category entry is required.');
    }
  };
  
  function updateCategoryCount() {
    const container = document.getElementById('category-entries');
    if (!container) return;
    
    const entries = container.querySelectorAll('.category-entry');
    
    // Update labels if needed
    entries.forEach((entry, index) => {
      const legend = entry.querySelector('.entry-legend');
      if (legend) {
        legend.textContent = `Category ${index + 1}`;
      }
    });
  }
  
  
  // ===== FILTER CARD TOGGLE (already handled in page, but ensure it works) =====
  window.toggleFilterCard = function() {
    const content = document.getElementById('filterContent');
    const icon = document.getElementById('filterToggleIcon');
    
    if (!content || !icon) return;
    
    const isVisible = content.style.display !== 'none';
    
    if (isVisible) {
      content.style.display = 'none';
      icon.classList.remove('fa-chevron-up');
      icon.classList.add('fa-chevron-down');
    } else {
      content.style.display = 'block';
      icon.classList.remove('fa-chevron-down');
      icon.classList.add('fa-chevron-up');
    }
  };
  
  
  // ===== REMOVE FILTER PILL =====
  window.removeFilter = function(filterType) {
    const url = new URL(window.location.href);
    
    if (filterType === 'month') {
      url.searchParams.delete('month');
    } else if (filterType === 'search') {
      url.searchParams.delete('search');
    }
    
    window.location.href = url.toString();
  };
  
  
  // ===== FORM VALIDATION HELPERS =====
  
  // Payment method change handler
  const paymentMethodSelect = document.getElementById('payment_method');
  const paymentRefInput = document.getElementById('payment_reference');
  
  if (paymentMethodSelect && paymentRefInput) {
    paymentMethodSelect.addEventListener('change', function() {
      const method = this.value;
      const refLabel = paymentRefInput.previousElementSibling;
      
      if (method === 'bank' || method === 'cheque') {
        paymentRefInput.required = true;
        if (refLabel) {
          if (!refLabel.querySelector('.required-star')) {
            const star = document.createElement('span');
            star.className = 'required-star';
            star.style.color = 'red';
            star.textContent = ' *';
            refLabel.appendChild(star);
          }
        }
      } else {
        paymentRefInput.required = false;
        if (refLabel) {
          const star = refLabel.querySelector('.required-star');
          if (star) star.remove();
        }
      }
    });
    
    // Trigger on load
    paymentMethodSelect.dispatchEvent(new Event('change'));
  }
  
  
  // ===== MOBILE-SPECIFIC FORM ENHANCEMENTS =====
  
  // Auto-focus first empty required field on mobile
  if (window.innerWidth < 768) {
    const firstEmptyRequired = document.querySelector('input[required]:not([value]), select[required]:not([value])');
    if (firstEmptyRequired && document.activeElement === document.body) {
      // Don't auto-focus on mobile as it causes keyboard to pop up
      // Instead, just scroll it into view
      firstEmptyRequired.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  }
  
  
  // ===== AMOUNT FORMATTING =====
  
  // Format currency inputs
  const amountInputs = document.querySelectorAll('input[type="number"][step="0.01"]');
  
  amountInputs.forEach(input => {
    input.addEventListener('blur', function() {
      if (this.value) {
        const value = parseFloat(this.value);
        if (!isNaN(value)) {
          this.value = value.toFixed(2);
        }
      }
    });
  });
  
  
  console.log('Financial module mobile enhancements loaded');
});
