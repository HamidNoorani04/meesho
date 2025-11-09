<?php
$pageTitle = 'Admin - Manage Products'; 
require __DIR__.'/partials/head.php'; 
require __DIR__.'/partials/header.php'; 
require __DIR__.'/util.php';

// Check admin authentication
if (!is_admin()) {
    header('Location: ' . url('admin-login.php'));
    exit;
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . url('admin-login.php'));
    exit;
}

// Verify CSRF for all POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
}

$products = load_products();
$editProduct = null;

// Handle review operations
if(isset($_GET['manage_reviews'])){
  $id = $_GET['manage_reviews'];
  foreach($products as $p){
    if($p['id'] === $id){
      $editProduct = $p;
      break;
    }
  }
}

// Save review
if(isset($_POST['save_review'])){
  $prodId = $_POST['product_id'];
  $reviewData = [
    'name' => trim($_POST['review_name']),
    'rating' => (float)$_POST['review_rating'],
    'date' => $_POST['review_date'],
    'text' => trim($_POST['review_text']),
    'helpful' => (int)$_POST['review_helpful']
  ];
  
  foreach($products as &$p){
    if($p['id'] === $prodId){
      if(!isset($p['customer_reviews'])) $p['customer_reviews'] = [];
      
      if(isset($_POST['review_index']) && $_POST['review_index'] !== ''){
        // Edit existing
        $p['customer_reviews'][(int)$_POST['review_index']] = $reviewData;
      } else {
        // Add new
        $p['customer_reviews'][] = $reviewData;
      }
      
      // Auto-calculate rating
      $totalRating = 0;
      $totalReviews = count($p['customer_reviews']);
      foreach($p['customer_reviews'] as $r){
        $totalRating += $r['rating'];
      }
      $p['rating'] = $totalReviews > 0 ? round($totalRating / $totalReviews, 1) : 4.0;
      $p['reviews'] = $totalReviews;
      
      break;
    }
  }
  save_products($products);
  header('Location: '.url('admin.php?manage_reviews='.urlencode($prodId).'&saved=1')); 
  exit;
}

// Save rating distribution
if(isset($_POST['save_rating_dist'])){
  $prodId = $_POST['product_id'];
  
  foreach($products as &$p){
    if($p['id'] === $prodId){
      $p['rating_distribution'] = [
        'excellent' => (int)$_POST['excellent'],
        'very_good' => (int)$_POST['very_good'],
        'good' => (int)$_POST['good'],
        'average' => (int)$_POST['average'],
        'poor' => (int)$_POST['poor']
      ];
      
      // Calculate total and average rating
      $dist = $p['rating_distribution'];
      $total = $dist['excellent'] + $dist['very_good'] + $dist['good'] + $dist['average'] + $dist['poor'];
      
      if($total > 0){
        $avgRating = (
          ($dist['excellent'] * 5) + 
          ($dist['very_good'] * 4) + 
          ($dist['good'] * 3) + 
          ($dist['average'] * 2) + 
          ($dist['poor'] * 1)
        ) / $total;
        
        $p['rating'] = round($avgRating, 1);
        $p['reviews'] = $total;
      }
      
      break;
    }
  }
  save_products($products);
  header('Location: '.url('admin.php?manage_reviews='.urlencode($prodId).'&dist_saved=1')); 
  exit;
}

// Delete review
if(isset($_GET['delete_review'])){
  $prodId = $_GET['product_id'];
  $reviewIndex = (int)$_GET['delete_review'];
  
  foreach($products as &$p){
    if($p['id'] === $prodId){
      if(isset($p['customer_reviews'][$reviewIndex])){
        array_splice($p['customer_reviews'], $reviewIndex, 1);
        
        // Recalculate
        $totalRating = 0;
        $totalReviews = count($p['customer_reviews']);
        foreach($p['customer_reviews'] as $r){
          $totalRating += $r['rating'];
        }
        $p['rating'] = $totalReviews > 0 ? round($totalRating / $totalReviews, 1) : 4.0;
        $p['reviews'] = $totalReviews;
      }
      break;
    }
  }
  save_products($products);
  header('Location: '.url('admin.php?manage_reviews='.urlencode($prodId).'&deleted=1')); 
  exit;
}

// Handle form submission
if($_SERVER['REQUEST_METHOD']==='POST'){
  $id = trim($_POST['id'] ?? '');
  
  // Split images by newline and filter empty
  $images = array_values(array_filter(array_map('trim', explode("\n", $_POST['images'] ?? ''))));
  
  $payload = [
    "id" => $id ?: ("P".rand(1000,9999)),
    "title" => trim($_POST['title'] ?? ''),
    "price" => (int)($_POST['price'] ?? 0),
    "mrp" => (int)($_POST['mrp'] ?? 0),
    "category" => trim($_POST['category'] ?? 'General'),
    "images" => $images,
    "badge" => trim($_POST['badge'] ?? ''),
    "rating" => (float)($_POST['rating'] ?? 4.0),
    "reviews" => (int)($_POST['reviews'] ?? 0),
    "description" => trim($_POST['description'] ?? ''),
    "material" => trim($_POST['material'] ?? 'Cotton'),
    "closure" => trim($_POST['closure'] ?? 'Button'),
    "features" => trim($_POST['features'] ?? 'Comfortable, Breathable'),
    "collar" => trim($_POST['collar'] ?? 'Regular'),
    "thickness" => trim($_POST['thickness'] ?? 'Medium'),
    "sleeve_style" => trim($_POST['sleeve_style'] ?? 'Regular'),
    "colors" => trim($_POST['colors'] ?? 'Multiple'),
    "sizes" => trim($_POST['sizes'] ?? 'S, M, L, XL'),
  ];
  
  // Update or create
  $found = false;
  foreach($products as &$p){ 
    if($p['id'] === $payload['id']){ 
      // Preserve existing reviews
      if(isset($p['customer_reviews'])){
        $payload['customer_reviews'] = $p['customer_reviews'];
      }
      $p = $payload; 
      $found = true; 
      break; 
    } 
  }
  if(!$found){ 
    $payload['customer_reviews'] = [];
    
    // Auto-generate default rating distribution (1000-10000 ratings)
    $totalRatings = rand(1000, 10000);
    $poor_avg_total = (int)($totalRatings * 0.10); // 10% for poor + average combined
    
    $excellent = (int)($totalRatings * rand(45, 55) / 100); // 45-55%
    $veryGood = (int)($totalRatings * rand(20, 30) / 100); // 20-30%
    $remaining = $totalRatings - $excellent - $veryGood - $poor_avg_total;
    $good = max(0, $remaining);
    
    $average = (int)($poor_avg_total * rand(50, 70) / 100); // 50-70% of the 10%
    $poor = $poor_avg_total - $average;
    
    $payload['rating_distribution'] = [
      'excellent' => $excellent,
      'very_good' => $veryGood,
      'good' => $good,
      'average' => $average,
      'poor' => $poor
    ];
    
    // Calculate average rating from distribution
    $avgRating = (
      ($excellent * 5) + 
      ($veryGood * 4) + 
      ($good * 3) + 
      ($average * 2) + 
      ($poor * 1)
    ) / $totalRatings;
    
    $payload['rating'] = round($avgRating, 1);
    $payload['reviews'] = $totalRatings;
    
    $products[] = $payload; 
  }
  
  save_products($products);
  header('Location: '.url('admin.php?success=1')); 
  exit;
}

// Handle delete
if(isset($_GET['delete'])){
  $id = $_GET['delete'];
  $products = array_values(array_filter($products, fn($p) => $p['id'] !== $id));
  save_products($products);
  header('Location: '.url('admin.php?deleted=1')); 
  exit;
}

// Handle edit
if(isset($_GET['edit'])){
  $editId = $_GET['edit'];
  foreach($products as $p){
    if($p['id'] === $editId){
      $editProduct = $p;
      break;
    }
  }
}
?>
<main class="container admin">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <h2>Admin Panel - Manage Products</h2>
    <div style="display:flex;gap:10px">
      <a href="<?php echo url('admin-orders.php'); ?>" style="background:#059669;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:600;">📦 View Orders</a>
      <a href="<?php echo url('admin-settings.php'); ?>" style="background:#6366f1;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:600;">⚙️ Settings</a>
      <a href="<?php echo url('admin.php?logout=1'); ?>" style="background:#dc2626;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:600;">Logout</a>
    </div>
  </div>
  
  <?php if(isset($_GET['success'])): ?>
    <div style="background:#d4edda;color:#155724;padding:12px;border-radius:8px;margin-bottom:16px">
      ✓ Product saved successfully!
    </div>
  <?php endif; ?>
  
  <?php if(isset($_GET['deleted'])): ?>
    <div style="background:#f8d7da;color:#721c24;padding:12px;border-radius:8px;margin-bottom:16px">
      ✓ Product deleted successfully!
    </div>
  <?php endif; ?>

  <!-- Product Form -->
  <form method="post" class="prod-form" action="<?php echo url('admin.php'); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
    <h3 style="margin-top:0"><?php echo $editProduct ? 'Edit Product' : 'Add New Product'; ?></h3>
    
    <div class="grid two">
      <label>
        Product ID <?php echo $editProduct ? '' : '(auto-generated if blank)'; ?>
        <input name="id" value="<?php echo htmlspecialchars($editProduct['id'] ?? ''); ?>" 
               <?php echo $editProduct ? 'readonly style="background:#f5f5f5"' : ''; ?>/>
      </label>
      
      <label>
        Title <span style="color:red">*</span>
        <input name="title" required value="<?php echo htmlspecialchars($editProduct['title'] ?? ''); ?>"/>
      </label>
      
      <label>
        Price (₹) <span style="color:red">*</span>
        <input type="number" name="price" required value="<?php echo $editProduct['price'] ?? ''; ?>"/>
      </label>
      
      <label>
        MRP (₹) <span style="color:red">*</span>
        <input type="number" name="mrp" required value="<?php echo $editProduct['mrp'] ?? ''; ?>"/>
      </label>
      
      <label>
        Category
        <input name="category" placeholder="e.g., Clothing, Electronics" 
               value="<?php echo htmlspecialchars($editProduct['category'] ?? 'General'); ?>"/>
      </label>
      
      <label>
        Badge
        <input name="badge" placeholder="Hot Sell, Free Delivery" 
               value="<?php echo htmlspecialchars($editProduct['badge'] ?? ''); ?>"/>
      </label>
      
      <label>
        Rating (0-5)
        <input type="number" step="0.1" name="rating" value="<?php echo $editProduct['rating'] ?? '4.0'; ?>" min="0" max="5"/>
      </label>
      
      <label>
        Reviews Count
        <input type="number" name="reviews" value="<?php echo $editProduct['reviews'] ?? '0'; ?>"/>
      </label>

      <label style="grid-column: 1/-1">
  Product Description
  <textarea name="description" rows="4" placeholder="Enter detailed product description"><?php echo htmlspecialchars($editProduct['description'] ?? ''); ?></textarea>
</label>
    </div>
    
    <label>
      Image URLs (one per line) <span style="color:red">*</span>
      <textarea name="images" rows="5" required placeholder="https://example.com/image1.jpg&#10;https://example.com/image2.jpg"><?php 
        if($editProduct && isset($editProduct['images'])){
          echo htmlspecialchars(implode("\n", $editProduct['images']));
        }
      ?></textarea>
    </label>

    <h3 style="margin:24px 0 16px;color:#0f172a">Product Specifications</h3>

<div class="grid two">
  <label>
    Material
    <input name="material" placeholder="e.g., Cotton, Polyester" value="<?php echo htmlspecialchars($editProduct['material'] ?? 'Cotton'); ?>"/>
  </label>
  
  <label>
    Closure Type
    <input name="closure" placeholder="e.g., Zipper, Button" value="<?php echo htmlspecialchars($editProduct['closure'] ?? 'Button'); ?>"/>
  </label>
  
  <label>
    Collar Style
    <input name="collar" placeholder="e.g., Round, V-neck" value="<?php echo htmlspecialchars($editProduct['collar'] ?? 'Regular'); ?>"/>
  </label>
  
  <label>
    Thickness
    <input name="thickness" placeholder="e.g., Thin, Medium, Thick" value="<?php echo htmlspecialchars($editProduct['thickness'] ?? 'Medium'); ?>"/>
  </label>
  
  <label>
    Sleeve Style
    <input name="sleeve_style" placeholder="e.g., Short, Long" value="<?php echo htmlspecialchars($editProduct['sleeve_style'] ?? 'Regular'); ?>"/>
  </label>
  
  <label>
    Available Colors
    <input name="colors" placeholder="e.g., Red, Blue, Green" value="<?php echo htmlspecialchars($editProduct['colors'] ?? 'Multiple'); ?>"/>
  </label>
</div>

<label>
  Features (comma separated)
  <input name="features" placeholder="e.g., Waterproof, Breathable, Eco-Friendly" value="<?php echo htmlspecialchars($editProduct['features'] ?? 'Comfortable, Breathable'); ?>"/>
</label>

<label>
  Available Sizes (comma separated)
  <input name="sizes" placeholder="e.g., S, M, L, XL, XXL" value="<?php echo htmlspecialchars($editProduct['sizes'] ?? 'S, M, L, XL'); ?>"/>
</label>
    
    <div style="display:flex;gap:10px">
      <button type="submit" class="btn primary">
        <?php echo $editProduct ? 'Update Product' : 'Add Product'; ?>
      </button>
      <?php if($editProduct): ?>
        <a href="<?php echo url('admin.php'); ?>" class="btn ghost">Cancel Edit</a>
      <?php endif; ?>
    </div>
  </form>

  <!-- Existing Products -->
  <h3>Existing Products (<?php echo count($products); ?>)</h3>
  
  <?php if(empty($products)): ?>
    <p style="color:#666;padding:20px;text-align:center">No products yet. Add your first product above!</p>
  <?php else: ?>
    <div class="grid cards">
      <?php foreach($products as $p): ?>
        <div class="admin-card">
          <div class="thumb" style="background-image:url('<?php echo htmlspecialchars($p['images'][0] ?? ''); ?>')"></div>
          <div style="flex:1">
            <b><?php echo htmlspecialchars($p['title']); ?></b>
            <div style="margin:4px 0">
              <b>₹<?php echo number_format($p['price']); ?></b> 
              <span class="mrp">₹<?php echo number_format($p['mrp']); ?></span>
            </div>
            <div class="small">
              ID: <?php echo htmlspecialchars($p['id']); ?> · 
              <?php echo htmlspecialchars($p['category'] ?? 'General'); ?>
            </div>
            <div class="actions">
              <a class="btn ghost" href="<?php echo url('product.php?id='.urlencode($p['id'])); ?>">View</a>
              <a class="btn ghost" href="<?php echo url('admin.php?edit='.urlencode($p['id'])); ?>">Edit</a>
              <a class="btn ghost" href="<?php echo url('admin.php?manage_reviews='.urlencode($p['id'])); ?>">Reviews (<?php echo count($p['customer_reviews'] ?? []); ?>)</a>
              <a class="btn danger" href="<?php echo url('admin.php?delete='.urlencode($p['id'])); ?>" 
                 onclick="return confirm('Delete this product?')">Delete</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>

<?php if(isset($_GET['manage_reviews'])): 
  $reviewProd = null;
  foreach($products as $p){
    if($p['id'] === $_GET['manage_reviews']){
      $reviewProd = $p;
      break;
    }
  }
  if($reviewProd):
  $editReviewIndex = isset($_GET['edit_review']) ? (int)$_GET['edit_review'] : null;
  $editReview = $editReviewIndex !== null && isset($reviewProd['customer_reviews'][$editReviewIndex]) ? $reviewProd['customer_reviews'][$editReviewIndex] : null;
?>
<div style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:999;display:flex;align-items:center;justify-content:center;padding:20px;overflow-y:auto">
  <div style="background:#fff;border-radius:12px;max-width:900px;width:100%;max-height:90vh;overflow-y:auto;padding:24px">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
      <h2 style="margin:0">Manage Reviews - <?php echo htmlspecialchars($reviewProd['title']); ?></h2>
      <a href="<?php echo url('admin.php'); ?>" style="font-size:24px;text-decoration:none;color:#666">×</a>
    </div>
    
    <?php if(isset($_GET['saved'])): ?>
      <div style="background:#d4edda;color:#155724;padding:12px;border-radius:8px;margin-bottom:16px">✓ Review saved! Rating auto-calculated.</div>
    <?php endif; ?>
    
    <?php if(isset($_GET['dist_saved'])): ?>
      <div style="background:#d4edda;color:#155724;padding:12px;border-radius:8px;margin-bottom:16px">✓ Rating distribution saved! Stats updated.</div>
    <?php endif; ?>
    
    <!-- Rating Distribution Editor -->
    <form method="post" action="<?php echo url('admin.php'); ?>" style="border:1px solid #ddd;border-radius:8px;padding:20px;margin-bottom:24px;background:#fff3cd">
      <h3 style="margin-top:0">📊 Rating Distribution (Total Ratings Bar)</h3>
      <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="save_rating_dist" value="1"/>
      <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($reviewProd['id']); ?>"/>
      
      <?php 
      // Set defaults if not exists
      if(!isset($reviewProd['rating_distribution'])){
        $totalRatings = rand(1000, 10000);
        $poor_avg = (int)($totalRatings * 0.10); // 10% total for poor+average
        $excellent = (int)($totalRatings * 0.50);
        $veryGood = (int)($totalRatings * 0.25);
        $good = $totalRatings - $excellent - $veryGood - $poor_avg;
        $average = (int)($poor_avg * 0.6);
        $poor = $poor_avg - $average;
      } else {
        $excellent = $reviewProd['rating_distribution']['excellent'];
        $veryGood = $reviewProd['rating_distribution']['very_good'];
        $good = $reviewProd['rating_distribution']['good'];
        $average = $reviewProd['rating_distribution']['average'];
        $poor = $reviewProd['rating_distribution']['poor'];
      }
      ?>
      
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr 1fr;gap:12px;margin-bottom:16px">
        <label>
          ⭐⭐⭐⭐⭐ Excellent
          <input type="number" name="excellent" value="<?php echo $excellent; ?>" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px"/>
        </label>
        
        <label>
          ⭐⭐⭐⭐ Very Good
          <input type="number" name="very_good" value="<?php echo $veryGood; ?>" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px"/>
        </label>
        
        <label>
          ⭐⭐⭐ Good
          <input type="number" name="good" value="<?php echo $good; ?>" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px"/>
        </label>
        
        <label style="background:#fff8e1;padding:10px;border-radius:6px">
          ⭐⭐ Average
          <input type="number" name="average" value="<?php echo $average; ?>" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px"/>
        </label>
        
        <label style="background:#ffebee;padding:10px;border-radius:6px">
          ⭐ Poor
          <input type="number" name="poor" value="<?php echo $poor; ?>" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px"/>
        </label>
      </div>
      
      <button type="submit" class="btn primary">Save Rating Distribution</button>
      <small style="display:block;color:#666;margin-top:8px">💡 Total ratings and average will auto-calculate from these numbers</small>
    </form>
    
    <!-- Add/Edit Review Form -->
    <form method="post" action="<?php echo url('admin.php'); ?>" style="border:1px solid #ddd;border-radius:8px;padding:20px;margin-bottom:24px;background:#f8f9fa">
      <h3 style="margin-top:0"><?php echo $editReview ? 'Edit Review' : 'Add New Review'; ?></h3>
      <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="save_review" value="1"/>
      <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($reviewProd['id']); ?>"/>
      <?php if($editReview): ?>
        <input type="hidden" name="review_index" value="<?php echo $editReviewIndex; ?>"/>
      <?php endif; ?>
      
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
        <label>
          Customer Name <span style="color:red">*</span>
          <input name="review_name" required value="<?php echo htmlspecialchars($editReview['name'] ?? ''); ?>" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px"/>
        </label>
        
        <label>
          Rating (1-5) <span style="color:red">*</span>
          <select name="review_rating" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px">
            <option value="5" <?php echo ($editReview['rating'] ?? 0) == 5 ? 'selected' : ''; ?>>⭐⭐⭐⭐⭐ (5.0)</option>
            <option value="4.5" <?php echo ($editReview['rating'] ?? 0) == 4.5 ? 'selected' : ''; ?>>⭐⭐⭐⭐ (4.5)</option>
            <option value="4" <?php echo ($editReview['rating'] ?? 0) == 4 ? 'selected' : ''; ?>>⭐⭐⭐⭐ (4.0)</option>
            <option value="3.5" <?php echo ($editReview['rating'] ?? 0) == 3.5 ? 'selected' : ''; ?>>⭐⭐⭐ (3.5)</option>
            <option value="3" <?php echo ($editReview['rating'] ?? 0) == 3 ? 'selected' : ''; ?>>⭐⭐⭐ (3.0)</option>
            <option value="2.5" <?php echo ($editReview['rating'] ?? 0) == 2.5 ? 'selected' : ''; ?>>⭐⭐ (2.5)</option>
            <option value="2" <?php echo ($editReview['rating'] ?? 0) == 2 ? 'selected' : ''; ?>>⭐⭐ (2.0)</option>
            <option value="1.5" <?php echo ($editReview['rating'] ?? 0) == 1.5 ? 'selected' : ''; ?>>⭐ (1.5)</option>
            <option value="1" <?php echo ($editReview['rating'] ?? 0) == 1 ? 'selected' : ''; ?>>⭐ (1.0)</option>
          </select>
        </label>
        
        <label>
          Date <span style="color:red">*</span>
          <input type="date" name="review_date" required value="<?php echo $editReview['date'] ?? date('Y-m-d'); ?>" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px"/>
        </label>
        
        <label>
          Helpful Count
          <input type="number" name="review_helpful" value="<?php echo $editReview['helpful'] ?? 0; ?>" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px"/>
        </label>
      </div>
      
      <label>
        Review Text <span style="color:red">*</span>
        <textarea name="review_text" required rows="4" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-top:4px"><?php echo htmlspecialchars($editReview['text'] ?? ''); ?></textarea>
      </label>
      
      <div style="display:flex;gap:10px;margin-top:16px">
        <button type="submit" class="btn primary"><?php echo $editReview ? 'Update Review' : 'Add Review'; ?></button>
        <?php if($editReview): ?>
          <a href="<?php echo url('admin.php?manage_reviews='.urlencode($reviewProd['id'])); ?>" class="btn ghost">Cancel Edit</a>
        <?php endif; ?>
      </div>
    </form>
    
    <!-- Existing Reviews -->
    <h3>Existing Reviews (<?php echo count($reviewProd['customer_reviews'] ?? []); ?>)</h3>
    <div style="background:#fff;padding:12px;border:1px solid #ddd;border-radius:8px;margin-bottom:16px">
      <strong>Auto-Calculated Stats:</strong><br>
      Average Rating: <strong><?php echo $reviewProd['rating'] ?? 4.0; ?>⭐</strong> | 
      Total Reviews: <strong><?php echo $reviewProd['reviews'] ?? 0; ?></strong>
    </div>
    
    <?php if(empty($reviewProd['customer_reviews'])): ?>
      <p style="text-align:center;color:#666;padding:20px">No reviews yet. Add your first review above!</p>
    <?php else: ?>
      <div style="display:grid;gap:12px">
        <?php foreach(($reviewProd['customer_reviews'] ?? []) as $idx => $rev): ?>
          <div style="border:1px solid #ddd;border-radius:8px;padding:16px;background:#fff">
            <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:8px">
              <div>
                <strong><?php echo htmlspecialchars($rev['name']); ?></strong>
                <span style="background:#059669;color:#fff;padding:2px 8px;border-radius:4px;font-size:12px;margin-left:8px">
                  <?php echo $rev['rating']; ?>⭐
                </span>
              </div>
              <div style="display:flex;gap:8px">
                <a href="<?php echo url('admin.php?manage_reviews='.urlencode($reviewProd['id']).'&edit_review='.$idx); ?>" class="btn ghost" style="padding:6px 12px;font-size:13px">Edit</a>
                <a href="<?php echo url('admin.php?delete_review='.$idx.'&product_id='.urlencode($reviewProd['id'])); ?>" class="btn danger" style="padding:6px 12px;font-size:13px" onclick="return confirm('Delete this review?')">Delete</a>
              </div>
            </div>
            <div style="font-size:12px;color:#999;margin-bottom:8px"><?php echo htmlspecialchars($rev['date']); ?></div>
            <p style="color:#666;margin:8px 0"><?php echo htmlspecialchars($rev['text']); ?></p>
            <div style="font-size:13px;color:#666">👍 Helpful (<?php echo $rev['helpful']; ?>)</div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php endif; endif; ?>

<?php require __DIR__.'/partials/footer.php'; ?>