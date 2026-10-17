<?php
session_start();
include '../includes/mysqli_connect.php';

$error = '';

if(isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM admins WHERE username = '$username'");
    
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if(password_verify($password, $row['password'])) {
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['admin_name'] = $row['fullname'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid Password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Kind Steps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: #1a252f;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            width: 400px;
            padding: 40px;
        }
        .login-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
        }
        .login-icon i {
            font-size: 2.5rem;
            color: white;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 2px solid #e0e0e0;
        }
        .form-control:focus {
            border-color: #3498db;
            box-shadow: none;
        }
        .btn-login {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="login-box text-center">
    <div class="login-icon">
        <i class="fas fa-user-shield"></i>
    </div>
    <h4 class="mb-4">Admin Login</h4>
    
    <?php if($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="mb-3">
            <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>
        <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <button type="submit" name="login" class="btn btn-login">
            <i class="fas fa-sign-in-alt"></i> Login
        </button>
    </form>
    
    <div class="mt-4">
        <a href="../index.php" class="text-decoration-none text-muted">
            <i class="fas fa-arrow-left"></i> Back to Website
        </a>
    </div>
</div>

</body>
</html>