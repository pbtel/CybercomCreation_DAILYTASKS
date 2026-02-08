<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container auth-container">
    <div class="auth-card">
        <h1 class="auth-title">
            Welcome Back
        </h1>
        <p class="auth-subtitle">
            Login to access your account
        </p>

        <form action="<?php echo BASE_URL; ?>/auth/loginProcess" method="POST">
            <?php if (isset($redirect)): ?>
                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
            <?php endif; ?>

            <div class="form-input-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" required class="form-input" placeholder="your@email.com">
            </div>

            <div class="form-input-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" required class="form-input" placeholder="Enter your password">
            </div>

            <div class="form-flex">
                <label class="form-checkbox-label">
                    <input type="checkbox" name="remember" class="mr-0-75">
                    <span class="form-checkbox-text">Remember me</span>
                </label>
                <a href="#" class="fs-0-9375 color-primary font-600">Forgot Password?</a>
            </div>

            <button type="submit" class="action-button w-100 mb-1-5">
                Login to Account
            </button>
        </form>

        <div class="auth-footer">
            <p class="text-muted-sm">
                Don't have an account?
                <a href="<?php echo BASE_URL; ?>/signup" class="color-primary font-700">Sign Up</a>
            </p>
        </div>

        <!-- Demo Login Info -->
        <div class="demo-box">
            <h3 class="fs-1-0625 font-700 mb-1">Demo Login Credentials</h3>
            <p class="text-muted-sm mb-0-5">
                <strong>Email:</strong> demo@easycart.com<br>
                <strong>Password:</strong> demo123
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
