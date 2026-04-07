<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireRole('ngo');

$ngo_id = $_SESSION['ngo_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $goal_amount = $_POST['goal_amount'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Mock file upload
    $cover_image = ''; 
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === 0) {
        // Logic to move_uploaded_file would go here in real system
        // For MVP, we'll store a placeholder or dummy path
        $cover_image = 'assets/images/default-project.jpg';
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO projects (ngo_id, title, description, goal_amount, start_date, end_date, cover_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$ngo_id, $title, $description, $goal_amount, $start_date, $end_date, $cover_image]);
        $success = "Project created successfully!";
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
                <div class="card p-4">
                    <h3 class="fw-bold mb-4"><i class="fas fa-plus-circle me-2 text-primary"></i>Create New Impact Project</h3>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?> <a href="dashboard.php" class="alert-link">Back to Dashboard</a></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Project Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Clean Water for Rural Schools" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Description</label>
                            <textarea name="description" class="form-control" rows="5" placeholder="Explain the project goals, impact, and how funds will be used..." required></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Goal Amount ($)</label>
                                <input type="number" name="goal_amount" class="form-control" step="0.01" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Cover Image</label>
                                <input type="file" name="cover_image" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">End Date (Estimated)</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-5">Publish Project</button>
                            <a href="dashboard.php" class="btn btn-outline-secondary px-5">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
