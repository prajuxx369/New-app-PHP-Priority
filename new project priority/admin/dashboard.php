<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireRole('admin');

// Admin Stats
$users_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$donor_count = $pdo->query("SELECT COUNT(*) FROM users WHERE role='donor'")->fetchColumn();
$ngo_count = $pdo->query("SELECT COUNT(*) FROM ngos")->fetchColumn();
$pending_ngo = $pdo->query("SELECT COUNT(*) FROM ngos WHERE verification_status='pending'")->fetchColumn();
$total_donated = $pdo->query("SELECT SUM(amount) FROM donations")->fetchColumn();
$total_used = $pdo->query("SELECT SUM(amount_used) FROM fund_updates")->fetchColumn();

include '../includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <h2 class="fw-bold mb-4">Platform Administration 🛡️</h2>
        
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card p-3 border-0 shadow-sm bg-primary text-white">
                    <h5 class="small opacity-75">Total Users</h5>
                    <h3 class="fw-bold"><?php echo $users_count; ?></h3>
                    <p class="m-0 small"><?php echo $donor_count; ?> Donors | <?php echo $ngo_count; ?> NGOs</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 border-0 shadow-sm bg-warning text-dark">
                    <h5 class="small opacity-75">Pending Verifications</h5>
                    <h3 class="fw-bold"><?php echo $pending_ngo; ?></h3>
                    <p class="m-0 small">NGOs awaiting review</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 border-0 shadow-sm bg-success text-white">
                    <h5 class="small opacity-75">Total Donations</h5>
                    <h3 class="fw-bold">$<?php echo number_format($total_donated ?: 0, 2); ?></h3>
                    <p class="m-0 small">Project funding</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 border-0 shadow-sm bg-white text-dark">
                    <h5 class="small opacity-75 text-muted">Funds Utilized</h5>
                    <h3 class="fw-bold">$<?php echo number_format($total_used ?: 0, 2); ?></h3>
                    <p class="m-0 small text-success"><?php echo (round(($total_used / ($total_donated ?: 1)) * 100)); ?>% Transparency Rate</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <h4 class="fw-bold mb-4">Pending NGO Verifications</h4>
                <div class="table-responsive bg-white p-4 rounded-3 shadow-sm">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr class="text-muted small">
                                <th>NGO Name</th>
                                <th>Reg No</th>
                                <th>Email</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmtPending = $pdo->query("SELECT * FROM ngos WHERE verification_status = 'pending' ORDER BY created_at DESC");
                            if ($stmtPending->rowCount() === 0):
                                echo "<tr><td colspan='4' class='text-center py-4'>No pending verifications at this time.</td></tr>";
                            endif;
                            while ($n = $stmtPending->fetch()):
                            ?>
                            <tr>
                                <td class="fw-bold"><?php echo htmlspecialchars($n['ngo_name']); ?></td>
                                <td><code><?php echo htmlspecialchars($n['registration_no']); ?></code></td>
                                <td><?php echo htmlspecialchars($n['email']); ?></td>
                                <td class="text-end">
                                    <form method="POST" action="ngo-verification-logic.php" class="d-inline">
                                        <input type="hidden" name="ngo_id" value="<?php echo $n['id']; ?>">
                                        <button name="action" value="verified" class="btn btn-sm btn-success px-3">Approve</button>
                                        <button name="action" value="rejected" class="btn btn-sm btn-outline-danger px-3">Reject</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-4">
                <h4 class="fw-bold mb-4">Quick Links</h4>
                <div class="list-group shadow-sm border-0">
                    <a href="users.php" class="list-group-item list-group-item-action py-3">
                        <i class="fas fa-users me-2 text-primary"></i>Manage All Users
                    </a>
                    <a href="projects.php" class="list-group-item list-group-item-action py-3">
                        <i class="fas fa-project-diagram me-2 text-primary"></i>Monitor Projects
                    </a>
                    <a href="fund-summary.php" class="list-group-item list-group-item-action py-3">
                        <i class="fas fa-chart-line me-2 text-primary"></i>Fund Usage Summary
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
