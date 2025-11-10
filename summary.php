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
    <h3><svg width="24" height="25" fill="none" xmlns="http://www.w3.org/2000/svg" ml="16" iconsize="24" class="sc-gswNZR dJzkYm">
                                                <g clip-path="url(#cart-header_svg__a)">
                                                    <path fill="#fff" d="M2.001 1.368h20v20h-20z"></path>
                                                    <g clip-path="url(#cart-header_svg__b)">
                                                        <g clip-path="url(#cart-header_svg__c)">
                                                            <path d="M6.003 5.183h15.139c.508 0 .908.49.85 1.046l-.762 7.334c-.069.62-.537 1.1-1.103 1.121l-12.074.492-2.05-9.993Z" fill="#C53EAD"></path>
                                                            <path d="M11.8 21.367c.675 0 1.22-.597 1.22-1.334 0-.737-.545-1.335-1.22-1.335-.673 0-1.22.598-1.22 1.335s.547 1.334 1.22 1.334ZM16.788 21.367c.674 0 1.22-.597 1.22-1.334 0-.737-.546-1.335-1.22-1.335-.673 0-1.22.598-1.22 1.335s.547 1.334 1.22 1.334Z" fill="#9F2089"></path>
                                                            <path d="m2.733 4.169 3.026 1.42 2.528 12.085c.127.609.615 1.036 1.181 1.036h9.615" stroke="#9F2089" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        </g>
                                                    </g>
                                                </g>
                                                <defs>
                                                    <clipPath id="cart-header_svg__a">
                                                        <path fill="#fff" transform="translate(2.001 1.368)" d="M0 0h20v20H0z"></path>
                                                    </clipPath>
                                                    <clipPath id="cart-header_svg__b">
                                                        <path fill="#fff" transform="translate(2.001 1.368)" d="M0 0h20v20H0z"></path>
                                                    </clipPath>
                                                    <clipPath id="cart-header_svg__c">
                                                        <path fill="#fff" transform="translate(2.001 3.368)" d="M0 0h20v18H0z"></path>
                                                    </clipPath>
                                                </defs>
                                            </svg> Order Items</h3>
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