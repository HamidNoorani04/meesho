<?php
$pageTitle = 'Product - Meesho Shop';
require __DIR__.'/partials/head.php';
require __DIR__.'/partials/header.php';
require __DIR__.'/util.php';

$all = load_products();
$id = $_GET['id'] ?? '';
$product = null;

foreach($all as $p){ 
  if($p['id'] === $id){ 
    $product = $p; 
    break; 
  } 
}

if(!$product){ 
  echo '<main class="container" style="padding:40px 0;text-align:center">
          <h2>Product Not Found</h2>
          <p style="color:#666">The product you are looking for does not exist.</p>
          <a href="'.url('index.php').'" class="btn primary">Back to Home</a>
        </main>'; 
  require __DIR__.'/partials/footer.php'; 
  exit; 
}

if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// Add to cart
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $qty = max(1, (int)($_POST['qty'] ?? 1));
  
  if(!isset($_SESSION['cart'][$id])){
    $_SESSION['cart'][$id] = [
      "id" => $product['id'],
      "title" => $product['title'],
      "price" => $product['price'],
      "image" => $product['images'][0] ?? "", 
      "qty" => $qty
    ];
  } else { 
    $_SESSION['cart'][$id]['qty'] += $qty; 
  }
  
  header('Location: '.url('cart.php')); 
  exit;
}

$pageTitle = htmlspecialchars($product['title']).' - Meesho Shop';
?>
<style>
.site-footer{display:none !important}
</style>
<main class="product-page">
  <!-- Main Product Image -->
  <img src="<?php echo htmlspecialchars($product['images'][0] ?? ''); ?>" 
       alt="<?php echo htmlspecialchars($product['title']); ?>" 
       class="main-image" id="mainImage">
  
  <!-- Image Thumbnails -->
  <div class="image-thumbs">
    <div><?php echo count($product['images'] ?? []); ?> Products Images</div>
    <div class="thumb-grid">
      <?php foreach(($product['images'] ?? []) as $idx => $img): ?>
        <img src="<?php echo htmlspecialchars($img); ?>" 
             alt="Image <?php echo $idx+1; ?>"
             class="<?php echo $idx === 0 ? 'active' : ''; ?>"
             onclick="document.getElementById('mainImage').src=this.src; document.querySelectorAll('.thumb-grid img').forEach(i=>i.classList.remove('active')); this.classList.add('active');">
      <?php endforeach; ?>
    </div>
  </div>
  
  <!-- Product Info -->
  <div class="product-info">
    <div class="product-title">
      <h1><?php echo htmlspecialchars($product['title']); ?></h1>
      <div class="action-icons">
        <div class="icon-box">
          <div>♡</div>
          <span>Wishlist</span>
        </div>
        <div class="icon-box">
          <div>↗</div>
          <span>Share</span>
        </div>
      </div>
    </div>
    
    <div class="price-section">
      <span class="price">₹<?php echo number_format($product['price']); ?><svg width="55" height="20" fill="none" xmlns="http://www.w3.org/2000/svg" iconsize="20" class="sc-gswNZR eCiixe">
                                <path d="M9.901 5.496a2 2 0 0 1 2-2h41.6a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-41.6a2 2 0 0 1-2-2v-9Z" fill="#FFE7FB"></path>
                                <path d="M24.712 6H19.5v1.03h2.052v5.843h1.12V7.03h2.041V6ZM24.698 8.229v4.644h1.06v-2.17c0-1.09.52-1.532 1.228-1.532a.95.95 0 0 1 .353.06V8.198a.85.85 0 0 0-.363-.068c-.55 0-1.031.314-1.267.844h-.02v-.746h-.991ZM32.226 12.873V8.229h-1.07v2.67c0 .697-.481 1.188-1.09 1.188-.56 0-.884-.383-.884-1.1V8.23h-1.06v2.975c0 1.129.628 1.816 1.63 1.816.658 0 1.188-.314 1.443-.766h.05v.619h.981ZM35.25 13.02c1.1 0 1.846-.59 1.846-1.532 0-1.855-2.543-1.03-2.543-2.052 0-.304.236-.55.698-.55.422 0 .765.246.814.59l.992-.207c-.167-.706-.893-1.188-1.836-1.188-1.03 0-1.728.57-1.728 1.434 0 1.856 2.543 1.03 2.543 2.052 0 .393-.265.658-.756.658-.481 0-.874-.255-.992-.668l-.972.197c.226.795.943 1.266 1.934 1.266ZM40.083 12.97c.343 0 .638-.058.795-.136l-.118-.855a.992.992 0 0 1-.471.099c-.501 0-.747-.226-.747-.914V9.132h1.287v-.903h-1.287V6.746l-1.07.206V8.23h-.844v.903h.844v2.21c0 1.207.658 1.629 1.61 1.629ZM45.823 11.744l-.894-.265c-.206.422-.589.657-1.09.657-.746 0-1.256-.53-1.355-1.305h3.525v-.265c-.02-1.6-1.03-2.485-2.297-2.485-1.365 0-2.308 1.07-2.308 2.485 0 1.403.992 2.454 2.425 2.454.933 0 1.61-.442 1.994-1.276ZM43.73 8.906c.6 0 1.12.373 1.169 1.198h-2.406c.118-.766.56-1.198 1.237-1.198ZM46.776 10.556c0 1.463.923 2.464 2.17 2.464.619 0 1.237-.255 1.542-.854h.03v.707h.981V6h-1.07v2.828c-.246-.432-.766-.747-1.463-.747-1.247 0-2.19.992-2.19 2.475Zm1.07 0c0-.874.501-1.542 1.316-1.542.805 0 1.296.638 1.296 1.542 0 .893-.49 1.522-1.296 1.522-.795 0-1.315-.648-1.315-1.522Z" fill="#9F2089"></path>
                                <path d="M16.5 3.239 9.027.059a.746.746 0 0 0-.585 0L.969 3.24a.782.782 0 0 0-.47.721v6.36c0 5.321 3.139 7.611 7.947 9.622a.746.746 0 0 0 .576 0c4.809-2.01 7.948-4.3 7.948-9.622V3.96c0-.316-.186-.6-.47-.721Z" fill="#FFE7FB"></path>
                                <path d="m15.748 3.894-6.75-2.871a.673.673 0 0 0-.528 0l-6.75 2.87a.706.706 0 0 0-.424.652v5.745c0 4.806 2.835 6.874 7.178 8.69.167.07.353.07.52 0 4.343-1.816 7.178-3.884 7.178-8.69V4.545a.706.706 0 0 0-.424-.651Z" fill="#60014A"></path>
                                <path d="M10.852 6.455c.804.006 1.482.28 2.04.817.565.54.843 1.185.837 1.946l-.023 3.58c-.003.426-.37.77-.824.77-.45-.003-.814-.35-.81-.777l.022-3.58a1.098 1.098 0 0 0-.367-.85 1.216 1.216 0 0 0-.885-.35 1.247 1.247 0 0 0-.921.372c-.23.227-.344.54-.347.856l-.02 3.528c-.003.432-.376.782-.833.78-.458-.004-.828-.357-.824-.79l.022-3.548c.004-.31-.11-.617-.334-.844a1.254 1.254 0 0 0-.918-.378 1.253 1.253 0 0 0-.892.34c-.24.23-.37.513-.37.845l-.022 3.576c-.004.43-.373.777-.827.774-.455-.003-.818-.353-.815-.783l.023-3.564c.003-.66.25-1.308.714-1.799.6-.632 1.34-.948 2.199-.942.82.006 1.521.285 2.082.853.578-.565 1.272-.835 2.093-.832Z" fill="#FF9D00"></path>
                            </svg></span>
      <span class="mrp">₹<?php echo number_format($product['mrp']); ?></span>
      <span class="discount"><?php echo round((($product['mrp'] - $product['price']) / $product['mrp']) * 100); ?>% off</span>
    </div>
    
    <div class="offers-badge">
      💰 ₹<?php echo number_format($product['price']); ?> with 2 Special Offers ›
    </div>
    
    <div class="rating-row">
      <span class="rating-box">★ <?php echo $product['rating'] ?? '4.5'; ?></span>
      <span style="color:#666;font-size:13px"><?php echo number_format($product['reviews'] ?? 0); ?> ratings and <?php echo rand(10,50); ?> reviews</span>
      <span class="trusted-badge">🛡️ Trusted</span>
    </div>
    
    <div class="free-delivery">Free Delivery</div>
  </div>
  
  <!-- Product Details Section -->
  <div class="section-title">Product Details</div>
  <div class="details-text">
  <?php echo nl2br(htmlspecialchars($product['description'] ?? 'High-quality product with premium materials and excellent craftsmanship.')); ?>
</div>
  
<table class="spec-table">
  <tr>
    <td>Material</td>
    <td><?php echo htmlspecialchars($product['material'] ?? 'Cotton'); ?></td>
  </tr>
  <tr>
    <td>Closure</td>
    <td><?php echo htmlspecialchars($product['closure'] ?? 'Button'); ?></td>
  </tr>
  <tr>
    <td>Features</td>
    <td><?php echo htmlspecialchars($product['features'] ?? 'Comfortable, Breathable'); ?></td>
  </tr>
  <tr>
    <td>Collar</td>
    <td><?php echo htmlspecialchars($product['collar'] ?? 'Regular'); ?></td>
  </tr>
  <tr>
    <td>Thickness</td>
    <td><?php echo htmlspecialchars($product['thickness'] ?? 'Medium'); ?></td>
  </tr>
  <tr>
    <td>Sleeve Style</td>
    <td><?php echo htmlspecialchars($product['sleeve_style'] ?? 'Regular'); ?></td>
  </tr>
  <tr>
    <td>Available Colors</td>
    <td><?php echo htmlspecialchars($product['colors'] ?? 'Multiple'); ?></td>
  </tr>
  <tr>
    <td>Available Sizes</td>
    <td><?php echo htmlspecialchars($product['sizes'] ?? 'S, M, L, XL'); ?></td>
  </tr>
  <tr>
    <td>Category</td>
    <td><?php echo htmlspecialchars($product['category'] ?? 'General'); ?></td>
  </tr>
</table>
  
  <!-- Ratings Section -->
  <div class="section-title">Product Ratings & Reviews</div>
  <div class="rating-summary">
    <?php
    // Use rating distribution from admin if available, otherwise calculate from reviews
    if(isset($product['rating_distribution']) && !empty($product['rating_distribution'])){
      $ratingCounts = [
        '5' => $product['rating_distribution']['excellent'],
        '4' => $product['rating_distribution']['very_good'],
        '3' => $product['rating_distribution']['good'],
        '2' => $product['rating_distribution']['average'],
        '1' => $product['rating_distribution']['poor']
      ];
      $totalReviews = array_sum($ratingCounts);
    } else {
      // Calculate rating distribution from actual reviews
      $reviews = $product['customer_reviews'] ?? [];
      $totalReviews = count($reviews);
      $ratingCounts = ['5'=>0, '4'=>0, '3'=>0, '2'=>0, '1'=>0];
      
      foreach($reviews as $r){
        $rating = (float)$r['rating'];
        if($rating >= 4.5) $ratingCounts['5']++;
        elseif($rating >= 3.5) $ratingCounts['4']++;
        elseif($rating >= 2.5) $ratingCounts['3']++;
        elseif($rating >= 1.5) $ratingCounts['2']++;
        else $ratingCounts['1']++;
      }
    }
    ?>
    
    <div class="rating-big"><?php echo $product['rating'] ?? '4.5'; ?>★</div>
    <div class="rating-meta">
      <?php echo number_format($totalReviews); ?> Ratings<br>
      <?php echo number_format(count($product['customer_reviews'] ?? [])); ?> Reviews
    </div>
    
    <div class="rating-bars">
      <div class="rating-bar-item">
        <span class="label">Excellent</span>
        <div class="bar"><div class="fill" style="width:<?php echo $totalReviews > 0 ? ($ratingCounts['5']/$totalReviews*100) : 0; ?>%"></div></div>
        <span class="count"><?php echo $ratingCounts['5']; ?></span>
      </div>
      <div class="rating-bar-item">
        <span class="label">Very Good</span>
        <div class="bar"><div class="fill" style="width:<?php echo $totalReviews > 0 ? ($ratingCounts['4']/$totalReviews*100) : 0; ?>%"></div></div>
        <span class="count"><?php echo $ratingCounts['4']; ?></span>
      </div>
      <div class="rating-bar-item">
        <span class="label">Good</span>
        <div class="bar"><div class="fill" style="width:<?php echo $totalReviews > 0 ? ($ratingCounts['3']/$totalReviews*100) : 0; ?>%"></div></div>
        <span class="count"><?php echo $ratingCounts['3']; ?></span>
      </div>
      <div class="rating-bar-item">
        <span class="label">Average</span>
        <div class="bar"><div class="fill" style="width:<?php echo $totalReviews > 0 ? ($ratingCounts['2']/$totalReviews*100) : 0; ?>%;background:#fbbf24"></div></div>
        <span class="count"><?php echo $ratingCounts['2']; ?></span>
      </div>
      <div class="rating-bar-item">
        <span class="label">Poor</span>
        <div class="bar"><div class="fill" style="width:<?php echo $totalReviews > 0 ? ($ratingCounts['1']/$totalReviews*100) : 0; ?>%;background:#ef4444"></div></div>
        <span class="count"><?php echo $ratingCounts['1']; ?></span>
      </div>
    </div>
  </div>
  
  <!-- Customer Reviews -->
  <div class="customer-reviews" style="margin:20px 0">
    <?php 
    // Use custom reviews from admin or default samples
    $reviews = $product['customer_reviews'] ?? [
      ['name' => 'Ali Patil', 'rating' => 4.6, 'date' => 'October 26, 2025', 'text' => 'Could be better, but overall satisfied.', 'helpful' => 561],
      ['name' => 'Geeta bhanushali', 'rating' => 5.4, 'date' => 'October 14, 2025', 'text' => 'Exceeded my expectations, great job!', 'helpful' => 537],
      ['name' => 'Rajesh Kumar', 'rating' => 4.8, 'date' => 'October 8, 2025', 'text' => 'Good quality product. Worth the price!', 'helpful' => 799]
    ];
    foreach($reviews as $review): ?>
    <div style="border-bottom:1px solid #e5e7eb;padding:16px 0">
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
        <div style="width:32px;height:32px;border-radius:50%;background:#e5e7eb;display:flex;align-items:center;justify-content:center;font-weight:600;color:#666">
          <?php echo strtoupper(substr($review['name'], 0, 1)); ?>
        </div>
        <span style="font-weight:500;color:#333"><?php echo htmlspecialchars($review['name']); ?></span>
      </div>
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
        <span style="background:#059669;color:#fff;padding:4px 8px;border-radius:4px;font-size:12px;font-weight:600">
          <?php echo $review['rating']; ?>★
        </span>
        <span style="font-size:12px;color:#999">Posted on <?php echo htmlspecialchars($review['date']); ?></span>
      </div>
      <p style="color:#666;font-size:14px;margin:8px 0"><?php echo htmlspecialchars($review['text']); ?></p>
      <img src="<?php echo htmlspecialchars($product['images'][0] ?? ''); ?>" 
           style="width:60px;height:60px;object-fit:cover;border-radius:8px;margin:8px 0">
      <div style="color:#666;font-size:13px;margin-top:8px">
        👍 Helpful (<?php echo $review['helpful']; ?>)
      </div>
    </div>
    <?php endforeach; ?>
    
    <a href="#" style="display:block;text-align:center;color:var(--accent-2);font-weight:600;margin:16px 0;text-decoration:none">
      VIEW ALL REVIEWS ›
    </a>
  </div>
  
  <!-- USP Banner -->
  <div style="background:#e8f4fc;border-radius:12px;padding:20px;margin:20px 0;display:flex;justify-content:space-around;text-align:center">
    <div>
      <div style="font-size:36px;margin-bottom:4px"><img src="https://images.meesho.com/images/value_props/lowest_price_new.png" alt="lowest-price"></div>
      <div style="font-size:12px;color:#666;font-weight:500">Lowest Price</div>
    </div>
    <div>
      <div style="font-size:36px;margin-bottom:4px"><img src="https://images.meesho.com/images/value_props/cod_new.png" alt="cod"></div>
      <div style="font-size:12px;color:#666;font-weight:500">Cash on Delivery</div>
    </div>
    <div>
      <div style="font-size:36px;margin-bottom:4px"><img src="https://images.meesho.com/images/value_props/return_new.png" alt="return"></div>
      <div style="font-size:12px;color:#666;font-weight:500">7-day Returns</div>
    </div>
  </div>
  
  <!-- Products For You -->
  <div class="section-title">Products For You</div>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:80px">
    <?php 
    $allProducts = load_products();
    $recommendations = array_filter($allProducts, fn($p) => $p['id'] !== $product['id']);
    $recommendations = array_slice($recommendations, 0, 4);
    foreach($recommendations as $p): ?>
    <a href="<?php echo url('product.php?id='.urlencode($p['id'])); ?>" 
       style="display:block;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;text-decoration:none;color:inherit;background:#fff">
      <img src="<?php echo htmlspecialchars($p['images'][0] ?? ''); ?>" 
           style="width:100%;height:180px;object-fit:cover">
      <div style="padding:10px">
        <div style="font-size:13px;color:#666;height:36px;overflow:hidden;margin-bottom:6px">
          <?php echo htmlspecialchars(substr($p['title'], 0, 50)); ?>...
        </div>
        <div style="font-size:16px;font-weight:700;color:#000">
          ₹<?php echo number_format($p['price']); ?>
        </div>
        <div style="font-size:12px;color:#999;text-decoration:line-through">
          ₹<?php echo number_format($p['mrp']); ?>
        </div>
        <div style="font-size:12px;color:#22c55e;font-weight:600">
          <?php echo round((($p['mrp'] - $p['price']) / $p['mrp']) * 100); ?>% off
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
  
  <!-- Bottom Actions -->
  <form method="post" action="<?php echo url('product.php?id='.urlencode($product['id'])); ?>">
    <input type="hidden" name="qty" value="1"/>
    <div class="bottom-actions">
      <button type="submit" class="btn outline">🛒 Add to Cart</button>
      <button type="submit" class="btn primary">▶▶ Buy Now</button>
    </div>
  </form>
</main>
<?php require __DIR__.'/partials/footer.php'; ?>