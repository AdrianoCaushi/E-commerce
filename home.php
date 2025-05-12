<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
};

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
   <title>home page</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="home-bg">

   <section class="home">

      <div class="content">
         <span>Cilësia është kursim!</span>
         <h3>Gjej zgjidhje per cdo deshire tuajen!</h3>
         <p>Miresevini ne dyqanin me te mire online ne Shqiperi!</p>
         <a href="about.php" class="btn">Reth nesh</a>
      </div>

   </section>

</div>

<section class="home-category">

   <h1 class="title">produktet sipas kategorise</h1>

   <div class="box-container">

      <div class="box">
         <img src="images/cat-1.png" alt="">
         <h3>Smartphone</h3>
         <p>Me poshte do te gjeni smartphonet me te mire ne treg si dhe ne cmimin me te mire!</p>
         <a href="category.php?category=smartphone" class="btn">Smartphone</a>
      </div>

      <div class="box">
         <img src="images/cat-2.jpg" alt="">
         <h3>Smartwatch</h3>
         <p>Smartwatch e rinj per fitnes dhe perdorime te tjera.</p>
         <a href="category.php?category=smartwatch" class="btn">Smartwatch</a>
      </div>

      <div class="box">
         <img src="images/cat-3.jpg" alt="">
         <h3>Kompjuter</h3>
         <p>Gjeni kompjuterat e markave me te mira ne treg per cdo kerkese tuajen.</p>
         <a href="category.php?category=pcs" class="btn">Kompjuter</a>
      </div>

      <div class="box">
         <img src="images/cat-4.jpg" alt="">
         <h3>Kufje</h3>
         <p>Kufjet me te reja per te degjuar muzike ne kualitetin me te larte.</p>
         <a href="category.php?category=kufje" class="btn">Kufje</a>
      </div>

   </div>

</section>

<section class="products">

   <h1 class="title">Produktet e fundit</h1>

   <div class="box-container">

   <?php
      $select_products = $conn->prepare("SELECT * FROM `products` LIMIT 3");
      $select_products->execute();
      if($select_products->rowCount() > 0){
         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <form action="" class="box" method="POST">
      <div class="price">$<span><?= $fetch_products['price']; ?></span></div>
      <a href="view_page.php?product_id=<?= $fetch_products['id']; ?>" class="fas fa-eye"></a>
      <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
      <div class="name"><?= $fetch_products['name']; ?></div>
      <input type="hidden" name="product_id" value="<?= $fetch_products['id']; ?>">
      <input type="hidden" name="p_name" value="<?= $fetch_products['name']; ?>">
      <input type="hidden" name="p_price" value="<?= $fetch_products['price']; ?>">
      <input type="hidden" name="p_image" value="<?= $fetch_products['image']; ?>">
      <p><strong>Stoku i disponueshem:</strong> <?= $fetch_products['stock']; ?></p>
      <input type="number" min="1" max="<?= $fetch_products['stock']; ?>" value="1" name="p_qty" class="qty">
      <input type="submit" value="shto ne wishlist" class="option-btn" name="add_to_wishlist">
      <input type="submit" value="shto ne cart" class="btn" name="add_to_cart">
   </form>
   <?php
      }
   }else{
      echo '<p class="empty">nuk eshte shtuar asnje produkt!</p>';
   }
   ?>

   </div>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>

