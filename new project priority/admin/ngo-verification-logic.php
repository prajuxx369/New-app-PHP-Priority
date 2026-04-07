<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ngo_id = $_POST['ngo_id'];
    $status = $_POST['action'];
    $admin_id = $_SESSION['user_id'];

    try {
        $pdo->beginTransaction();

        // Update NGO status
        $stmtUpdate = $pdo->prepare("UPDATE ngos SET verification_status = ? WHERE id = ?");
        $stmtUpdate->execute([$status, $ngo_id]);

        // Log verification
        $stmtLog = $pdo->prepare("INSERT INTO admin_verifications (ngo_id, verified_by, status) VALUES (?, ?, ?)");
        $stmtLog->execute([$ngo_id, $admin_id, $status]);

        $pdo->commit();
        header("Location: dashboard.php?msg=success");
    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: dashboard.php?msg=error");
    }
}
?>
