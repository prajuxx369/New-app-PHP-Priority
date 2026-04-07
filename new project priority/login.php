<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

redirectLoggedIn();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        
        // If NGO, get NGO ID
        if ($user['role'] === 'ngo') {
            $stmtNgo = $pdo->prepare("SELECT id FROM ngos WHERE user_id = ?");
            $stmtNgo->execute([$user['id']]);
            $ngo = $stmtNgo->fetch();
            $_SESSION['ngo_id'] = $ngo['id'] ?? null;
        }

        redirectLoggedIn();
    } else {
        $error = "Invalid email or password.";
    }
}

include 'includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card p-4">
                    <h2 class="text-center fw-bold mb-4">Welcome Back</h2>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Login</button>
                        <p class="text-center text-muted">Don't have an account? <a href="register.php">Register here</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
