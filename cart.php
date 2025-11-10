<?php
$pageTitle = 'Shopping Cart - Meesho Shop';
require __DIR__.'/partials/head.php';
require __DIR__.'/partials/header.php';

if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// Load offer setting from file
$OFFER_ENABLED = get_setting('offer_enabled', false);

// Remove item completely
if(isset($_POST['remove'])){ 
  unset($_SESSION['cart'][$_POST['remove']]); 
}

// Decrease quantity
if(isset($_POST['decrease_qty'])){
  $id = $_POST['item_id'];
  if(isset($_SESSION['cart'][$id])){
    $_SESSION['cart'][$id]['qty']--;
    if($_SESSION['cart'][$id]['qty'] <= 0){
      unset($_SESSION['cart'][$id]);
    }
  }
}

// Increase quantity
if(isset($_POST['increase_qty'])){
  $id = $_POST['item_id'];
  if(isset($_SESSION['cart'][$id])){
    $_SESSION['cart'][$id]['qty']++;
  }
}

// Calculate total items (including quantities)
$totalItems = 0;
foreach($_SESSION['cart'] as $item){
  $totalItems += $item['qty'];
}

// Calculate offer (only if total items >= 3)
$offerDiscount = 0;
$freeProductId = '';
if($OFFER_ENABLED && $totalItems >= 3){
  // Find cheapest product (considering single item price)
  $cheapest = null;
  $cheapestPrice = PHP_INT_MAX;
  foreach($_SESSION['cart'] as $item){
    if($item['price'] < $cheapestPrice){
      $cheapestPrice = $item['price'];
      $cheapest = $item;
    }
  }
  if($cheapest){
    $offerDiscount = $cheapestPrice;
    $freeProductId = $cheapest['id'];
  }
}
?>

<style>
/* Progress Stepper */

.progress-bar-container{background:#fff;padding:20px 0;border-bottom:1px solid #e5e7eb;position:sticky;top:71px;z-index:40}
.progress-steps{display:flex;justify-content:space-between;align-items:center;max-width:600px;margin:0 auto;position:relative}
.progress-steps::before{content:'';position:absolute;top:15px;left:0;right:0;height:2px;background:#e5e7eb;z-index:0}
.progress-line{position:absolute;top:15px;left:0;height:2px;background:#9333ea;z-index:1;transition:width 0.3s}
.progress-step{display:flex;flex-direction:column;align-items:center;gap:6px;position:relative;z-index:2;flex:1}
.step-circle{width:32px;height:32px;border-radius:50%;background:#fff;border:2px solid #e5e7eb;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:14px;color:#9ca3af;transition:all 0.3s}
.progress-step.completed .step-circle{background:#9333ea;border-color:#9333ea;color:#fff}

.step-label{font-size:12px;color:#9ca3af;font-weight:500}


.progress-stepper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 600px;
  margin: 24px auto;
  padding: 0 20px;
  position: relative;
}

.progress-stepper::before {
  content: '';
  position: absolute;
  top: 16px;
  left: 80px;
  right: 80px;
  height: 2px;
  background: #e5e7eb;
  z-index: 0;
}

.step {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  position: relative;
  z-index: 1;
}

.step-circle {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #fff;
  border: 2px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 600;
  color: #9ca3af;
}

.step.active .step-circle {
  background: #9f2089;
  border-color: #9f2089;
  color: #fff;
}

.step.completed .step-circle {
  background: #10b981;
  border-color: #10b981;
  color: #fff;
}

.step-label {
  font-size: 12px;
  color: #9ca3af;
  font-weight: 500;
}

.step.active .step-label {
  color: #0f172a;
  font-weight: 600;
}

/* Enhanced Cart Styles */
.cart-page {
  padding: 20px 0 100px;
  min-height: 60vh;
}

.cart-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
}

.back-btn {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #0f172a;
  padding: 8px;
}

.cart-header h2 {
  font-size: 20px;
  font-weight: 600;
  margin: 0;
  color: #0f172a;
}

.cart-items-container {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  margin-bottom: 20px;
}

.cart-item {
  display: flex;
  gap: 16px;
  padding: 16px;
  border-bottom: 1px solid #f3f4f6;
  position: relative;
}

.cart-item:last-child {
  border-bottom: none;
}

.cart-item-image {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 8px;
  background: #f8f9fa;
  flex-shrink: 0;
}

.cart-item-details {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.cart-item-header {
  display: flex;
  justify-content: space-between;
  align-items: start;
}

.cart-item-title {
  font-size: 14px;
  color: #0f172a;
  line-height: 1.4;
  margin: 0;
  flex: 1;
  padding-right: 12px;
}

.cart-item-remove {
  background: none;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  font-size: 20px;
  padding: 0;
  line-height: 1;
}

.cart-item-remove:hover {
  color: #dc2626;
}

.cart-item-price {
  display: flex;
  align-items: center;
  gap: 8px;
  margin: 4px 0;
}

.cart-item-price .current {
  font-size: 16px;
  font-weight: 700;
  color: #0f172a;
}

.cart-item-price .original {
  font-size: 14px;
  color: #9ca3af;
  text-decoration: line-through;
}

.cart-item-meta {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-top: 4px;
}

.cart-item-size {
  font-size: 13px;
  color: #6b7280;
}

.qty-control {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #f3f4f6;
  padding: 4px 8px;
  border-radius: 6px;
}

.qty-btn {
  background: none;
  border: none;
  font-size: 18px;
  font-weight: 700;
  color: #9f2089;
  cursor: pointer;
  padding: 0 6px;
  line-height: 1;
}

.qty-btn:hover {
  color: #7a1a6b;
}

.qty-display {
  font-size: 14px;
  font-weight: 600;
  color: #0f172a;
  min-width: 20px;
  text-align: center;
}

.free-badge {
  position: absolute;
  top: 8px;
  left: 8px;
  background: #10b981;
  color: #fff;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  z-index: 10;
}

.offer-banner {
  background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
  border: 2px solid #fbbf24;
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.offer-icon {
  font-size: 32px;
  flex-shrink: 0;
}

.offer-text {
  flex: 1;
}

.offer-text strong {
  display: block;
  font-size: 15px;
  color: #92400e;
  margin-bottom: 4px;
}

.offer-text small {
  font-size: 12px;
  color: #78350f;
}

.cart-summary-box {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 20px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  font-size: 15px;
}

.summary-row.total {
  border-top: 1px solid #e5e7eb;
  margin-top: 12px;
  padding-top: 16px;
  font-size: 17px;
  font-weight: 700;
}

.summary-row .label {
  color: #6b7280;
}

.summary-row .value {
  color: #0f172a;
  font-weight: 600;
}

.summary-row.total .value {
  font-size: 20px;
  color: #0f172a;
}

.summary-row .free {
  color: #10b981;
  font-weight: 600;
}

.summary-row .discount {
  color: #10b981;
  font-weight: 600;
}

.price-details-link {
  color: #9f2089;
  font-size: 13px;
  text-decoration: none;
  font-weight: 500;
  display: inline-block;
  margin-top: 8px;
}

/* Safety Banner */
.safety-banner {
  background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
  border: 1px solid #bae6fd;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 80px;
}

.safety-badge {
  background: #6366f1;
  color: #fff;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 8px;
}

.safety-content h3 {
  font-size: 18px;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 8px;
}

.safety-content p {
  font-size: 14px;
  color: #64748b;
  margin: 0;
  line-height: 1.5;
}

.safety-illustration {
  flex-shrink: 0;
}

.safety-illustration img {
  width: 140px;
  height: auto;
}

/* Fixed Bottom Continue Button */
.cart-bottom-bar {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: #fff;
  border-top: 1px solid #e5e7eb;
  padding: 16px;
  z-index: 100;
  box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
}

.cart-bottom-content {
  max-width: 1150px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.cart-total-display {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.cart-total-display .amount {
  font-size: 18px;
  font-weight: 700;
  color: #0f172a;
}

.cart-total-display .link {
  font-size: 11px;
  color: #9f2089;
  text-decoration: none;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.3px;
}

.continue-btn {
  background: #9f2089;
  color: #fff;
  border: none;
  padding: 16px 56px;
  border-radius: 6px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  text-decoration: none;
  display: inline-block;
  transition: background 0.2s;
}

.continue-btn:hover {
  background: #88176e;
}

@media (max-width: 768px) {
  .progress-stepper {
    padding: 0 10px;
  }
  
  .safety-banner {
    flex-direction: column;
    text-align: center;
  }
  
  .cart-item-image {
    width: 80px;
    height: 80px;
  }
}
</style>

<main class="container cart-page">
  <!-- Back button and title -->
  <div class="cart-header">
    <button class="back-btn" onclick="window.history.back()">‹</button>
    <h2>CART</h2>
  </div>

  <!-- Progress Stepper -->
  <div class="progress-bar-container">
  <div class="container">
    <div class="progress-steps">
      <div class="progress-line" style="width:33.33%"></div>
      
      <div class="progress-step completed">
        <div class="step-circle">1</div>
        <span class="step-label">Cart</span>
      </div>
      
      <div class="progress-step active">
        <div class="step-circle">2</div>
        <span class="step-label">Address</span>
      </div>
      
      <div class="progress-step">
        <div class="step-circle">3</div>
        <span class="step-label">Payment</span>
      </div>
      
      <div class="progress-step">
        <div class="step-circle">4</div>
        <span class="step-label">Summary</span>
      </div>
    </div>
  </div>
</div>
  
  <?php if(empty($_SESSION['cart'])): ?>
    <div style="text-align:center;padding:60px 20px">
      <div style="font-size:60px;margin-bottom:16px"><svg width="24" height="25" fill="none" xmlns="http://www.w3.org/2000/svg" ml="16" iconsize="24" class="sc-gswNZR dJzkYm">
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
                                            </svg></div>
      <p style="font-size:18px;color:#666;margin-bottom:20px">Your cart is empty</p>
      <a href="<?php echo url('index.php'); ?>" class="btn primary">Start Shopping</a>
    </div>
  <?php else: ?>
    
    <!-- Offer Banner -->
    <?php if($OFFER_ENABLED && $offerDiscount > 0): ?>
      <div class="offer-banner">
        <div class="offer-icon">🎉</div>
        <div class="offer-text">
          <strong>Congratulations! You got 1 product FREE</strong>
          <small>Buy 3 items, get the one free!</small>
        </div>
      </div>
    <?php elseif($OFFER_ENABLED && $totalItems < 3): ?>
      <div class="offer-banner">
        <div class="offer-icon">🔥</div>
        <div class="offer-text">
          <strong>Special Offer: Buy 3 Get 1 FREE!</strong>
          <small>Add <?php echo 3 - $totalItems; ?> more item(s) to get the cheapest one free</small>
        </div>
      </div>
    <?php endif; ?>
    
    <!-- Cart Items -->
    <div class="cart-items-container">
      <?php 
      $total = 0; 
      $totalMrp = 0; 
      foreach($_SESSION['cart'] as $it): 
        $isFree = ($OFFER_ENABLED && $offerDiscount > 0 && $it['id'] === $freeProductId);
        $itemTotal = $it['price'] * $it['qty'];
        $itemMrp = (isset($it['mrp']) ? $it['mrp'] : $it['price']) * $it['qty'];
        
        // Subtract one item price if it's the free product
        if($isFree){
          $itemTotal -= $it['price'];
        }
        
        $total += $itemTotal;
        $totalMrp += $itemMrp;
      ?>
      <div class="cart-item">
        <?php if($isFree): ?>
          <div class="free-badge">1 FREE</div>
        <?php endif; ?>
        
        <img class="cart-item-image" src="<?php echo htmlspecialchars($it['image']); ?>" alt="<?php echo htmlspecialchars($it['title']); ?>"/>
        <div class="cart-item-details">
          <div class="cart-item-header">
            <h4 class="cart-item-title"><?php echo htmlspecialchars($it['title']); ?></h4>
            <form method="post" action="<?php echo url('cart.php'); ?>" style="display:inline">
              <input type="hidden" name="remove" value="<?php echo htmlspecialchars($it['id']); ?>"/>
              <button type="submit" class="cart-item-remove">🗑</button>
            </form>
          </div>
          
          <div class="cart-item-price">
            <span class="current">₹<?php echo number_format($itemTotal); ?></span>
            <?php if(isset($it['mrp']) && $it['mrp'] > $it['price']): ?>
              <span class="original">₹<?php echo number_format($itemMrp); ?></span>
            <?php endif; ?>
          </div>
          
          <div class="cart-item-meta">
            <?php if(isset($it['size'])): ?>
              <span class="cart-item-size">Size: <?php echo htmlspecialchars($it['size']); ?></span>
            <?php else: ?>
              <span class="cart-item-size">Size: -</span>
            <?php endif; ?>
            
            <div class="qty-control">
              <form method="post" action="<?php echo url('cart.php'); ?>" style="display:inline">
                <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($it['id']); ?>"/>
                <button type="submit" name="decrease_qty" class="qty-btn">-</button>
              </form>
              
              <span class="qty-display"><?php echo $it['qty']; ?></span>
              
              <form method="post" action="<?php echo url('cart.php'); ?>" style="display:inline">
                <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($it['id']); ?>"/>
                <button type="submit" name="increase_qty" class="qty-btn">+</button>
              </form>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; 
      $_SESSION['order_total'] = $total;
      ?>
    </div>
    
    <!-- Price Summary -->
    <div class="cart-summary-box">
      <div class="summary-row">
        <span class="label">Shipping:</span>
        <span class="value free">FREE</span>
      </div>
      <?php if($OFFER_ENABLED && $offerDiscount > 0): ?>
        <div class="summary-row">
          <span class="label">Offer Discount (Buy 3 Get 1 Free):</span>
          <span class="value discount">-₹<?php echo number_format($offerDiscount); ?></span>
        </div>
      <?php endif; ?>
      <div class="summary-row">
        <span class="label">Total Product Price:</span>
        <span class="value">₹<?php echo number_format($totalMrp); ?></span>
      </div>
      <div class="summary-row total">
        <span class="label">Order Total:</span>
        <span class="value">₹<?php echo number_format($total); ?></span>
      </div>
      <a href="#" class="price-details-link">VIEW PRICE DETAILS</a>
    </div>

    <!-- Safety Banner -->
    <div class="safety-banner">
      <div class="safety-content">
        <div class="safety-badge">
          <span>🛡</span>
          <span>Meesho Safe</span>
        </div>
        <h3>Your Safety, Our Priority</h3>
        <p>We make sure that your package is safe at every point of contact.</p>
      </div>
      <div class="safety-illustration">
        <svg width="140" height="140" viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg">
          <!-- Delivery person illustration -->
          <rect x="50" y="80" width="40" height="45" rx="4" fill="#f97316"/>
          <circle cx="70" cy="50" r="18" fill="#fbbf24"/>
          <rect x="55" y="100" width="30" height="25" rx="2" fill="#fcd34d"/>
          <path d="M85 95 L110 95 L110 105 L95 105 Z" fill="#6366f1"/>
          <circle cx="75" cy="48" r="3" fill="#000"/>
          <circle cx="65" cy="48" r="3" fill="#000"/>
          <path d="M65 55 Q70 58 75 55" stroke="#000" stroke-width="2" fill="none"/>
        </svg>
      </div>
    </div>

    <!-- Fixed Bottom Bar -->
    <div class="cart-bottom-bar">
      <div class="cart-bottom-content">
        <div class="cart-total-display">
          <span class="amount">₹<?php echo number_format($total); ?></span>
          <a href="#" class="link">VIEW PRICE DETAILS</a>
        </div>
        <a href="<?php echo url('address.php'); ?>" class="continue-btn">Continue</a>
      </div>
    </div>
  <?php endif; ?>
</main>