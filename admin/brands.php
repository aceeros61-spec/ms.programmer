<?php
session_start();
include '../includes/mysqli_connect.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Add Brand
if(isset($_POST['add_brand'])) {
    $name = mysqli_real_escape_string($conn, $_POST['brand_name']);
    
    $image = $_FILES['brand_logo']['name'];
    $target = "../assets/images/" . basename($image);
    move_uploaded_file($_FILES['brand_logo']['tmp_name'], $target);
    
    mysqli_query($conn, "INSERT INTO brands (brand_name, brand_logo) VALUES ('$name', '$image')");
    echo "<script>alert('Brand Added!'); window.location='brands.php';</script>";
}

// Delete Brand
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM brands WHERE brand_id = $id");
    header("Location: brands.php");
}

$brands = mysqli_query($conn, "SELECT * FROM brands ORDER BY brand_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Brands - Kind Steps Admin</title>
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
        <a href="brands.php" class="nav-link active" style="background:#495057;">Brands</a>
        <a href="categories.php" class="nav-link">Categories</a>
        <a href="admin_logout.php" class="nav-link text-danger">Logout</a>
    </nav>
</div>

<div class="content">
    <h2>Brand Management</h2>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addBrandModal">Add Brand</button>
    
    <div class="row">
        <?php while($row = mysqli_fetch_assoc($brands)): ?>
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <img src="../assets/images/<?php echo $row['brand_logo']; ?>" class="img-fluid mb-3" style="height:80px;" onerror="this.src='https://placehold.co/150x80?text=No+Logo'">
                    <h5><?php echo $row['brand_name']; ?></h5>
                    <a href="?delete=<?php echo $row['brand_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">Delete</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Add Brand Modal -->
<div class="modal fade" id="addBrandModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Add Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Brand Name</label>
                        <input type="text" name="brand_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Brand Logo</label>
                        <input type="file" name="brand_logo" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_brand" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>