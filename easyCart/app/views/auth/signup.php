<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container auth-container-lg">
    <div class="auth-card">
        <h1 class="auth-title">
            Create Account
        </h1>
        <p class="auth-subtitle">
            Join EasyCart today and start shopping!
        </p>

        <form action="<?php echo BASE_URL; ?>/auth/signupProcess" method="POST">
            <div class="form-row form-input-group">
                <div>
                    <label class="form-label">First Name *</label>
                    <input type="text" name="first_name" required class="form-input">
                </div>
                <div>
                    <label class="form-label">Last Name *</label>
                    <input type="text" name="last_name" required class="form-input">
                </div>
            </div>

            <div class="form-input-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" required class="form-input">
            </div>


            <div class="form-input-group">
                <label class="form-label">Password *</label>
                <input type="password" name="password" required class="form-input">
                <small class="text-muted-sm">Minimum 6 characters</small>
            </div>

            <div class="form-input-group">
                <label class="form-label">Confirm Password *</label>
                <input type="password" name="confirm_password" required class="form-input">
            </div>

            <div class="form-flex flex-start-gap-0-75">
                <input type="checkbox" name="terms" required class="mt-0-25">
                <span class="form-checkbox-text">
                    I agree to the <a href="#" class="color-primary font-600">Terms and Conditions</a>
                    and <a href="#" class="color-primary font-600">Privacy Policy</a>
                </span>
            </div>

            <div class="form-flex flex-start-gap-0-75 mb-2">
                <input type="checkbox" name="newsletter" class="mt-0-25">
                <span class="form-checkbox-text">
                    Subscribe to newsletter for exclusive deals
                </span>
            </div>

            <button type="submit" class="action-button w-100 mb-1-5">
                Create Account
            </button>
        </form>

        <div class="auth-footer">
            <p class="text-muted-sm">
                Already have an account?
                <a href="<?php echo BASE_URL; ?>/login" class="color-primary font-700">Login</a>
            </p>
        </div>

        <!-- Member Benefits -->
        <div class="benefits-box">
            <h3 class="fs-1-0625 font-700 mb-1">Member Benefits</h3>
            <div class="benefits-grid">
                <div class="benefit-tick">✓ Free shipping on orders over ₹999</div>
                <div class="benefit-tick">✓ Early access to sales</div>
                <div class="benefit-tick">✓ Earn reward points</div>
                <div class="benefit-tick">✓ Track orders in real-time</div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>