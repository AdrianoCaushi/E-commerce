<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
   exit;
}

// Delete message
if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_message = $conn->prepare("DELETE FROM `message` WHERE id = ?");
   $delete_message->execute([$delete_id]);
   header('location:admin_contacts.php');
   exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Mesazhet</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="messages">

   <h1 class="title">Mesazhet</h1>

   <div class="box-container">

   <?php
      // Join with users table to get name, email, and number
      $select_message = $conn->prepare("
         SELECT m.id, m.message, u.name, u.email, u.number
         FROM `message` m
         JOIN `users` u ON m.user_id = u.id
      ");
      $select_message->execute();
      
      if($select_message->rowCount() > 0){
         while($fetch_message = $select_message->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p> Emri: <span><?= $fetch_message['name']; ?></span> </p>
      <p> Numri: <span><?= $fetch_message['number']; ?></span> </p>
      <p> Email: <span><?= $fetch_message['email']; ?></span> </p>
      <p> Mesazhi: <span><?= $fetch_message['message']; ?></span> </p>
      <a href="admin_contacts.php?delete=<?= $fetch_message['id']; ?>" onclick="return confirm('Fshini kete mesazh?');" class="delete-btn">Fshi</a>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">Nuk keni asnje mesazh!</p>';
      }
   ?>

   </div>

</section>

<script src="js/script.js"></script>

</body>
</html>
