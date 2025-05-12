<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
   exit();
}

if (isset($_POST['add_to_wishlist'])) {

   $product_id = $_POST['product_id'];
   $product_id = filter_var($product_id, FILTER_SANITIZE_NUMBER_INT);

   $check_wishlist = $conn->prepare("SELECT * FROM `wishlist` WHERE product_id = ? AND user_id = ?");
   $check_wishlist->execute([$product_id, $user_id]);

   $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE product_id = ? AND user_id = ?");
   $check_cart->execute([$product_id, $user_id]);

   if ($check_wishlist->rowCount() > 0) {
      $message[] = 'Produkti eshte shtuar me pare ne wishlist!';
   } elseif ($check_cart->rowCount() > 0) {
      $message[] = 'Produkti eshte shtuar me pare ne cart!';
   } else {
      $insert_wishlist = $conn->prepare("INSERT INTO `wishlist`(user_id, product_id) VALUES(?,?)");
      $insert_wishlist->execute([$user_id, $product_id]);
      $message[] = 'Produkti u shtua ne wishlist!';
   }
}

if (isset($_POST['add_to_cart'])) {

   $product_id = $_POST['product_id'];
   $product_id = filter_var($product_id, FILTER_SANITIZE_NUMBER_INT);
   $p_qty = $_POST['p_qty'];
   $p_qty = filter_var($p_qty, FILTER_SANITIZE_NUMBER_INT);

   // Fetch the stock quantity of the product
   $stock_query = $conn->prepare("SELECT stock FROM `products` WHERE id = ?");
   $stock_query->execute([$product_id]);
   $stock_data = $stock_query->fetch(PDO::FETCH_ASSOC);
   $available_stock = $stock_data['stock'];

   if ($p_qty > $available_stock) {
      $message[] = "Sasia e zgjedhur tejkalon stokun e disponueshëm ($available_stock)!";
   } else {
      $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE product_id = ? AND user_id = ?");
      $check_cart->execute([$product_id, $user_id]);

      if ($check_cart->rowCount() > 0) {
         $message[] = 'Produkti eshte shtuar me pare ne cart!';
      } else {
         $check_wishlist = $conn->prepare("SELECT * FROM `wishlist` WHERE product_id = ? AND user_id = ?");
         $check_wishlist->execute([$product_id, $user_id]);

         if ($check_wishlist->rowCount() > 0) {
            $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE product_id = ? AND user_id = ?");
            $delete_wishlist->execute([$product_id, $user_id]);
         }

         $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, product_id, quantity) VALUES(?,?,?)");
         $insert_cart->execute([$user_id, $product_id, $p_qty]);
         $message[] = 'Produkti u shtua ne cart!';
      }
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quick View</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<?php include 'header.php'; ?>

<section class="quick-view">

   <h1 class="title">Shiko Produktin</h1>

   <?php
      if (isset($_GET['product_id'])) {
         $product_id = $_GET['product_id'];
         $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
         $select_products->execute([$product_id]);

         if ($select_products->rowCount() > 0) {
            while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) { 
   ?>
   <form action="" class="box" method="POST">
      <div class="price">$<span><?= $fetch_products['price']; ?></span></div>
      <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
      <div class="name"><?= $fetch_products['name']; ?></div>
      <div class="details"><?= $fetch_products['details']; ?></div>
      <p><strong>Stoku i disponueshem:</strong> <?= $fetch_products['stock']; ?></p>
      <input type="hidden" name="product_id" value="<?= $fetch_products['id']; ?>">
      <input type="number" min="1" max="<?= $fetch_products['stock']; ?>" value="1" name="p_qty" class="qty">
      <input type="submit" value="Shto ne wishlist" class="option-btn" name="add_to_wishlist">
      <input type="submit" value="Shto ne cart" class="btn" name="add_to_cart">
   </form>
   <?php
            }
         } else {
            echo '<p class="empty">Nuk eshte shtuar asnje produkt!</p>';
         }
      }
   ?>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
