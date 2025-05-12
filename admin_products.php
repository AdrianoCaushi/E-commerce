<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
};

if(isset($_POST['add_product'])){
   
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);
   
   $category = $_POST['category'];
   $category = filter_var($category, FILTER_SANITIZE_STRING);
   
   $details = $_POST['details'];
   $details = filter_var($details, FILTER_SANITIZE_STRING);
   
   $stock = $_POST['stock'];  // Add stock field
   $stock = filter_var($stock, FILTER_SANITIZE_NUMBER_INT); // Ensure it's an integer

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   // Check if the product already exists
   $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
   $select_products->execute([$name]);

   if($select_products->rowCount() > 0){
      $message[] = 'Nje produkt me te njejtin emer ekziston!';
   }else{
      // Insert product along with stock into the database
      $insert_products = $conn->prepare("INSERT INTO `products`(name, category, details, price, image, stock) VALUES(?,?,?,?,?,?)");
      $insert_products->execute([$name, $category, $details, $price, $image, $stock]);

      if($insert_products){
         if($image_size > 2000000){
            $message[] = 'Madhesia e fotos teper e larte!';
         }else{
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'Produkti u shtua me sukses!';
         }
      }
   }
};

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $select_delete_image = $conn->prepare("SELECT image FROM `products` WHERE id = ?");
   $select_delete_image->execute([$delete_id]);
   $fetch_delete_image = $select_delete_image->fetch(PDO::FETCH_ASSOC);
   unlink('uploaded_img/'.$fetch_delete_image['image']);
   $delete_products = $conn->prepare("DELETE FROM `products` WHERE id = ?");
   $delete_products->execute([$delete_id]);
   $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE pid = ?");
   $delete_wishlist->execute([$delete_id]);
   $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
   $delete_cart->execute([$delete_id]);
   header('location:admin_products.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Products</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="add-products">
   <h1 class="title">Shto nje produkt te ri</h1>
   <form action="" method="POST" enctype="multipart/form-data">
      <div class="flex">
         <div class="inputBox">
            <input type="text" name="name" class="box" required placeholder="Vendos emrin e produktit">
            <select name="category" class="box" required>
               <option value="" selected disabled>Zgjidh kategorine</option>
               <option value="smartphone">Smartphone</option>
               <option value="smartwatch">Smartwatch</option>
               <option value="pcs">Kompjuter</option>
               <option value="kufje">Kufje</option>
            </select>
         </div>
         <div class="inputBox">
            <input type="number" min="0" name="price" class="box" required placeholder="Vendos cmimin e produktit">
            <input type="file" name="image" required class="box" accept="image/jpg, image/jpeg, image/png">
            
         </div>
      </div>
      <input type="number" min="0" name="stock" class="box" required placeholder="Vendos sasinë e produktit">
      <textarea name="details" class="box" required placeholder="Vendos detajet e produktit" cols="30" rows="10"></textarea>
      <input type="submit" class="btn" value="Shto produktin" name="add_product">
   </form>
</section>

<section class="show-products">
   <h1 class="title">Produktet</h1>
   <div class="box-container">
      <?php
         $show_products = $conn->prepare("SELECT * FROM `products`");
         $show_products->execute();
         if($show_products->rowCount() > 0){
            while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
      ?>
      <div class="box">
         <div class="price">$<?= $fetch_products['price']; ?></div>
         <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
         <div class="name"><?= $fetch_products['name']; ?></div>
         <div class="cat"><?= $fetch_products['category']; ?></div>
         <div class="details"><?= $fetch_products['details']; ?></div>
         <div class="stock">Stok: <span><?= $fetch_products['stock']; ?></span></div> <!-- Display the stock -->
         <div class="flex-btn">
            <a href="admin_update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">Ndrysho</a>
            <a href="admin_products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('Fshi kete produkt?');">Fshi</a>
         </div>
      </div>
      <?php
            }
         }else{
            echo '<p class="empty">Akoma nuk eshte shtuar nje produkt!</p>';
         }
      ?>
   </div>
</section>

<script src="js/script.js"></script>

</body>
</html>
