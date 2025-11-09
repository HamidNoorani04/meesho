
<footer class="site-footer">
  <nav class="tabbar">
    <a href="<?php echo url('index.php'); ?>" class="tab<?php if(($activeTab ?? '')==='home') echo ' active';?>">
      <div>🏠</div><span>Home</span>
    </a>
    <a href="<?php echo url('categories.php'); ?>" class="tab<?php if(($activeTab ?? '')==='categories') echo ' active';?>">
      <div>🗂️</div><span>Categories</span>
    </a>
    <a href="<?php echo url('orders.php'); ?>" class="tab<?php if(($activeTab ?? '')==='orders') echo ' active';?>">
      <div>📦</div><span>My Orders</span>
    </a>
    <a href="<?php echo url('help.php'); ?>" class="tab<?php if(($activeTab ?? '')==='help') echo ' active';?>">
      <div>❓</div><span>Help</span>
    </a>
    <a href="<?php echo url('account.php'); ?>" class="tab<?php if(($activeTab ?? '')==='account') echo ' active';?>">
      <div>👤</div><span>Account</span>
    </a>
  </nav>
  <p class="copy">© <?php echo date('Y'); ?> Meesho Shop - Lowest Prices</p>
</footer>
</body>
</html>
