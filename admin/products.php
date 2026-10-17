<?php
session_start();
include '../includes/mysqli_connect.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

if(isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $brand = $_POST['brand_id'];
    $cat = $_POST['category_id'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $color = $_POST['color'];
    $gender = $_POST['gender_type'];
    $size = $_POST['shoe_size'];
    
    $image = $_FILES['product_image']['name'];
    $target = "../assets/images/" . basename($image);
    move_uploaded_file($_FILES['product_image']['tmp_name'], $target);
    
    $insert = mysqli_query($conn, "INSERT INTO products (product_name, brand_id, category_id, price, stock, description, color, gender_type, shoe_size, product_image) 
    VALUES ('$name', '$brand', '$cat', '$price', '$stock', '$desc', '$color', '$gender', '$size', '$image')");
    
    if($insert) {
        echo "<script>alert('Product Added Successfully!'); window.location='products.php';</script>";
    }
}


if(isset($_GET['delete'])) {
    $del_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE product_id = $del_id");
    header("Location: products.php");
    exit;
}


$products = mysqli_query($conn, "SELECT p.*, b.brand_name, c.category_name FROM products p LEFT JOIN brands b ON p.brand_id = b.brand_id LEFT JOIN categories c ON p.category_id = c.category_id ORDER BY p.product_id DESC");
$brands = mysqli_query($conn, "SELECT * FROM brands");
$categories = mysqli_query($conn, "SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Products - Kind Steps Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        <a href="dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="products.php" class="nav-link active" style="background:#495057;"><i class="fas fa-box"></i> Products</a>
        <a href="orders.php" class="nav-link"><i class="fas fa-shopping-bag"></i> Orders</a>
        <a href="customers.php" class="nav-link"><i class="fas fa-users"></i> Customers</a>
        <a href="brands.php" class="nav-link"><i class="fas fa-tags"></i> Brands</a>
        <a href="categories.php" class="nav-link"><i class="fas fa-list"></i> Categories</a>
        <a href="settings.php" class="nav-link"><i class="fas fa-cog"></i> Settings</a>
        <a href="admin_logout.php" class="nav-link text-danger mt-4"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>

<div class="content">
    <div class="d-flex justify-content-between mb-3">
        <h2>Product Management</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal"><i class="fas fa-plus"></i> Add New Product</button>
    </div>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Brand</th>
                        <th>Category</th>
                        <th>Size/Color</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($products) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($products)): ?>
                        <tr>
                            <td><?php echo $row['product_id']; ?></td>
                            <td><img src="../assets/images/<?php echo $row['product_image']; ?>" width="60" onerror="this.src='https://placehold.co/60'"></td>
                            <td><?php echo $row['product_name']; ?></td>
                            <td><?php echo $row['brand_name']; ?></td>
                            <td><?php echo $row['category_name']; ?></td>
                            <td><?php echo $row['shoe_size']; ?> / <?php echo $row['color']; ?></td>
                            <td>₱<?php echo number_format($row['price'], 2); ?></td>
                            <td>
                                <?php if($row['stock'] < 5): ?>
                                    <span class="badge bg-danger"><?php echo $row['stock']; ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success"><?php echo $row['stock']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?delete=<?php echo $row['product_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this product?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center">No products found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Product Name</label>
                            <input type="text" name="product_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Brand</label>
                            <select name="brand_id" class="form-select">
                                <?php while($b = mysqli_fetch_assoc($brands)): ?>
                                <option value="<?php echo $b['brand_id']; ?>"><?php echo $b['brand_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Category</label>
                            <select name="category_id" class="form-select">
                                <?php mysqli_data_seek($categories, 0); ?>
                                <?php while($c = mysqli_fetch_assoc($categories)): ?>
                                <option value="<?php echo $c['category_id']; ?>"><?php echo $c['category_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Gender Type</label>
                            <select name="gender_type" class="form-select">
                                <option value="Men">Men</option>
                                <option value="Women">Women</option>
                                <option value="Unisex">Unisex</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Price (₱)</label>
                            <input type="number" name="price" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Stock Quantity</label>
                            <input type="number" name="stock" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Shoe Size</label>
                            <input type="text" name="shoe_size" class="form-control" placeholder="e.g. 8, 9, 10">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Color</label>
                            <input type="text" name="color" class="form-control" placeholder="e.g. Black, White">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Product Image</label>
                            <input type="file" name="product_image" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_product" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>