/**
 * Cart Storage Manager - EasyCart
 * Handles localStorage for guest carts and session sync for logged-in users
 */

const CartStorage = {
    // Check if user is logged in
    isLoggedIn() {
        return document.querySelector('.header-nav a[href="logout.php"]') !== null;
    },

    // Get cart from appropriate storage
    getCart() {
        if (this.isLoggedIn()) {
            // For logged-in users, cart is managed by PHP session
            return null; // PHP handles it
        } else {
            // For guests, use localStorage
            const cart = localStorage.getItem('guestCart');
            return cart ? JSON.parse(cart) : [];
        }
    },

    // Save cart to localStorage (guests only)
    saveCart(cart) {
        if (!this.isLoggedIn()) {
            localStorage.setItem('guestCart', JSON.stringify(cart));
            this.updateCartBadge();
        }
    },

    // Add item to cart
    addItem(productId, quantity, variants) {
        if (this.isLoggedIn()) {
            // Use PHP form submission (existing behavior)
            return false; // Let form submit normally
        } else {
            // Add to localStorage
            const cart = this.getCart();
            const cartKey = this.generateCartKey(productId, variants);

            const existingItem = cart.find(item => item.key === cartKey);
            if (existingItem) {
                existingItem.quantity += quantity;
            } else {
                cart.push({
                    key: cartKey,
                    productId: productId,
                    quantity: quantity,
                    variants: variants,
                    addedAt: Date.now()
                });
            }

            this.saveCart(cart);
            return true; // Prevent form submission
        }
    },

    // Generate unique cart key
    generateCartKey(productId, variants) {
        const variantStr = JSON.stringify(variants || {});
        return `${productId}_${btoa(variantStr)}`;
    },

    // Update cart count badge
    updateCartBadge() {
        const cart = this.getCart();
        if (cart) {
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            const badge = document.querySelector('.cart-badge');
            if (badge) {
                badge.textContent = totalItems;
                badge.style.display = totalItems > 0 ? 'inline-block' : 'none';
            } else if (totalItems > 0) {
                // Create badge if it doesn't exist
                const cartLink = document.querySelector('.header-nav a[href="cart.php"]');
                if (cartLink) {
                    const newBadge = document.createElement('span');
                    newBadge.className = 'cart-badge';
                    newBadge.textContent = totalItems;
                    cartLink.appendChild(newBadge);
                }
            }
        }
    },

    // Sync localStorage cart to session on login
    syncToSession() {
        const guestCart = localStorage.getItem('guestCart');
        if (guestCart) {
            // Send to PHP to merge with session
            fetch('sync-cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: guestCart
            }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        localStorage.removeItem('guestCart');
                        console.log('Cart synced successfully');
                    }
                })
                .catch(error => {
                    console.error('Cart sync failed:', error);
                });
        }
    },

    // Initialize cart badge on page load
    init() {
        if (!this.isLoggedIn()) {
            this.updateCartBadge();
        }
    }
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
    CartStorage.init();
});
