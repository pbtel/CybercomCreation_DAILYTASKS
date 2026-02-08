/**
 * EasyCart - Phase 5: AJAX Cart Operations
 * Handles all AJAX-based cart interactions
 */

// ============================================
// 1. ADD TO CART - AJAX
// ============================================


function addToCartAjax(formElement, buttonElement = null) {
    const formData = new FormData(formElement);
    const validButton = buttonElement || formElement.querySelector('button[type="submit"]');

    if (validButton) {
        validButton.disabled = true;
        validButton.dataset.originalText = validButton.textContent;
        validButton.textContent = 'Adding...';
    }

    fetch(BASE_URL + '/api/cart-add', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.text();
        })
        .then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Server returned invalid JSON:', text);
                throw new Error('Invalid server response (not JSON)');
            }
        })
        .then(data => {
            if (data.success) {
                updateCartBadge(data.cart_count);
                showCartToast(data.message, 'success');
            } else {
                showCartToast(data.message || 'Failed to add product', 'error');
            }
            if (validButton) {
                validButton.textContent = validButton.dataset.originalText;
                validButton.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error adding to cart:', error);
            showCartToast('Error: ' + error.message, 'error');
            if (validButton) {
                validButton.textContent = validButton.dataset.originalText;
                validButton.disabled = false;
            }
        });
}

// ============================================
// 2. UPDATE CART QUANTITY - AJAX
// ============================================

function updateCartQuantityAjax(cartKey, quantity, itemElement) {
    console.log('updateCartQuantityAjax called:', { cartKey, quantity, itemElement });
    // Show loading state
    if (itemElement) {
        itemElement.style.opacity = '0.6';
        itemElement.style.pointerEvents = 'none';
    }

    // Prepare form data
    const formData = new FormData();
    formData.append('cart_key', cartKey);
    formData.append('quantity', quantity);

    // Send AJAX request
    fetch(BASE_URL + '/api/cart-update', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.text();
        })
        .then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Server invalid JSON response:', text);
                throw new Error('Server returned invalid JSON');
            }
        })
        .then(data => {
            if (data.success) {
                // Update cart badge
                updateCartBadge(data.cart_count);

                // Update item subtotal
                if (itemElement) {
                    const subtotalElement = itemElement.querySelector('.item-subtotal');
                    if (subtotalElement) {
                        subtotalElement.textContent = '₹' + (data.item_subtotal || 0).toLocaleString('en-IN');
                    }
                }

                // Update cart summary
                updateCartSummaryDisplay(data.cart_subtotal || data.subtotal);

                // Show success toast
                showCartToast(data.message, 'success');
            } else {
                showCartToast(data.message || 'Failed to update cart', 'error');
            }

            // Reset loading state
            if (itemElement) {
                itemElement.style.opacity = '1';
                itemElement.style.pointerEvents = 'auto';
            }
        })
        .catch(error => {
            console.error('Error updating cart:', error);
            showCartToast('Error: ' + error.message, 'error');

            if (itemElement) {
                itemElement.style.opacity = '1';
                itemElement.style.pointerEvents = 'auto';
            }
        });
}

// ============================================
// 3. REMOVE CART ITEM - AJAX
// ============================================

function removeCartItemAjax(cartKey, itemElement) {
    // Confirm removal
    if (!confirm('Are you sure you want to remove this item from your cart?')) {
        return;
    }

    // Add fade-out animation
    if (itemElement) {
        itemElement.classList.add('fade-out');
    }

    // Prepare form data
    const formData = new FormData();
    formData.append('cart_key', cartKey);

    // Send AJAX request after animation
    setTimeout(() => {
        fetch(BASE_URL + '/api/cart-remove', {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Remove item from DOM
                    if (itemElement) {
                        itemElement.remove();
                    }

                    // Update cart badge
                    updateCartBadge(data.cart_count);

                    // Update cart summary
                    updateCartSummaryDisplay(data.cart_subtotal || data.subtotal);

                    // Show success toast
                    showCartToast(data.message, 'success');

                    // If cart is empty, show empty cart message
                    if (data.is_empty || data.cart_count === 0) {
                        showEmptyCartMessage();
                    }
                } else {
                    showCartToast(data.message || 'Failed to remove item', 'error');
                    if (itemElement) itemElement.classList.remove('fade-out');
                }
            })
            .catch(error => {
                console.error('Error removing item:', error);
                showCartToast('Error: ' + error.message, 'error');
                if (itemElement) itemElement.classList.remove('fade-out');
            });
    }, 300);
}

// ============================================
// 4. FETCH CART SUMMARY - AJAX
// ============================================

function fetchCartSummary() {
    return fetch(BASE_URL + '/api/cart-summary', {
        method: 'GET'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                return data;
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching cart summary:', error);
            return null;
        });
}

// ============================================
// 5. UPDATE SHIPPING CALCULATION - AJAX
// ============================================

function updateShippingCalculation(shippingMethod) {
    // Show loading state
    const summaryShipping = document.getElementById('summary-shipping');
    const summaryTax = document.getElementById('summary-tax');
    const summaryTotal = document.getElementById('summary-total');

    if (summaryShipping) summaryShipping.textContent = 'Calculating...';
    if (summaryTax) summaryTax.textContent = 'Calculating...';
    if (summaryTotal) summaryTotal.textContent = 'Calculating...';

    // Prepare form data
    const formData = new FormData();
    formData.append('shipping_method', shippingMethod);

    // Send AJAX request
    fetch(BASE_URL + '/api/shipping-calculate', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update shipping cost
                if (summaryShipping) {
                    summaryShipping.textContent = '₹' + data.shipping_cost.toLocaleString('en-IN');
                }

                // Update tax
                if (summaryTax) {
                    summaryTax.textContent = '₹' + data.tax.toLocaleString('en-IN');
                }

                // Update total
                if (summaryTotal) {
                    summaryTotal.textContent = '₹' + data.total.toLocaleString('en-IN');
                }

                // Add animation effect
                [summaryShipping, summaryTax, summaryTotal].forEach(element => {
                    if (element) {
                        element.classList.remove('scale-pulse');
                        void element.offsetWidth; // trigger reflow
                        element.classList.add('scale-pulse');
                        setTimeout(() => {
                            element.classList.remove('scale-pulse');
                        }, 300);
                    }
                });
            } else {
                showCartToast(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error calculating shipping:', error);
            showCartToast('Failed to calculate shipping. Please try again.', 'error');
        });
}

// ============================================
// 6. UPDATE CART BADGE
// ============================================

function updateCartBadge(count) {
    const cartBadge = document.getElementById('cartBadge');
    if (cartBadge) {
        cartBadge.textContent = count;

        // Show badge if count > 0, hide if count = 0
        if (count > 0) {
            cartBadge.classList.remove('hidden');
            cartBadge.style.display = 'inline-block';

            // Add pulse animation
            cartBadge.classList.add('updating');
            setTimeout(() => {
                cartBadge.classList.remove('updating');
            }, 300);
        } else {
            cartBadge.classList.add('hidden');
            cartBadge.style.display = 'none';
        }
    }
}

// ============================================
// 7. UPDATE CART SUMMARY DISPLAY
// ============================================

function updateCartSummaryDisplay(subtotal) {
    // Update subtotal display
    const summarySubtotal = document.getElementById('summary-subtotal');
    if (summarySubtotal) {
        summarySubtotal.textContent = '₹' + subtotal.toLocaleString('en-IN');

        // Add animation effect
        summarySubtotal.classList.remove('scale-pulse');
        void summarySubtotal.offsetWidth; // trigger reflow
        summarySubtotal.classList.add('scale-pulse');
        setTimeout(() => {
            summarySubtotal.classList.remove('scale-pulse');
        }, 300);
    }

    // Fetch updated cart summary to get coupon discount
    fetch(BASE_URL + '/api/cart-summary', {
        method: 'GET'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update coupon discount if exists
                const couponDiscountElement = document.querySelector('[data-coupon-discount]');
                const couponDiscountContainer = couponDiscountElement ? couponDiscountElement.closest('div[style*="border-bottom"]') : null;

                if (data.coupon_discount && data.coupon_discount > 0) {
                    // Coupon is applied, update or create discount line
                    if (couponDiscountElement) {
                        couponDiscountElement.textContent = '-₹' + data.coupon_discount.toLocaleString('en-IN');
                    }
                } else {
                    // No coupon, remove discount line if it exists
                    if (couponDiscountContainer) {
                        couponDiscountContainer.remove();
                    }
                }

                // Update estimated total
                const estimatedTotalElement = document.querySelector('[data-estimated-total]');
                if (estimatedTotalElement) {
                    const finalSubtotal = data.subtotal_after_coupon || subtotal;
                    estimatedTotalElement.textContent = '₹' + finalSubtotal.toLocaleString('en-IN') + '+';

                    // Add animation
                    estimatedTotalElement.classList.remove('scale-pulse');
                    void estimatedTotalElement.offsetWidth; // trigger reflow
                    estimatedTotalElement.classList.add('scale-pulse');
                    setTimeout(() => {
                        estimatedTotalElement.classList.remove('scale-pulse');
                    }, 300);
                }
            }
        })
        .catch(error => {
            console.error('Error fetching cart summary:', error);
        });
}

// ============================================
// 8. SHOW EMPTY CART MESSAGE
// ============================================

function showEmptyCartMessage() {
    const cartLayout = document.querySelector('.cart-layout');
    if (cartLayout) {
        cartLayout.innerHTML = `
            <div class="empty-cart-message">
                <div class="empty-icon">&#128722;</div>
                <h2 class="empty-title">Your cart is empty</h2>
                <p class="empty-text">Start shopping to add items to your cart</p>
                <a href="${BASE_URL}/products" class="btn-gradient">
                    Continue Shopping
                </a>
            </div>
        `;
    }
}

// ============================================
// 9. SHOW CART TOAST NOTIFICATION
// ============================================

function showCartToast(message, type = 'info') {
    // Use existing showToast function if available
    if (typeof showToast === 'function') {
        showToast(message, type);
        return;
    }

    // Fallback: Create toast container if not exists
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container';
        document.body.appendChild(container); // Append manually if creating
    }

    if (!container.classList.contains('toast-container')) {
        container.className = 'toast-container'; // Ensure class is present
    }

    // Create toast element
    const toast = document.createElement('div');
    toast.className = 'toast toast-' + type;

    // determine icon and colors
    let icon = '•';
    let color = '#3b82f6';
    switch (type) {
        case 'success':
            icon = '✓';
            color = '#10b981';
            break;
        case 'error':
            icon = '✗';
            color = '#ef4444';
            break;
        case 'info':
            icon = 'ℹ';
            color = '#3b82f6';
            break;
    }

    // Create content
    toast.innerHTML = `
        <span style="font-size: 1.5rem; font-weight: bold; color: ${color};">${icon}</span>
        <span style="flex: 1; color: #333; font-weight: 500;">${message}</span>
        <span class="toast-close" style="font-size: 1.5rem; color: #666; cursor: pointer; line-height: 1; padding: 0 4px;">&times;</span>
    `;

    // Add to container
    container.appendChild(toast);

    // Auto dismiss after 4 seconds
    setTimeout(() => {
        removeToast(toast);
    }, 4000);

    // Click to dismiss
    toast.onclick = () => removeToast(toast);

    function removeToast(toastElement) {
        toastElement.style.opacity = '0';
        toastElement.style.transform = 'translateX(400px)';
        setTimeout(() => {
            if (toastElement.parentNode) {
                toastElement.parentNode.removeChild(toastElement);
            }
        }, 300);
    }
}

// ============================================
// 10. INITIALIZE AJAX CART HANDLERS
// ============================================

function initAjaxCartHandlers() {
    // Initialize Add to Cart forms
    const addToCartForms = document.querySelectorAll('form#addToCartForm');
    addToCartForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            addToCartAjax(this);
        });
    });

    // Initialize Update Quantity buttons on cart page
    const updateForms = document.querySelectorAll('form[action="cart-update.php"]');
    updateForms.forEach(form => {
        const updateButton = form.querySelector('button[type="submit"]');
        if (updateButton) {
            updateButton.addEventListener('click', function (e) {
                e.preventDefault();

                const cartKey = form.querySelector('input[name="cart_key"]').value;
                const quantity = form.querySelector('input[name="quantity"]').value;
                const itemElement = form.closest('[data-cart-item]') || form.closest('div[style*="grid"]');

                updateCartQuantityAjax(cartKey, quantity, itemElement);
            });
        }
    });

    // Initialize Remove buttons on cart page
    const removeForms = document.querySelectorAll('form[action="cart-remove.php"]');
    removeForms.forEach(form => {
        const removeButton = form.querySelector('button[type="submit"]');
        if (removeButton) {
            removeButton.addEventListener('click', function (e) {
                e.preventDefault();

                const cartKey = form.querySelector('input[name="cart_key"]').value;
                const itemElement = form.closest('[data-cart-item]') || form.closest('div[style*="grid"]');

                removeCartItemAjax(cartKey, itemElement);
            });
        }
    });

    // Note: Shipping Method changes are handled by inline onchange handlers in checkout.php
    // to avoid duplicate AJAX calls
}

// ============================================
// 11. APPLY COUPON CODE - AJAX
// ============================================

function applyCouponCode() {
    const couponInput = document.getElementById('couponCode');
    const couponCode = couponInput ? couponInput.value.trim() : '';
    const messageDiv = document.getElementById('couponMessage');

    if (!couponCode) {
        if (messageDiv) {
            if (messageDiv) {
                messageDiv.innerHTML = '<span class="text-danger">Please enter a coupon code</span>';
            }
        }
        return;
    }

    // Show loading state
    if (messageDiv) {
        messageDiv.innerHTML = '<span class="text-secondary">Applying...</span>';
    }

    // Prepare form data
    const formData = new FormData();
    formData.append('coupon_code', couponCode);

    // Send AJAX request
    fetch(BASE_URL + '/api/coupon-apply', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message and reload page to update UI
                showCartToast(data.message, 'success');
                setTimeout(() => {
                    location.reload();
                }, 500);
            } else {
                // Show error message
                if (messageDiv) {
                    messageDiv.innerHTML = `<span class="text-danger">${data.message}</span>`;
                }
            }
        })
        .catch(error => {
            console.error('Error applying coupon:', error);
            if (messageDiv) {
                messageDiv.innerHTML = '<span class="text-danger">Failed to apply coupon. Please try again.</span>';
            }
        });
}

// ============================================
// 12. REMOVE COUPON CODE - AJAX
// ============================================

function removeCouponCode() {
    if (!confirm('Remove this coupon code?')) {
        return;
    }

    // Send AJAX request
    fetch(BASE_URL + '/api/coupon-remove', {
        method: 'POST'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message and reload page
                showCartToast(data.message, 'success');
                setTimeout(() => {
                    location.reload();
                }, 500);
            } else {
                showCartToast(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error removing coupon:', error);
            showCartToast('Failed to remove coupon. Please try again.', 'error');
        });
}

// ============================================
// AUTO-INITIALIZE ON DOM READY
// ============================================

document.addEventListener('DOMContentLoaded', function () {
    initAjaxCartHandlers();
});
