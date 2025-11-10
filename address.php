<?php
$pageTitle = 'Add Delivery Address - Meesho Shop';
require __DIR__.'/partials/head.php';
require __DIR__.'/partials/header.php';

if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])){
  header('Location: '.url('index.php'));
  exit;
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $_SESSION['address'] = [
    'full_name' => trim($_POST['full_name'] ?? ''),
    'mobile' => trim($_POST['mobile'] ?? ''),
    'pincode' => trim($_POST['pincode'] ?? ''),
    'city' => trim($_POST['city'] ?? ''),
    'state' => trim($_POST['state'] ?? ''),
    'house_no' => trim($_POST['house_no'] ?? ''),
    'road_name' => trim($_POST['road_name'] ?? '')
  ];
  header('Location: '.url('payment.php'));
  exit;
}

$address = $_SESSION['address'] ?? [];
?>

<style>
.site-footer{display:none !important}
.progress-bar-container{background:#fff;padding:20px 0;border-bottom:1px solid #e5e7eb;position:sticky;top:71px;z-index:40}
.progress-steps{display:flex;justify-content:space-between;align-items:center;max-width:600px;margin:0 auto;position:relative}
.progress-steps::before{content:'';position:absolute;top:15px;left:0;right:0;height:2px;background:#e5e7eb;z-index:0}
.progress-line{position:absolute;top:15px;left:0;height:2px;background:#9333ea;z-index:1;transition:width 0.3s}
.progress-step{display:flex;flex-direction:column;align-items:center;gap:6px;position:relative;z-index:2;flex:1}
.step-circle{width:32px;height:32px;border-radius:50%;background:#fff;border:2px solid #e5e7eb;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:14px;color:#9ca3af;transition:all 0.3s}
.progress-step.active .step-circle{background:#9333ea;border-color:#9333ea;color:#fff}
.progress-step.completed .step-circle{background:#10b981;border-color:#10b981;color:#fff}
.progress-step.completed .step-circle::after{content:'✓'}
.step-label{font-size:12px;color:#9ca3af;font-weight:500}
.progress-step.active .step-label{color:#9333ea;font-weight:600}
.progress-step.completed .step-label{color:#10b981}
.address-page{max-width:600px;margin:0 auto;padding:20px 16px 100px}
.address-header{display:flex;align-items:center;gap:12px;margin-bottom:24px}
.address-header h2{margin:0;font-size:20px;font-weight:600;color:#0f172a}
.form-group{margin-bottom:20px}
.form-group label{display:block;font-size:13px;color:#6b7280;margin-bottom:6px;font-weight:500}
.form-group input,.form-group select{width:100%;padding:14px;border:1px solid #d1d5db;border-radius:8px;font-size:15px;background:#f9fafb;transition:all 0.2s;color: black}
.form-group input:focus,.form-group select:focus{outline:none;border-color:#9333ea;background:#fff}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.trust-badges{display:flex;justify-content:center;align-items:center;gap:20px;padding:16px;background:#f9fafb;border-radius:8px;margin-top:24px}
.trust-badge{display:flex;flex-direction:column;align-items:center;gap:4px}
.trust-badge-icon{width:40px;height:40px;background:#e5e7eb;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px}
.trust-badge-text{font-size:10px;color:#6b7280;font-weight:600;text-align:center}
.powered-by{text-align:center;margin-top:12px;font-size:11px;color:#9ca3af}
.powered-by img{height:16px;display:inline-block;vertical-align:middle;margin-left:4px}
.bottom-actions{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #e5e7eb;padding:16px;z-index:100;box-shadow:0 -2px 10px rgba(0,0,0,0.05)}
.bottom-actions button{width:100%;padding:16px;background: center center rgb(159, 32, 137);color:#fff;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer}
.bottom-actions button:disabled{background:#d1d5db;cursor:not-allowed}
</style>

<div class="progress-bar-container">
  <div class="container">
    <div class="progress-steps">
      <div class="progress-line" style="width:33.33%"></div>
      
      <div class="progress-step completed">
        <div class="step-circle"></div>
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

<main class="address-page">
  <div class="address-header">
    <span style="font-size:24px;color:#9333ea">📍</span>
    <h2>Address</h2>
  </div>
  
  <form method="post" action="<?php echo url('address.php'); ?>" id="addressForm">
    <div class="form-group">
      <label>Full Name</label>
      <input type="text" name="full_name" required value="<?php echo htmlspecialchars($address['full_name'] ?? ''); ?>"/>
    </div>
    
    <div class="form-group">
      <label>Mobile number</label>
      <input type="tel" name="mobile" required pattern="[0-9]{10}" maxlength="10" value="<?php echo htmlspecialchars($address['mobile'] ?? ''); ?>"/>
    </div>
    
    <div class="form-group">
      <label>Pincode</label>
      <input type="text" name="pincode" required pattern="[0-9]{6}" maxlength="6" value="<?php echo htmlspecialchars($address['pincode'] ?? ''); ?>"/>
    </div>
    
    <div class="form-row">
      <div class="form-group">
        <label>City</label>
        <input type="text" name="city" required value="<?php echo htmlspecialchars($address['city'] ?? ''); ?>"/>
      </div>
      
      <div class="form-group">
      <label>State</label>
      <select name="state" required>
        <option value="">Select State</option>
        <?php
        // A-Z list of all states and UTs
        $all_states = [
            "Andaman and Nicobar Islands", "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", 
            "Chandigarh", "Chhattisgarh", "Dadra and Nagar Haveli and Daman and Diu", "Delhi", "Goa", 
            "Gujarat", "Haryana", "Himachal Pradesh", "Jammu and Kashmir", "Jharkhand", "Karnataka", 
            "Kerala", "Ladakh", "Lakshadweep", "Madhya Pradesh", "Maharashtra", "Manipur", 
            "Meghalaya", "Mizoram", "Nagaland", "Odisha", "Puducherry", "Punjab", "Rajasthan", 
            "Sikkim", "Tamil Nadu", "Telangana", "Tripura", "Uttar Pradesh", "Uttarakhand", "West Bengal"
        ];
        
        $selected_state = $address['state'] ?? '';
        
        foreach ($all_states as $state):
        ?>
          <option value="<?php echo htmlspecialchars($state); ?>" <?php echo $selected_state === $state ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($state); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    
    <div class="form-group">
      <label>House No., Building Name</label>
      <input type="text" name="house_no" required value="<?php echo htmlspecialchars($address['house_no'] ?? ''); ?>"/>
    </div>
    
    <div class="form-group">
      <label>Road name, Area, Colony</label>
      <input type="text" name="road_name" required value="<?php echo htmlspecialchars($address['road_name'] ?? ''); ?>"/>
    </div>
    
    
    
  </form>
  
</main>
        
<div class="trust-badges"> 
        <img src="https://messho.shop/assets/website/images/secure.jpg" alt="" style="
    	height: -webkit-fill-available;
    	width: -webkit-fill-available;
      max-width: 600px;
    margin: 0 auto;
    padding: 20px 16px 100px;">
    </div> 

  
<div class="bottom-actions">
  <button type="submit" form="addressForm">CONTINUE</button>
</div>

<?php require __DIR__.'/partials/footer.php'; ?>