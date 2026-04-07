<?php
require_once 'includes/db.php';
include 'includes/header.php';

$search = $_GET['search'] ?? '';
$query = "SELECT p.*, n.ngo_name FROM projects p JOIN ngos n ON p.ngo_id = n.id WHERE p.status = 'active'";

if ($search) {
    $query .= " AND (p.title LIKE :search OR n.ngo_name LIKE :search)";
}

$stmt = $pdo->prepare($query);
if ($search) {
    $stmt->execute(['search' => "%$search%"]);
} else {
    $stmt->execute();
}
?>

<section class="py-5">
    <div class="container">
        <div class="row mb-5 align-items-center">
            <div class="col-md-6">
                <h2 class="fw-bold m-0">Impact Projects</h2>
            </div>
            <div class="col-md-6">
                <form class="d-flex border rounded p-1 bg-white">
                    <input type="text" name="search" class="form-control border-0 shadow-none" placeholder="Search projects or NGOs..." value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-primary px-4">Search</button>
                </form>
            </div>
        </div>

        <div class="row">
            <?php if ($stmt->rowCount() === 0): ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No projects found. Try a different search.</p>
                </div>
            <?php endif; ?>

            <?php while ($project = $stmt->fetch()): 
                $progress = ($project['goal_amount'] > 0) ? ($project['collected_amount'] / $project['goal_amount']) * 100 : 0;
            ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="<?php echo $project['cover_image'] ?: 'assets/images/default-project.jpg'; ?>" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title fw-bold"><?php echo htmlspecialchars($project['title']); ?></h5>
                        <p class="text-muted small">by <?php echo htmlspecialchars($project['ngo_name']); ?></p>
                        <p class="card-text text-truncate-3"><?php echo htmlspecialchars($project['description']); ?></p>
                        <div class="mt-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">$<?php echo number_format($project['collected_amount'], 2); ?> raised</span>
                                <span class="small"><?php echo round($progress); ?>%</span>
                            </div>
                            <div class="progress mb-3" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress; ?>%"></div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="project-details.php?id=<?php echo $project['id']; ?>" class="btn btn-outline-primary flex-grow-1">Details</a>
                                <a href="donate.php?project_id=<?php echo $project['id']; ?>" class="btn btn-primary flex-grow-1">Donate</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
