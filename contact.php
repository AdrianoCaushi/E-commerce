<?php
@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
}

// Initialize the $message variable to avoid undefined variable warnings
$message = [];

// Fetch User Info (to display or use if needed)
$select_user = $conn->prepare("SELECT name, email, number FROM `users` WHERE id = ?");
$select_user->execute([$user_id]);
$user = $select_user->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['send'])) {
   $msg = $_POST['msg'];
   $msg = filter_var($msg, FILTER_SANITIZE_STRING);

   // Check if identical message was sent before
   $select_message = $conn->prepare("SELECT * FROM `message` WHERE user_id = ? AND message = ?");
   $select_message->execute([$user_id, $msg]);

   if ($select_message->rowCount() > 0) {
      $message[] = 'Mesazhi eshte derguar me pare!';
   } else {
      // Insert new message
      $insert_message = $conn->prepare("INSERT INTO `message`(user_id, message) VALUES(?,?)");
      $insert_message->execute([$user_id, $msg]);

      $message[] = 'Mesazhi u dergua!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Kontakt</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'header.php'; ?>

<section class="contact">
   <h1 class="title">Lidhu me ne!</h1>

   <!-- Display User Info -->
   <div class="user-info">
      <p>Emri: <span><?= $user['name']; ?></span></p>
      <p>Email: <span><?= $user['email']; ?></span></p>
      <p>Numri: <span><?= $user['number']; ?></span></p>
   </div>

   <form action="" method="POST">
      <!-- Only the message field is needed now -->
      <textarea name="msg" class="box" required placeholder="Cila eshte arsyeja qe po na kontaktoni..." cols="30" rows="10"></textarea>
      <input type="submit" value="Dergoni mesazhin" class="btn" name="send">
   </form>

   <!-- Display messages (e.g., success or error) -->
   <?php
   if (is_array($message) && !empty($message)) {
      foreach ($message as $msg) {
         echo '<p class="message">' . $msg . '</p>';
      }
   }
   ?>
</section>

<?php include 'footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>