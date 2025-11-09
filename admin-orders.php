<?php
$pageTitle = 'View Orders - Admin';
require __DIR__.'/partials/head.php';
require __DIR__.'/partials/header.php';
require __DIR__.'/order-functions.php';

// Check admin authentication
if (!is_admin()) {
    header('Location: ' . url('admin-login.php'));
    exit;
}

$orders = get_all_orders(200);
?>
<style>
.admin-orders{max-width:1200px;margin:20px auto;padding:20px}
.orders-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px}
.orders-table{width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.1)}
.orders-table th{background:#f9fafb;padding:12px;text-align:left;font-weight:600;color:#374151;border-bottom:2px solid #e5e7eb}
.orders-table td{padding:12px;border-bottom:1px solid #f3f4f6}
.orders-table tr:hover{background:#f9fafb}
.status-badge{padding:4px 12px;border-radius:12px;font-size:12px;font-weight:600}
.status-completed{background:#d1fae5;color:#065f46}
.status-pending{background:#fef3c7;color:#92400e}
.status-processing{background:#dbeafe;color:#1e40af}
.btn-back{background:#6b7280;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:600}
</style>

<div class="admin-orders">
    <div class="orders-header">
        <h2>All Orders (<?php echo count($orders); ?>)</h2>
        <a href="<?php echo url('admin.php'); ?>" class="btn-back">← Back to Products</a>
    </div>
    
    <?php if (empty($orders)): ?>
        <div style="text-align:center;padding:60px;background:#fff;border-radius:8px">
            <p style="color:#9ca3af;font-size:16px">No orders yet</p>
        </div>
    <?php else: ?>
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Mobile</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><strong><?php echo e($order['order_number']); ?></strong></td>
                        <td><?php echo e($order['customer_name']); ?></td>
                        <td><?php echo e($order['mobile']); ?></td>
                        <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $order['payment_status']; ?>">
                                <?php echo ucfirst($order['payment_status']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo $order['order_status']; ?>">
                                <?php echo ucfirst($order['order_status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require __DIR__.'/partials/footer.php'; ?>
<?php
$pageTitle = 'View Orders - Admin';
require __DIR__.'/partials/head.php';
require __DIR__.'/order-functions.php';

// Check admin authentication
if (!is_admin()) {
    header('Location: ' . url('admin-login.php'));
    exit;
}

$orders = get_all_orders(200);
?>
<style>
.admin-orders{max-width:1200px;margin:20px auto;padding:20px}
.orders-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px}
.orders-table{width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.1)}
.orders-table th{background:#f9fafb;padding:12px;text-align:left;font-weight:600;color:#374151;border-bottom:2px solid #e5e7eb}
.orders-table td{padding:12px;border-bottom:1px solid #f3f4f6}
.orders-table tr:hover{background:#f9fafb}
.status-badge{padding:4px 12px;border-radius:12px;font-size:12px;font-weight:600}
.status-completed{background:#d1fae5;color:#065f46}
.status-pending{background:#fef3c7;color:#92400e}
.status-processing{background:#dbeafe;color:#1e40af}
.btn-back{background:#6b7280;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:600}
</style>

<div class="admin-orders">
    <div class="orders-header">
        <h2>All Orders (<?php echo count($orders); ?>)</h2>
        <a href="<?php echo url('admin.php'); ?>" class="btn-back">← Back to Products</a>
    </div>
    
    <?php if (empty($orders)): ?>
        <div style="text-align:center;padding:60px;background:#fff;border-radius:8px">
            <p style="color:#9ca3af;font-size:16px">No orders yet</p>
        </div>
    <?php else: ?>
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Mobile</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><strong><?php echo e($order['order_number']); ?></strong></td>
                        <td><?php echo e($order['customer_name']); ?></td>
                        <td><?php echo e($order['mobile']); ?></td>
                        <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $order['payment_status']; ?>">
                                <?php echo ucfirst($order['payment_status']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo $order['order_status']; ?>">
                                <?php echo ucfirst($order['order_status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

