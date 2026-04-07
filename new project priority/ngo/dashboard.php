<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireRole('ngo');

$ngo_id = $_SESSION['ngo_id'];
$name = $_SESSION['name'];

// NGO Stats
$stmtNGO = $pdo->prepare("SELECT ngo_name, verification_status FROM ngos WHERE id = ?");
$stmtNGO->execute([$ngo_id]);
$ngo = $stmtNGO->fetch();

$stmtCount = $pdo->prepare("SELECT COUNT(*) as proj_count, SUM(collected_amount) as total_raised FROM projects WHERE ngo_id = ?");
$stmtCount->execute([$ngo_id]);
$stats = $stmtCount->fetch();

include '../includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-md-8">
                <h2 class="fw-bold m-0"><?php echo htmlspecialchars($ngo['ngo_name']); ?> NGO Panel 🏢</h2>
                <div class="d-flex align-items-center mt-2">
                    <?php if ($ngo['verification_status'] === 'verified'): ?>
                        <span class="badge bg-success me-2"><i class="fas fa-check-circle me-1"></i>Verified</span>
                    <?php elseif ($ngo['verification_status'] === 'rejected'): ?>
                        <span class="badge bg-danger me-2"><i class="fas fa-times-circle me-1"></i>Rejected</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark me-2"><i class="fas fa-clock me-1"></i>Verification Pending</span>
                    <?php endif; ?>
                    <p class="text-muted small m-0">Organization Dashboard</p>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="create-project.php" class="btn btn-primary px-4 py-2"><i class="fas fa-plus me-2"></i>Create New Project</a>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-3">
                <div class="card p-3 shadow-sm border-0">
                    <div>
                        <h4 class="fw-bold m-0"><?php echo $stats['proj_count']; ?></h4>
                        <p class="text-muted small m-0">Total Projects</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 shadow-sm border-0">
                    <div>
                        <h4 class="fw-bold m-0">$<?php echo number_format($stats['total_raised'] ?: 0, 2); ?></h4>
                        <p class="text-muted small m-0">Total Funds Collected</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h4 class="fw-bold mb-4">Manage Active Projects</h4>
                <div class="table-responsive bg-white rounded-3 shadow-sm p-4">
                    <table class="table table-hover align-middle m-0">
                        <thead>
                            <tr class="text-muted small">
                                <th>Project Title</th>
                                <th>Target Amount</th>
                                <th>Raised</th>
                                <th>Updates</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmtProjs = $pdo->prepare("SELECT *, (SELECT COUNT(*) FROM fund_updates WHERE project_id = projects.id) as update_count FROM projects WHERE ngo_id = ? ORDER BY created_at DESC");
                            $stmtProjs->execute([$ngo_id]);
                            if ($stmtProjs->rowCount() === 0):
                                echo "<tr><td colspan='5' class='text-center py-4'>You haven't created any projects yet.</td></tr>";
                            endif;
                            while ($p = $stmtProjs->fetch()):
                            ?>
                            <tr>
                                <td class="fw-bold"><?php echo htmlspecialchars($p['title']); ?></td>
                                <td>$<?php echo number_format($p['goal_amount'], 2); ?></td>
                                <td class="text-success fw-bold">$<?php echo number_format($p['collected_amount'], 2); ?></td>
                                <td><span class="badge bg-info text-dark"><?php echo $p['update_count']; ?> updates</span></td>
                                <td class="text-end">
                                    <a href="upload-update.php?project_id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-success me-1">Add Update</a>
                                    <a href="../project-details.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary" target="_blank">View Live</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
