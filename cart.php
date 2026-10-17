<?php
session_start();
include 'includes/mysqli_connect.php';

if(!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}


if(isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    mysqli_query($conn, "DELETE FROM cart WHERE cart_id = $remove_id AND customer_id = ".$_SESSION['customer_id']);
    header("Location: cart.php");
    exit;
}


if(isset($_GET['update'])) {
    $cart_id = (int)$_GET['update'];
    $qty = (int)$_GET['qty'];
    
    if($qty > 0) {
        
        $get_price = mysqli_query($conn, "SELECT price FROM cart c JOIN products p ON c.product_id = p.product_id WHERE cart_id = $cart_id AND customer_id = ".$_SESSION['customer_id']);
        if(mysqli_num_rows($get_price) > 0) {
            $price_row = mysqli_fetch_assoc($get_price);
            $new_subtotal = $price_row['price'] * $qty;
            mysqli_query($conn, "UPDATE cart SET quantity = $qty, subtotal = $new_subtotal WHERE cart_id = $cart_id AND customer_id = ".$_SESSION['customer_id']);
        }
    }
    header("Location: cart.php");
    exit;
}


$cart_query = mysqli_query($conn, "SELECT c.*, p.product_name, p.price, p.product_image, p.stock 
    FROM cart c 
    JOIN products p ON c.product_id = p.product_id 
    WHERE c.customer_id = " . $_SESSION['customer_id']);

$total = 0;
$items_count = mysqli_num_rows($cart_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Kind Steps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f4f6f9; }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="container my-5">
    <h2 class="mb-4"><i class="fas fa-shopping-cart"></i> My Shopping Cart</h2>
    
    <?php if($items_count > 0): ?>
    <div class="row">
        <div class="col-md-8">
            <?php while($cart = mysqli_fetch_assoc($cart_query)): 
                $total += $cart['subtotal'];
            ?>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="assets/images/<?php echo $cart['product_image']; ?>" width="80" 
                                 onerror="this.src='https://placehold.co/80'" class="img-fluid">
                        </div>
                        <div class="col-md-4">
                            <h5><?php echo $cart['product_name']; ?></h5>
                            <p class="text-muted mb-0">₱<?php echo number_format($cart['price'], 2); ?> each</p>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <a href="?update=<?php echo $cart['cart_id']; ?>&qty=<?php echo $cart['quantity'] - 1; ?>" 
                                   class="btn btn-outline-secondary">-</a>
                                <input type="text" class="form-control text-center" value="<?php echo $cart['quantity']; ?>" readonly>
                                <a href="?update=<?php echo $cart['cart_id']; ?>&qty=<?php echo $cart['quantity'] + 1; ?>" 
                                   class="btn btn-outline-secondary">+</a>
                            </div>
                        </div>
                        <div class="col-md-2 text-end">
                            <h5 class="text-primary">₱<?php echo number_format($cart['subtotal'], 2); ?></h5>
                        </div>
                        <div class="col-md-1 text-end">
                            <a href="?remove=<?php echo $cart['cart_id']; ?>" class="btn btn-danger btn-sm" 
                               onclick="return confirm('Remove this item?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal (<?php echo $items_count; ?> items)</span>
                        <span>₱<?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <h4>Total</h4>
                        <h4 class="text-primary">₱<?php echo number_format($total, 2); ?></h4>
                    </div>
                    <a href="checkout.php" class="btn btn-success w-100 btn-lg">
                        <i class="fas fa-credit-card"></i> Proceed to Checkout
                    </a>
                    <a href="shop.php" class="btn btn-outline-primary w-100 mt-2">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="text-center py-5">
        <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
        <h3>Your cart is empty</h3>
        <p class="text-muted">Add some products to your cart to checkout.</p>
        <a href="shop.php" class="btn btn-primary">Shop Now</a>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>