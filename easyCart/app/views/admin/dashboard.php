<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-2">
    <div class="flex-between align-center mb-2rem">
        <h1 class="auth-title m-0">Admin Dashboard</h1>
        <div class="flex-gap-1">
            <a href="<?php echo BASE_URL; ?>/admin/orders" class="chip">Manage Orders</a>
            <a href="<?php echo BASE_URL; ?>/admin/products" class="chip">Manage Products</a>
            <a href="<?php echo BASE_URL; ?>/admin/importExport" class="chip"
                style="background: var(--primary); color: white;">Import / Export</a>
        </div>
    </div>

    <!-- STATS GRID -->
    <div class="stats-grid mb-3rem">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(var(--primary-rgb), 0.1); color: var(--primary);">💰</div>
            <div class="stat-info">
                <h3>₹
                    <?php echo number_format($totalSales); ?>
                </h3>
                <p>Total Revenue</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(var(--teal-rgb), 0.1); color: var(--teal);">📦</div>
            <div class="stat-info">
                <h3>
                    <?php echo $totalOrders; ?>
                </h3>
                <p>Total Orders</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(var(--accent-rgb), 0.1); color: var(--accent);">👤</div>
            <div class="stat-info">
                <h3>
                    <?php echo $totalUsers; ?>
                </h3>
                <p>Total Customers</p>
            </div>
        </div>
    </div>

    <!-- RECENT PENDING ORDERS -->
    <div class="card p-2rem">
        <h2 class="section-title mb-1-5">Pending Orders</h2>

        <?php if (empty($recentOrders)): ?>
            <p class="text-muted text-center py-2">No pending orders at the moment.</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td class="font-600">
                                    <?php echo $order['order_number']; ?>
                                </td>
                                <td>₹
                                    <?php echo number_format($order['final_amount']); ?>
                                </td>
                                <td><span class="badge badge-warning">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/admin/orders" class="text-primary font-600">View
                                        Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
    }

    .stat-card {
        background: var(--card-bg);
        padding: 2rem;
        border-radius: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
    }

    .stat-info h3 {
        font-size: 1.75rem;
        margin: 0;
        font-weight: 700;
    }

    .stat-info p {
        margin: 0;
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .admin-table {
        width: 100%;
        border-collapse: collapse;
    }

    .admin-table th {
        text-align: left;
        padding: 1rem;
        border-bottom: 2px solid var(--border-color);
        color: var(--text-secondary);
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .admin-table td {
        padding: 1.25rem 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    .badge-warning {
        background: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>