<?php include 'includes/header.php'; 
$query = "SELECT p.*, b.brand_name, c.category_name FROM products p 
         LEFT JOIN brands b ON p.brand_id = b.brand_id 
         LEFT JOIN categories c ON p.category_id = c.category_id 
         WHERE p.status = 'Active' ORDER BY p.created_at DESC LIMIT 8";
$result = mysqli_query($conn, $query);
?>


<div class="p-5 mb-4 bg-light rounded-3">
    <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold">Welcome to Kind Steps</h1>
        <p class="col-md-8 fs-4">Premium footwear for running, casual, and sports. Comfort meets style.</p>
        <a href="shop.php" class="btn btn-primary btn-lg" type="button">Shop Now</a>
    </div>
</div>


<div class="container my-5">
    <h2 class="mb-4">New Arrivals</h2>
    <div class="row">
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
                
                <img src="https://source.unsplash.com/300x300/?sneakers" class="card-img-top" alt="Shoe">
                <div class="card-body">
                    <h6 class="card-title"><?php echo $row['product_name']; ?></h6>
                    <small class="text-muted"><?php echo $row['brand_name']; ?></small>
                    <h5 class="text-primary mt-2">₱<?php echo number_format($row['price'], 2); ?></h5>
                    <a href="product_details.php?id=<?php echo $row['product_id']; ?>" class="btn btn-outline-dark w-100 mt-2">View Details</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>