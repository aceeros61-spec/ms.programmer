<?php
session_start();
include '../includes/mysqli_connect.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}


$daily = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(total_amount),0) as total FROM orders WHERE DATE(order_date) = CURDATE()"))['total'];
$monthly = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(total_amount),0) as total FROM orders WHERE MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())"))['total'];
$yearly = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(total_amount),0) as total FROM orders WHERE YEAR(order_date) = YEAR(CURDATE())"))['total'];


$top_products = mysqli_query($conn, "SELECT p.product_name, SUM(oi.quantity) as qty_sold, SUM(oi.subtotal) as revenue 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.product_id 
    GROUP BY oi.product_id 
    ORDER BY qty_sold DESC LIMIT 5");


$top_customers = mysqli_query($conn, "SELECT c.fullname, SUM(o.total_amount) as spent, COUNT(o.order_id) as orders 
    FROM orders o 
    JOIN customers c ON o.customer_id = c.customer_id 
    GROUP BY o.customer_id 
    ORDER BY spent DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reports - Kind Steps Admin</title>
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
        <a href="dashboard.php" class="nav-link">Dashboard</a>
        <a href="products.php" class="nav-link">Products</a>
        <a href="orders.php" class="nav-link">Orders</a>
        <a href="customers.php" class="nav-link">Customers</a>
        <a href="brands.php" class="nav-link">Brands</a>
        <a href="categories.php" class="nav-link">Categories</a>
        <a href="reports.php" class="nav-link active" style="background:#495057;">Reports</a>
        <a href="admin_logout.php" class="nav-link text-danger">Logout</a>
    </nav>
</div>

<div class="content">
    <h2>Sales Reports</h2>
    
   
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white p-3">
                <h5>Today's Sales</h5>
                <h3>₱<?php echo number_format($daily, 2); ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white p-3">
                <h5>This Month</h5>
                <h3>₱<?php echo number_format($monthly, 2); ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white p-3">
                <h5>This Year</h5>
                <h3>₱<?php echo number_format($yearly, 2); ?></h3>
            </div>
        </div>
    </div>

    <div class="row">
       
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Top Selling Products</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead><tr><th>Product</th><th>Sold</th><th>Revenue</th></tr></thead>
                        <tbody>
                            <?php while($p = mysqli_fetch_assoc($top_products)): ?>
                            <tr>
                                <td><?php echo $p['product_name']; ?></td>
                                <td><?php echo $p['qty_sold']; ?></td>
                                <td>₱<?php echo number_format($p['revenue'], 2); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

       
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Top Customers</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead><tr><th>Customer</th><th>Orders</th><th>Total Spent</th></tr></thead>
                        <tbody>
                            <?php while($c = mysqli_fetch_assoc($top_customers)): ?>
                            <tr>
                                <td><?php echo $c['fullname']; ?></td>
                                <td><?php echo $c['orders']; ?></td>
                                <td>₱<?php echo number_format($c['spent'], 2); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    
    <div class="mt-3">
        <button onclick="window.print()" class="btn btn-dark"><i class="fas fa-print"></i> Print Report</button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>