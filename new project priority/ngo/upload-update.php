<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireRole('ngo');

$project_id = $_GET['project_id'] ?? 0;
$ngo_id = $_SESSION['ngo_id'];

// Verify ownership
$stmtCheck = $pdo->prepare("SELECT * FROM projects WHERE id = ? AND ngo_id = ?");
$stmtCheck->execute([$project_id, $ngo_id]);
$project = $stmtCheck->fetch();

if (!$project) {
    header("Location: dashboard.php");
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $amount_used = $_POST['amount_used'];

    // Mock file upload
    $update_image = '';
    if (isset($_FILES['update_image']) && $_FILES['update_image']['error'] === 0) {
        $update_image = 'assets/images/update-proof.jpg';
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO fund_updates (project_id, title, description, amount_used, update_image) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$project_id, $title, $description, $amount_used, $update_image]);
        $success = "Fund utilization update uploaded successfully!";
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

include '../includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card p-4 border-success border-top border-4 shadow-sm">
                    <h3 class="fw-bold mb-4 text-success"><i class="fas fa-file-upload me-2"></i>Upload Utilization Proof</h3>
                    <div class="alert alert-info py-2 small">
                        Adding update for: <strong><?php echo htmlspecialchars($project['title']); ?></strong>
                    </div>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?> <a href="dashboard.php" class="alert-link">Back to Dashboard</a></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Update Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Purchased 500 backpacks for students" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">What was accomplished?</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Briefly explain how the funds were used and the outcome..." required></textarea>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Amount Utilized ($)</label>
                                <input type="number" name="amount_used" class="form-control" step="0.01" placeholder="0.00" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Proof Image / Receipt</label>
                                <input type="file" name="update_image" class="form-control">
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success px-5">Submit Update</button>
                            <a href="dashboard.php" class="btn btn-outline-secondary px-5">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
