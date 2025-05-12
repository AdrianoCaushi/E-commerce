<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
   exit;
}

// Update order payment status
if(isset($_POST['update_order'])){
   $order_id = $_POST['order_id'];
   $update_payment = $_POST['update_payment'];
   $update_payment = filter_var($update_payment, FILTER_SANITIZE_STRING);
   $update_orders = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
   $update_orders->execute([$update_payment, $order_id]);
   $message[] = 'Pagesa u Ndryshua!';
}

// Delete order
if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_orders = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_orders->execute([$delete_id]);
   header('location:admin_orders.php');
   exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Porosite</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="placed-orders">

   <h1 class="title">Porosite</h1>

   <div class="box-container">

      <?php
         // Join with users table to get user details
         $select_orders = $conn->prepare("
            SELECT o.*, u.name, u.email, u.number 
            FROM `orders` o 
            JOIN `users` u ON o.user_id = u.id
         ");
         $select_orders->execute();
         
         if($select_orders->rowCount() > 0){
            while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
      ?>
      <div class="box">
         <p> Emri: <span><?= $fetch_orders['name']; ?></span> </p>
         <p> Email: <span><?= $fetch_orders['email']; ?></span> </p>
         <p> Numri: <span><?= $fetch_orders['number']; ?></span> </p>
         <p> Adresa: <span><?= $fetch_orders['address']; ?></span> </p>
         <p> Produktet: <span><?= $fetch_orders['total_products']; ?></span> </p>
         <p> Totali Çmimit: <span>$<?= $fetch_orders['total_price']; ?></span> </p>
         <p> Metoda Pagesës: <span><?= $fetch_orders['method']; ?></span> </p>
         <p> Statusi Pagesës: 
            <span style="color:<?php if($fetch_orders['payment_status'] == 'Ne pritje'){ echo 'red'; }else{ echo 'green'; }; ?>">
               <?= $fetch_orders['payment_status']; ?>
            </span> 
         </p>
         <form action="" method="POST">
            <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
            <select name="update_payment" class="drop-down">
               <option value="" selected disabled><?= $fetch_orders['payment_status']; ?></option>
               <option value="Ne pritje">Ne pritje</option>
               <option value="E kompletuar">E kompletuar</option>
            </select>
            <div class="flex-btn">
               <input type="submit" name="update_order" class="option-btn" value="Ndrysho">
               <a href="admin_orders.php?delete=<?= $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('Fshini kete porosi?');">Fshi</a>
            </div>
         </form>
      </div>
      <?php
            }
         }else{
            echo '<p class="empty">Nuk ka asnje porosi!</p>';
         }
      ?>

   </div>

</section>

<script src="js/script.js"></script>

</body>
</html>
