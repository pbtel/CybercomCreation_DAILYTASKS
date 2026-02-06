<?php
$pageTitle = "Sign Up";
require_once 'includes/header.php';
?>

    <div class="container" style="max-width: 700px; margin-top: 3rem; margin-bottom: 3rem;">
        <div style="background: white; padding: 3rem; border-radius: 16px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);">
            <h1 style="font-size: 2.25rem; font-weight: 700; text-align: center; margin-bottom: 0.5rem; background: linear-gradient(135deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                Create Account
            </h1>
            <p style="text-align: center; color: var(--text-secondary); margin-bottom: 2rem;">
                Join EasyCart today and start shopping!
            </p>

            <form action="signup-process.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>" method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">First Name *</label>
                        <input type="text" name="first_name" required 
                               style="width: 100%; padding: 0.875rem; border: 2px solid var(--border); border-radius: 12px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Last Name *</label>
                        <input type="text" name="last_name" required 
                               style="width: 100%; padding: 0.875rem; border: 2px solid var(--border); border-radius: 12px;">
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Email Address *</label>
                    <input type="email" name="email" required 
                           style="width: 100%; padding: 0.875rem; border: 2px solid var(--border); border-radius: 12px;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Phone Number *</label>
                    <input type="tel" name="phone" required 
                           style="width: 100%; padding: 0.875rem; border: 2px solid var(--border); border-radius: 12px;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Password *</label>
                    <input type="password" name="password" required 
                           style="width: 100%; padding: 0.875rem; border: 2px solid var(--border); border-radius: 12px;">
                    <small style="color: var(--text-secondary); font-size: 0.8125rem;">Minimum 8 characters</small>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Confirm Password *</label>
                    <input type="password" name="confirm_password" required 
                           style="width: 100%; padding: 0.875rem; border: 2px solid var(--border); border-radius: 12px;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: flex; align-items: start; cursor: pointer;">
                        <input type="checkbox" name="terms" required style="margin-right: 0.75rem; margin-top: 0.25rem;">
                        <span style="font-size: 0.9375rem; color: var(--text-secondary);">
                            I agree to the <a href="#" style="color: var(--primary); font-weight: 600;">Terms and Conditions</a> 
                            and <a href="#" style="color: var(--primary); font-weight: 600;">Privacy Policy</a>
                        </span>
                    </label>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" name="newsletter" style="margin-right: 0.75rem;">
                        <span style="font-size: 0.9375rem; color: var(--text-secondary);">
                            Subscribe to newsletter for exclusive deals
                        </span>
                    </label>
                </div>

                <button type="submit" class="action-button" style="width: 100%; margin-bottom: 1.5rem;">
                    Create Account
                </button>
            </form>

            <div style="text-align: center; padding-top: 1.5rem; border-top: 2px solid var(--border);">
                <p style="color: var(--text-secondary);">
                    Already have an account? 
                    <a href="login.php" style="color: var(--primary); font-weight: 700;">Login</a>
                </p>
            </div>

            <!-- Member Benefits -->
            <div style="margin-top: 2rem; padding: 1.5rem; background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(236, 72, 153, 0.05)); border-radius: 12px;">
                <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem;">Member Benefits</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div style="font-size: 0.875rem; color: var(--text-secondary);">✓ Free shipping on orders over ₹999</div>
                    <div style="font-size: 0.875rem; color: var(--text-secondary);">✓ Early access to sales</div>
                    <div style="font-size: 0.875rem; color: var(--text-secondary);">✓ Earn reward points</div>
                    <div style="font-size: 0.875rem; color: var(--text-secondary);">✓ Track orders in real-time</div>
                </div>
            </div>
        </div>
    </div>

<?php require_once 'includes/footer.php'; ?>
