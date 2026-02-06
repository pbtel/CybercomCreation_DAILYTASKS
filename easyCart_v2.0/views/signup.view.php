<?php
$pageTitle = "Sign Up";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container auth-container">
    <div class="auth-card">
        <h1 class="auth-title">
            Create Account
        </h1>
        <p class="auth-subtitle">
            Join EasyCart today and start shopping!
        </p>

        <form
            action="../../signup-process.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>"
            method="POST">
            <div class="form-grid">
                <div>
                    <label class="form-label">First Name *</label>
                    <input type="text" name="first_name" required class="form-input">
                </div>
                <div>
                    <label class="form-label">Last Name *</label>
                    <input type="text" name="last_name" required class="form-input">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" required class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">Phone Number *</label>
                <input type="tel" name="phone" required class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">Password *</label>
                <input type="password" name="password" required class="form-input">
                <small class="form-hint">Minimum 8 characters</small>
            </div>

            <div class="form-group">
                <label class="form-label">Confirm Password *</label>
                <input type="password" name="confirm_password" required class="form-input">
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="terms" required class="checkbox-input">
                    <span class="checkbox-text">
                        I agree to the <a href="#" class="link-primary">Terms and Conditions</a>
                        and <a href="#" class="link-primary">Privacy Policy</a>
                    </span>
                </label>
            </div>

            <div class="form-group">
                <label class="checkbox-label center">
                    <input type="checkbox" name="newsletter" class="checkbox-input center">
                    <span class="checkbox-text">
                        Subscribe to newsletter for exclusive deals
                    </span>
                </label>
            </div>

            <button type="submit" class="action-button" style="width: 100%; margin-bottom: 1.5rem;">
                Create Account
            </button>
        </form>

        <div class="auth-footer">
            <p style="color: var(--text-secondary);">
                Already have an account?
                <a href="../../login.php" class="link-primary bold">Login</a>
            </p>
        </div>

        <!-- Member Benefits -->
        <div class="auth-benefits">
            <h3 class="benefits-title">Member Benefits</h3>
            <div class="benefits-grid">
                <div class="benefit-item">✓ Free shipping on orders over \u0026#8377;999</div>
                <div class="benefit-item">✓ Early access to sales</div>
                <div class="benefit-item">✓ Earn reward points</div>
                <div class="benefit-item">✓ Track orders in real-time</div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>