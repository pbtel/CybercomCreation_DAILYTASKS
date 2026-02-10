# Implementation Plan - Fix Sales Cart Values

## Problem
The new columns in `sales_cart` (`subtotal`, `final_amount`, `shipping_cost`, etc.) are not reflecting the correct values, likely because they are only updated during the specific "Place Order" action. The user wants these values to be accurate even if the user hasn't placed the order yet (e.g., during browsing/cart updates).

## Solution
We will ensure that the `sales_cart` table is updated continuously whenever the cart content changes (add/update/remove items), not just at the final checkout step.

## Steps

### 1. Update `CartModel::syncGuestCartToDb`
This method is called whenever the guest cart changes (items added/removed). We will enhance it to:
*   Calculate the current `subtotal` of items.
*   Calculate `discount_amount` if a coupon is applied.
*   Retrieve the currently selected `shipping_method` for this cart (default to 'standard' if none).
*   Calculate `shipping_cost` `tax`, and `final_amount`.
*   Update the `sales_cart` row with these calculated values.

### 2. Update `CartModel::getOrCreateDbCart`
*   Ensure that when a new `sales_cart` row is created, we generate and save a unique `order_number` (e.g., `CART-guest_session_id`) so it is never NULL.

### 3. Logic Flow
*   **Add Item**: `addToCart` -> `setCurrentCart` -> `syncGuestCartToDb` ->  **Updates specific item rows AND Updates sales_cart total columns.**
*   **Update Qty**: `updateQuantity` -> ... -> **Updates sales_cart total columns.**
*   **Checkout**: `saveGuestCheckoutData` -> **Updates contact info (email/phone) and specific shipping/tax based on form selection.**

## File Changes
*   `app/models/CartModel.php`:
    *   Modify `syncGuestCartToDb` to perform calculations and run an `UPDATE sales_cart SET ...` query.
    *   Modify `getOrCreateDbCart` to insert `order_number`.
    *   Helper method `calculateCartTotals($cartId)` to allow reuse of calculation logic.

## Verification
*   Add items as guest. Check DB `sales_cart`. Subtotal should match.
*   Apply coupon. Check DB. Discount should appear.
*   Change quantity. Check DB. Totals should update.
*   Checkout form. Check DB. Email/Phone should appear.
