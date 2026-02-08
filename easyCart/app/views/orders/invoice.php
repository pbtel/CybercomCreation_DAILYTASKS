<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice -
        <?php echo $order['order_number']; ?>
    </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --bg-light: #f8fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            color: var(--text-main);
            line-height: 1.5;
            background: #f1f5f9;
            padding: 40px 20px;
        }

        .invoice-wrapper {
            max-width: 850px;
            margin: 0 auto;
            background: #ffffff;
            padding: 60px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        /* Decorative Header Bar */
        .invoice-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: var(--primary);
        }

        /* Header Layout */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 50px;
        }

        .company-info h1 {
            font-size: 2rem;
            font-weight: 900;
            color: var(--primary);
            letter-spacing: -0.02em;
            margin-bottom: 10px;
        }

        .company-info p {
            color: var(--text-muted);
            font-size: 0.9rem;
            max-width: 250px;
        }

        .invoice-details {
            text-align: right;
        }

        .invoice-details h2 {
            font-size: 1.5rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 15px;
        }

        .detail-row {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-bottom: 5px;
            font-size: 0.95rem;
        }

        .detail-row label {
            color: var(--text-muted);
            font-weight: 500;
        }

        .detail-row span {
            font-weight: 700;
        }

        /* Addresses */
        .addresses-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 50px;
            padding-top: 30px;
            border-top: 1px solid var(--border);
        }

        .address-box h3 {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-muted);
            margin-bottom: 15px;
            font-weight: 800;
        }

        .address-box p {
            font-size: 0.95rem;
            color: var(--text-main);
            line-height: 1.6;
        }

        .address-box strong {
            font-weight: 700;
            font-size: 1.1rem;
            display: block;
            margin-bottom: 5px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        .items-table th {
            text-align: left;
            padding: 15px 10px;
            border-bottom: 2px solid var(--text-main);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
        }

        .items-table td {
            padding: 15px 10px;
            border-bottom: 1px solid var(--border);
            vertical-align: top;
        }

        .item-name {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 4px;
        }

        .item-variant {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .items-table .mono {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 600;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Summary Section */
        .summary-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 50px;
        }

        .summary-box {
            width: 300px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 0.95rem;
        }

        .summary-row label {
            color: var(--text-muted);
        }

        .summary-row.total {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid var(--text-main);
            font-size: 1.25rem;
            font-weight: 900;
        }

        .summary-row.total span {
            color: var(--primary);
        }

        /* Footer */
        .invoice-footer {
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px solid var(--border);
            text-align: center;
        }

        .invoice-footer p {
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-bottom: 10px;
        }

        .badge-payment {
            display: inline-block;
            padding: 6px 15px;
            background: var(--bg-light);
            border-radius: 99px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            border: 1px solid var(--border);
        }

        /* Action Buttons */
        .invoice-sidebar {
            position: fixed;
            top: 40px;
            right: 40px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .btn-action {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 25px;
            border-radius: 12px;
            background: white;
            border: 1px solid var(--border);
            color: var(--text-main);
            text-decoration: none;
            font-family: inherit;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            border-color: var(--primary);
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: #4f46e5;
        }

        /* Print Specific Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .invoice-wrapper {
                box-shadow: none;
                max-width: 100%;
                width: 100%;
                margin: 0;
                padding: 30px;
            }

            .invoice-sidebar {
                display: none;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar Actions -->
    <div class="invoice-sidebar">
        <button onclick="window.print()" class="btn-action btn-primary">
            <span>🖨️ Print Invoice</span>
        </button>
        <a href="<?php echo BASE_URL; ?>/order/<?php echo $order['order_id']; ?>" class="btn-action">
            <span>🔙 Back to Order</span>
        </a>
    </div>

    <!-- Invoice Content -->
    <div class="invoice-wrapper">
        <div class="invoice-header">
            <div class="company-info">
                <h1>EasyCart</h1>
                <p>
                    123 Innovation Drive, Tech Valley<br>
                    Silicon City, Maharashtra - 400001<br>
                    GSTIN: 27AABCE1234F1Z1<br>
                    support@easycart.com
                </p>
            </div>
            <div class="invoice-details">
                <h2>Tax Invoice</h2>
                <div class="detail-row">
                    <label>Invoice #:</label>
                    <span class="mono">
                        <?php echo $order['order_number']; ?>
                    </span>
                </div>
                <div class="detail-row">
                    <label>Date:</label>
                    <span>
                        <?php echo date('d M, Y', strtotime($order['created_at'])); ?>
                    </span>
                </div>
                <div class="detail-row">
                    <label>Status:</label>
                    <span style="color: <?php echo ($order['status'] == 'delivered') ? '#16a34a' : '#ea580c'; ?>;">
                        <?php echo strtoupper($order['status']); ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="addresses-grid">
            <div class="address-box">
                <h3>Billed To</h3>
                <strong>
                    <?php echo htmlspecialchars($user['name'] ?? 'Valued Customer'); ?>
                </strong>
                <p>
                    <?php echo htmlspecialchars($user['email']); ?><br>
                    <?php if (isset($order['address']['phone'])): ?>
                        Ph:
                        <?php echo htmlspecialchars($order['address']['phone']); ?>
                    <?php endif; ?>
                </p>
            </div>
            <div class="address-box">
                <h3>Shipped To</h3>
                <?php if ($order['address']): ?>
                    <strong>
                        <?php echo htmlspecialchars($order['address']['full_name']); ?>
                    </strong>
                    <p>
                        <?php echo htmlspecialchars($order['address']['address_line1']); ?><br>
                        <?php if (isset($order['address']['address_line2']) && $order['address']['address_line2']): ?>
                            <?php echo htmlspecialchars($order['address']['address_line2']); ?><br>
                        <?php endif; ?>
                        <?php echo htmlspecialchars($order['address']['city']); ?>,
                        <?php echo htmlspecialchars($order['address']['state']); ?> -
                        <?php echo htmlspecialchars($order['address']['pincode'] ?? $order['address']['postal_code']); ?><br>
                        <?php echo htmlspecialchars($order['address']['country']); ?>
                    </p>
                <?php else: ?>
                    <p>No address information available.</p>
                <?php endif; ?>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td>
                            <div class="item-name">
                                <?php echo htmlspecialchars($item['product_name']); ?>
                            </div>
                            <?php if (!empty($item['variant'])): ?>
                                <div class="item-variant">
                                    <?php
                                    $vLines = [];
                                    foreach ($item['variant'] as $type => $value) {
                                        $vLines[] = ucfirst($type) . ': ' . $value;
                                    }
                                    echo implode(' | ', $vLines);
                                    ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="text-center mono">
                            <?php echo $item['quantity']; ?>
                        </td>
                        <td class="text-right mono">₹
                            <?php echo number_format($item['price']); ?>
                        </td>
                        <td class="text-right mono">₹
                            <?php echo number_format($item['price'] * $item['quantity']); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="summary-container">
            <div class="summary-box">
                <div class="summary-row">
                    <label>Subtotal Amount</label>
                    <span class="mono">₹<?php echo number_format($order['subtotal']); ?></span>
                </div>

                <div class="summary-row">
                    <label>Shipping / Handling</label>
                    <span class="mono">₹<?php echo number_format($order['shipping_cost'] ?? 0); ?></span>
                </div>

                <?php
                $taxTotal = $order['tax'] ?? 0;
                $cgst = $taxTotal / 2;
                $sgst = $taxTotal / 2;
                ?>
                <div class="summary-row">
                    <label>CGST (9%)</label>
                    <span class="mono">₹<?php echo number_format($cgst, 2); ?></span>
                </div>
                <div class="summary-row">
                    <label>SGST (9%)</label>
                    <span class="mono">₹<?php echo number_format($sgst, 2); ?></span>
                </div>

                <?php
                $discount = $order['discount_amount'] ?? $order['discount'] ?? 0;
                if ($discount > 0):
                    ?>
                    <div class="summary-row" style="color: #16a34a; font-weight: 700;">
                        <label>Loyalty Discount</label>
                        <span class="mono">-₹<?php echo number_format($discount); ?></span>
                    </div>
                <?php endif; ?>

                <div class="summary-row total">
                    <label>Amount Payable</label>
                    <span class="mono">₹<?php echo number_format($order['final_amount']); ?></span>
                </div>
            </div>
        </div>

        <div
            style="margin-top: 40px; padding: 20px; background: var(--bg-light); border-radius: 8px; font-size: 0.8rem; color: var(--text-muted);">
            <p style="font-weight: 700; margin-bottom: 5px; color: var(--text-main); text-transform: uppercase;">Terms &
                Conditions:</p>
            <ul style="padding-left: 20px;">
                <li>Please keep this invoice for your records. This is a computer-generated document.</li>
                <li>Returns are accepted within 30 days of delivery. Terms apply.</li>
                <li>For support, please contact <strong>support@easycart.com</strong> with your Order Number.</li>
            </ul>
        </div>

        <div class="invoice-footer">
            <p>Payment via: <span
                    class="badge-payment"><?php echo str_replace('_', ' ', $order['payment_method']); ?></span></p>
            <p style="margin-top: 15px; font-size: 1.1rem; font-weight: 800; color: var(--primary);">Thank you for
                choosing EasyCart!</p>
        </div>
    </div>

</body>