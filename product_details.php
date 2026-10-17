<?php 
session_start();
include 'includes/mysqli_connect.php'; 

if(!isset($_GET['id'])) {
    header("Location: shop.php");
    exit;
}

$product_id = $_GET['id'];
$query = "SELECT p.*, b.brand_name, c.category_name FROM products p 
          LEFT JOIN brands b ON p.brand_id = b.brand_id 
          LEFT JOIN categories c ON p.category_id = c.category_id 
          WHERE p.product_id = $product_id AND p.status = 'Active'";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if(!$product) {
    echo "Product not found.";
    exit;
}


if(isset($_POST['add_to_cart'])) {
    if(!isset($_SESSION['customer_id'])) {
        echo "<script>alert('Please login to add to cart'); window.location='login.php';</script>";
    } else {
        $qty = $_POST['quantity'];
        $subtotal = $product['price'] * $qty;
        
        
        $check = mysqli_query($conn, "SELECT * FROM cart WHERE customer_id = ".$_SESSION['customer_id']." AND product_id = $product_id");
        
        if(mysqli_num_rows($check) > 0) {
            $update = mysqli_query($conn, "UPDATE cart SET quantity = quantity + $qty, subtotal = subtotal + $subtotal WHERE customer_id = ".$_SESSION['customer_id']." AND product_id = $product_id");
        } else {
            $insert = mysqli_query($conn, "INSERT INTO cart (customer_id, product_id, quantity, subtotal) VALUES (".$_SESSION['customer_id'].", $product_id, $qty, $subtotal)");
        }
        
        echo "<script>alert('Added to Cart!');</script>";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-6">
            <img src="assets/images/<?php echo $product['product_image']; ?>" class="img-fluid rounded" alt="Product Image" onerror="this.src='https://placehold.co/600x600?text=No+Image'">
        </div>
        <div class="col-md-6">
            <h2><?php echo $product['product_name']; ?></h2>
            <p class="text-muted">Brand: <strong><?php echo $product['brand_name']; ?></strong> | Category: <?php echo $product['category_name']; ?></p>
            <h3 class="text-primary">₱<?php echo number_format($product['price'], 2); ?></h3>
            <p class="mt-3"><?php echo $product['description']; ?></p>
            
            <div class="mt-4">
                <form method="POST">
                    <div class="mb-3">
                        <label>Size</label>
                        <select name="size" class="form-select">
                            <option>Select Size</option>
                            <option value="7">Size 7</option>
                            <option value="8">Size 8</option>
                            <option value="9">Size 9</option>
                            <option value="10">Size 10</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Quantity</label>
                        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" class="form-control" style="width: 100px;">
                    </div>
                    <button type="submit" name="add_to_cart" class="btn btn-success btn-lg w-100"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>