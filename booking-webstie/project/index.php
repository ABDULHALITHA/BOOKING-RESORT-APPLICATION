<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   setcookie('user_id', create_unique_id(), time() + 60*60*24*30, '/');
   header('location:index.php');
}

if(isset($_POST['check'])){

   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }

   // if the hotel has total 30 rooms 
   if($total_rooms >= 30){
      $warning_msg[] = 'rooms are not available';
   }else{
      $success_msg[] = 'rooms are available';
   }

}

if(isset($_POST['book'])){

   $booking_id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $rooms = $_POST['rooms'];
   $rooms = filter_var($rooms, FILTER_SANITIZE_STRING);
   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);
   $check_out = $_POST['check_out'];
   $check_out = filter_var($check_out, FILTER_SANITIZE_STRING);
   $adults = $_POST['adults'];
   $adults = filter_var($adults, FILTER_SANITIZE_STRING);
   $childs = $_POST['childs'];
   $childs = filter_var($childs, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }

   if($total_rooms >= 30){
      $warning_msg[] = 'rooms are not available';
   }else{

      $verify_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ? AND name = ? AND email = ? AND number = ? AND rooms = ? AND check_in = ? AND check_out = ? AND adults = ? AND childs = ?");
      $verify_bookings->execute([$user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);

      if($verify_bookings->rowCount() > 0){
         $warning_msg[] = 'room booked alredy!';
      }else{
         $book_room = $conn->prepare("INSERT INTO `bookings`(booking_id, user_id, name, email, number, rooms, check_in, check_out, adults, childs) VALUES(?,?,?,?,?,?,?,?,?,?)");
         $book_room->execute([$booking_id, $user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);
         $success_msg[] = 'room booked successfully!';
      }

   }

}

if(isset($_POST['send'])){

   $id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $message = $_POST['message'];
   $message = filter_var($message, FILTER_SANITIZE_STRING);

   $verify_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $verify_message->execute([$name, $email, $number, $message]);

   if($verify_message->rowCount() > 0){
      $warning_msg[] = 'message sent already!';
   }else{
      $insert_message = $conn->prepare("INSERT INTO `messages`(id, name, email, number, message) VALUES(?,?,?,?,?)");
      $insert_message->execute([$id, $name, $email, $number, $message]);
      $success_msg[] = 'message send successfully!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- home section starts  -->

<section class="home" id="home">

   <div class="swiper home-slider">

      <div class="swiper-wrapper">

         <div class="box swiper-slide">
            <img src="images/home-img-1.jpg" alt="">
            <div class="flex">
               <h3>luxurious rooms</h3>
               <a href="#availability" class="btn">check availability</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/home-img-2.jpg" alt="">
            <div class="flex">
               <h3>foods and drinks</h3>
               <a href="#reservation" class="btn">make a reservation</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/home-img-3.jpg" alt="">
            <div class="flex">
               <h3>luxurious halls</h3>
               <a href="#contact" class="btn">contact us</a>
            </div>
         </div>

      </div>

      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>

   </div>

</section>

<!-- home section ends -->

<!-- availability section starts  -->

<section class="availability" id="availability">

   <form action="" method="post">
      <div class="flex">
         <div class="box">
            <p>check in <span>*</span></p>
            <input type="date" name="check_in" class="input" required>
         </div>
         <div class="box">
            <p>check out <span>*</span></p>
            <input type="date" name="check_out" class="input" required>
         </div>
         <div class="box">
            <p>adults <span>*</span></p>
            <select name="adults" class="input" required>
               <option value="1">1 adult</option>
               <option value="2">2 adults</option>
               <option value="3">3 adults</option>
               <option value="4">4 adults</option>
               <option value="5">5 adults</option>
               <option value="6">6 adults</option>
            </select>
         </div>
         <div class="box">
            <p>childs <span>*</span></p>
            <select name="childs" class="input" required>
               <option value="-">0 child</option>
               <option value="1">1 child</option>
               <option value="2">2 childs</option>
               <option value="3">3 childs</option>
               <option value="4">4 childs</option>
               <option value="5">5 childs</option>
               <option value="6">6 childs</option>
            </select>
         </div>
         <div class="box">
            <p>rooms <span>*</span></p>
            <select name="rooms" class="input" required>
               <option value="1">1 room</option>
               <option value="2">2 rooms</option>
               <option value="3">3 rooms</option>
               <option value="4">4 rooms</option>
               <option value="5">5 rooms</option>
               <option value="6">6 rooms</option>
            </select>
         </div>
      </div>
      <input type="submit" value="check availability" name="check" class="btn">
   </form>

</section>

<!-- availability section ends -->

<!-- about section starts  -->

<section class="about" id="about">

   <div class="row">
      <div class="image">
         <img src="images/about-img-1.jpg" alt="">
      </div>
      <div class="content">
         <h3>best staff</h3>
         <p>We have a best staff members do whatever makes guests comfortable,including taking reservations, cleaning guest rooms, planning parties and maintaining the building.In general the staff can be placed into three different catogories: Administration,Guest services and Support staff.Administartion positions range from manage to secretaries.Guest services are the staff the care for the guest directly e.g: Maids,Waiter and Cookes.Our staff are regularly motivated and training to be friendly receptive and service oriented.</p>
         <a href="#reservation" class="btn">make a reservation</a>
      </div>
   </div>

   <div class="row revers">
      <div class="image">
         <img src="images/about-img-2.jpg" alt="">
      </div>
      <div class="content">
         <h3>best foods</h3>
         <p>Meat can be smoky, Vegetables can range between fresh, Sauces are fruity, Tastes great! Eating something delicious right now?.. <br> The open  and elevated concept of the restaurant is highly commended by our valued guests,since the sea view is offered to from every single table,from where you could enjoy the pleasing view. The restaurant could accommodate 130 pax & we offer a e wide variety of mouth watering cusine with a great choice of Asian,Sri Lankan,Eastern & Western cuisine with seafood specialists as well as under the stars Bar-Bar-que.Quench the thirst with a tropical cocktail,mocktail or a cold beer.<br></p>        
         <a href="#contact" class="btn">contact us</a>
      </div>
   </div>

   <div class="row">
      <div class="image">
         <img src="images/about-img-3.jpg" alt="">
      </div>
      <div class="content">
         <h3>swimming pool</h3>
         <p>"Life is better when you'r swimming" <br> Everyone feels like taking a dip in refershing activity and a mandatory exercise for many athelets. Swimming Pool is for all age groups and allow you to enjoy quality time with your friends and family.There are many Swimming Pools in Chennai that can help you feel refreshed during the hot summers,if you are a swimming lover and like to  indulge in it time and again,Sun Shine Resort,Chennai,Tamilnadu,India.</p>
         <h3>outdoor games</h3> 
         <p>As we believe in offering an interactive holiday for our valuable guests, opportunities are extended to play outdoor games such as Beach Volleyball, Cricket and Frisbee with our off duty staff together with the guests. For Groups, on request initiatives are taken to arrange matches with local youth or schools.To keep you entertained and occupied, Insight offers you a range of indoor games such as Table Tennis,Darts,Chess,Drafts and Scrabble.</p>
         <a href="#availability" class="btn">check availability</a>
      </div>
   </div>

</section>

<!-- about section ends -->

<!-- services section starts  -->

<section class="services">

   <div class="box-container">

      <div class="box">
         <img src="images/icon-1.png" alt="">
         <h3>food & drinks</h3>
         <p>"All-inclusive” can be a scary phrase when you’re booking a vacation. Although we love to eat and drink with abandon, these food and beverage packages often come with mediocre choices and hefty price tags.We are the best all-inclusive resorts that actually serve delicious cocktails,premium spirits and diverse food menus.If you’re looking on your next tropical getaway,book a room at onces sip unlimited cocktails in bliss.</p>
      </div>

      <div class="box">
         <img src="images/icon-2.png" alt="">
         <h3>outdoor dining</h3>
         <p>“For a dinner date, I eat light all day to save room, then I go all in: I choose this meal and this order, and I choose you, the person across from me, to share it with.There’s a beautiful intimacy in a meal like that.”Spread over two levels (both overlooking the ocean),We also offers an outdoor lawn and floor to ceiling glass-covered private dining rooms.Fresh air + food + my favorite person = total bliss.</p>
      </div>

      <div class="box">
         <img src="images/icon-3.png" alt="">
         <h3>beach view</h3>
         <p>There’s nothing quite like waking up to the sound of swirling waves and the smell of sea air through your bedroom window. Beachfront accommodation comes in a variety of forms, from well decked-out resorts to secluded homestays and villas. Beach hotel amenities can include air-conditioned rooms, private terraces overlooking the sea and outdoor pools with adjacent bars.</p>     
      </div>

      <div class="box">
         <img src="images/icon-4.png" alt="">
         <h3>decorations</h3>
         <p>VIP Wedding Planner & Birthday Planner in Chennai is a top player in category Event Organisers for Wedding.This well-known establishement a one-stop destination servicing customers both local and from other part of chennai.It is known to provide top services in following catogries: Ballon Decorators,Event Organisers,Birthday Party,Stage,Flower And Wedding Decorators.</p>
      </div>

      <div class="box">
         <img src="images/icon-5.png" alt="">
         <h3>swimming pool</h3>
         <p>Resort offers a selection of elegant, modern apartments, all spacious, self contained and nestled around a glittering, 185 m central swimming lagoon.opportunities are extended to play outdoor games such as Beach Volleyball, Cricket and Frisbee with our off duty staff together with the guests.The only place that’ll keep you cool.You’ll love swimming at our pool.</p>
      </div>

      <div class="box">
         <img src="images/icon-6.png" alt="">
         <h3>resort beach</h3>
         <p>Combining stunning hinterland views and friendly onsite management, our Four Mile Beach accommodation sets the scene for a holiday of rest, relaxation and rejuvenation.A 10 minute walk to the famous Four Mile Beach.It provides guests with the perfect base to explore the nearby Great Barrier Reef and Daintree Rainforest.We are taking you to a wonderland of Sun🌞,Sand🌙 And Stars🌟</p>
      </div>

   </div>

</section>

<!-- services section ends -->

<!-- reservation section starts  -->

<section class="reservation" id="reservation">

   <form action="" method="post">
      <h3>make a reservation</h3>
      <div class="flex">
         <div class="box">
            <p>your name <span>*</span></p>
            <input type="text" name="name" maxlength="50" required placeholder="enter your name" class="input">
         </div>
         <div class="box">
            <p>your email <span>*</span></p>
            <input type="email" name="email" maxlength="50" required placeholder="enter your email" class="input">
         </div>
         <div class="box">
            <p>your number <span>*</span></p>
            <input type="number" name="number" maxlength="10" min="0" max="9999999999" required placeholder="enter your number" class="input">
         </div>
         <div class="box">
            <p>rooms <span>*</span></p>
            <select name="rooms" class="input" required>
               <option value="1" selected>1 room</option>
               <option value="2">2 rooms</option>
               <option value="3">3 rooms</option>
               <option value="4">4 rooms</option>
               <option value="5">5 rooms</option>
               <option value="6">6 rooms</option>
            </select>
         </div>
         <div class="box">
            <p>check in <span>*</span></p>
            <input type="date" name="check_in" class="input" required>
         </div>
         <div class="box">
            <p>check out <span>*</span></p>
            <input type="date" name="check_out" class="input" required>
         </div>
         <div class="box">
            <p>adults <span>*</span></p>
            <select name="adults" class="input" required>
               <option value="1" selected>1 adult</option>
               <option value="2">2 adults</option>
               <option value="3">3 adults</option>
               <option value="4">4 adults</option>
               <option value="5">5 adults</option>
               <option value="6">6 adults</option>
            </select>
         </div>
         <div class="box">
            <p>childs <span>*</span></p>
            <select name="childs" class="input" required>
               <option value="0" selected>0 child</option>
               <option value="1">1 child</option>
               <option value="2">2 childs</option>
               <option value="3">3 childs</option>
               <option value="4">4 childs</option>
               <option value="5">5 childs</option>
               <option value="6">6 childs</option>
            </select>
         </div>
      </div>
      <input type="submit" value="book now" name="book" class="btn">
   </form>

</section>

<!-- reservation section ends -->

<!-- gallery section starts  -->

<section class="gallery" id="gallery">

   <div class="swiper gallery-slider">
      <div class="swiper-wrapper">
         <img src="images/gallery-img-1.jpg" class="swiper-slide" alt="">
         <img src="images/gallery-img-2.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-3.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-4.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-5.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-6.webp" class="swiper-slide" alt="">
      </div>
      <div class="swiper-pagination"></div>
   </div>

</section>

<!-- gallery section ends -->

<!-- contact section starts  -->

<section class="contact" id="contact">

   <div class="row">

      <form action="" method="post">
         <h3>send us message</h3>
         <input type="text" name="name" required maxlength="50" placeholder="enter your name" class="box">
         <input type="email" name="email" required maxlength="50" placeholder="enter your email" class="box">
         <input type="number" name="number" required maxlength="10" min="0" max="9999999999" placeholder="enter your number" class="box">
         <textarea name="message" class="box" required maxlength="1000" placeholder="enter your message" cols="30" rows="10"></textarea>
         <input type="submit" value="send message" name="send" class="btn">
      </form>

      <div class="faq">
         <h3 class="title">Frequently Asked Questions</h3>
         <div class="box active">
            <h3>How to cancel?</h3>
            <p>Open their reservation details page, on right pane is Request Cancellation, then you get options 1 &2. This method is if you want to trigger the cancellation, including if the guest emails or phones you to do it.</p>
         </div>
         <div class="box">
            <h3>Is there any vacancy?</h3>
            <p>Visit the hotel in person to find out about job opportunities. Ask the hotel's concierge about information concerning job openings and where to apply. The concierge will most likely direct you to the hotel manager or assistant manager.They will provide you with a list of job openings and how to apply for them.</p>
         </div>
         <div class="box">
            <h3>What are payment methods?</h3>
            <p>Stay first,Pay after.Resorts offers its users the option to make deposits and withdrawals by using credit and debit cards.There doesn't appear to be any limit to what bank you are with for making your deposits so you can rest assured that with a credit or debit card you can easily conduct transactions to and from your Resorts account.</p>
         </div>
         <div class="box">
            <h3>How to claim coupons codes?</h3>
            <p>However, these are available for a limited time and they are subject to change the next day.You can look for the deals while you are about to book hotel after narrowing down your choices.On the App,You can check a property that has 'MMT Exclusive Deal'button below its discounted rate.By Tapping on it,you would know how many offers are available. </p>
         </div>
         <div class="box">
            <h3>What are the age requirements?</h3>
            <p>hotels require that a person be of legal age (18+) to book a hotel or to form any other type of contract.Children aged 16 or 17 years will be permitted to stay in separate room(s) under the responsibility of the parent or guardian and the parent or guardian must also stay in the hotel at all times the children are within the hotel. You must not exceed the maximum occupancy for the room allocated to you.</p>
         </div>
      </div>

   </div>

</section>

<!-- contact section ends -->

<!-- reviews section starts  -->

<section class="reviews" id="reviews">

   <div class="swiper reviews-slider">

      <div class="swiper-wrapper">
         <div class="swiper-slide box">
            <img src="images/pic-1.png" alt="">
            <h3>Jackson</h3>
            <p>Excellent experience staying there,it is the best resort in chennai to stay.Best thing is the staff is so good that they never makes you feel like you are outsidde you are outside your home.This resort is budget friendly and is best in the lower prices and aslo good in compared to other high priced resorts.i like it the most and i also advice to go and visit the resort for your best experience </p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-2.png" alt="">
            <h3>Jasmine</h3>
            <p>I love this hotel because of their good service and room is very clean and food is very good.The resort is  situated at a very good location,we can access every available public transport very easily and quickly.I also went to city forest to explore as it was nearby.it was amazing experience here.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-3.png" alt="">
            <h3>Vipin Singh</h3>
            <p>Went for a business trip and his hotel was ideal stay to spend the night.The rooms are exteremely high standards to the cost.Location is near to ocean and many food outlets.A well furnished and spacious room was alloted to us after a smooth check-in process.Finding the resort was hassle-free thing.Room Service was preety quick.Would surely recommended.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-4.png" alt="">
            <h3>Hajira Banu</h3>
            <p>You are given an upright and a clean room after a fast check-in.All the basic amenities are there in the room.Hygine norms are followed properly.People working there are soft spoken and helpful.Would definitely visit again.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-5.png" alt="">
            <h3>Raja</h3>
            <p>I have stayed at this reesort for a lot of time.A beautiful room with a big window provide amazing ventilation in the room.Perfect place to have a fun and great time with friends and family.It is easily accessible by public transport as well.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-6.png" alt="">
            <h3>Sophia</h3>
            <p>Excellent experience staying there.Room's ambience is very good.They are following all protocols for COVID.Best thing is that staff is so good that they never makes you feel like you are outside your home.This resort is budget friendly and best in the lower prices and also good in compared to other high priced resort.</p>
         </div>
      </div>

      <div class="swiper-pagination"></div>
   </div>

</section>

<!-- reviews section ends  -->





<?php include 'components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<?php include 'components/message.php'; ?>

</body>
</html>