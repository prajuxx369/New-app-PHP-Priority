<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    header("Location: login.php?redirect=donate.php?project_id=" . ($_GET['project_id'] ?? ''));
    exit();
}

$project_id = $_GET['project_id'] ?? 0;
$stmt = $pdo->prepare("SELECT p.*, n.ngo_name FROM projects p JOIN ngos n ON p.ngo_id = n.id WHERE p.id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch();

if (!$project) {
    header("Location: projects.php");
    exit();
}

$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $donor_id = $_SESSION['user_id'];

    try {
        $pdo->beginTransaction();

        // Save donation
        $stmtDonation = $pdo->prepare("INSERT INTO donations (donor_id, project_id, amount, payment_status, transaction_ref) VALUES (?, ?, ?, 'completed', ?)");
        $stmtDonation->execute([$donor_id, $project_id, $amount, 'TXN'.time().rand(10,99)]);

        // Update project collected amount
        $stmtUpdate = $pdo->prepare("UPDATE projects SET collected_amount = collected_amount + ? WHERE id = ?");
        $stmtUpdate->execute([$amount, $project_id]);

        $pdo->commit();
        $success = "Thank you! Your donation of $" . number_format($amount, 2) . " has been successfully recorded.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Payment failed: " . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card p-4">
                    <h3 class="fw-bold mb-4">Complete Donation</h3>
                    <div class="alert alert-info py-2 small">
                        Donating to: <strong><?php echo htmlspecialchars($project['title']); ?></strong>
                        <br>by <i><?php echo htmlspecialchars($project['ngo_name']); ?></i>
                    </div>

                    <?php if ($success): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                            <h4 class="fw-bold">Donation Successful</h4>
                            <p class="text-muted"><?php echo $success; ?></p>
                            <a href="donor/dashboard.php" class="btn btn-primary mt-3">Go to Dashboard</a>
                        </div>
                    <?php else: ?>
                        <form method="POST">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Donation Amount ($)</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="amount" class="form-control" placeholder="0.00" min="1" step="0.01" required>
                                </div>
                            </div>

                            <h6 class="fw-bold mb-3">Payment Details (Demo)</h6>
                            <div class="mb-3">
                                <label class="form-label small">Card Number</label>
                                <input type="text" class="form-control" placeholder="•••• •••• •••• ••••" disabled>
                            </div>
                            <div class="row mb-4">
                                <div class="col-6">
                                    <label class="form-label small">Expiry</label>
                                    <input type="text" class="form-control" placeholder="MM/YY" disabled>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">CVV</label>
                                    <input type="text" class="form-control" placeholder="•••" disabled>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success btn-lg w-100 py-3 fw-bold">Complete Donation</button>
                            <p class="text-center text-muted small mt-3">This is a mock payment for MVP demonstration.</p>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
