<?php 
require_once 'includes/db.php';
include 'includes/header.php'; 

$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $stmt = $pdo->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $subject, $message]);
    $success = "Message sent! We will get back to you soon.";
}
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 mb-5 mb-md-0">
                <h2 class="fw-bold mb-4">Get In Touch</h2>
                <p class="text-muted mb-5">Have questions about NGO verification or how fund tracking works? Our team is here to help.</p>
                
                <div class="d-flex mb-4">
                    <div class="bg-primary text-white rounded p-3 me-3">
                        <i class="fas fa-envelope fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold m-0">Email Support</h6>
                        <p class="text-muted">support@smartngo.org</p>
                    </div>
                </div>
                <div class="d-flex mb-4">
                    <div class="bg-primary text-white rounded p-3 me-3">
                        <i class="fas fa-location-dot fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold m-0">Our Office</h6>
                        <p class="text-muted">Transparency Way, Global Tech Park</p>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card p-4 border-0 shadow-sm">
                    <h4 class="fw-bold mb-4">Send a Message</h4>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small">Your Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Subject</label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Message</label>
                            <textarea name="message" class="form-control" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-3 mt-2">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
