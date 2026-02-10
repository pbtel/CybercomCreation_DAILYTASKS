<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-2">
    <div class="flex-between align-center mb-2rem">
        <div>
            <a href="<?php echo BASE_URL; ?>/admin" class="text-primary fs-0-9">← Back to Dashboard</a>
            <h1 class="auth-title m-0">Manage Products</h1>
        </div>
    </div>

    <div class="mb-1rem">
        <?php require __DIR__ . '/../partials/pagination.php'; ?>
    </div>

    <div class="card p-0" style="overflow: hidden;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Update Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <div class="flex-center-gap-1">
                                <div class="fs-1-5">
                                    <?php echo $product['image'] ?? '📦'; ?>
                                </div>
                                <div class="font-600">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </div>
                            </div>
                        </td>
                        <td class="fs-0-85 text-muted">
                            <?php echo $product['sku']; ?>
                        </td>
                        <td><span class="chip">
                                <?php echo ucfirst($product['category']); ?>
                            </span></td>
                        <td class="font-600">₹
                            <?php echo number_format($product['price']); ?>
                        </td>
                        <td>
                            <span
                                class="badge <?php echo $product['stock'] > 10 ? 'badge-success' : ($product['stock'] > 0 ? 'badge-warning' : 'badge-danger'); ?>">
                                <?php echo $product['stock']; ?> in stock
                            </span>
                        </td>
                        <td>
                            <form action="<?php echo BASE_URL; ?>/admin/updateStock" method="POST" class="flex-gap-0-5">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="number" name="stock" value="<?php echo $product['stock']; ?>" min="0"
                                    class="form-input py-0-25 px-0-5 fs-0-8" style="width: 80px;">
                                <button type="submit" class="btn-primary py-0-25 px-0-5 fs-0-8">Save</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php require __DIR__ . '/../partials/pagination.php'; ?>
</div>

<style>
    .admin-table {
        width: 100%;
        border-collapse: collapse;
    }

    .admin-table th {
        text-align: left;
        padding: 1.25rem 1rem;
        background: rgba(var(--primary-rgb), 0.03);
        border-bottom: 2px solid var(--border-color);
        color: var(--text-secondary);
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .admin-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
    }
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>