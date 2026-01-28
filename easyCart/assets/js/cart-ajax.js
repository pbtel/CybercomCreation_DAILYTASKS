/**
 * EasyCart - Phase 5: AJAX Cart Operations
 * Handles all AJAX-based cart interactions
 */

// ============================================
// 1. ADD TO CART - AJAX
// ============================================

function addToCartAjax(productId, quantity = 1, variant = {}) {
    // Show loading state
    const addButton = event ? event.target : null;
    if (addButton) {
        addButton.disabled = true;
        addButton.dataset.originalText = addButton.textContent;
        addButton.textContent = 'Adding...';
    }

    // Prepare form data
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    // Add variant data if provided
    if (variant.color) formData.append('variant_color', variant.color);
    if (variant.storage) formData.append('variant_storage', variant.storage);
    if (variant.size) formData.append('variant_size', variant.size);

    // Send AJAX request
    fetch('api/cart-add-ajax.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart badge
                updateCartBadge(data.cart_count);

                // Show success toast
                showCartToast(data.message, 'success');

                // Reset button
                if (addButton) {
                    addButton.textContent = addButton.dataset.originalText;
                    addButton.disabled = false;
                }
            } else {
                // Show error toast
                showCartToast(data.message, 'error');

                // Reset button
                if (addButton) {
                    addButton.textContent = addButton.dataset.originalText;
                    addButton.disabled = false;
                }
            }
        })
        .catch(error => {
            console.error('Error adding to cart:', error);
            showCartToast('Failed to add item to cart. Please try again.', 'error');

            // Reset button
            if (addButton) {
                addButton.textContent = addButton.dataset.originalText;
                addButton.disabled = false;
            }
        });
}

// ============================================
// 2. UPDATE CART QUANTITY - AJAX
// ============================================

function updateCartQuantityAjax(cartKey, quantity, itemElement) {
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
    fetch('api/cart-update-ajax.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart badge
                updateCartBadge(data.cart_count);

                // Update item subtotal
                if (itemElement) {
                    const subtotalElement = itemElement.querySelector('.item-subtotal');
                    if (subtotalElement) {
                        subtotalElement.textContent = '₹' + data.item_subtotal.toLocaleString('en-IN');
                    }
                }

                // Update cart summary
                updateCartSummaryDisplay(data.cart_subtotal);

                // Show success toast
                showCartToast(data.message, 'success');

                // Reset loading state
                if (itemElement) {
                    itemElement.style.opacity = '1';
                    itemElement.style.pointerEvents = 'auto';
                }
            } else {
                // Show error toast
                showCartToast(data.message, 'error');

                // Reset loading state
                if (itemElement) {
                    itemElement.style.opacity = '1';
                    itemElement.style.pointerEvents = 'auto';
                }
            }
        })
        .catch(error => {
            console.error('Error updating cart:', error);
            showCartToast('Failed to update cart. Please try again.', 'error');

            // Reset loading state
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
        itemElement.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        itemElement.style.opacity = '0';
        itemElement.style.transform = 'translateX(-20px)';
    }

    // Prepare form data
    const formData = new FormData();
    formData.append('cart_key', cartKey);

    // Send AJAX request after animation
    setTimeout(() => {
        fetch('api/cart-remove-ajax.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove item from DOM
                    if (itemElement) {
                        itemElement.remove();
                    }

                    // Update cart badge
                    updateCartBadge(data.cart_count);

                    // Update cart summary
                    updateCartSummaryDisplay(data.cart_subtotal);

                    // Show success toast
                    showCartToast(data.message, 'success');

                    // If cart is empty, show empty cart message
                    if (data.is_empty) {
                        showEmptyCartMessage();
                    }
                } else {
                    // Show error toast
                    showCartToast(data.message, 'error');

                    // Reset item appearance
                    if (itemElement) {
                        itemElement.style.opacity = '1';
                        itemElement.style.transform = 'translateX(0)';
                    }
                }
            })
            .catch(error => {
                console.error('Error removing item:', error);
                showCartToast('Failed to remove item. Please try again.', 'error');

                // Reset item appearance
                if (itemElement) {
                    itemElement.style.opacity = '1';
                    itemElement.style.transform = 'translateX(0)';
                }
            });
    }, 300);
}

// ============================================
// 4. FETCH CART SUMMARY - AJAX
// ============================================

function fetchCartSummary() {
    return fetch('api/cart-summary-ajax.php', {
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
    fetch('api/shipping-calculate-ajax.php', {
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
                        element.style.transition = 'all 0.3s ease';
                        element.style.transform = 'scale(1.05)';
                        setTimeout(() => {
                            element.style.transform = 'scale(1)';
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
            cartBadge.style.display = 'inline-block';

            // Add pulse animation
            cartBadge.classList.add('updating');
            setTimeout(() => {
                cartBadge.classList.remove('updating');
            }, 300);
        } else {
            cartBadge.style.display = 'none';
        }
    }
}

// ============================================
// 7. UPDATE CART SUMMARY DISPLAY
// ============================================

function updateCartSummaryDisplay(subtotal) {
    const summarySubtotal = document.getElementById('summary-subtotal');
    if (summarySubtotal) {
        summarySubtotal.textContent = '₹' + subtotal.toLocaleString('en-IN');

        // Add animation effect
        summarySubtotal.style.transition = 'all 0.3s ease';
        summarySubtotal.style.transform = 'scale(1.05)';
        setTimeout(() => {
            summarySubtotal.style.transform = 'scale(1)';
        }, 300);
    }
}

// ============================================
// 8. SHOW EMPTY CART MESSAGE
// ============================================

function showEmptyCartMessage() {
    const cartLayout = document.querySelector('.cart-layout');
    if (cartLayout) {
        cartLayout.innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem; background: white; border-radius: 16px;">
                <div style="font-size: 5rem; margin-bottom: 1rem;">🛒</div>
                <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Your cart is empty</h2>
                <p style="color: var(--text-secondary); margin-bottom: 2rem;">Start shopping to add items to your cart</p>
                <a href="products.php" style="display: inline-block; padding: 1rem 2.5rem; background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; text-decoration: none; border-radius: 12px; font-weight: 700;">
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
        container.style.cssText = `
            position: fixed;
            top: 6rem;
            right: 2rem;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        `;
        document.body.appendChild(container);
    }

    // Create toast element
    const toast = document.createElement('div');
    toast.className = 'toast toast-' + type;

    // Set colors based on type
    let bgColor, borderColor, icon;
    switch (type) {
        case 'success':
            bgColor = '#d4edda';
            borderColor = '#28a745';
            icon = '✓';
            break;
        case 'error':
            bgColor = '#f8d7da';
            borderColor = '#dc3545';
            icon = '✗';
            break;
        case 'info':
            bgColor = '#d1ecf1';
            borderColor = '#17a2b8';
            icon = 'ℹ';
            break;
        default:
            bgColor = '#f8f9fa';
            borderColor = '#6c757d';
            icon = '•';
    }

    // Style the toast
    toast.style.cssText = `
        background: ${bgColor};
        border-left: 4px solid ${borderColor};
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        min-width: 300px;
        max-width: 400px;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideIn 0.3s ease-out;
        cursor: pointer;
        transition: transform 0.2s, opacity 0.3s;
    `;

    // Create content
    toast.innerHTML = `
        <span style="font-size: 1.5rem; font-weight: bold; color: ${borderColor};">${icon}</span>
        <span style="flex: 1; color: #333; font-weight: 500;">${message}</span>
        <span style="font-size: 1.5rem; color: #666; cursor: pointer; line-height: 1; padding: 0 4px;">×</span>
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
    // Initialize Add to Cart buttons on product pages
    const addToCartForms = document.querySelectorAll('form#addToCartForm, form[action="cart-add.php"]');
    addToCartForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const productId = form.querySelector('input[name="product_id"]').value;
            const quantity = form.querySelector('input[name="quantity"]')?.value || 1;

            // Get variant data
            const variant = {};
            const variantColor = form.querySelector('input[name="variant_color"]');
            const variantStorage = form.querySelector('input[name="variant_storage"]');
            const variantSize = form.querySelector('input[name="variant_size"]');

            if (variantColor) variant.color = variantColor.value;
            if (variantStorage) variant.storage = variantStorage.value;
            if (variantSize) variant.size = variantSize.value;

            addToCartAjax(productId, quantity, variant);
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

    // Initialize Shipping Method changes on checkout page
    const shippingRadios = document.querySelectorAll('input[name="shipping_method"]');
    shippingRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            updateShippingCalculation(this.value);
        });
    });
}

// ============================================
// AUTO-INITIALIZE ON DOM READY
// ============================================

document.addEventListener('DOMContentLoaded', function () {
    initAjaxCartHandlers();
});
