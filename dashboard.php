<?php
require_once __DIR__ . '/includes/config.php';

if (!isset($_SESSION['user_id'])) {
    redirect('/login.php');
}

$orders = [];
try {
    $stmt = Database::query(
        "SELECT o.*, s.name as service_name 
         FROM orders o
         JOIN services s ON o.service_id = s.service_id
         WHERE o.user_id = ?
         ORDER BY o.created_at DESC",
        [$_SESSION['user_id']]
    );
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Orders Error: " . $e->getMessage());
    $error = "Unable to load orders. Please try again.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard - Sagun Laundry</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <div class="container">
        <?php include __DIR__ . '/includes/header.php'; ?>
        
        <section class="dashboard">
            <h2>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h2>
            
            <?php if (isset($_SESSION['order_success'])): ?>
                <div class="notification show success-message"><?= htmlspecialchars($_SESSION['order_success']) ?></div>
                <?php unset($_SESSION['order_success']); ?>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <div class="user-actions">
                <a href="/order.php" class="btn">Place New Order</a>
                <a href="/processes/logout.php" class="btn btn-outline">Logout</a>
            </div>
            
            <div class="orders-list">
                <h3>Your Orders</h3>
                
                <?php if (empty($orders)): ?>
                    <p>No orders found.</p>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                    <div class="order-item">
                        <h4>Order #<?= $order['order_id'] ?></h4>
                        <p><strong>Service:</strong> <?= htmlspecialchars($order['service_name']) ?></p>
                        <p><strong>Quantity:</strong> <?= number_format($order['quantity'], 2) ?></p>
                        <p><strong>Total:</strong> NPR <?= number_format($order['total_price'], 2) ?></p>
                        <p><strong>Status:</strong> <span class="status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></p>
                        <p><strong>Date:</strong> <?= date('M d, Y h:i A', strtotime($order['created_at'])) ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
        
        <?php include __DIR__ . '/includes/footer.php'; ?>
    </div>
</body>
</html>