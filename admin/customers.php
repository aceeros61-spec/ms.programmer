<?php
session_start();
include '../includes/mysqli_connect.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM customers WHERE customer_id = $id");
    header("Location: customers.php");
    exit;
}

$customers = mysqli_query($conn, "SELECT * FROM customers ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Customers - Kind Steps Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar { background: #343a40; height: 100vh; position: fixed; width: 250px; padding-top: 20px; }
        .content { margin-left: 250px; padding: 20px; }
        .nav-link { color: #bbb; text-decoration: none; padding: 10px 20px; display: block; }
        .nav-link:hover { background: #495057; color: #fff; }
    </style>
</head>
<body>

<div class="sidebar">
    <h4 class="text-center text-white mb-4">Kind Steps</h4>
    <nav>
        <a href="dashboard.php" class="nav-link">Dashboard</a>
        <a href="products.php" class="nav-link">Products</a>
        <a href="orders.php" class="nav-link">Orders</a>
        <a href="customers.php" class="nav-link active" style="background:#495057;">Customers</a>
        <a href="brands.php" class="nav-link">Brands</a>
        <a href="categories.php" class="nav-link">Categories</a>
        <a href="admin_logout.php" class="nav-link text-danger">Logout</a>
    </nav>
</div>

<div class="content">
    <h2>Customer Management</h2>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($customers)): ?>
                    <tr>
                        <td><?php echo $row['customer_id']; ?></td>
                        <td><?php echo $row['fullname']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['phone']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td>
                            <a href="?delete=<?php echo $row['customer_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete customer?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>