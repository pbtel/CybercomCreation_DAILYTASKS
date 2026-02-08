<?php
$pageTitle = "Login";
require_once 'includes/header.php';
?>

    <div class="container" style="max-width: 500px; margin-top: 3rem; margin-bottom: 3rem;">
        <div style="background: white; padding: 3rem; border-radius: 16px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);">
            <h1 style="font-size: 2.25rem; font-weight: 700; text-align: center; margin-bottom: 0.5rem; background: linear-gradient(135deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                Welcome Back
            </h1>
            <p style="text-align: center; color: var(--text-secondary); margin-bottom: 2rem;">
                Login to access your account
            </p>

            <form action="login-process.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>" method="POST">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Email Address</label>
                    <input type="email" name="email" required 
                           style="width: 100%; padding: 0.875rem; border: 2px solid var(--border); border-radius: 12px; font-size: 1rem;"
                           placeholder="your@email.com">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Password</label>
                    <input type="password" name="password" required 
                           style="width: 100%; padding: 0.875rem; border: 2px solid var(--border); border-radius: 12px; font-size: 1rem;"
                           placeholder="Enter your password">
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" name="remember" style="margin-right: 0.5rem;">
                        <span style="font-size: 0.9375rem; color: var(--text-secondary);">Remember me</span>
                    </label>
                    <a href="#" style="font-size: 0.9375rem; color: var(--primary); font-weight: 600;">Forgot Password?</a>
                </div>

                <button type="submit" class="action-button" style="width: 100%; margin-bottom: 1.5rem;">
                    Login to Account
                </button>
            </form>

            <div style="text-align: center; padding-top: 1.5rem; border-top: 2px solid var(--border);">
                <p style="color: var(--text-secondary);">
                    Don't have an account? 
                    <a href="signup.php" style="color: var(--primary); font-weight: 700;">Sign Up</a>
                </p>
            </div>

            <!-- Demo Login Info -->
            <div style="margin-top: 2rem; padding: 1.5rem; background: var(--bg-primary); border-radius: 12px;">
                <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem;">Demo Login Credentials</h3>
                <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">
                    <strong>Email:</strong> demo@easycart.com<br>
                    <strong>Password:</strong> demo123
                </p>
            </div>
        </div>
    </div>

<?php require_once 'includes/footer.php'; ?>
