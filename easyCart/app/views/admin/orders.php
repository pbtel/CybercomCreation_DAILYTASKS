<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-2">
    <div class="flex-between align-center mb-2rem">
        <div>
            <a href="<?php echo BASE_URL; ?>/admin" class="text-primary fs-0-9">← Back to Dashboard</a>
            <h1 class="auth-title m-0">Manage Orders</h1>
        </div>
    </div>

    <div class="mb-1rem">
        <?php require __DIR__ . '/../partials/pagination.php'; ?>
    </div>

    <div class="card p-0" style="overflow: hidden;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Update Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td class="fs-0-85 text-muted">
                            <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                        </td>
                        <td class="font-600">
                            <?php echo $order['order_number']; ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($order['customer_name'] ?? 'Guest'); ?>
                        </td>
                        <td class="font-600">₹
                            <?php echo number_format($order['final_amount']); ?>
                        </td>
                        <td>
                            <span class="badge badge-<?php
                            echo match ($order['status']) {
                                'pending' => 'warning',
                                'processing' => 'info',
                                'shipped' => 'primary',
                                'delivered' => 'success',
                                'cancelled' => 'danger',
                                default => 'secondary'
                            };
                            ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </td>
                        <td>
                            <form action="<?php echo BASE_URL; ?>/admin/updateOrderStatus" method="POST"
                                class="flex-gap-0-5">
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                <select name="status" class="form-input py-0-25 px-0-5 fs-0-8" style="width: auto;">
                                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>
                                        Pending</option>
                                    <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>
                                        Shipped</option>
                                    <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <button type="submit" class="btn-primary py-0-25 px-0-5 fs-0-8">Update</button>
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

    .badge-info {
        background: rgba(0, 188, 212, 0.1);
        color: #00bcd4;
    }

    .badge-primary {
        background: rgba(var(--primary-rgb), 0.1);
        color: var(--primary);
    }

    .badge-success {
        background: rgba(76, 175, 80, 0.1);
        color: #4caf50;
    }

    .badge-danger {
        background: rgba(244, 67, 54, 0.1);
        color: #f44336;
    }
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>