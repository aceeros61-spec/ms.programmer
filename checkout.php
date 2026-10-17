<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$error = '';
$success = '';


if(!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}


$conn = mysqli_connect("localhost", "root", "", "kind_steps_db");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8");


$cart_query = mysqli_query($conn, "SELECT c.*, p.product_name, p.price, p.product_image 
    FROM cart c 
    JOIN products p ON c.product_id = p.product_id 
    WHERE c.customer_id = " . $_SESSION['customer_id']);

$cart_total = 0;
$cart_items_count = mysqli_num_rows($cart_query);

if($cart_items_count == 0) {
    echo "<script>alert('Your cart is empty!'); window.location='shop.php';</script>";
    exit;
}


while($cart_item = mysqli_fetch_assoc($cart_query)) {
    $cart_total += $cart_item['subtotal'];
}
mysqli_data_seek($cart_query, 0);


$user_id = $_SESSION['customer_id'];
$user_result = mysqli_query($conn, "SELECT * FROM customers WHERE customer_id = $user_id");
$user = mysqli_fetch_assoc($user_result);


$payments = mysqli_query($conn, "SELECT * FROM payment_modes");


if(isset($_POST['place_order'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment_id = (int)$_POST['payment_id'];
    
   
    if(empty($fullname) || empty($email) || empty($phone) || empty($address)) {
        $error = "Please fill in all fields!";
    } elseif($payment_id == 0) {
        $error = "Please select a payment method!";
    } else {
        
        $insert = mysqli_query($conn, "INSERT INTO orders (customer_id, payment_id, total_amount, shipping_address, status_id) 
        VALUES ($user_id, $payment_id, $cart_total, '$address', 1)");
        
        if($insert) {
            $order_id = mysqli_insert_id($conn);
            
           
            $cart_items = mysqli_query($conn, "SELECT * FROM cart WHERE customer_id = $user_id");
            
            while($item = mysqli_fetch_assoc($cart_items)) {
                $product_id = $item['product_id'];
                $qty = $item['quantity'];
                $price = $item['subtotal'] / $qty;
                $subtotal = $item['subtotal'];
                
                mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) 
                VALUES ($order_id, $product_id, $qty, $price, $subtotal)");
                
              
                mysqli_query($conn, "UPDATE products SET stock = stock - $qty WHERE product_id = $product_id");
            }
            
            
            mysqli_query($conn, "DELETE FROM cart WHERE customer_id = $user_id");
            
            $success = $order_id;
        } else {
            $error = "Failed to place order. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Kind Steps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f4f6f9; }
        .checkout-card { background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
    </style>
</head>
<body>


<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-shoe-prints"></i> Kind Steps</a>
    </div>
</nav>

<div class="container my-5">
    <h2 class="mb-4"><i class="fas fa-shopping-bag"></i> Checkout</h2>
    
    <?php if($success): ?>
   
    <div class="checkout-card p-5 text-center">
        <i class="fas fa-check-circle fa-5x text-success mb-4"></i>
        <h2>Order Placed Successfully!</h2>
        <p>Order ID: #<?php echo $success; ?></p>
        <p>Thank you for your order!</p>
        <a href="my_orders.php" class="btn btn-primary">View My Orders</a>
        <a href="shop.php" class="btn btn-outline-primary">Continue Shopping</a>
    </div>
    
    <?php else: ?>
    
    <form method="POST">
    <div class="row">
        
        <div class="col-md-7">
            
            <?php if($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            
            <div class="checkout-card p-4 mb-4">
                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-user"></i> Customer Information</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="fullname" class="form-control" value="<?php echo $user['fullname'] ?? ''; ?>" placeholder="Enter your full name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="<?php echo $user['email'] ?? ''; ?>" placeholder="Enter your email" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" value="<?php echo $user['phone'] ?? ''; ?>" placeholder="Enter phone number" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" value="<?php echo $user['username'] ?? ''; ?>" readonly>
                    </div>
                </div>
            </div>
            
           
            <div class="checkout-card p-4 mb-4">
                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-shipping-fast"></i> Shipping Address</h5>
                <div class="mb-3">
                    <label class="form-label">Complete Address <span class="text-danger">*</span></label>
                    <textarea name="address" class="form-control" rows="3" placeholder="House No., Street, City, Province, ZIP Code" required><?php echo $user['address'] ?? ''; ?></textarea>
                </div>
            </div>
            
           
            <div class="checkout-card p-4">
                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-credit-card"></i> Payment Method</h5>
                <div class="row">
                    <?php while($pm = mysqli_fetch_assoc($payments)): ?>
                    <div class="col-md-6 mb-2">
                        <div class="border rounded p-3">
                            <input class="form-check-input" type="radio" name="payment_id" id="pm<?php echo $pm['payment_id']; ?>" value="<?php echo $pm['payment_id']; ?>" required>
                            <label class="form-check-label ms-2" for="pm<?php echo $pm['payment_id']; ?>">
                                <?php echo $pm['payment_name']; ?>
                            </label>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        
        
        <div class="col-md-5">
            <div class="checkout-card p-4 sticky-top" style="top: 80px;">
                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-shopping-cart"></i> Order Summary</h5>
                
                
                <div style="max-height: 200px; overflow-y: auto;">
                    <?php while($item = mysqli_fetch_assoc($cart_query)): ?>
                    <div class="d-flex align-items-center mb-2 pb-2 border-bottom">
                        <img src="assets/images/<?php echo $item['product_image']; ?>" width="40" onerror="this.src='https://placehold.co/40'" class="me-2">
                        <div class="flex-grow-1">
                            <small><?php echo $item['product_name']; ?></small><br>
                            <small class="text-muted">Qty: <?php echo $item['quantity']; ?> × ₱<?php echo number_format($item['price'], 2); ?></small>
                        </div>
                        <small class="fw-bold">₱<?php echo number_format($item['subtotal'], 2); ?></small>
                    </div>
                    <?php endwhile; ?>
                </div>
                
                
                <div class="d-flex justify-content-between mt-3">
                    <span>Subtotal</span>
                    <span>₱<?php echo number_format($cart_total, 2); ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Shipping</span>
                    <span class="text-success">Free</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <h4>Total</h4>
                    <h4 class="text-primary">₱<?php echo number_format($cart_total, 2); ?></h4>
                </div>
                
                <button type="submit" name="place_order" class="btn btn-success w-100 btn-lg">
                    <i class="fas fa-check-circle"></i> Place Order
                </button>
                
                <a href="cart.php" class="btn btn-outline-secondary w-100 mt-2">Back to Cart</a>
            </div>
        </div>
    </div>
    </form>
    
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>