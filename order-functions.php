<?php
require_once __DIR__ . '/db.php';

// Save order to database
function save_order_to_db($order_data, $cart_items, $status = 'success', $failure_reason = '') {
    $db = get_db_connection();
    
    try {
        $db->beginTransaction();
        
        // Generate unique order number
        $order_number = 'ORD' . time() . rand(1000, 9999);
        
        // Insert order
        $stmt = $db->prepare("
            INSERT INTO orders (
                order_number, customer_name, mobile, email, 
                address_line1, address_line2, city, state, pincode,
                total_amount, payment_method, payment_status, 
                order_status, status, transaction_id, failure_reason
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $order_number,
            $order_data['full_name'],
            $order_data['mobile'],
            $order_data['email'] ?? '',
            $order_data['address_line1'] ?? '',
            $order_data['address_line2'] ?? '',
            $order_data['city'] ?? '',
            $order_data['state'] ?? '',
            $order_data['pincode'] ?? '',
            $order_data['total_amount'],
            $order_data['payment_method'] ?? 'COD',
            $order_data['payment_status'] ?? 'pending',
            'processing',
            $status,
            $order_data['transaction_id'] ?? '',
            $failure_reason
        ]);
        
        $order_id = $db->lastInsertId();
        
        // Insert order items
        $stmt = $db->prepare("
            INSERT INTO order_items (
                order_id, product_id, product_title, product_image,
                quantity, price, size, color
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($cart_items as $item) {
            $stmt->execute([
                $order_id,
                $item['id'],
                $item['title'],
                $item['image'] ?? '',
                $item['qty'],
                $item['price'],
                $item['size'] ?? '',
                $item['color'] ?? ''
            ]);
        }
        
        $db->commit();
        return $order_number;
        
    } catch (Exception $e) {
        $db->rollBack();
        error_log("Order save failed: " . $e->getMessage());
        return false;
    }
}

// Get order by order number
function get_order_by_number($order_number) {
    $db = get_db_connection();
    
    $stmt = $db->prepare("SELECT * FROM orders WHERE order_number = ?");
    $stmt->execute([$order_number]);
    $order = $stmt->fetch();
    
    if ($order) {
        $stmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->execute([$order['id']]);
        $order['items'] = $stmt->fetchAll();
    }
    
    return $order;
}

// Get all orders (for admin)
function get_all_orders($limit = 100, $offset = 0) {
    $db = get_db_connection();
    
    $stmt = $db->prepare("
        SELECT * FROM orders 
        ORDER BY order_date DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$limit, $offset]);
    return $stmt->fetchAll();
}

// Update payment status
function update_payment_status($order_number, $status, $transaction_id = '') {
    $db = get_db_connection();
    
    $stmt = $db->prepare("
        UPDATE orders 
        SET payment_status = ?, transaction_id = ? 
        WHERE order_number = ?
    ");
    
    return $stmt->execute([$status, $transaction_id, $order_number]);
}

// ADD THIS NEW FUNCTION TO THE END OF THE FILE
function update_order_status_after_payment($order_number, $status, $payment_status, $transaction_id, $failure_reason = '') {
    $db = get_db_connection();
    
    try {
        $stmt = $db->prepare("
            UPDATE orders 
            SET 
                status = ?, 
                payment_status = ?, 
                order_status = ?, 
                transaction_id = ?, 
                failure_reason = ?
            WHERE 
                order_number = ?
        ");
        
        $order_status = ($status === 'success') ? 'processing' : 'failed';
        
        $stmt->execute([
            $status,
            $payment_status,
            $order_status,
            $transaction_id,
            $failure_reason,
            $order_number
        ]);
        
        return true;
        
    } catch (Exception $e) {
        error_log("Order update failed: " . $e->getMessage());
        return false;
    }
}
?>