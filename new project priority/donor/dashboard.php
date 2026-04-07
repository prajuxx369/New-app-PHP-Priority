<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireRole('donor');

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Summary stats
$stmtDonations = $pdo->prepare("SELECT SUM(amount) as total, COUNT(*) as count FROM donations WHERE donor_id = ?");
$stmtDonations->execute([$user_id]);
$don_stats = $stmtDonations->fetch();

include '../includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col">
                <h2 class="fw-bold m-0">Welcome, <?php echo htmlspecialchars($name); ?> 👋</h2>
                <p class="text-muted">Here's your impact summary.</p>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-3">
                <div class="card p-3 shadow-sm border-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded p-3 me-3">
                            <i class="fas fa-hand-holding-usd fa-lg"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold m-0">$<?php echo number_format($don_stats['total'] ?: 0, 2); ?></h4>
                            <p class="text-muted small m-0">Total Donated</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 shadow-sm border-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-success text-white rounded p-3 me-3">
                            <i class="fas fa-heart fa-lg"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold m-0"><?php echo $don_stats['count']; ?></h4>
                            <p class="text-muted small m-0">Donations Made</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 shadow-sm border-0 text-center">
                    <a href="../projects.php" class="btn btn-outline-primary w-100 py-3">Find New Projects</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h4 class="fw-bold mb-4">Your Recent Donations</h4>
                <div class="table-responsive bg-white rounded-3 shadow-sm p-4">
                    <table class="table table-hover align-middle m-0">
                        <thead>
                            <tr class="text-muted small">
                                <th>Project Name</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-end">Impact Tracking</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmtHist = $pdo->prepare("SELECT d.*, p.title as project_title, p.id as p_id, p.status as p_status FROM donations d JOIN projects p ON d.project_id = p.id WHERE d.donor_id = ? ORDER BY d.donated_at DESC");
                            $stmtHist->execute([$user_id]);
                            if ($stmtHist->rowCount() === 0):
                                echo "<tr><td colspan='5' class='text-center py-4'>You haven't made any donations yet.</td></tr>";
                            endif;
                            while ($hist = $stmtHist->fetch()):
                            ?>
                            <tr>
                                <td class="fw-bold"><?php echo htmlspecialchars($hist['project_title']); ?></td>
                                <td class="fw-bold">$<?php echo number_format($hist['amount'], 2); ?></td>
                                <td class="small"><?php echo date('M d, Y', strtotime($hist['donated_at'])); ?></td>
                                <td><span class="badge bg-success small"><?php echo ucfirst($hist['payment_status']); ?></span></td>
                                <td class="text-end">
                                    <a href="../project-details.php?id=<?php echo $hist['p_id']; ?>" class="btn btn-sm btn-outline-primary">Track Progress</a>
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
