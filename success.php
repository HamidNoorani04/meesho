<?php
$pageTitle = 'Order Success - Meesho Shop';
require __DIR__.'/partials/head.php';
require __DIR__.'/partials/header.php';
require __DIR__.'/order-functions.php';

$order_number = '';

// Handle PayU response
// Handle PayU response
if (isset($_POST['status']) && isset($_POST['txnid'])) {
    $salt = env('PAYU_SALT');
    $status = $_POST['status'];
    $txnid = $_POST['txnid']; // This is our Order Number
    $hash = $_POST['hash'];
    
    // Verify hash
    $hash_string = $salt.'|'.$status.'|||||||||||'.$_POST['email'].'|'.$_POST['firstname'].'|'.$_POST['productinfo'].'|'.$_POST['amount'].'|'.$txnid.'|'.$_POST['key'];
    $generated_hash = strtolower(hash('sha512', $hash_string));
    
    if ($generated_hash == $hash && $status == 'success') {
        // Payment successful - UPDATE the order
        // We do not need the session!
        
        update_order_status_after_payment(
            $txnid,         // order_number (which is the txnid)
            'success',     // status
            'completed',   // payment_status
            $txnid,         // transaction_id
            ''             // failure_reason
        );
        
        $order_number = $txnid;
        $_SESSION['last_order_number'] = $order_number;
        
        // Clear cart
        unset($_SESSION['cart']);
        unset($_SESSION['address']);
        
    } else {
        // Hash failed or status was not success
        // We must UPDATE the order to 'failed'
         update_order_status_after_payment(
            $txnid,         // order_number (which is the txnid)
            'failed',      // status
            'failed',      // payment_status
            $txnid,         // transaction_id
            'Invalid hash or payment not successful' // failure_reason
        );
        
        header('Location: ' . url('failure.php'));
        exit;
    }
} else {
    // Direct access or COD order
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
            'email' => $address['email'] ?? '',
            'address_line1' => $address['address_line1'] ?? '',
            'address_line2' => $address['address_line2'] ?? '',
            'city' => $address['city'] ?? '',
            'state' => $address['state'] ?? '',
            'pincode' => $address['pincode'],
            'total_amount' => $total,
            'payment_method' => $_SESSION['payment_method'] ?? 'COD',
            'payment_status' => 'pending',
            'transaction_id' => ''
        ];
        
        $order_number = save_order_to_db($order_data, $cart, 'success');
        $_SESSION['last_order_number'] = $order_number;
        
        // Clear cart
        unset($_SESSION['cart']);
        unset($_SESSION['address']);
    }
}

$order_number = $_SESSION['last_order_number'] ?? 'ORD' . time();
?>
<style>
.success-page{max-width:500px;margin:60px auto;padding:20px;text-align:center}
.success-icon{width:80px;height:80px;background:#10b981;border-radius:50%;margin:0 auto 20px;display:flex;align-items:center;justify-content:center;font-size:40px;color:#fff}
.success-page h1{color:#059669;margin-bottom:10px}
.order-number{background:#f0fdf4;padding:16px;border-radius:8px;margin:20px 0;font-size:18px;font-weight:600}
.btn{display:inline-block;padding:14px 32px;background:#9f2089;color:#fff;text-decoration:none;border-radius:8px;margin-top:20px;font-weight:600}
</style>
<div class="success-page">
    <div class="success-icon">✓</div>
    <h1>Order Placed Successfully!</h1>
    <p>Thank you for your order. We'll process it soon.</p>
    <div class="order-number">
        Order Number: <span style="color:#059669"><?php echo e($order_number); ?></span>
    </div>
    <a href="<?php echo url('index.php'); ?>" class="btn">Continue Shopping</a>
</div>
<?php require __DIR__.'/partials/footer.php'; ?>