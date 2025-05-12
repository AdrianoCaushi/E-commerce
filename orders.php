<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible=IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Porosite</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="placed-orders">

   <h1 class="title">Porosite</h1>

   <div class="box-container">

   <?php
      // Get Orders and User Details
      $select_orders = $conn->prepare("
         SELECT o.*, u.name, u.number, u.email 
         FROM `orders` o 
         JOIN `users` u ON o.user_id = u.id 
         WHERE o.user_id = ?
      ");
      $select_orders->execute([$user_id]);

      if($select_orders->rowCount() > 0){
         while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <div class="box">
      <p> Data : <span><?= $fetch_orders['placed_on']; ?></span> </p>
      <p> Emri : <span><?= $fetch_orders['name']; ?></span> </p>
      <p> Numri : <span><?= $fetch_orders['number']; ?></span> </p>
      <p> Email : <span><?= $fetch_orders['email']; ?></span> </p>
      <p> <span><?= $fetch_orders['address']; ?></span> </p>
      <p> Menyra pageses : <span><?= $fetch_orders['method']; ?></span> </p>
      <p> Produktet : <span><?= $fetch_orders['total_products']; ?></span> </p>
      <p> Totali cmimit : <span>$<?= $fetch_orders['total_price']; ?></span> </p>
      <p> Statusi : 
         <span style="color:<?php if($fetch_orders['payment_status'] == 'Ne pritje'){ echo 'red'; }else{ echo 'green'; }; ?>">
            <?= $fetch_orders['payment_status']; ?>
         </span> 
      </p>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">Nuk keni asnje porosi!</p>';
      }
   ?>

   </div>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
