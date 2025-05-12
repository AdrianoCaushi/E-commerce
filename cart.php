<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit();
}

// Delete a single item from the cart
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
    $delete_cart_item->execute([$delete_id]);
    header('location:cart.php');
    exit();
}

// Delete all items for the current user
if (isset($_GET['delete_all'])) {
    $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
    $delete_cart_item->execute([$user_id]);
    header('location:cart.php');
    exit();
}

// Update the quantity of a cart item
if (isset($_POST['update_qty'])) {
    $cart_id = $_POST['cart_id'];
    $p_qty = $_POST['p_qty'];
    $p_qty = filter_var($p_qty, FILTER_SANITIZE_NUMBER_INT);

    // Check if stock is sufficient
    $check_stock = $conn->prepare("SELECT p.stock FROM products p JOIN cart c ON c.product_id = p.id WHERE c.id = ?");
    $check_stock->execute([$cart_id]);
    $stock = $check_stock->fetch(PDO::FETCH_ASSOC)['stock'];

    if ($p_qty > $stock) {
        $message[] = 'Sasia e kerkuar nuk eshte ne stok!';
    } else {
        $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
        $update_qty->execute([$p_qty, $cart_id]);
        $message[] = 'Cart u ndryshua';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<section class="shopping-cart">

    <h1 class="title">Produktet e zgjedhura</h1>

    <div class="box-container">

        <?php
        $grand_total = 0;

        // Fetch cart items with product details
        $select_cart = $conn->prepare("
            SELECT c.id AS cart_id, c.quantity, 
                   p.id AS product_id, p.name, p.price, p.image, p.stock
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ?
        ");
        $select_cart->execute([$user_id]);

        if ($select_cart->rowCount() > 0) {
            while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                $sub_total = $fetch_cart['price'] * $fetch_cart['quantity'];
        ?>
                <form action="" method="POST" class="box">
                    <a href="cart.php?delete=<?= $fetch_cart['cart_id']; ?>" class="fas fa-times" onclick="return confirm('Fshi kete produkt nga cart?');"></a>
                    <a href="view_page.php?product_id=<?= $fetch_cart['product_id']; ?>" class="fas fa-eye"></a>
                    <img src="uploaded_img/<?= htmlspecialchars($fetch_cart['image']); ?>" alt="">
                    <div class="name"><?= htmlspecialchars($fetch_cart['name']); ?></div>
                    <div class="price">$<?= htmlspecialchars($fetch_cart['price']); ?></div>
                    <input type="hidden" name="cart_id" value="<?= $fetch_cart['cart_id']; ?>">
                    <div class="flex-btn">
                        <input type="number" min="1" max="<?= htmlspecialchars($fetch_cart['stock']); ?>" value="<?= htmlspecialchars($fetch_cart['quantity']); ?>" class="qty" name="p_qty">
                        <input type="submit" value="Ndysho" name="update_qty" class="option-btn">
                    </div>
                    <div class="sub-total"> Sub total : <span>$<?= $sub_total; ?></span> </div>
                </form>
        <?php
                $grand_total += $sub_total;
            }
        } else {
            echo '<p class="empty">Cart eshte bosh!</p>';
        }
        ?>
    </div>

    <div class="cart-total">
        <p>Totali : <span>$<?= $grand_total; ?></span></p>
        <a href="shop.php" class="option-btn">Vazhdo te blesh</a>
        <a href="cart.php?delete_all" class="delete-btn <?= ($grand_total > 0) ? '' : 'disabled'; ?>">Fshi te gjitha</a>
        <a href="checkout.php" class="btn <?= ($grand_total > 0) ? '' : 'disabled'; ?>">Vazhdo porosine</a>
    </div>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
