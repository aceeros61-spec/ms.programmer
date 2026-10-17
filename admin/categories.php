<?php
session_start();
include '../includes/mysqli_connect.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}


if(isset($_POST['add_category'])) {
    $name = mysqli_real_escape_string($conn, $_POST['category_name']);
    mysqli_query($conn, "INSERT INTO categories (category_name) VALUES ('$name')");
    echo "<script>alert('Category Added!');</script>";
}


if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM categories WHERE category_id = $id");
    header("Location: categories.php");
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Categories - Kind Steps Admin</title>
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
        <a href="customers.php" class="nav-link">Customers</a>
        <a href="brands.php" class="nav-link">Brands</a>
        <a href="categories.php" class="nav-link active" style="background:#495057;">Categories</a>
        <a href="admin_logout.php" class="nav-link text-danger">Logout</a>
    </nav>
</div>

<div class="content">
    <h2>Category Management</h2>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addCatModal">Add Category</button>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr><th>ID</th><th>Category Name</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($categories)): ?>
                    <tr>
                        <td><?php echo $row['category_id']; ?></td>
                        <td><?php echo $row['category_name']; ?></td>
                        <td><a href="?delete=<?php echo $row['category_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">Delete</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="modal fade" id="addCatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header"><h5 class="modal-title">Add Category</h5></div>
                <div class="modal-body">
                    <input type="text" name="category_name" class="form-control" placeholder="Category Name" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_category" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>