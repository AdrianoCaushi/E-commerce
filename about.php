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
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>about</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="about">

   <div class="row">

      <div class="box">
         <img src="images/about-img-1.jpg" alt="">
         <h3>Pse te na zgjidhni ne?</h3>
         <p>Duke nisur qe nga variacioni i larte i produkteve deri tek cmimet me te ulta ne treg, faqa jone eshte e vetmja faqe online per te cilen do te keni nevoje. Tek ne do te gjeni produkte nga markat me prestigjoze ne treg si: Apple, Samsung, Lg, Sony etj.</p>
         <a href="contact.php" class="btn">Kontakt</a>
      </div>
   </div>

</section>

<section class="reviews">

   <h1 class="title">reivew te klienteve</h1>

   <div class="box-container">

      <div class="box">
         <img src="images/pic-1.png" alt="">
         <p>Produkti erdhi ne kohe dhe ne gjendje shume te mire. Nje nder dyqanet online me te mira qe kam perdorur prej shume kohesh.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
         </div>
         <h3>Claus Burkhart</h3>
      </div>

      <div class="box">
         <img src="images/pic-2.png" alt="">
         <p>Sherbimi i klientit teper i mire. Pata nje problem ne vendosjen e te dhenave dhe kontatktova sherbimin e klientit. Mora pergjigje teper shpejt dhe me ndihmuan menjehere per te zgjidhur problemin.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>Felicia Ferdinand</h3>
      </div>

      <div class="box">
         <img src="images/pic-3.png" alt="">
         <p>Kam porositur shpesh produkte nga kjo faqe dhe gjithmone kam mbetur i kenaqur nga kualiteti i produkteve.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
         </div>
         <h3>Gianni Erhard</h3>
      </div>

      <div class="box">
         <img src="images/pic-4.png" alt="">
         <p>Pasi kam perdorur shume dyqane online, mund te them se kjo eshte faqa me e mire qe mund te perdorni.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
         </div>
         <h3>Fiorenza Églantine</h3>
      </div>

      <div class="box">
         <img src="images/pic-5.png" alt="">
         <p>Pasi bera porosine time te pare mbeta i suprizuar kur produkti erdhi 1 dite para kohe. Cilesia ishte teper e mire, do te vazhdoj ta perdor kete dyqan.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
         </div>
         <h3>Nicolaus Sokol</h3>
      </div>

      <div class="box">
         <img src="images/pic-6.png" alt="">
         <p>Nje mik ma rekomandoi kete dyqan online. Mbeta i suprizuar kur pash qe cmimet ishin nder me te mirat ne treg. Jua rekomandoj ta perdorni.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>Adriana Loïc</h3>
      </div>

   </div>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>