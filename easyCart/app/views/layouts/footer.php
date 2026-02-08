<!-- FOOTER -->
<footer class="site-footer">
    <div class="container footer-container">
        <div class="footer-grid">
            <!-- Brand Section -->
            <div class="footer-brand">
                <a href="<?php echo BASE_URL; ?>" class="footer-logo">Easy<span>Cart</span></a>
                <p class="footer-tagline">Premium shopping experience with curated collections and lightning-fast
                    delivery.</p>
                <div class="footer-socials">
                    <a href="#" class="social-link" title="Facebook">𝕗</a>
                    <a href="#" class="social-link" title="Twitter">𝕥</a>
                    <a href="#" class="social-link" title="Instagram">𝕚</a>
                    <a href="#" class="social-link" title="LinkedIn">𝕝</a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-links-group">
                <h4 class="footer-heading">Shop</h4>
                <ul class="footer-links-list">
                    <li><a href="<?php echo BASE_URL; ?>/products">All Products</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/products?category=electronics">Electronics</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/products?category=fashion">Fashion</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/products?category=home">Home & Living</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div class="footer-links-group">
                <h4 class="footer-heading">Support</h4>
                <ul class="footer-links-list">
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">Shipping Policy</a></li>
                    <li><a href="#">Returns & Refunds</a></li>
                    <li><a href="#">FAQS</a></li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div class="footer-newsletter">
                <h4 class="footer-heading">Newsletter</h4>
                <p class="footer-tagline">Subscribe for exclusive offers and deals.</p>
                <form class="newsletter-form" onsubmit="event.preventDefault(); alert('Thank you for subscribing!');">
                    <input type="email" placeholder="Email address" required class="newsletter-input">
                    <button type="submit" class="newsletter-btn">Join</button>
                </form>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="footer-bottom">
            <div class="footer-copyright">
                &copy; <?php echo date('Y'); ?> <span class="text-primary font-700">EasyCart</span>. All rights
                reserved.
            </div>
            <div class="footer-legal">
                <a href="#">Privacy</a>
                <a href="#">Terms</a>
                <a href="#">Cookies</a>
            </div>
        </div>
    </div>
</footer>

</body>

</html>