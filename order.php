<?php
require_once __DIR__ . '/includes/config.php';

if (!isset($_SESSION['user_id'])) {
    redirect('/login.php');
}

$services = [];
try {
    $stmt = Database::query("SELECT * FROM services");
    $services = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Services Error: " . $e->getMessage());
    $error = "Unable to load services. Please try again.";
}

$error = $_SESSION['order_error'] ?? '';
$success = $_SESSION['order_success'] ?? '';
unset($_SESSION['order_error']);
unset($_SESSION['order_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Place Order - Sagun Laundry</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <div class="container">
        <?php include __DIR__ . '/includes/header.php'; ?>
        
        <section class="form-section">
            <h2>Place Your Order</h2>
            
            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <div id="price-display">Estimated Total: NPR 0.00</div>
            <form action="/processes/order_process.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <div class="form-group">
                    <label for="service_id">Service</label>
                    <select id="service_id" name="service_id" required>
                        <option value="">Select a Service</option>
                        <?php foreach ($services as $service): ?>
                            <option value="<?= $service['service_id'] ?>" data-price="<?= $service['price'] ?>">
                                <?= htmlspecialchars($service['name']) ?> - NPR <?= number_format($service['price'], 2) ?>/<?= htmlspecialchars($service['unit']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" min="0.1" step="0.1" value="1" required>
                </div>
                
                <div class="form-group">
                    <label for="address">Delivery Address</label>
                    <textarea id="address" name="address" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="instructions">Special Instructions</label>
                    <textarea id="instructions" name="instructions" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn">Place Order</button>
            </form>
        </section>
        
        <?php include __DIR__ . '/includes/footer.php'; ?>
    </div>
    
    <script src="/assets/js/script.js"></script>
    <script>
        const serviceSelect = document.getElementById('service_id');
        const quantityInput = document.getElementById('quantity');
        if (serviceSelect && quantityInput) {
            serviceSelect.addEventListener('change', updatePrice);
            quantityInput.addEventListener('input', updatePrice);
            
            function updatePrice() {
                if (!serviceSelect.value) {
                    document.getElementById('price-display').textContent = 'Estimated Total: NPR 0.00';
                    return;
                }
                const option = serviceSelect.options[serviceSelect.selectedIndex];
                const price = parseFloat(option.dataset.price) || 0;
                const quantity = parseFloat(quantityInput.value) || 0;
                document.getElementById('price-display').textContent = 
                    `Estimated Total: NPR ${(price * quantity).toFixed(2)}`;
            }
        }
    </script>
</body>
</html>