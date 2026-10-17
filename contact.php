<?php
session_start();
include 'includes/mysqli_connect.php';

$msg = '';

if(isset($_POST['send_message'])) {
    $name = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    mysqli_query($conn, "INSERT INTO contact_messages (fullname, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')");
    $msg = "<div class='alert alert-success'>Message Sent Successfully!</div>";
}
?>

<?php include 'includes/header.php'; ?>

<div class="container my-5">
    <h2 class="mb-4 text-center">Contact Us</h2>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card p-4">
                <?php echo $msg; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label>Full Name</label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Subject</label>
                        <input type="text" name="subject" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Message</label>
                        <textarea name="message" class="form-control" rows="5" required></textarea>
                    </div>
                    <button type="submit" name="send_message" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-4 bg-light">
                <h4>Get In Touch</h4>
                <p><i class="fas fa-map-marker-alt"></i> Manila, Philippines</p>
                <p><i class="fas fa-phone"></i> +63 912 345 6789</p>
                <p><i class="fas fa-envelope"></i> support@kindsteps.com</p>
                <hr>
                <h5>Store Hours:</h5>
                <p>Mon -Fri: 9:00 AM - 6:00 PM</p>
                <p>Sat - Sun: 10:00 AM - 4:00 PM</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>