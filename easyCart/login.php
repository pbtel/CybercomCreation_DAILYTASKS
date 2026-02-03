<?php
$pageTitle = "Login";
require_once 'includes/header.php';
?>

    <div class="container auth-container">
        <div class="auth-card">
            <h1 class="auth-title">
                Welcome Back
            </h1>
            <p class="auth-subtitle">
                Login to access your account
            </p>

            <form action="login-process.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>" method="POST">
                <div class="form-input-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" required 
                           class="form-input"
                           placeholder="your@email.com">
                </div>

                <div class="form-input-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" required 
                           class="form-input"
                           placeholder="Enter your password">
                </div>

                <div class="form-flex">
                    <label class="form-checkbox-label">
                        <input type="checkbox" name="remember" style="margin-right: 0.5rem;">
                        <span class="form-checkbox-text">Remember me</span>
                    </label>
                    <a href="#" style="font-size: 0.9375rem; color: var(--primary); font-weight: 600;">Forgot Password?</a>
                </div>

                <button type="submit" class="action-button" style="width: 100%; margin-bottom: 1.5rem;">
                    Login to Account
                </button>
            </form>

            <div class="auth-footer">
                <p class="text-muted-sm">
                    Don't have an account? 
                    <a href="signup.php" style="color: var(--primary); font-weight: 700;">Sign Up</a>
                </p>
            </div>

            <!-- Demo Login Info -->
            <div class="demo-box">
                <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem;">Demo Login Credentials</h3>
                <p class="text-muted-sm" style="margin-bottom: 0.5rem;">
                    <strong>Email:</strong> demo@easycart.com<br>
                    <strong>Password:</strong> demo123
                </p>
            </div>
        </div>
    </div>

<?php require_once 'includes/footer.php'; ?>
