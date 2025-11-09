<?php
$pageTitle = 'Order Summary - Meesho Shop';
require __DIR__.'/partials/head.php';
require __DIR__.'/partials/header.php';

if(!isset($_SESSION['address']) || !isset($_SESSION['payment_method'])){
  header('Location: '.url('cart.php'));
  exit;
}

$address = $_SESSION['address'];
$cart = $_SESSION['cart'] ?? [];
$total = 0;
foreach($cart as $item){
  $total += $item['price'] * $item['qty'];
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  // Clear cart and redirect to orders
  $_SESSION['cart'] = [];
  unset($_SESSION['address'], $_SESSION['payment_method']);
  header('Location: '.url('orders.php?placed=1'));
  exit;
}
?>

<style>
.site-footer{display:none !important}
.progress-bar-container{background:#fff;padding:20px 0;border-bottom:1px solid #e5e7eb;position:sticky;top:71px;z-index:40}
.progress-steps{display:flex;justify-content:space-between;align-items:center;max-width:600px;margin:0 auto;position:relative}
.progress-steps::before{content:'';position:absolute;top:15px;left:0;right:0;height:2px;background:#e5e7eb;z-index:0}
.progress-line{position:absolute;top:15px;left:0;height:2px;background:#9333ea;z-index:1;transition:width 0.3s}
.progress-step{display:flex;flex-direction:column;align-items:center;gap:6px;position:relative;z-index:2;flex:1}
.step-circle{width:32px;height:32px;border-radius:50%;background:#10b981;border:2px solid #10b981;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:14px;color:#fff;transition:all 0.3s}
.step-circle::after{content:'✓'}
.step-label{font-size:12px;color:#10b981;font-weight:500}
.progress-step.active .step-circle{background:#9333ea;border-color:#9333ea}
.progress-step.active .step-label{color:#9333ea;font-weight:600}
.summary-page{max-width:600px;margin:0 auto;padding:20px 16px 100px}
.info-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px;margin-bottom:16px}
.info-card h3{margin:0 0 12px;font-size:16px}
.bottom-actions{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #e5e7eb;padding:16px;z-index:100}
.bottom-actions button{width:100%;padding:16px;background:#10b981;color:#fff;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer}
</style>

<div class="progress-bar-container">
  <div class="container">
    <div class="progress-steps">
      <div class="progress-line" style="width:100%"></div>
      
      <div class="progress-step completed">
        <div class="step-circle"></div>
        <span class="step-label">Cart</span>
      </div>
      
      <div class="progress-step completed">
        <div class="step-circle"></div>
        <span class="step-label">Address</span>
      </div>
      
      <div class="progress-step completed">
        <div class="step-circle"></div>
        <span class="step-label">Payment</span>
      </div>
      
      <div class="progress-step active">
        <div class="step-circle">4</div>
        <span class="step-label">Summary</span>
      </div>
    </div>
  </div>
</div>

<main class="summary-page">
  <h2>Order Summary</h2>
  
  <div class="info-card">
    <h3>📍 Delivery Address</h3>
    <p><strong><?php echo htmlspecialchars($address['full_name']); ?></strong></p>
    <p><?php echo htmlspecialchars($address['house_no']); ?>, <?php echo htmlspecialchars($address['road_name']); ?></p>
    <p><?php echo htmlspecialchars($address['city']); ?>, <?php echo htmlspecialchars($address['state']); ?> - <?php echo htmlspecialchars($address['pincode']); ?></p>
    <p>📱 <?php echo htmlspecialchars($address['mobile']); ?></p>
  </div>
  
  <div class="info-card">
    <h3>🛒 Order Items</h3>
    <?php foreach($cart as $item): ?>
      <div style="display:flex;gap:12px;margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid #f3f4f6">
        <img src="<?php echo htmlspecialchars($item['image']); ?>" style="width:60px;height:60px;object-fit:cover;border-radius:8px"/>
        <div style="flex:1">
          <div style="font-size:14px;color:#333"><?php echo htmlspecialchars($item['title']); ?></div>
          <div style="font-size:13px;color:#666">Qty: <?php echo $item['qty']; ?></div>
          <div style="font-weight:600;margin-top:4px">₹<?php echo number_format($item['price'] * $item['qty']); ?></div>
        </div>
      </div>
    <?php endforeach; ?>
    <div style="display:flex;justify-content:space-between;font-size:18px;font-weight:700;margin-top:16px">
      <span>Total:</span>
      <span>₹<?php echo number_format($total); ?></span>
    </div>
  </div>
  
  <div class="info-card">
    <h3>💳 Payment Method</h3>
    <p><?php echo $_SESSION['payment_method'] === 'cod' ? '💵 Cash on Delivery' : '📱 Online Payment'; ?></p>
  </div>
</main>

<form method="post" action="<?php echo url('summary.php'); ?>" id="orderForm">
  <div class="bottom-actions">
    <button type="submit">PLACE ORDER</button>
  </div>
</form>

<?php require __DIR__.'/partials/footer.php'; ?>