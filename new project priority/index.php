<?php
require_once 'includes/db.php';
include 'includes/header.php';

// Fetch stats for transparency section
$total_donations = $pdo->query("SELECT SUM(amount) FROM donations")->fetchColumn() ?: 0;
$total_projects = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn() ?: 0;
$verified_ngos = $pdo->query("SELECT COUNT(*) FROM ngos WHERE verification_status = 'verified'")->fetchColumn() ?: 0;
?>

<section class="hero-section">
    <div class="container py-5 text-center">
        <h1 class="display-3 fw-bold text-primary mb-4">Transparent Donations.<br>Real Impact. Verified NGOs.</h1>
        <p class="lead text-muted mb-5">Join the smarter way to give. Track every cent of your donation and see the real-world change you're making.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="projects.php" class="btn btn-primary btn-lg px-5">Donate Now</a>
            <a href="transparency.php" class="btn btn-outline-primary btn-lg px-5">Track Impact</a>
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container text-center">
        <div class="row">
            <div class="col-md-4 mb-4">
                <i class="fas fa-hand-holding-heart fa-3x text-primary mb-3"></i>
                <h3 class="fw-bold">$<?php echo number_format($total_donations, 2); ?></h3>
                <p class="text-muted">Total Donations Collected</p>
            </div>
            <div class="col-md-4 mb-4">
                <i class="fas fa-project-diagram fa-3x text-primary mb-3"></i>
                <h3 class="fw-bold"><?php echo $total_projects; ?></h3>
                <p class="text-muted">Active Impact Projects</p>
            </div>
            <div class="col-md-4 mb-4">
                <i class="fas fa-check-circle fa-3x text-primary mb-3"></i>
                <h3 class="fw-bold"><?php echo $verified_ngos; ?></h3>
                <p class="text-muted">Verified NGO Partners</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">Featured Projects</h2>
        <div class="row">
            <?php
            $stmt = $pdo->query("SELECT p.*, n.ngo_name FROM projects p JOIN ngos n ON p.ngo_id = n.id WHERE p.status = 'active' LIMIT 3");
            while ($project = $stmt->fetch()):
                $progress = ($project['collected_amount'] / $project['goal_amount']) * 100;
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
                            <a href="project-details.php?id=<?php echo $project['id']; ?>" class="btn btn-primary w-100">View Project</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <div class="text-center mt-4">
            <a href="projects.php" class="btn btn-outline-primary btn-lg">Browse All Projects</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
