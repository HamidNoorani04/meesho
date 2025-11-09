<?php
$pageTitle = 'Payment Failed - Meesho Shop';
require __DIR__.'/partials/head.php';
require __DIR__.'/partials/header.php';
require __DIR__.'/order-functions.php';

$order_number = '';
$failure_reason = '';

// Handle PayU failure response
if (isset($_POST['txnid'])) {
    $txnid = $_POST['txnid'];
    $failure_reason = $_POST['error_Message'] ?? $_POST['error'] ?? 'Payment failed';
    
    // Save failed order
    if (isset($_SESSION['cart']) && isset($_SESSION['address'])) {
        $cart = $_SESSION['cart'];
        $address = $_SESSION['address'];
        
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['qty'];
        }
        
        $order_data = [
            'full_name' => $address['full_name'],
            'mobile' => $address['mobile'],
            'email' => $_POST['email'] ?? '',
            'address_line1' => $address['address_line1'] ?? '',
            'address_line2' => $address['address_line2'] ?? '',
            'city' => $address['city'] ?? '',
            'state' => $address['state'] ?? '',
            'pincode' => $address['pincode'],
            'total_amount' => $total,
            'payment_method' => 'PayU',
            'payment_status' => 'failed',
            'transaction_id' => $txnid
        ];
        
        $order_number = save_order_to_db($order_data, $cart, 'failed', $failure_reason);
        $_SESSION['last_failed_order'] = $order_number;
    }
}
?>
<style>
.failure-page{max-width:500px;margin:60px auto;padding:20px;text-align:center}
.failure-icon{width:80px;height:80px;background:#ef4444;border-radius:50%;margin:0 auto 20px;display:flex;align-items:center;justify-content:center;font-size:40px;color:#fff}
.failure-page h1{color:#dc2626;margin-bottom:10px}
.error-message{background:#fef2f2;padding:16px;border-radius:8px;margin:20px 0;color:#991b1b}
.order-number{background:#fef2f2;padding:16px;border-radius:8px;margin:20px 0;font-size:14px;color:#991b1b}
.btn{display:inline-block;padding:14px 32px;background:#9f2089;color:#fff;text-decoration:none;border-radius:8px;margin:10px;font-weight:600}
.btn-secondary{background:#6b7280}
</style>
<div class="failure-page">
    <div class="failure-icon">✗</div>
    <h1>Payment Failed</h1>
    <p>Unfortunately, your payment could not be processed.</p>
    
    <?php if ($failure_reason): ?>
        <div class="error-message">
            <strong>Reason:</strong> <?php echo e($failure_reason); ?>
        </div>
    <?php endif; ?>
    
    <?php if ($order_number): ?>
        <div class="order-number">
            Reference Number: <?php echo e($order_number); ?>
        </div>
    <?php endif; ?>
    
    <div>
        <a href="<?php echo url('cart.php'); ?>" class="btn">Try Again</a>
        <a href="<?php echo url('index.php'); ?>" class="btn btn-secondary">Back to Home</a>
    </div>
</div>
<?php require __DIR__.'/partials/footer.php'; ?>