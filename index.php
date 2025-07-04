<?php
require_once __DIR__ . '/includes/config.php';

$currentUser = null;
if (isset($_SESSION['user_id'])) {
    $currentUser = [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email']
    ];
}

$services = [];
try {
    $stmt = Database::query("SELECT * FROM services");
    $services = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Services Error: " . $e->getMessage());
    $error = "Unable to load services. Please try again later.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sagun Laundry</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <div class="container">
        <?php include __DIR__ . '/includes/header.php'; ?>
        
        <section class="hero">
            <h2>DD Laundry Service</h2>
            <p>Fast, Affordable & Reliable Laundry Services.</p>
            <?php if ($currentUser): ?>
                <a href="/order.php" class="btn">Place New Order</a>
            <?php else: ?>
                <a href="/register.php" class="btn">Register Now</a>
                <a href="/login.php" class="btn btn-outline">Login</a>
            <?php endif; ?>
        </section>
        
        <section class="services" id="services">
            <h2 class="section-title">Our Services</h2>
            <?php if (isset($error)): ?>
                <p class="error-message"><?= htmlspecialchars($error) ?></p>
            <?php elseif (empty($services)): ?>
                <p class="error-message">No services available.</p>
            <?php else: ?>
                <div class="services-grid">
                    <?php foreach ($services as $service): ?>
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-<?= htmlspecialchars($service['icon'] ?? 'tshirt') ?>"></i>
                        </div>
                        <div class="service-content">
                            <h3><?= htmlspecialchars($service['name']) ?></h3>
                            <p><?= htmlspecialchars($service['description']) ?></p>
                            <div class="price">NPR <?= number_format($service['price'], 2) ?> per <?= htmlspecialchars($service['unit']) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
        
        <section class="location" id="location">
            <h2 class="section-title">Our Location</h2>
            <div class="map-container">
                <div class="map-placeholder">
                    <i class="fas fa-map-marker-alt"></i>
                    <p>Google Maps Placeholder</p>
                </div>
                <div class="address">
                    <p><i class="fas fa-map-pin"></i> Hattiban Kharibot, Lalitpur District</p>
                    <p><i class="fas fa-phone"></i> 9749863285</p>
                    <p><i class="fas fa-clock"></i> Open: Sunday to Friday 7 AM to 10 PM | Closed on Saturday</p>
                </div>
            </div>
        </section>
        
        <?php include __DIR__ . '/includes/footer.php'; ?>
    </div>
    
    <script src="/assets/js/script.js"></script>
</body>
</html>