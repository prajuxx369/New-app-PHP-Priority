<?php
require_once 'includes/db.php';
include 'includes/header.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT p.*, n.ngo_name, n.verification_status FROM projects p JOIN ngos n ON p.ngo_id = n.id WHERE p.id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch();

if (!$project) {
    echo "<div class='container py-5 text-center'><h3>Project not found.</h3><a href='projects.php'>Back to projects</a></div>";
    include 'includes/footer.php';
    exit();
}

$progress = ($project['goal_amount'] > 0) ? ($project['collected_amount'] / $project['goal_amount']) * 100 : 0;
?>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <img src="<?php echo $project['cover_image'] ?: 'assets/images/default-project.jpg'; ?>" class="img-fluid rounded mb-4" alt="...">
                <h2 class="fw-bold mb-3"><?php echo htmlspecialchars($project['title']); ?></h2>
                <div class="d-flex align-items-center mb-4">
                    <span class="text-muted me-2">By <?php echo htmlspecialchars($project['ngo_name']); ?></span>
                    <?php if ($project['verification_status'] === 'verified'): ?>
                        <span class="badge bg-success small"><i class="fas fa-check-circle me-1"></i>Verified NGO</span>
                    <?php endif; ?>
                </div>
                
                <h5 class="fw-bold mt-5 mb-3">About this Project</h5>
                <p class="lead text-muted"><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>

                <h5 class="fw-bold mt-5 mb-3">Transparency Updates</h5>
                <div class="timeline">
                    <?php
                    $stmtUpdate = $pdo->prepare("SELECT * FROM fund_updates WHERE project_id = ? ORDER BY created_at DESC");
                    $stmtUpdate->execute([$id]);
                    if ($stmtUpdate->rowCount() === 0):
                        echo "<p class='text-muted italic'>No updates yet from the NGO.</p>";
                    endif;
                    while ($update = $stmtUpdate->fetch()):
                    ?>
                    <div class="card mb-3 border-start border-4 border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <h6 class="fw-bold m-0 text-success"><?php echo htmlspecialchars($update['title']); ?></h6>
                                <span class="small text-muted"><?php echo date('M d, Y', strtotime($update['created_at'])); ?></span>
                            </div>
                            <p class="mb-3"><?php echo nl2br(htmlspecialchars($update['description'])); ?></p>
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-light text-dark border"><i class="fas fa-coins me-1"></i>Amount Used: $<?php echo number_format($update['amount_used'], 2); ?></span>
                                <?php if ($update['update_image']): ?>
                                    <a href="<?php echo htmlspecialchars($update['update_image']); ?>" target="_blank" class="small link-primary"><i class="fas fa-image me-1"></i>View Proof</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card sticky-top" style="top: 100px;">
                    <div class="card-body">
                        <h4 class="fw-bold mb-4">Donation Status</h4>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="h4 m-0 fw-bold">$<?php echo number_format($project['collected_amount'], 2); ?></span>
                            <span class="text-muted">of $<?php echo number_format($project['goal_amount'], 2); ?></span>
                        </div>
                        <div class="progress mb-4" style="height: 12px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress; ?>%"></div>
                        </div>
                        
                        <div class="row text-center mb-4">
                            <div class="col-6 border-end">
                                <div class="h5 m-0 fw-bold"><?php 
                                    $donors = $pdo->prepare("SELECT COUNT(DISTINCT donor_id) FROM donations WHERE project_id = ?");
                                    $donors->execute([$id]);
                                    echo $donors->fetchColumn();
                                ?></div>
                                <div class="small text-muted">Donors</div>
                            </div>
                            <div class="col-6">
                                <div class="h5 m-0 fw-bold"><?php echo round($progress); ?>%</div>
                                <div class="small text-muted">Funded</div>
                            </div>
                        </div>

                        <a href="donate.php?project_id=<?php echo $project['id']; ?>" class="btn btn-primary btn-lg w-100 py-3 fw-bold">Donate Now</a>
                        <p class="small text-center text-muted mt-3"><i class="fas fa-shield-alt me-1"></i>Secure Transparency Platform</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
