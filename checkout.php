<?php 
$pageTitle = 'Checkout - Meesho Shop'; 
require __DIR__.'/partials/head.php'; 
require __DIR__.'/partials/header.php'; 
?>
<main class="container checkout-page">
  <h2>Checkout</h2>
  <form class="checkout-form" action="<?php echo url('orders.php'); ?>">
    <label>Full Name<input required placeholder="Enter your name"/></label>
    <label>Phone Number<input required placeholder="10 digit mobile number"/></label>
    <label>Address<textarea required rows="3" placeholder="House no., Building name, Road name, Area"></textarea></label>
    <div class="grid two">
      <label>City<input required placeholder="City"/></label>
      <label>Pincode<input required placeholder="6 digit pincode"/></label>
    </div>
    <button type="submit" class="btn primary">Place Order</button>
  </form>
</main>
<?php require __DIR__.'/partials/footer.php'; ?>
