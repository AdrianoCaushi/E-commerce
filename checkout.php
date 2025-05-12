<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
};

// Get User Info
$user_query = $conn->prepare("SELECT name, email, number FROM `users` WHERE id = ?");
$user_query->execute([$user_id]);
$user = $user_query->fetch(PDO::FETCH_ASSOC);

if(isset($_POST['order'])){

   $method = $_POST['method'];
   $method = filter_var($method, FILTER_SANITIZE_STRING);
   $address = 'adresa: '. $_POST['street'] .' '. $_POST['city'] .' '. $_POST['country'] .' - '. $_POST['pin_code'];
   $address = filter_var($address, FILTER_SANITIZE_STRING);
   $placed_on = date('d-M-Y');

   $cart_total = 0;
   $cart_products = [];

   // Get Cart Details
   $cart_query = $conn->prepare("
      SELECT c.quantity, p.name, p.price 
      FROM `cart` c 
      JOIN `products` p ON c.product_id = p.id 
      WHERE c.user_id = ?
   ");
   $cart_query->execute([$user_id]);
   if($cart_query->rowCount() > 0){
      while($cart_item = $cart_query->fetch(PDO::FETCH_ASSOC)){
         $cart_products[] = $cart_item['name'].' ( '.$cart_item['quantity'].' )';
         $sub_total = ($cart_item['price'] * $cart_item['quantity']);
         $cart_total += $sub_total;
      }
   }

   $total_products = implode(', ', $cart_products);

   $order_query = $conn->prepare("
      SELECT * FROM `orders` 
      WHERE user_id = ? AND method = ? AND address = ? AND total_products = ? AND total_price = ?
   ");
   $order_query->execute([$user_id, $method, $address, $total_products, $cart_total]);

   if($cart_total == 0){
      $message[] = 'cart juaj eshte bosh';
   }elseif($order_query->rowCount() > 0){
      $message[] = 'porosia u krye!';
   }else{
      $insert_order = $conn->prepare("
         INSERT INTO `orders`(user_id, method, address, total_products, total_price, placed_on) 
         VALUES(?,?,?,?,?,?)
      ");
      $insert_order->execute([$user_id, $method, $address, $total_products, $cart_total, $placed_on]);
      $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
      $delete_cart->execute([$user_id]);
      $message[] = 'porosia u krye me sukses!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="display-orders">
   <?php
      $cart_grand_total = 0;
      $select_cart_items = $conn->prepare("
         SELECT c.quantity, p.name, p.price 
         FROM `cart` c 
         JOIN `products` p ON c.product_id = p.id 
         WHERE c.user_id = ?
      ");
      $select_cart_items->execute([$user_id]);
      if($select_cart_items->rowCount() > 0){
         while($fetch_cart_items = $select_cart_items->fetch(PDO::FETCH_ASSOC)){
            $cart_total_price = ($fetch_cart_items['price'] * $fetch_cart_items['quantity']);
            $cart_grand_total += $cart_total_price;
   ?>
   <p> <?= $fetch_cart_items['name']; ?> <span>(<?= '$'.$fetch_cart_items['price'].' x '. $fetch_cart_items['quantity']; ?>)</span> </p>
   <?php
         }
      }else{
         echo '<p class="empty">cart juaj eshte bosh!</p>';
      }
   ?>
   <div class="grand-total">Totali : <span>$<?= $cart_grand_total; ?></span></div>
</section>

<section class="checkout-orders">

   <form action="" method="POST">

      <h3>Detajet e porosise</h3>

      <div class="flex">
         <div class="inputBox">
            <span>Emri juaj :</span>
            <input type="text" value="<?= $user['name']; ?>" class="box" readonly>
         </div>
         <div class="inputBox">
            <span>Numri juaj i telefonit :</span>
            <input type="text" value="<?= $user['number']; ?>" class="box" readonly>
         </div>
         <div class="inputBox">
            <span>Email juaj :</span>
            <input type="email" value="<?= $user['email']; ?>" class="box" readonly>
         </div>
         <div class="inputBox">
            <span>Menyra e pageses :</span>
            <select name="method" class="box" required>
               <option value="kash">kash</option>
               <option value="karte krediti">karte krediti</option>
               <option value="paytm">paytm</option>
               <option value="paypal">paypal</option>
            </select>
         </div>

         <div class="inputBox">
            <span>Emri i rruges:</span>
            <input type="text" name="street" placeholder="psh. Rruga e barrikadave" class="box" required>
         </div>
         <div class="inputBox">
            <span>Qyteti :</span>
            <input type="text" name="city" placeholder="psh. Tirane" class="box" required>
         </div>

         <div class="inputBox">
            <span>Shteti :</span>
            <input type="text" name="country" placeholder="psh. Shqiperi" class="box" required>
         </div>
         <div class="inputBox">
            <span>Kodi pin:</span>
            <input type="number" min="0" name="pin_code" placeholder="psh. 111111" class="box" required>
         </div>
      </div>

      <input type="submit" name="order" class="btn <?= ($cart_grand_total > 1)?'':'disabled'; ?>" value="Vendos porosine">

   </form>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
