<?php
$pageTitle = 'Payment - Meesho Shop';
require __DIR__.'/partials/head.php';
require __DIR__.'/partials/header.php';

if(!isset($_SESSION['address']) || empty($_SESSION['address'])){
  header('Location: '.url('cart.php'));
  exit;
}

// Calculate totals
// NEW CODE (Fixed)
// Calculate totals
$cart = $_SESSION['cart'] ?? [];

// Get the CORRECT total (with offer) from the session
$total = $_SESSION['order_total'] ?? 0 ;

// We still need $subtotal just for display
$subtotal = 0;
foreach($cart as $item){
  $subtotal += $item['price'] * $item['qty'];
}
// We no longer calculate $total, we just use the one from the session.

// PayU credentials from environment variables
$PAYU_MERCHANT_KEY = env('PAYU_MERCHANT_KEY');
$PAYU_SALT = env('PAYU_SALT');
$PAYU_BASE_URL = env('PAYU_BASE_URL', 'https://secure.payu.in');

// ...
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // --- THIS IS THE NEW LOGIC ---
    
    // 1. Get all data from session
    $payment_method = $_POST['payment_method'] ?? 'payu';
    $_SESSION['payment_method'] = $payment_method;
    $cart = $_SESSION['cart'];
    $address = $_SESSION['address'];

    // 2. Build the order data array
    $order_data = [
        'full_name' => $address['full_name'],
        'mobile' => $address['mobile'],
        'email' => 'customer' . time() . '@meesho.com', // Unique email for PayU
        'address_line1' => $address['house_no'], // <-- This is the fix from before
        'address_line2' => $address['road_name'], // <-- This is the fix from before
        'city' => $address['city'],
        'state' => $address['state'],
        'pincode' => $address['pincode'],
        'total_amount' => $total,
        'payment_method' => $payment_method,
        'payment_status' => 'pending', // Set to pending
        'transaction_id' => ''
    ];

    // 3. Save the order to DB *BEFORE* payment
    require_once __DIR__.'/order-functions.php';
    // We save it as 'pending'
    $order_number = save_order_to_db($order_data, $cart, 'pending', ''); 
    
    if (!$order_number) {
        // Handle database save error
        die("Could not create order. Please try again.");
    }
    
    // 4. Use OUR order_number as the PayU transaction ID
    $txnid = $order_number; 
    $amount = $total;
    $productinfo = 'Meesho Order - ' . $order_number;
    $firstname = $address['full_name'];
    $email = $order_data['email'];
    $phone = $address['mobile'];

    // 5. Generate hash
    $hash_string = $PAYU_MERCHANT_KEY."|".$txnid."|".$amount."|".$productinfo."|".$firstname."|".$email."|||||||||||".$PAYU_SALT;
    $hash = strtolower(hash('sha512', $hash_string));
    
    // 6. Store transaction details (we only need this for the form)
    $_SESSION['payu_txn'] = [
      'txnid' => $txnid,
      'amount' => $amount,
      'productinfo' => $productinfo,
      'firstname' => $firstname,
      'email' => $email,
      'phone' => $phone,
      'hash' => $hash
    ];
    
    $show_payu_form = true;
    // --- END OF NEW LOGIC ---
  }
?>
<style>.site-footer{display:none !important}.app-header{position:relative !important}.progress-bar-container{background:#fff;padding:20px 0;border-bottom:1px solid #e5e7eb}.progress-steps{display:flex;justify-content:space-between;align-items:center;max-width:600px;margin:0 auto;position:relative}.progress-steps::before{content:'';position:absolute;top:15px;left:0;right:0;height:2px;background:#e5e7eb;z-index:0}.progress-line{position:absolute;top:15px;left:0;height:2px;background:#9333ea;z-index:1;transition:width 0.3s}.progress-step{display:flex;flex-direction:column;align-items:center;gap:6px;position:relative;z-index:2;flex:1}.step-circle{width:32px;height:32px;border-radius:50%;background:#fff;border:2px solid #e5e7eb;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:14px;color:#9ca3af;transition:all 0.3s}.progress-step.active .step-circle{background:#9333ea;border-color:#9333ea;color:#fff}.progress-step.completed .step-circle{background:#4f46e5;border-color:#4f46e5;color:#fff}.progress-step.completed .step-circle::after{content:'✓'}.step-label{font-size:12px;color:#9ca3af;font-weight:500;white-space:nowrap}.progress-step.active .step-label{color:#9333ea;font-weight:600}.progress-step.completed .step-label{color:#4f46e5}.payment-page{max-width:600px;margin:0 auto;padding:20px 16px 100px}.page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px}.page-header h2{margin:0;font-size:18px;font-weight:600;color:#1f2937}.safe-badge{display:flex;align-items:center;gap:6px;background:#dbeafe;padding:6px 12px;border-radius:6px}.safe-badge svg{width:16px;height:16px;color:#2563eb}.safe-badge span{font-size:11px;color:#2563eb;font-weight:600}.offer-banner{background:linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 100%);border-radius:12px;padding:16px;margin-bottom:24px;display:flex;align-items:center;gap:12px}.offer-icon{width:40px;height:40px;background:#8b5cf6;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0}.offer-icon svg{width:24px;height:24px;color:#fff}.offer-text{color:#5b21b6;font-weight:600;font-size:15px}.section-title{font-size:13px;color:#6b7280;font-weight:600;text-transform:uppercase;margin:24px 0 12px;letter-spacing:0.5px}.payment-methods{display:flex;flex-direction:column;gap:12px;margin-bottom:24px}.payment-option{background:#fff;border:2px solid #e5e7eb;border-radius:12px;padding:16px;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;gap:12px}.payment-option:hover{border-color:#9333ea;background:#faf5ff}.payment-option.selected{border-color:#9333ea;background:#f3e8ff}.payment-option input[type="radio"]{width:20px;height:20px;accent-color:#9333ea;cursor:pointer}.payment-badge{background:#4f46e5;color:#fff;font-size:10px;font-weight:700;padding:4px 8px;border-radius:4px;text-transform:uppercase}.payment-label{flex:1;display:flex;align-items:center;gap:8px}.payment-label strong{color:#1f2937;font-size:15px}.payment-icon{width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:20px;opacity:0.7}.order-summary{background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:16px;margin-bottom:100px}.summary-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;font-size:15px}.summary-row.total{border-top:2px solid #e5e7eb;margin-top:8px;padding-top:12px;font-weight:700;font-size:16px;color:#1f2937}.summary-label{color:#6b7280}.summary-value{color:#1f2937;font-weight:600}.summary-value.free{color:#10b981;font-weight:700}.bottom-actions{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #e5e7eb;padding:16px;z-index:100;box-shadow:0 -2px 10px rgba(0,0,0,0.05)}.bottom-actions button{width:100%;padding:16px;background:#9f2089;color:#fff;border:none;border-radius:10px;font-size:16px;font-weight:600;cursor:pointer;transition:all 0.2s}.bottom-actions button:hover{background:#8a1c76}</style>
<div class="progress-bar-container"><div class="container"><div class="progress-steps"><div class="progress-line" style="width:66.66%"></div><div class="progress-step completed"><div class="step-circle"></div><span class="step-label">Cart</span></div><div class="progress-step completed"><div class="step-circle"></div><span class="step-label">Address</span></div><div class="progress-step active"><div class="step-circle">3</div><span class="step-label">Payment</span></div><div class="progress-step"><div class="step-circle">4</div><span class="step-label">Summary</span></div></div></div></div>
<main class="payment-page"><div class="page-header"><h2>Select Payment Method</h2><div class="safe-badge"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg><span>100% SAFE PAYMENTS</span></div></div><div class="offer-banner">
  
  <div class="offer-icon" style="background: none; width: 48px; height: 48px;">
    <video autoplay loop muted playsinline style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
      <source src="https://messho.shop/assets/cod_lat.webm" type="video/webm">
    </video>
  </div>

  <div class="offer-text">Pay online & get EXTRA ₹33 off</div>
</div><form method="post" action="<?php echo url('payment.php'); ?>" id="paymentForm"><div class="section-title">PAY ONLINE</div><div class="payment-methods"><label class="payment-option selected"><input type="radio" name="payment_method" value="payu" checked/><div class="payment-label"><span class="payment-badge">UPI</span><strong>UPI (GPay/PhonePe/Paytm)</strong></div><div class="payment-icon">🏛️</div></label><label class="payment-option"><input type="radio" name="payment_method" value="card"/><div class="payment-label"><strong>Credit / Debit / ATM Card</strong></div><div class="payment-icon">💳</div></label></div><div class="order-summary"><div class="summary-row"><span class="summary-label">Shipping:</span><span class="summary-value free">FREE</span></div><div class="summary-row"><span class="summary-label">Total Product Price:</span><span class="summary-value">₹<?php echo number_format($subtotal, 2); ?></span></div><div class="summary-row total"><span class="summary-label">Order Total:</span><span class="summary-value">₹<?php echo number_format($total, 2); ?></span></div></div></form></main>
<div class="bottom-actions"><button type="submit" form="paymentForm">CONTINUE TO PAYMENT</button></div>
<?php if(isset($show_payu_form) && $show_payu_form): $payu = $_SESSION['payu_txn']; ?>
<form action="<?php echo $PAYU_BASE_URL; ?>/_payment" method="post" id="payuForm" style="display:none">
<input type="hidden" name="key" value="<?php echo $PAYU_MERCHANT_KEY; ?>" />
<input type="hidden" name="txnid" value="<?php echo $payu['txnid']; ?>" />
<input type="hidden" name="amount" value="<?php echo $payu['amount']; ?>" />
<input type="hidden" name="productinfo" value="<?php echo $payu['productinfo']; ?>" />
<input type="hidden" name="firstname" value="<?php echo $payu['firstname']; ?>" />
<input type="hidden" name="email" value="<?php echo $payu['email']; ?>" />
<input type="hidden" name="phone" value="<?php echo $payu['phone']; ?>" />
<input type="hidden" name="surl" value="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . url('success.php'); ?>" />
<input type="hidden" name="furl" value="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . url('failure.php'); ?>" />
<input type="hidden" name="hash" value="<?php echo $payu['hash']; ?>" />
</form>
<script>document.getElementById('payuForm').submit();</script>
<?php endif; ?>
<script>document.querySelectorAll('.payment-option').forEach(option => {option.addEventListener('click', function() {const radio = this.querySelector('input[type="radio"]');if(radio) {radio.checked = true;document.querySelectorAll('.payment-option').forEach(opt => {opt.classList.remove('selected');});this.classList.add('selected');}});});</script>
<?php require __DIR__.'/partials/footer.php'; ?>