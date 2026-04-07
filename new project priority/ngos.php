<?php
require_once 'includes/db.php';
include 'includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <h2 class="fw-bold mb-5">Verified Non-Profit Partners</h2>
        <div class="row g-4">
            <?php
            $stmt = $pdo->query("SELECT * FROM ngos WHERE verification_status = 'verified' ORDER BY ngo_name ASC");
            if ($stmt->rowCount() === 0):
                echo "<div class='col-12 text-center py-5'><p class='text-muted'>No verified NGOs listed yet. We are auditing applications!</p></div>";
            endif;
            while ($ngo = $stmt->fetch()):
            ?>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-building fa-2x text-primary me-3"></i>
                            <div>
                                <h5 class="fw-bold m-0"><?php echo htmlspecialchars($ngo['ngo_name']); ?></h5>
                                <span class="badge bg-success small"><i class="fas fa-check-circle me-1"></i>Verified</span>
                            </div>
                        </div>
                        <p class="text-muted small text-truncate-3 mb-4"><?php echo htmlspecialchars($ngo['description'] ?: 'A trusted NGO partner working towards social impact and transparency.'); ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted"><i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($ngo['address'] ?: 'Global'); ?></span>
                            <a href="projects.php?search=<?php echo urlencode($ngo['ngo_name']); ?>" class="btn btn-sm btn-outline-primary">View Projects</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
