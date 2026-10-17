<?php 
session_start();
include 'includes/mysqli_connect.php'; 


$limit = 8;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;


$condition = "WHERE p.status = 'Active'";
if(isset($_GET['category']) && is_numeric($_GET['category'])) {
    $condition .= " AND p.category_id = " . $_GET['category'];
}
if(isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $condition .= " AND p.product_name LIKE '%$search%'";
}


$cat_result = mysqli_query($conn, "SELECT * FROM categories");


$query = "SELECT p.*, b.brand_name FROM products p LEFT JOIN brands b ON p.brand_id = b.brand_id $condition ORDER BY p.created_at DESC LIMIT $start, $limit";
$product_result = mysqli_query($conn, $query);


$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM products p $condition");
$total_data = mysqli_fetch_assoc($total_query);
$total_pages = ceil($total_data['total'] / $limit);
?>

<?php include 'includes/header.php'; ?>

<div class="container my-5">
    <div class="row">
      
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">Categories</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><a href="shop.php" class="text-decoration-none text-dark">All Shoes</a></li>
                    <?php while($cat = mysqli_fetch_assoc($cat_result)): ?>
                    <li class="list-group-item">
                        <a href="shop.php?category=<?php echo $cat['category_id']; ?>" class="text-decoration-none text-dark">
                            <?php echo $cat['category_name']; ?>
                        </a>
                    </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>

      
        <div class="col-md-9">
            <div class="row mb-3">
                <div class="col-md-12">
                    <form method="GET" class="d-flex">
                        <input class="form-control me-2" type="search" name="search" placeholder="Search shoes...">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </form>
                </div>
            </div>

            <div class="row">
                <?php if(mysqli_num_rows($product_result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($product_result)): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <img src="assets/images/<?php echo $row['product_image']; ?>" class="card-img-top p-2" style="height: 200px; object-fit: contain;" alt="<?php echo $row['product_name']; ?>" onerror="this.src='https://placehold.co/300x300?text=No+Image'">
                            <div class="card-body">
                                <h6 class="card-title"><?php echo $row['product_name']; ?></h6>
                                <p class="text-muted small"><?php echo $row['brand_name']; ?></p>
                                <h5 class="text-primary">₱<?php echo number_format($row['price'], 2); ?></h5>
                                <a href="product_details.php?id=<?php echo $row['product_id']; ?>" class="btn btn-dark w-100 mt-2">View Details</a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <h3>No products found.</h3>
                    </div>
                <?php endif; ?>
            </div>

          
            <nav>
                <ul class="pagination justify-content-center">
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item"><a class="page-link" href="shop.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>