<?php
require_once __DIR__ . '/config.php';

function data_file(){ return __DIR__.'/data/products.json'; }

function load_products(){
  $path = data_file();
  if(!file_exists($path)) return [];
  $json = file_get_contents($path);
  return json_decode($json, true) ?? [];
}

function save_products($arr){
  // Check if admin is logged in before allowing save
  if (!is_admin()) {
    die('Unauthorized: Admin access required');
  }
  
  $path = data_file();
  
  // Validate data before saving
  if (!is_array($arr)) {
    die('Invalid data format');
  }
  
  // Sanitize products array
  $sanitized = [];
  foreach ($arr as $product) {
    if (isset($product['id']) && isset($product['title'])) {
      $sanitized[] = $product;
    }
  }
  
  file_put_contents($path, json_encode(array_values($sanitized), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
}
?>
