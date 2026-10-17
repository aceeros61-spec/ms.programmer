<?php
session_start();
include '../includes/mysqli_connect.php';


if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}


$products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as p FROM products"))['p'];
$customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM customers"))['c'];
$orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as o FROM orders"))['o'];
$revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(total_amount),0) as r FROM orders"))['r'];


$low_stock = mysqli_query($conn, "SELECT * FROM products WHERE stock < 5");


$recent = mysqli_query($conn, "SELECT o.*, c.username FROM orders o LEFT JOIN customers c ON o.customer_id = c.customer_id ORDER BY o.order_date DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kind Steps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .sidebar { background: #343a40; height: 100vh; position: fixed; width: 250px; padding-top: 20px; }
        .content { margin-left: 250px; padding: 20px; }
        .nav-link { color: #bbb; text-decoration: none; padding: 10px 20px; display: block; }
        .nav-link:hover { background: #495057; color: #fff; }
        .card-icon { font-size: 2rem; }
    </style>
</head>
<body>

<div class="sidebar">
    <h4 class="text-center text-white mb-4"><i class="fas fa-shoe-prints"></i> Kind Steps</h4>
    <nav>
        <a href="dashboard.php" class="nav-link active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="products.php" class="nav-link"><i class="fas fa-box"></i> Products</a>
        <a href="orders.php" class="nav-link"><i class="fas fa-shopping-bag"></i> Orders</a>
        <a href="customers.php" class="nav-link"><i class="fas fa-users"></i> Customers</a>
        <a href="brands.php" class="nav-link"><i class="fas fa-tags"></i> Brands</a>
        <a href="categories.php" class="nav-link"><i class="fas fa-list"></i> Categories</a>
        <a href="reports.php" class="nav-link"><i class="fas fa-chart-bar"></i> Reports</a>
        <a href="admin_logout.php" class="nav-link text-danger mt-4"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>

<div class="content">
    <h2>Admin Dashboard</h2>
    <hr>
    
  
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $products; ?></h4>
                            <p>Total Products</p>
                        </div>
                        <i class="fas fa-box card-icon"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $customers; ?></h4>
                            <p>Customers</p>
                        </div>
                        <i class="fas fa-users card-icon"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $orders; ?></h4>
                            <p>Total Orders</p>
                        </div>
                        <i class="fas fa-shopping-cart card-icon"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>₱<?php echo number_format($revenue, 2); ?></h4>
                            <p>Total Revenue</p>
                        </div>
                        <i class="fas fa-money-bill-wave card-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
       
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Recent Orders</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($recent)): ?>
                            <tr>
                                <td>#<?php echo $row['order_id']; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['order_date'])); ?></td>
                                <td><span class="badge bg-info"><?php echo $row['status_id']; ?></span></td>
                                <td><a href="order_view.php?id=<?php echo $row['order_id']; ?>" class="btn btn-sm btn-primary">View</a></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

      
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-danger text-white">Low Stock Alert</div>
                <div class="card-body">
                    <?php if(mysqli_num_rows($low_stock) > 0): ?>
                        <ul class="list-group">
                            <?php while($ls = mysqli_fetch_assoc($low_stock)): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo $ls['product_name']; ?>
                                <span class="badge bg-danger rounded-pill"><?php echo $ls['stock']; ?></span>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-center text-muted mt-3">No low stock products</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>