<?php
session_start();
include 'includes/mysqli_connect.php';

$error = '';

if(isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    $result = mysqli_query($conn, "SELECT * FROM customers WHERE username = '$username'");
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if(password_verify($password, $row['password'])) {
            $_SESSION['customer_id'] = $row['customer_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['fullname'] = $row['fullname'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Username not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kind Steps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .login-header i {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        .login-body {
            padding: 40px 30px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        .btn-login {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            transition: transform 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            color: white;
        }
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        .form-group {
            position: relative;
            margin-bottom: 20px;
        }
        .form-group input {
            padding-left: 45px;
        }
        .social-login {
            text-align: center;
            margin-top: 20px;
        }
        .social-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 8px;
            color: white;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <i class="fas fa-shoe-prints"></i>
        <h3>Welcome Back</h3>
        <p class="mb-0">Sign in to continue to Kind Steps</p>
    </div>
    <div class="login-body">
        <?php if(isset($error) && $error != ''): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <i class="fas fa-user input-icon"></i>
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            
            <div class="form-group">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            
            <div class="d-flex justify-content-between mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <a href="#" class="text-decoration-none">Forgot password?</a>
            </div>
            
            <button type="submit" name="login" class="btn btn-login">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        
        <div class="text-center mt-4">
            <p class="text-muted">Don't have an account? 
                <a href="register.php" class="text-decoration-none fw-bold">Register here</a>
            </p>
        </div>
        
        <div class="social-login">
            <p class="text-muted small">Or login with</p>
            <a href="#" class="social-btn bg-primary"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-btn bg-danger"><i class="fab fa-google"></i></a>
            <a href="#" class="social-btn bg-info"><i class="fab fa-twitter"></i></a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>