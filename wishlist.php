<?php
@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
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

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_wishlist_item = $conn->prepare("DELETE FROM `wishlist` WHERE id = ?");
   $delete_wishlist_item->execute([$delete_id]);
   header('location:wishlist.php');
}

if (isset($_GET['delete_all'])) {
   $delete_wishlist_item = $conn->prepare("DELETE FROM `wishlist` WHERE user_id = ?");
   $delete_wishlist_item->execute([$user_id]);
   header('location:wishlist.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Wishlist</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'header.php'; ?>

<section class="wishlist">
   <h1 class="title">Produktet ne wishlist</h1>
   <div class="box-container">
      <?php
      $grand_total = 0;
      // Fetch product details from 'products' table by joining with 'wishlist'
      $select_wishlist = $conn->prepare("SELECT w.id, w.product_id, p.name, p.price, p.image, p.stock FROM `wishlist` w JOIN `products` p ON w.product_id = p.id WHERE w.user_id = ?");
      $select_wishlist->execute([$user_id]);
      if ($select_wishlist->rowCount() > 0) {
         while ($fetch_wishlist = $select_wishlist->fetch(PDO::FETCH_ASSOC)) {
      ?>
            <form action="" method="POST" class="box">
               <a href="wishlist.php?delete=<?= $fetch_wishlist['id']; ?>" class="fas fa-times" onclick="return confirm('Fshi produktin nga wishlist?');"></a>
               <a href="view_page.php?pid=<?= $fetch_wishlist['product_id']; ?>" class="fas fa-eye"></a>
               <img src="uploaded_img/<?= $fetch_wishlist['image']; ?>" alt="">
               <div class="name"><?= $fetch_wishlist['name']; ?></div>
               <div class="price">$<?= $fetch_wishlist['price']; ?></div>
               <p><strong>Stoku i disponueshem:</strong> <?= $fetch_wishlist['stock']; ?></p>
               <input type="number" min="1" max="<?= $fetch_wishlist['stock']; ?>" value="1" name="p_qty" class="qty">
               <input type="hidden" name="product_id" value="<?= $fetch_wishlist['product_id']; ?>">
               <input type="hidden" name="p_name" value="<?= $fetch_wishlist['name']; ?>">
               <input type="hidden" name="p_price" value="<?= $fetch_wishlist['price']; ?>">
               <input type="hidden" name="p_image" value="<?= $fetch_wishlist['image']; ?>">
               <input type="submit" value="Shto ne cart" class="btn" name="add_to_cart">
            </form>
      <?php
            $grand_total += $fetch_wishlist['price'];
         }
      } else {
         echo '<p class="empty">Wishlist juaj eshte bosh!</p>';
      }
      ?>
   </div>

   <div class="wishlist-total">
      <p>Totali : <span>$<?= $grand_total; ?></span></p>
      <a href="shop.php" class="option-btn">Vazhdo te blesh</a>
      <a href="wishlist.php?delete_all" class="delete-btn <?= ($grand_total > 1) ? '' : 'disabled'; ?>">Fshi te gjitha</a>
   </div>
</section>

<?php include 'footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>