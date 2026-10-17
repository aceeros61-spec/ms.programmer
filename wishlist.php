<?php
session_start();
include 'includes/mysqli_connect.php';

if(!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}


if(isset($_GET['add'])) {
    $pid = $_GET['add'];
    $cid = $_SESSION['customer_id'];
    
    $check = mysqli_query($conn, "SELECT * FROM wishlist WHERE customer_id = $cid AND product_id = $pid");
    if(mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO wishlist (customer_id, product_id) VALUES ($cid, $pid)");
    }
    header("Location: wishlist.php");
}


if(isset($_GET['remove'])) {
    $id = $_GET['remove'];
    mysqli_query($conn, "DELETE FROM wishlist WHERE wishlist_id = $id AND customer_id = ".$_SESSION['customer_id']);
    header("Location: wishlist.php");
}

$wishlist = mysqli_query($conn, "SELECT w.*, p.product_name, p.price, p.product_image 
    FROM wishlist w 
    JOIN products p ON w.product_id = p.product_id 
    WHERE w.customer_id = ".$_SESSION['customer_id']);
?>

<?php include 'includes/header.php'; ?>

<div class="container my-5">
    <h2>My Wishlist</h2>
    
    <?php if(mysqli_num_rows($wishlist) > 0): ?>
    <div class="row">
        <?php while($row = mysqli_fetch_assoc($wishlist)): ?>
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <img src="assets/images/<?php echo $row['product_image']; ?>" class="card-img-top p-2" style="height:200px; object-fit:contain;" onerror="this.src='https://placehold.co/300x200'">
                <div class="card-body">
                    <h6><?php echo $row['product_name']; ?></h6>
                    <h5 class="text-primary">₱<?php echo number_format($row['price'], 2); ?></h5>
                    <div class="d-flex justify-content-between mt-3">
                        <a href="product_details.php?id=<?php echo $row['product_id']; ?>" class="btn btn-sm btn-dark">View</a>
                        <a href="?remove=<?php echo $row['wishlist_id']; ?>" class="btn btn-sm btn-danger">Remove</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="alert alert-info">Your wishlist is empty. <a href="shop.php">Start shopping</a></div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>