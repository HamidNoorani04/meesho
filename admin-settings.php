<?php
$pageTitle = 'Admin Settings - Meesho Shop'; 
require __DIR__.'/partials/head.php'; 
require __DIR__.'/partials/header.php';

// Check admin authentication
if (!is_admin()) {
    header('Location: ' . url('admin-login.php'));
    exit;
}

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    verify_csrf();
    
    $settings = load_settings();
    $settings['offer_enabled'] = isset($_POST['offer_enabled']) ? true : false;
    
    save_settings($settings);
    header('Location: ' . url('admin-settings.php?saved=1'));
    exit;
}

$settings = load_settings();
?>

<style>
.admin-settings {
    max-width: 800px;
    margin: 40px auto;
    padding: 20px;
}

.settings-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.settings-header h2 {
    margin: 0;
    color: #0f172a;
    font-size: 24px;
}

.back-link {
    background: #6b7280;
    color: #fff;
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
}

.settings-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 20px;
}

.settings-section {
    margin-bottom: 32px;
}

.settings-section:last-child {
    margin-bottom: 0;
}

.section-title {
    font-size: 18px;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 8px;
}

.section-desc {
    font-size: 14px;
    color: #6b7280;
    margin: 0 0 16px;
    line-height: 1.5;
}

.toggle-container {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: #f9fafb;
    border-radius: 8px;
}

.toggle-switch {
    position: relative;
    width: 52px;
    height: 28px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #e5e7eb;
    transition: .3s;
    border-radius: 28px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
}

input:checked + .toggle-slider {
    background-color: #10b981;
}

input:checked + .toggle-slider:before {
    transform: translateX(24px);
}

.toggle-label {
    flex: 1;
}

.toggle-label strong {
    display: block;
    font-size: 15px;
    color: #0f172a;
    margin-bottom: 4px;
}

.toggle-label span {
    font-size: 13px;
    color: #6b7280;
}

.save-btn {
    background: #9f2089;
    color: #fff;
    border: none;
    padding: 14px 32px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
}

.save-btn:hover {
    background: #88176e;
}

.success-message {
    background: #d4edda;
    color: #155724;
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-box {
    background: #fef3c7;
    border: 1px solid #fbbf24;
    border-radius: 8px;
    padding: 16px;
    margin-top: 16px;
}

.info-box strong {
    display: block;
    font-size: 14px;
    color: #92400e;
    margin-bottom: 8px;
}

.info-box ul {
    margin: 0;
    padding-left: 20px;
    color: #78350f;
    font-size: 13px;
}

.info-box li {
    margin-bottom: 4px;
}
</style>

<main class="admin-settings">
    <div class="settings-header">
        <h2>⚙️ Settings</h2>
        <a href="<?php echo url('admin.php'); ?>" class="back-link">← Back to Admin</a>
    </div>

    <?php if(isset($_GET['saved'])): ?>
        <div class="success-message">
            <span style="font-size:20px">✓</span>
            <span>Settings saved successfully!</span>
        </div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        
        <div class="settings-card">
            <div class="settings-section">
                <h3 class="section-title">Promotional Offers</h3>
                <p class="section-desc">
                    Manage special offers and promotions for your customers
                </p>
                
                <div class="toggle-container">
                    <label class="toggle-switch">
                        <input type="checkbox" 
                               name="offer_enabled" 
                               <?php echo $settings['offer_enabled'] ? 'checked' : ''; ?>>
                        <span class="toggle-slider"></span>
                    </label>
                    <div class="toggle-label">
                        <strong>Buy 3 Get 1 Free Offer</strong>
                        <span>When enabled, customers get the cheapest item free when buying 3+ products</span>
                    </div>
                </div>

                <div class="info-box">
                    <strong>📋 How it works:</strong>
                    <ul>
                        <li>Counts total items including quantities (e.g., 3 same items = 3 items)</li>
                        <li>Automatically finds the cheapest product in cart</li>
                        <li>Applies 1 free item discount at checkout</li>
                        <li>Shows promotional banner to customers</li>
                    </ul>
                </div>
            </div>
        </div>

        <button type="submit" name="save_settings" class="save-btn">
            💾 Save Settings
        </button>
    </form>
</main>

<?php require __DIR__.'/partials/footer.php'; ?>