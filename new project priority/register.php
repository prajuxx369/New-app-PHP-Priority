<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

redirectLoggedIn();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password, $role]);
        $user_id = $pdo->lastInsertId();

        if ($role === 'ngo') {
            $ngo_name = $_POST['ngo_name'];
            $reg_no = $_POST['reg_no'];
            $stmtNgo = $pdo->prepare("INSERT INTO ngos (user_id, ngo_name, registration_no, email) VALUES (?, ?, ?, ?)");
            $stmtNgo->execute([$user_id, $ngo_name, $reg_no, $email]);
        }

        $pdo->commit();
        $success = "Registration successful! You can now login.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Registration failed: " . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4">
                    <h2 class="text-center fw-bold mb-4">Join Smart NGO</h2>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Full Name / Point of Contact</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">I am a:</label>
                            <select name="role" id="roleSelector" class="form-select" required onchange="toggleNgoFields()">
                                <option value="donor">Donor (Individual Giver)</option>
                                <option value="ngo">NGO (Organization)</option>
                            </select>
                        </div>

                        <div id="ngoFields" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Official NGO Name</label>
                                <input type="text" name="ngo_name" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Registration Number</label>
                                <input type="text" name="reg_no" class="form-control">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Register</button>
                        <p class="text-center text-muted">Already have an account? <a href="login.php">Login here</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function toggleNgoFields() {
    var role = document.getElementById('roleSelector').value;
    document.getElementById('ngoFields').style.display = (role === 'ngo') ? 'block' : 'none';
}
</script>

<?php include 'includes/footer.php'; ?>
