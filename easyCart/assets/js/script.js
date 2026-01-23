/**
 * EasyCart - Phase 3: Client-Side Interactions
 * Includes form validations, cart interactions, and UI enhancements
 */

// ============================================
// 1. LOGIN FORM VALIDATION
// ============================================
function validateLoginForm(event) {
    const form = event.target;
    const email = form.querySelector('input[name="email"]');
    const password = form.querySelector('input[name="password"]');
    
    // Reset errors
    clearFormErrors(form);
    let isValid = true;

    // Email validation
    if (!email.value.trim()) {
        showFieldError(email, 'Email is required');
        isValid = false;
    } else if (!isValidEmail(email.value)) {
        showFieldError(email, 'Please enter a valid email address');
        isValid = false;
    }

    // Password validation
    if (!password.value.trim()) {
        showFieldError(password, 'Password is required');
        isValid = false;
    } else if (password.value.length < 6) {
        showFieldError(password, 'Password must be at least 6 characters');
        isValid = false;
    }

    if (!isValid) {
        event.preventDefault();
    }
}

// ============================================
// 2. SIGNUP FORM VALIDATION
// ============================================
function validateSignupForm(event) {
    const form = event.target;
    const firstName = form.querySelector('input[name="first_name"]');
    const lastName = form.querySelector('input[name="last_name"]');
    const email = form.querySelector('input[name="email"]');
    const phone = form.querySelector('input[name="phone"]');
    const password = form.querySelector('input[name="password"]');
    const confirmPassword = form.querySelector('input[name="confirm_password"]');
    
    // Reset errors
    clearFormErrors(form);
    let isValid = true;

    // First Name validation
    if (!firstName.value.trim()) {
        showFieldError(firstName, 'First name is required');
        isValid = false;
    } else if (firstName.value.trim().length < 2) {
        showFieldError(firstName, 'First name must be at least 2 characters');
        isValid = false;
    }

    // Last Name validation
    if (!lastName.value.trim()) {
        showFieldError(lastName, 'Last name is required');
        isValid = false;
    } else if (lastName.value.trim().length < 2) {
        showFieldError(lastName, 'Last name must be at least 2 characters');
        isValid = false;
    }

    // Email validation
    if (!email.value.trim()) {
        showFieldError(email, 'Email is required');
        isValid = false;
    } else if (!isValidEmail(email.value)) {
        showFieldError(email, 'Please enter a valid email address');
        isValid = false;
    }

    // Phone validation
    if (!phone.value.trim()) {
        showFieldError(phone, 'Phone number is required');
        isValid = false;
    } else if (!isValidPhone(phone.value)) {
        showFieldError(phone, 'Please enter a valid 10-digit phone number');
        isValid = false;
    }

    // Password validation
    if (!password.value.trim()) {
        showFieldError(password, 'Password is required');
        isValid = false;
    } else if (password.value.length < 8) {
        showFieldError(password, 'Password must be at least 8 characters');
        isValid = false;
    }

    // Confirm Password validation
    if (!confirmPassword.value.trim()) {
        showFieldError(confirmPassword, 'Please confirm your password');
        isValid = false;
    } else if (password.value !== confirmPassword.value) {
        showFieldError(confirmPassword, 'Passwords do not match');
        isValid = false;
    }

    if (!isValid) {
        event.preventDefault();
    }
}

// ============================================
// 3. CHECKOUT FORM VALIDATION
// ============================================
function validateCheckoutForm(event) {
    const form = event.target;
    
    // Reset errors
    clearFormErrors(form);
    let isValid = true;

    // Get all required fields
    const fields = {
        first_name: { input: form.querySelector('input[name="first_name"]'), message: 'First name is required' },
        last_name: { input: form.querySelector('input[name="last_name"]'), message: 'Last name is required' },
        email: { input: form.querySelector('input[name="email"]'), message: 'Valid email is required' },
        phone: { input: form.querySelector('input[name="phone"]'), message: 'Valid 10-digit phone is required' },
        address: { input: form.querySelector('input[name="address"]'), message: 'Address is required' },
        city: { input: form.querySelector('input[name="city"]'), message: 'City is required' },
        state: { input: form.querySelector('input[name="state"]'), message: 'State is required' },
        pincode: { input: form.querySelector('input[name="pincode"]'), message: 'Valid 6-digit PIN is required' }
    };

    // Validate each field
    if (!fields.first_name.input.value.trim()) {
        showFieldError(fields.first_name.input, fields.first_name.message);
        isValid = false;
    }

    if (!fields.last_name.input.value.trim()) {
        showFieldError(fields.last_name.input, fields.last_name.message);
        isValid = false;
    }

    if (!fields.email.input.value.trim() || !isValidEmail(fields.email.input.value)) {
        showFieldError(fields.email.input, fields.email.message);
        isValid = false;
    }

    if (!fields.phone.input.value.trim() || !isValidPhone(fields.phone.input.value)) {
        showFieldError(fields.phone.input, fields.phone.message);
        isValid = false;
    }

    if (!fields.address.input.value.trim()) {
        showFieldError(fields.address.input, fields.address.message);
        isValid = false;
    }

    if (!fields.city.input.value.trim()) {
        showFieldError(fields.city.input, fields.city.message);
        isValid = false;
    }

    if (!fields.state.input.value.trim()) {
        showFieldError(fields.state.input, fields.state.message);
        isValid = false;
    }

    if (!fields.pincode.input.value.trim() || !isValidPincode(fields.pincode.input.value)) {
        showFieldError(fields.pincode.input, fields.pincode.message);
        isValid = false;
    }

    if (!isValid) {
        event.preventDefault();
    }
}

// ============================================
// 4. CART PAGE INTERACTIONS
// ============================================
function initCartInteractions() {
    // Initialize quantity controls
    const quantityInputs = document.querySelectorAll('input[name="quantity"]');
    quantityInputs.forEach(input => {
        input.addEventListener('change', handleQuantityChange);
    });

    // Initialize remove buttons
    const removeButtons = document.querySelectorAll('button[class*="remove"]');
    removeButtons.forEach(btn => {
        if (btn.type === 'submit' && btn.closest('form[action="cart-remove.php"]')) {
            btn.addEventListener('click', handleRemoveItem);
        }
    });

    // Initialize update buttons
    const updateButtons = document.querySelectorAll('button[class*="update"]');
    updateButtons.forEach(btn => {
        if (btn.type === 'submit' && btn.closest('form[action="cart-update.php"]')) {
            btn.addEventListener('click', handleUpdateQuantity);
        }
    });
}

function handleQuantityChange(event) {
    const input = event.target;
    const value = parseInt(input.value);
    const max = parseInt(input.getAttribute('max')) || Infinity;
    const min = parseInt(input.getAttribute('min')) || 1;

    // Validate quantity
    if (value < min) {
        input.value = min;
    } else if (value > max) {
        input.value = max;
    }

    // Update subtotal display
    updateLineItemTotal(input);
}

function updateLineItemTotal(quantityInput) {
    const cartItem = quantityInput.closest('div[style*="grid"]');
    if (!cartItem) return;

    // Find price element - look for the price in the product details
    const priceText = cartItem.querySelector('.product-pricing, [style*="font-weight: 700"]');
    if (!priceText) return;

    // Extract price from text like "₹36,999"
    const priceMatch = cartItem.textContent.match(/₹([\d,]+)/);
    if (!priceMatch) return;

    const price = parseInt(priceMatch[1].replace(/,/g, ''));
    const quantity = parseInt(quantityInput.value);
    const subtotal = price * quantity;

    // Update subtotal in cart item
    const subtotalElement = cartItem.querySelector('p[style*="Subtotal"]');
    if (subtotalElement) {
        subtotalElement.innerHTML = `Subtotal: ₹${subtotal.toLocaleString('en-IN')}`;
    }
}

function handleRemoveItem(event) {
    event.preventDefault();
    const btn = event.target;
    const form = btn.closest('form[action="cart-remove.php"]');
    
    if (confirm('Are you sure you want to remove this item from your cart?')) {
        form.submit();
    }
}

function handleUpdateQuantity(event) {
    const btn = event.target;
    const quantityInput = btn.closest('form').querySelector('input[name="quantity"]');
    
    const quantity = parseInt(quantityInput.value);
    if (quantity < 1) {
        alert('Quantity must be at least 1');
        event.preventDefault();
        return;
    }

    // The form will submit normally
}

// ============================================
// 5. PRODUCT DETAIL PAGE - IMAGE SWITCHING
// ============================================
function initProductImageSwitching() {
    const thumbs = document.querySelectorAll('.showcase-thumb');
    const mainImage = document.querySelector('.showcase-main');

    if (!mainImage || thumbs.length === 0) return;

    thumbs.forEach((thumb, index) => {
        thumb.style.cursor = 'pointer';
        thumb.addEventListener('click', () => {
            // Get the emoji/content from the thumbnail
            const imageContent = thumb.textContent;
            mainImage.textContent = imageContent;
            
            // Remove active class from all thumbnails
            thumbs.forEach(t => t.style.opacity = '0.6');
            
            // Add active class to clicked thumbnail
            thumb.style.opacity = '1';
        });

        // Set initial state
        if (index === 0) {
            thumb.style.opacity = '1';
        } else {
            thumb.style.opacity = '0.6';
        }
    });
}

// ============================================
// 6. SHIPPING METHOD HIGHLIGHT
// ============================================
function initShippingMethodHighlight() {
    const shippingRadios = document.querySelectorAll('input[name="shipping_method"]');
    
    shippingRadios.forEach(radio => {
        const label = radio.closest('label');
        
        // Set initial state
        if (radio.checked && label) {
            label.style.borderColor = 'var(--primary)';
            label.style.backgroundColor = 'rgba(59, 130, 246, 0.05)';
        }

        // Add change listener
        radio.addEventListener('change', () => {
            shippingRadios.forEach(r => {
                const lbl = r.closest('label');
                if (lbl) {
                    if (r.checked) {
                        lbl.style.borderColor = 'var(--primary)';
                        lbl.style.backgroundColor = 'rgba(59, 130, 246, 0.05)';
                    } else {
                        lbl.style.borderColor = 'var(--border)';
                        lbl.style.backgroundColor = 'transparent';
                    }
                }
            });
        });
    });
}

// Also handle payment method highlighting
function initPaymentMethodHighlight() {
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    
    paymentRadios.forEach(radio => {
        const label = radio.closest('label');
        
        // Set initial state
        if (radio.checked && label) {
            label.style.borderColor = 'var(--primary)';
            label.style.backgroundColor = 'rgba(59, 130, 246, 0.05)';
        }

        // Add change listener
        radio.addEventListener('change', () => {
            paymentRadios.forEach(r => {
                const lbl = r.closest('label');
                if (lbl) {
                    if (r.checked) {
                        lbl.style.borderColor = 'var(--primary)';
                        lbl.style.backgroundColor = 'rgba(59, 130, 246, 0.05)';
                    } else {
                        lbl.style.borderColor = 'var(--border)';
                        lbl.style.backgroundColor = 'transparent';
                    }
                }
            });
        });
    });
}

// ============================================
// 7. PRODUCT COUNT DISPLAY ON PLP
// ============================================
function initProductCountDisplay() {
    // Find the products container
    const productsContainer = document.querySelector('.products-container');
    if (!productsContainer) return;

    // Count actual product items (those with href to product-detail.php)
    const productLinks = Array.from(productsContainer.querySelectorAll('a.product-item')).filter(link => {
        return link.getAttribute('href') && link.getAttribute('href').includes('product-detail.php');
    });

    if (productLinks.length === 0) return;

    // Find or create the product count display
    let countContainer = document.querySelector('.product-count-info');
    if (!countContainer) {
        countContainer = document.createElement('div');
        countContainer.className = 'product-count-info';
        countContainer.style.cssText = `
            text-align: right;
            margin-top: 2rem;
            color: var(--text-secondary);
            font-size: 0.9375rem;
            font-weight: 500;
        `;
        
        // Insert after products container
        productsContainer.parentNode.insertBefore(countContainer, productsContainer.nextSibling);
    }

    // Update the count - get existing text and replace the count
    const productCount = productLinks.length;
    countContainer.textContent = `Showing ${productCount} product${productCount !== 1 ? 's' : ''}`;
}

// ============================================
// UTILITY FUNCTIONS
// ============================================

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPhone(phone) {
    const phoneRegex = /^\d{10}$/;
    return phoneRegex.test(phone.replace(/\D/g, ''));
}

function isValidPincode(pincode) {
    const pincodeRegex = /^\d{6}$/;
    return pincodeRegex.test(pincode);
}

function showFieldError(field, message) {
    // Remove existing error if any
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }

    // Create and show error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.style.cssText = `
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
    `;
    errorDiv.textContent = message;
    
    field.style.borderColor = '#ef4444';
    field.style.borderWidth = '2px';
    field.parentNode.appendChild(errorDiv);
}

function clearFormErrors(form) {
    // Clear all error messages
    const errors = form.querySelectorAll('.field-error');
    errors.forEach(error => error.remove());

    // Clear error styling
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.style.borderColor = 'var(--border)';
        input.style.borderWidth = '2px';
    });
}

// ============================================
// 8. LOGOUT CONFIRMATION
// ============================================
function initLogoutConfirmation() {
    // Find logout links in header navigation
    const headerNav = document.querySelector('.header-nav');
    if (!headerNav) return;

    const logoutLink = Array.from(headerNav.querySelectorAll('a')).find(link => 
        link.textContent.includes('Logout')
    );

    if (logoutLink) {
        logoutLink.addEventListener('click', function(event) {
            event.preventDefault();
            const userName = logoutLink.textContent.match(/\(([^)]+)\)/)?.[1] || 'User';
            
            if (confirm(`Are you sure you want to logout, ${userName}?`)) {
                // Navigate to logout page
                window.location.href = this.getAttribute('href');
            }
        });
    }
}

// ============================================
// INITIALIZATION - RUN WHEN DOM IS READY
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Attach validation to login form
    const loginForm = document.querySelector('form[action="login-process.php"]');
    if (loginForm) {
        loginForm.addEventListener('submit', validateLoginForm);
    }

    // Attach validation to signup form
    const signupForm = document.querySelector('form[action="signup-process.php"]');
    if (signupForm) {
        signupForm.addEventListener('submit', validateSignupForm);
    }

    // Attach validation to checkout form
    const checkoutForm = document.querySelector('form[action="order-place.php"]');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', validateCheckoutForm);
    }

    // Initialize cart interactions
    const cartLayout = document.querySelector('.cart-layout');
    if (cartLayout) {
        initCartInteractions();
    }

    // Initialize product image switching
    const imageShowcase = document.querySelector('.image-showcase');
    if (imageShowcase) {
        initProductImageSwitching();
    }

    // Initialize shipping method highlight
    const shippingSection = document.querySelector('div');
    if (document.querySelector('input[name="shipping_method"]')) {
        initShippingMethodHighlight();
    }

    // Initialize payment method highlight
    if (document.querySelector('input[name="payment_method"]')) {
        initPaymentMethodHighlight();
    }

    // Initialize product count on products page
    if (document.querySelector('.filter-panel')) {
        initProductCountDisplay();
    }

    // Initialize logout confirmation
    initLogoutConfirmation();
});
