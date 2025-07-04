<?php
require_once __DIR__ . '/includes/config.php';

if (isset($_SESSION['user_id'])) {
    redirect('/dashboard.php');
}

$errors = $_SESSION['register_errors'] ?? [];
$error = $_SESSION['register_error'] ?? '';
unset($_SESSION['register_errors']);
unset($_SESSION['register_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Sagun Laundry</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <div class="container">
        <?php include __DIR__ . '/includes/header.php'; ?>
        
        <section class="form-section">
            <h2>Create Your Account</h2>
            
            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <?php foreach ($errors as $field => $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form action="/processes/register_process.php" method="POST" id="registrationForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                    <?php if (isset($errors['name'])): ?>
                        <span class="error"><?= htmlspecialchars($errors['name']) ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                    <?php if (isset($errors['email'])): ?>
                        <span class="error"><?= htmlspecialchars($errors['email']) ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required pattern="\+?977[0-9]{9,10}">
                    <?php if (isset($errors['phone'])): ?>
                        <span class="error"><?= htmlspecialchars($errors['phone']) ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <?php if (isset($errors['password'])): ?>
                        <span class="error"><?= htmlspecialchars($errors['password']) ?></span>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn">Register</button>
            </form>
            
            <div class="form-footer">
                Already have an account? <a href="/login.php">Login here</a>
            </div>
        </section>
        
        <?php include __DIR__ . '/includes/footer.php'; ?>
    </div>
    <script src="/assets/js/script.js"></script>
</body>
</html>