<?php
require_once __DIR__ . '/includes/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    redirect('/admin/login.php');
}

$orders = [];
try {
    $stmt = Database::query(
        "SELECT o.*, s.name as service_name, u.name as user_name
         FROM orders o
         JOIN services s ON o.service_id = s.service_id
         JOIN users u ON o.user_id = u.user_id
         ORDER BY o.created_at DESC"
    );
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Admin Orders Error: " . $e->getMessage());
    $error = "Unable to load orders. Please try again.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Sagun Laundry</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <?php include __DIR__ . '/includes/header.php'; ?>
        
        <section class="admin-panel">
            <h2><i class="fas fa-cog"></i> Order Management</h2>
            <?php if (isset($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <canvas id="orderStatusChart" width="400" height="200"></canvas>
            <div class="orders-list">
                <?php if (empty($orders)): ?>
                    <p>No orders found.</p>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                    <div class="order-item">
                        <h3>
                            Order #<?= $order['order_id'] ?> 
                            <span class="order-status status-<?= $order['status'] ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </h3>
                        <div class="order-details">
                            <p><strong>Customer:</strong> <?= htmlspecialchars($order['user_name']) ?></p>
                            <p><strong>Service:</strong> <?= htmlspecialchars($order['service_name']) ?></p>
                            <p><strong>Quantity:</strong> <?= number_format($order['quantity'], 2) ?></p>
                            <p><strong>Total:</strong> NPR <?= number_format($order['total_price'], 2) ?></p>
                            <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
                            <p><strong>Date:</strong> <?= date('M d, Y h:i A', strtotime($order['created_at'])) ?></p>
                            <?php if ($order['special_instructions']): ?>
                                <p><strong>Instructions:</strong> <?= htmlspecialchars($order['special_instructions']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="order-actions">
                            <form action="/processes/update_status.php" method="POST">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                <select name="status">
                                    <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                                    <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                </select>
                                <button type="submit" class="btn btn-complete">Update Status</button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
        
        <?php include __DIR__ . '/includes/footer.php'; ?>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('orderStatusChart')?.getContext('2d');
            if (ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Pending', 'Processing', 'Completed'],
                        datasets: [{
                            label: 'Number of Orders',
                            data: [<?php
                                $counts = ['pending' => 0, 'processing' => 0, 'completed' => 0];
                                foreach ($orders as $order) {
                                    $counts[$order['status']]++;
                                }
                                echo "{$counts['pending']}, {$counts['processing']}, {$counts['completed']}";
                            ?>],
                            backgroundColor: ['#ffc107', '#2196f3', '#4caf50'],
                            borderColor: ['#fff'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: { beginAtZero: true, title: { display: true, text: 'Number of Orders' } },
                            x: { title: { display: true, text: 'Order Status' } }
                        },
                        plugins: {
                            title: { display: true, text: 'Orders by Status - Sagun Laundry (Jul 04, 2025)' }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>