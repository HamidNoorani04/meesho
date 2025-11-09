<?php
$cartCount = isset($_SESSION['cart']) ? array_sum(array_map(fn($i)=>$i['qty'], $_SESSION['cart'])) : 0;
?>
<header class="app-header">
  <div class="header-row container">
    <a href="<?php echo url('categories.php'); ?>" class="icon-btn" aria-label="menu">☰</a>
    <a href="<?php echo url('index.php'); ?>" class="brand">
      <img src="<?php echo asset('assets/banner/meesho.png'); ?>" alt="Meesho" class="brand-logo">
    </a>
    <div class="header-actions">
      <a href="<?php echo url('cart.php'); ?>" class="icon-btn cart" aria-label="cart">🛒
        <?php if($cartCount > 0): ?><b class="count"><?php echo $cartCount; ?></b><?php endif; ?>
      </a>
    </div>
  </div>
  <div class="container search-row">
    <form action="<?php echo url('index.php'); ?>" method="get" class="searchbar">
      <span class="search-ic">🔎</span>
      <input name="q" placeholder="Search for Sarees, Kurtis, Cosmetics, etc." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>"/>
    </form>
  </div>
  <div class="container location-hint">
    <button type="button" class="link">📍 Add delivery location to check extra discount</button>
  </div>
</header>