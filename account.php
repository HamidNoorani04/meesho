<?php 
$pageTitle = 'Account - Meesho Shop'; 
$activeTab = 'account'; 
require __DIR__.'/partials/head.php'; 
require __DIR__.'/partials/header.php'; 
?>
<main class="container" style="padding:40px 0;min-height:60vh">
  <h2>My Account</h2>
  <div style="max-width:400px;margin:20px 0">
    <div style="border:1px solid var(--line);border-radius:12px;padding:20px;background:#fff;text-align:center">
      <div style="font-size:60px;margin-bottom:16px">👤</div>
      <h3>Login / Sign Up</h3>
      <p style="color:#666;margin-bottom:20px">Login to access your account and track orders</p>
      <button class="btn primary" style="width:100%">Continue with Phone</button>
    </div>
  </div>
</main>
<?php require __DIR__.'/partials/footer.php'; ?>
