<?php
session_start();
include '../includes/mysqli_connect.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}


if(isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status_id = $_POST['status_id'];
    $tracking = $_POST['tracking_number'];
    
    mysqli_query($conn, "UPDATE orders SET status_id = $status_id, tracking_number = '$tracking' WHERE order_id = $order_id");
    echo "<script>alert('Order Updated!');</script>";
}


$query = "SELECT o.*, c.fullname, c.email, c.phone, c.address, s.status_name, pm.payment_name 
          FROM orders o 
          LEFT JOIN customers c ON o.customer_id = c.customer_id 
          LEFT JOIN order_status s ON o.status_id = s.status_id 
          LEFT JOIN payment_modes pm ON o.payment_id = pm.payment_id 
          ORDER BY o.order_date DESC";
$orders = mysqli_query($conn, $query);


$statuses = mysqli_query($conn, "SELECT * FROM order_status");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Orders - Kind Steps Admin</title>
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
        <a href="orders.php" class="nav-link active" style="background:#495057;">Orders</a>
        <a href="customers.php" class="nav-link">Customers</a>
        <a href="brands.php" class="nav-link">Brands</a>
        <a href="categories.php" class="nav-link">Categories</a>
        <a href="admin_logout.php" class="nav-link text-danger mt-4">Logout</a>
    </nav>
</div>

<div class="content">
    <h2>Order Management</h2>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Tracking #</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($orders)): ?>
                    <tr>
                        <td>#<?php echo $row['order_id']; ?></td>
                        <td>
                            <?php echo $row['fullname']; ?><br>
                            <small class="text-muted"><?php echo $row['email']; ?></small>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($row['order_date'])); ?></td>
                        <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                        <td><?php echo $row['payment_name']; ?></td>
                        <td>
                            <span class="badge bg-<?php echo ($row['status_id'] == 5) ? 'success' : (($row['status_id'] == 6) ? 'danger' : 'warning'); ?>">
                                <?php echo $row['status_name']; ?>
                            </span>
                        </td>
                        <td><?php echo $row['tracking_number']; ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#statusModal<?php echo $row['order_id']; ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>

                    
                    <div class="modal fade" id="statusModal<?php echo $row['order_id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST">
                                    <div class="modal-header"><h5 class="modal-title">Update Order #<?php echo $row['order_id']; ?></h5></div>
                                    <div class="modal-body">
                                        <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                        <div class="mb-3">
                                            <label>Status</label>
                                            <select name="status_id" class="form-select">
                                                <?php mysqli_data_seek($statuses, 0); ?>
                                                <?php while($stat = mysqli_fetch_assoc($statuses)): ?>
                                                <option value="<?php echo $stat['status_id']; ?>" <?php echo ($stat['status_id'] == $row['status_id']) ? 'selected' : ''; ?>>
                                                    <?php echo $stat['status_name']; ?>
                                                </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Tracking Number</label>
                                            <input type="text" name="tracking_number" value="<?php echo $row['tracking_number']; ?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" name="update_status" class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>