<?php

require_once __DIR__ . '/../core/Controller.php';

class Controller_Checkout extends Controller
{
    public function index()
    {
        $cartModel = $this->model('Model_Cart');
        $userModel = $this->model('Model_User');
        $shippingModel = $this->model('Model_Shipping');
        $couponModel = $this->model('Model_Coupon');

        $cartItems = $cartModel->getItemsWithDetails();
        if (empty($cartItems)) {
            $this->redirect('cart');
            return;
        }

        $subtotal = $cartModel->getSubtotal();
        $appliedCoupon = $couponModel->getApplied();
        $couponDiscount = $couponModel->calculateDiscount($subtotal);
        $subtotalAfterCoupon = $subtotal - $couponDiscount;

        $availableShippingMethods = $shippingModel->getAvailableMethods($cartItems, $subtotalAfterCoupon);

        // Initial selection logic
        $selectedShippingMethod = Session::get('selected_shipping_method', 'standard');
        if (!in_array($selectedShippingMethod, $availableShippingMethods)) {
            $selectedShippingMethod = !empty($availableShippingMethods) ? $availableShippingMethods[0] : 'standard';
            Session::set('selected_shipping_method', $selectedShippingMethod);
            $shippingModel->setSelected($selectedShippingMethod);
        }

        // Add saved data (pending form entries) 
        $savedData = Session::get('pending_checkout_data', []);

        // Check for session data expiration
        if (isset($savedData['_timestamp']) && (time() - $savedData['_timestamp'] > 1800)) {
            Session::remove('pending_checkout_data');
            $savedData = [];
        }

        // --- Populate from Database if logged in ---
        if ($userModel->isLoggedIn()) {
            $user = $userModel->getCurrentUser();
            $dbCheckoutData = $cartModel->getCheckoutData($user['user_id']);

            // Merge: Combine both, preferring non-empty values
            if (empty($savedData)) {
                $savedData = $dbCheckoutData;
            } else {
                foreach ($dbCheckoutData as $key => $value) {
                    if (!empty($value) && (empty($savedData[$key]))) {
                        $savedData[$key] = $value;
                    }
                }
            }

            // Fallback: If First Name/Last Name still empty, split from user name
            if ((empty($savedData['first_name']) || empty($savedData['last_name'])) && !empty($user['name'])) {
                $parts = explode(' ', trim($user['name']));
                if (empty($savedData['first_name'])) {
                    $savedData['first_name'] = $parts[0] ?? '';
                }
                if (empty($savedData['last_name'])) {
                    array_shift($parts);
                    $savedData['last_name'] = implode(' ', $parts);
                }
            }
        }

        // --- Override selection with saved data if available ---
        if (isset($savedData['shipping_method']) && in_array($savedData['shipping_method'], $availableShippingMethods)) {
            $selectedShippingMethod = $savedData['shipping_method'];
            $shippingModel->setSelected($selectedShippingMethod);
        }

        // Ensure payment method is correctly prioritized
        if (!isset($savedData['payment_method']) || empty($savedData['payment_method'])) {
            $savedData['payment_method'] = 'cod'; // Default
        }

        // Calculate costs with finalized method
        $shippingCost = $shippingModel->calculateCost($subtotalAfterCoupon, $selectedShippingMethod);
        $tax = $shippingModel->calculateTax($subtotalAfterCoupon, $shippingCost);
        $total = $shippingModel->calculateOrderTotal($subtotalAfterCoupon, $shippingCost, $tax);

        // Pass data to view
        $data = [
            'pageTitle' => 'Checkout',
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'appliedCoupon' => $appliedCoupon,
            'couponDiscount' => $couponDiscount,
            'subtotalAfterCoupon' => $subtotalAfterCoupon,
            'availableShippingMethods' => $availableShippingMethods,
            'selectedShippingMethod' => $selectedShippingMethod,
            'shipping' => $shippingCost,
            'tax' => $tax,
            'total' => $total,
            'savedData' => $savedData
        ];

        // Use View_Checkout class
        require_once __DIR__ . '/../views/View_Checkout.php';
        $view = new View_Checkout($data);
        echo $view->toHtml();
    }

    /**
     * Real-time persistence for AJAX calls
     */
    public function persistAjax()
    {
        if (!$this->isPost()) {
            echo json_encode(['success' => false]);
            return;
        }

        $cartModel = $this->model('Model_Cart');

        // 1. Save to Session immediately for same-session persistence
        $data = $_POST;
        $data['_timestamp'] = time();
        Session::set('pending_checkout_data', $data);

        // 2. Persist to DB for cross-session/cross-device persistence
        $success = $cartModel->saveCheckoutData($data);

        echo json_encode(['success' => $success]);
    }

    public function place()
    {
        if (!$this->isPost()) {
            $this->redirect('checkout');
            return;
        }

        $cartModel = $this->model('Model_Cart');
        $userModel = $this->model('Model_User');

        // Handle Guest Checkout Interception
        if (!$userModel->isLoggedIn()) {
            // Save form data to session so it's maintained after login/back
            $formData = $_POST;
            $formData['_timestamp'] = time();
            Session::set('pending_checkout_data', $formData);

            // Also persist to database guest tables for fallback
            $cartModel->saveGuestCheckoutData($_POST);

            Session::setFlash('info', 'Please login to complete your order. Your details have been preserved.');
            $this->redirect('login?redirect=checkout'); // Redirect to login
            return;
        }

        // For logged in users: Finalize persistent checkout data
        $user = $userModel->getCurrentUser();
        $cartModel->saveUserCheckoutData($user['user_id'], $_POST);

        // Process order placement
        $subtotal = $cartModel->getSubtotal();

        require_once __DIR__ . '/../models/Model_Coupon.php';
        $couponModel = new Model_Coupon();
        $couponDiscount = $couponModel->calculateDiscount($subtotal);
        $subtotalAfterCoupon = $subtotal - $couponDiscount;

        require_once __DIR__ . '/../models/Model_Shipping.php';
        $shippingModel = new Model_Shipping();
        $shippingMethod = $_POST['shipping_method'] ?? 'standard';
        $shippingCost = $shippingModel->calculateCost($subtotalAfterCoupon, $shippingMethod);
        $tax = $shippingModel->calculateTax($subtotalAfterCoupon, $shippingCost);
        $total = $subtotalAfterCoupon + $shippingCost + $tax;

        // Transition the "CART" order to "PENDING"
        $orderId = $cartModel->getOrCreateDbOrderCart($user['user_id']);
        $orderModel = $this->model('Model_Order');
        $orderModel->load($orderId);

        if ($orderModel->getId()) {
            $orderNumber = 'ORD-' . strtoupper(substr(md5(uniqid()), 0, 8));

            // Update order status and final fields
            $orderModel->setData('order_number', $orderNumber);
            $orderModel->setData('status', 'pending');
            $orderModel->setData('subtotal', $subtotal);
            $orderModel->setData('shipping_cost', $shippingCost);
            $orderModel->setData('tax', $tax);
            $orderModel->setData('discount_amount', $couponDiscount);
            $orderModel->setData('final_amount', $total);
            $orderModel->setData('customer_email', $_POST['email']);
            $orderModel->setData('customer_phone', $_POST['phone']);
            $orderModel->setData('updated_at', date('Y-m-d H:i:s'));

            $orderModel->save();

            // Clear the cart
            $cartModel->clearCart();
            Session::remove('checkout_data');
            Session::remove('pending_checkout_data');
            Session::remove('selected_shipping_method');

            Session::setFlash('success', 'Thank you! Your order #' . $orderNumber . ' has been placed.');
            $this->redirect('order/success/' . $orderId);
        } else {
            Session::setFlash('error', 'Failed to process order. Please try again.');
            $this->redirect('checkout');
        }
    }
}
