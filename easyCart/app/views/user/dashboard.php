<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container py-2rem">
    <!-- GREETING SECTION -->
    <div class="dashboard-hero mb-3rem">
        <div class="hero-content">
            <h1 class="welcome-text">Welcome back, <span
                    class="text-primary"><?php echo explode(' ', $user['name'])[0]; ?></span></h1>
            <p class="text-secondary fs-1-1">Here's what's happening with your account today.</p>
        </div>
        <div class="hero-stats">
            <div class="hero-stat-item">
                <span class="hero-stat-label">Total Spent</span>
                <span class="hero-stat-value">₹<?php echo number_format($totalSpent); ?></span>
            </div>
            <div class="hero-stat-divider"></div>
            <div class="hero-stat-item">
                <span class="hero-stat-label">Total Orders</span>
                <span class="hero-stat-value"><?php echo $stats['total']; ?></span>
            </div>
        </div>
    </div>

    <!-- STATS GRID -->
    <div class="stats-grid mb-3rem">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(var(--primary-rgb), 0.1); color: var(--primary);">⏳</div>
            <div class="stat-info">
                <h3><?php echo $stats['processing'] + $stats['pending']; ?></h3>
                <p>Pending / Processing</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(var(--accent-rgb), 0.1); color: var(--accent);">🚛</div>
            <div class="stat-info">
                <h3><?php echo $stats['shipped']; ?></h3>
                <p>In Transit</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(var(--teal-rgb), 0.1); color: var(--teal);">✅</div>
            <div class="stat-info">
                <h3><?php echo $stats['delivered']; ?></h3>
                <p>Delivered</p>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- MAIN COLUMN -->
        <div class="dashboard-main">
            <!-- GRAPH CARD -->
            <div class="card p-2rem mb-2rem">
                <div class="flex-between align-center mb-1-5 flex-wrap gap-1">
                    <h2 class="section-title m-0">Cumulative Spending Trend</h2>
                    <div class="text-secondary fs-0-8 font-600">Growth of your total spend over every order</div>
                </div>
                <div class="chart-container" style="position: relative; height:280px; width:100%">
                    <canvas id="orderChart"></canvas>
                </div>
            </div>

            <!-- RECENT ORDERS -->
            <div class="card p-2rem">
                <div class="flex-between align-center mb-2rem flex-wrap gap-1">
                    <h2 class="section-title m-0">Recent Orders</h2>
                    <a href="<?php echo BASE_URL; ?>/orders" class="text-primary font-700 fs-0-9">View All Activity
                        →</a>
                </div>

                <?php if (empty($recentOrders)): ?>
                    <div class="empty-state-container">
                        <div class="empty-state-icon">🛍️</div>
                        <h3 class="empty-state-title">No orders yet</h3>
                        <p class="empty-state-text">You haven't placed any orders yet. Start shopping to fill this area!</p>
                        <a href="<?php echo BASE_URL; ?>/products" class="btn-primary-custom">Start Shopping</a>
                    </div>
                <?php else: ?>
                    <div style="overflow-x: auto; margin: 0 -1rem;">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td class="font-700 text-primary">#<?php echo substr($order['order_number'], -6); ?>
                                        </td>
                                        <td class="text-secondary">
                                            <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                        </td>
                                        <td class="font-700">₹<?php echo number_format($order['final_amount']); ?></td>
                                        <td>
                                            <a href="<?php echo BASE_URL; ?>/order/<?php echo $order['order_id']; ?>"
                                                class="action-link">Details</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- SIDEBAR COLUMN -->
        <div class="dashboard-sidebar">
            <div class="card p-2rem mb-2rem">
                <h2 class="section-title mb-1-5">Account Links</h2>
                <div class="quick-links">
                    <a href="<?php echo BASE_URL; ?>/orders" class="quick-link-item">
                        <span class="link-icon">📦</span>
                        <div class="link-content">
                            <span class="link-text">Order History</span>
                            <span class="link-subtext">View all past purchases</span>
                        </div>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/cart" class="quick-link-item">
                        <span class="link-icon">🛒</span>
                        <div class="link-content">
                            <span class="link-text">View My Cart</span>
                            <span class="link-subtext">Review items before checkout</span>
                        </div>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/products" class="quick-link-item">
                        <span class="link-icon">🔍</span>
                        <div class="link-content">
                            <span class="link-text">Browse Products</span>
                            <span class="link-subtext">Explore latest electronics</span>
                        </div>
                    </a>
                </div>
            </div>

            <div class="help-card card p-2rem bg-primary text-white">
                <div class="help-icon">🎧</div>
                <h3 class="m-0 fs-1-2 font-800">Support Center</h3>
                <p class="fs-0-85 opacity-0-8 mt-0-5 mb-1-5">Need help with an order or have a question? Our team is
                    here for you.</p>
                <a href="#" class="btn-help">Contact Us</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('orderChart').getContext('2d');

        // Prepare data from PHP
        const rawData = <?php echo json_encode($chartData); ?>;

        // Fallback if no data
        if (rawData.length === 0) {
            ctx.font = '16px Outfit';
            ctx.fillStyle = '#64748b';
            ctx.textAlign = 'center';
            ctx.fillText('Not enough order history for spending trends.', ctx.canvas.width / 2, ctx.canvas.height / 2);
            return;
        }

        const labels = rawData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        });

        // Calculate cumulative spend
        let currentTotal = 0;
        const values = rawData.map(item => {
            currentTotal += parseFloat(item.total);
            return currentTotal;
        });

        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.2)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Spend',
                    data: values,
                    borderColor: '#6366f1',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#6366f1',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 9,
                    pointHoverBackgroundColor: '#6366f1',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { family: 'Outfit', size: 14, weight: 'bold' },
                        bodyFont: { family: 'Outfit', size: 13 },
                        padding: 15,
                        cornerRadius: 12,
                        displayColors: false,
                        callbacks: {
                            title: function (context) {
                                const index = context[0].dataIndex;
                                return 'Order #' + rawData[index].order_number.slice(-8);
                            },
                            label: function (context) {
                                const index = context.dataIndex;
                                const amount = parseFloat(rawData[index].total).toLocaleString();
                                const total = context.parsed.y.toLocaleString();
                                return [
                                    'Order Amount: ₹' + amount,
                                    'Cumulative Total: ₹' + total
                                ];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(226, 232, 240, 0.5)',
                            drawBorder: false
                        },
                        ticks: {
                            font: { family: 'Outfit', size: 12 },
                            color: '#64748b',
                            callback: function (value) { return '₹' + value.toLocaleString(); }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Outfit', size: 12 },
                            color: '#64748b'
                        }
                    }
                }
            }
        });
    });
</script>

<style>
    /* DASHBOARD REFINED CSS - IMPROVED SPACING */
    .py-2rem {
        padding-top: 4rem;
        padding-bottom: 4rem;
    }

    .card {
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        padding: 2.5rem !important;
        /* Unified deeper padding */
    }

    .dashboard-hero {
        background: var(--bg-secondary);
        padding: 4rem;
        /* More breathing room */
        border-radius: 2.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid var(--border);
        background: linear-gradient(135deg, var(--bg-secondary) 0%, rgba(var(--primary-rgb), 0.04) 100%);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.03);
    }

    .welcome-text {
        font-size: 3rem;
        margin: 0;
        letter-spacing: -0.04em;
        font-weight: 800;
        line-height: 1;
    }

    .hero-stats {
        display: flex;
        gap: 4.5rem;
        /* Wider separation */
        align-items: center;
    }

    .hero-stat-item {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .hero-stat-label {
        font-size: 0.85rem;
        color: var(--text-secondary);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 0.75rem;
    }

    .hero-stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1;
    }

    .hero-stat-divider {
        width: 1px;
        height: 60px;
        background: var(--border);
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        /* Slightly wider sidebar for better links */
        gap: 3.5rem;
        /* Significantly more gap between columns */
        align-items: start;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 3rem;
        /* More gap between status cards */
    }

    .stat-card {
        background: var(--bg-secondary);
        padding: 2rem 2.5rem;
        border-radius: 2rem;
        display: flex;
        align-items: center;
        gap: 2rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.03);
        border: 1px solid var(--border);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        border-color: var(--primary);
    }

    .stat-icon {
        width: 64px;
        height: 64px;
        border-radius: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.06);
    }

    .stat-info h3 {
        font-size: 2rem;
        margin: 0;
        font-weight: 800;
        line-height: 1.1;
    }

    .stat-info p {
        margin: 6px 0 0;
        color: var(--text-secondary);
        font-size: 0.95rem;
        font-weight: 600;
    }

    .dashboard-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .dashboard-table th {
        text-align: left;
        padding: 1.5rem 1.25rem;
        background: rgba(var(--primary-rgb), 0.03);
        color: var(--text-secondary);
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        font-weight: 700;
        border-bottom: 2px solid var(--border);
    }

    .dashboard-table td {
        padding: 1.5rem 1.25rem;
        border-bottom: 1px solid var(--border);
        font-size: 1rem;
    }

    .status-pill {
        padding: 0.5rem 1.2rem;
        border-radius: 2.5rem;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .status-pending {
        background: #fffbeb;
        color: #92400e;
    }

    .status-processing {
        background: #eff6ff;
        color: #1e40af;
    }

    .status-shipped {
        background: #fdf2f8;
        color: #9d174d;
    }

    .status-delivered {
        background: #f0fdf4;
        color: #166534;
    }

    .status-cancelled {
        background: #fef2f2;
        color: #991b1b;
    }

    .action-link {
        color: var(--primary);
        font-weight: 800;
        text-decoration: none;
        font-size: 0.85rem;
        transition: opacity 0.2s;
    }

    .action-link:hover {
        opacity: 0.7;
    }

    .quick-links {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        /* More space between links */
    }

    .quick-link-item {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 1.25rem;
        border-radius: 1.5rem;
        text-decoration: none;
        color: var(--text-primary);
        transition: all 0.2s ease;
        border: 1px solid transparent;
        background: rgba(var(--primary-rgb), 0.02);
    }

    .quick-link-item:hover {
        background: rgba(var(--primary-rgb), 0.05);
        border-color: rgba(var(--primary-rgb), 0.1);
        transform: translateX(8px);
    }

    .link-icon {
        font-size: 1.4rem;
    }

    .link-content {
        font-weight: 700;
        font-size: 1rem;
        display: block;
    }

    .link-subtext {
        font-size: 0.75rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .help-card {
        position: relative;
        background: linear-gradient(135deg, var(--primary) 0%, #4f46e5 100%);
        border: none;
        text-align: center;
        padding-top: 3.5rem !important;
        margin-top: 1rem;
        /* Extra separation from Account links */
    }

    .help-icon {
        position: absolute;
        top: -20px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 60px;
        background: white;
        border-radius: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .btn-help {
        display: block;
        width: 100%;
        padding: 1rem;
        background: white;
        color: var(--primary);
        border-radius: 1rem;
        text-decoration: none;
        text-align: center;
        font-weight: 800;
        font-size: 0.95rem;
        transition: all 0.2s;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .btn-help:hover {
        transform: scale(1.02);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
    }

    .mb-3rem {
        margin-bottom: 4rem;
    }

    /* Global section spacing boost */
    .mb-2rem {
        margin-bottom: 3rem;
    }

    @media (max-width: 1200px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
            gap: 3rem;
        }
    }

    @media (max-width: 768px) {
        .dashboard-hero {
            padding: 3rem 2rem;
        }

        .hero-stats {
            gap: 2.5rem;
        }

        .welcome-text {
            font-size: 2.25rem;
        }
    }

    /* EMPTY STATE STYLING */
    .empty-state-container {
        text-align: center;
        padding: 4rem 2rem;
        background: rgba(var(--primary-rgb), 0.03);
        border-radius: 1.5rem;
        margin-top: 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 2px dashed var(--border);
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        opacity: 0.8;
        background: white;
        width: 100px;
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }

    .empty-state-title {
        font-size: 1.5rem;
        font-weight: 800;
        margin: 0 0 0.5rem 0;
        color: var(--text-primary);
    }

    .empty-state-text {
        color: var(--text-secondary);
        margin: 0 0 2rem 0;
        font-size: 1rem;
        max-width: 400px;
        line-height: 1.6;
    }

    .btn-primary-custom {
        padding: 0.875rem 2rem;
        background: var(--primary);
        color: white;
        border-radius: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        display: inline-block;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .btn-primary-custom:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
        background: var(--primary-dark);
    }
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>