/**
 * Main JavaScript file for TravelEase Travel Booking Website
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize search type switcher
    initSearchTypeSwitcher();
    
    // Initialize date pickers
    initDatePickers();
    
    // Initialize form validation
    initFormValidation();
    
    // Initialize animations
    initAnimations();
    
    // Initialize booking system
    initBookingSystem();
});

/**
 * Initialize the search type switcher to change form fields based on selection
 */
function initSearchTypeSwitcher() {
    const searchTypeSelect = document.getElementById('search-type');
    
    if (searchTypeSelect) {
        searchTypeSelect.addEventListener('change', function() {
            const searchType = this.value;
            const fromLabel = document.querySelector('label[for="search-from"]');
            const toLabel = document.querySelector('label[for="search-to"]');
            const dateLabel = document.querySelector('label[for="search-date"]');
            const fromInput = document.getElementById('search-from');
            const toInput = document.getElementById('search-to');
            
            switch (searchType) {
                case 'flight':
                    fromLabel.textContent = 'From';
                    toLabel.textContent = 'To';
                    dateLabel.textContent = 'Departure';
                    fromInput.placeholder = 'City or Airport';
                    toInput.placeholder = 'City or Airport';
                    break;
                case 'hotel':
                    fromLabel.textContent = 'Destination';
                    toLabel.textContent = 'Guests';
                    dateLabel.textContent = 'Check-in';
                    fromInput.placeholder = 'City or Hotel';
                    toInput.placeholder = 'Adults, Children';
                    break;
                case 'cruise':
                    fromLabel.textContent = 'Departure Port';
                    toLabel.textContent = 'Destination';
                    dateLabel.textContent = 'Departure';
                    fromInput.placeholder = 'Port';
                    toInput.placeholder = 'Region or Port';
                    break;
            }
        });
    }
}

/**
 * Initialize date pickers for booking forms
 */
function initDatePickers() {
    // This would typically use a date picker library
    // For this example, we're just setting min date to today
    const dateInputs = document.querySelectorAll('input[type="date"]');
    
    if (dateInputs.length) {
        const today = new Date().toISOString().split('T')[0];
        
        dateInputs.forEach(input => {
            input.min = today;
        });
    }
}

/**
 * Initialize form validation for all forms
 */
function initFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Validate required fields
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    
                    // Add error class if not already present
                    if (!field.classList.contains('border-red-500')) {
                        field.classList.add('border-red-500');
                        
                        // Add error message if not already present
                        const errorMessage = document.createElement('p');
                        errorMessage.className = 'text-red-500 text-xs mt-1';
                        errorMessage.textContent = 'This field is required';
                        
                        if (!field.parentNode.querySelector('.text-red-500')) {
                            field.parentNode.appendChild(errorMessage);
                        }
                    }
                } else {
                    // Remove error class and message if field is valid
                    field.classList.remove('border-red-500');
                    const errorMessage = field.parentNode.querySelector('.text-red-500');
                    if (errorMessage) {
                        errorMessage.remove();
                    }
                }
            });
            
            // Validate email fields
            const emailFields = form.querySelectorAll('input[type="email"]');
            
            emailFields.forEach(field => {
                if (field.value.trim() && !isValidEmail(field.value)) {
                    isValid = false;
                    
                    // Add error class if not already present
                    if (!field.classList.contains('border-red-500')) {
                        field.classList.add('border-red-500');
                        
                        // Add error message if not already present
                        const errorMessage = document.createElement('p');
                        errorMessage.className = 'text-red-500 text-xs mt-1';
                        errorMessage.textContent = 'Please enter a valid email address';
                        
                        if (!field.parentNode.querySelector('.text-red-500')) {
                            field.parentNode.appendChild(errorMessage);
                        }
                    }
                }
            });
            
            // Validate password confirmation if present
            const password = form.querySelector('input[name="password"]');
            const passwordConfirm = form.querySelector('input[name="password_confirm"]');
            
            if (password && passwordConfirm) {
                if (password.value !== passwordConfirm.value) {
                    isValid = false;
                    
                    // Add error class if not already present
                    if (!passwordConfirm.classList.contains('border-red-500')) {
                        passwordConfirm.classList.add('border-red-500');
                        
                        // Add error message if not already present
                        const errorMessage = document.createElement('p');
                        errorMessage.className = 'text-red-500 text-xs mt-1';
                        errorMessage.textContent = 'Passwords do not match';
                        
                        if (!passwordConfirm.parentNode.querySelector('.text-red-500')) {
                            passwordConfirm.parentNode.appendChild(errorMessage);
                        }
                    }
                }
            }
            
            if (!isValid) {
                event.preventDefault();
            }
        });
    });
}

/**
 * Validate email format
 * @param {string} email - The email to validate
 * @return {boolean} - Whether the email is valid
 */
function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

/**
 * Initialize animations for elements
 */
function initAnimations() {
    // Animate elements when they come into view
    const animatedElements = document.querySelectorAll('.fade-in, .slide-up');
    
    if (animatedElements.length) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        animatedElements.forEach(element => {
            element.style.opacity = '0';
            if (element.classList.contains('slide-up')) {
                element.style.transform = 'translateY(20px)';
            }
            element.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            observer.observe(element);
        });
    }
}

/**
 * Initialize booking system functionality
 */
function initBookingSystem() {
    // Handle booking button clicks
    const bookButtons = document.querySelectorAll('.book-now-btn, [data-action="book"]');
    
    bookButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            // Get booking details from data attributes or parent elements
            const itemId = this.dataset.id;
            const itemType = this.dataset.type;
            const itemPrice = this.dataset.price;
            
            // Check if user is logged in (would normally use a more sophisticated check)
            const isLoggedIn = document.body.classList.contains('logged-in') || (typeof window.userLoggedIn !== 'undefined' && window.userLoggedIn);
            
            if (!isLoggedIn) {
                // Show login prompt
                alert('Please login to make a booking.');
                window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
                return;
            }
            
            // For demonstration purposes
            if (confirm(`Confirm booking for this ${itemType}?`)) {
                // Simulate API call
                fetch('/api/bookings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        item_id: itemId,
                        booking_type: itemType,
                        price: itemPrice
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    alert('Booking successful! Booking ID: ' + data.booking_id);
                    window.location.href = '/bookings';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('There was a problem with your booking. Please try again.');
                });
            }
        });
    });
}