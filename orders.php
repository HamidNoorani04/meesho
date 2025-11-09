<?php 
$pageTitle = 'My Orders - Meesho Shop'; 
$activeTab = 'orders'; 
require __DIR__.'/partials/head.php'; 
require __DIR__.'/partials/header.php'; 
?>
<main class="container" style="padding:40px 0;min-height:60vh">
  <h2>My Orders</h2>
  <div style="text-align:center;padding:40px 20px;color:#666">
    <div style="font-size:60px;margin-bottom:16px">📦</div>
    <p>No orders yet. Start shopping to see your orders here!</p>
    <a href="<?php echo url('index.php'); ?>" class="btn primary" style="margin-top:20px">Browse Products</a>
  </div>
</main>
<?php require __DIR__.'/partials/footer.php'; ?>
