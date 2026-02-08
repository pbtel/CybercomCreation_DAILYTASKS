<?php if (isset($pagination) && $pagination->isRequired()): ?>
    <div class="pagination-container">
        <!-- Previous Page -->
        <a href="<?php echo $pagination->getUrl($pagination->getCurrentPage() - 1); ?>"
            class="pagination-btn pagination-nav <?php echo $pagination->getCurrentPage() <= 1 ? 'disabled' : ''; ?>"
            aria-label="Previous Page">
            ‹
        </a>

        <?php foreach ($pagination->getPageRange() as $page): ?>
            <?php if ($page === '...'): ?>
                <span class="pagination-dots">...</span>
            <?php else: ?>
                <a href="<?php echo $pagination->getUrl($page); ?>"
                    class="pagination-btn <?php echo $page == $pagination->getCurrentPage() ? 'active' : ''; ?>">
                    <?php echo $page; ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>

        <!-- Next Page -->
        <a href="<?php echo $pagination->getUrl($pagination->getCurrentPage() + 1); ?>"
            class="pagination-btn pagination-nav <?php echo $pagination->getCurrentPage() >= $pagination->getTotalPages() ? 'disabled' : ''; ?>"
            aria-label="Next Page">
            ›
        </a>
    </div>
<?php endif; ?>