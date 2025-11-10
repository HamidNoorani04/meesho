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
      <a href="<?php echo url('cart.php'); ?>" class="icon-btn cart" aria-label="cart"><svg width="24" height="25" fill="none" xmlns="http://www.w3.org/2000/svg" ml="16" iconsize="24" class="sc-gswNZR dJzkYm">
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
                                            </svg>
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