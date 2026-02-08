<?php
$pageTitle = "Login";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container auth-container-sm">
    <div class="auth-card">
        <h1 class="auth-title">
            Welcome Back
        </h1>
        <p class="auth-subtitle">
            Login to access your account
        </p>

        <form
            action="<?= BASE_URL ?>/auth/loginProcess<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>"
            method="POST">
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" required class="form-input" placeholder="your@email.com">
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" required class="form-input" placeholder="Enter your password">
            </div>

            <div class="form-group flex-between">
                <label class="checkbox-label center">
                    <input type="checkbox" name="remember" class="checkbox-input" style="margin-right: 0.5rem;">
                    <span class="checkbox-text">Remember me</span>
                </label>
                <a href="#" class="link-primary" style="font-size: 0.9375rem;">Forgot Password?</a>
            </div>

            <button type="submit" class="action-button" style="width: 100%; margin-bottom: 1.5rem;">
                Login to Account
            </button>
        </form>

        <div class="auth-footer">
            <p style="color: var(--text-secondary);">
                Don't have an account?
                <a href="<?= BASE_URL ?>/signup" class="link-primary bold">Sign Up</a>
            </p>
        </div>

        <!-- Demo Login Info -->
        <div class="demo-login">
            <h3 class="demo-title">Demo Login Credentials</h3>
            <p class="demo-text">
                <strong>Email:</strong> demo@easycart.com<br>
                <strong>Password:</strong> demo123
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>