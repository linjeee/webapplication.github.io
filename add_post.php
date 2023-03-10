<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
   header('location:login.php');
}

if(isset($_POST['submit'])){

   $id = unique_id();
   $status = $_POST['status'];
   $status = filter_var($status, FILTER_SANITIZE_STRING);
   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);
   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);
   $playlist = $_POST['playlist'];
   $playlist = filter_var($playlist, FILTER_SANITIZE_STRING);

   $thumb = $_FILES['thumb']['name'];
   $thumb = filter_var($thumb, FILTER_SANITIZE_STRING);
   $thumb_ext = pathinfo($thumb, PATHINFO_EXTENSION);
   $rename_thumb = unique_id().'.'.$thumb_ext;
   $thumb_size = $_FILES['thumb']['size'];
   $thumb_tmp_name = $_FILES['thumb']['tmp_name'];
   $thumb_folder = 'uploaded_files/'.$rename_thumb;


   if($thumb_size > 2000000){
      $message[] = 'image size is too large!';
   }else{
      $add_playlist = $conn->prepare("INSERT INTO `post`(id, user_id, playlist_id, title, description, thumb, status) VALUES(?,?,?,?,?,?,?)");
      $add_playlist->execute([$id, $user_id, $playlist, $title, $description, $rename_thumb, $status]);
      move_uploaded_file($thumb_tmp_name, $thumb_folder);
      $message[] = 'new post uploaded!';
   }

   

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>
   
<section class="video-form">

   <h1 class="heading">upload post</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <p>post status <span>*</span></p>
      <select name="status" class="box" required>
         <option value="" selected disabled>-- select status</option>
         <option value="active">active</option>
         <option value="deactive">deactive</option>
      </select>
      <p>post title <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="enter post title" class="box">
      <p>post description <span>*</span></p>
      <textarea name="description" class="box" required placeholder="write description" maxlength="1000" cols="30" rows="10"></textarea>
      <p>post playlist <span>*</span></p>
      <select name="playlist" class="box" required>
         <option value="" disabled selected>--select playlist</option>
         <?php
         $select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE user_id = ?");
         $select_playlists->execute([$user_id]);
         if($select_playlists->rowCount() > 0){
            while($fetch_playlist = $select_playlists->fetch(PDO::FETCH_ASSOC)){
         ?>
         <option value="<?= $fetch_playlist['id']; ?>"><?= $fetch_playlist['title']; ?></option>
         <?php
            }
         ?>
         <?php
         }else{
            echo '<option value="" disabled>no playlist created yet!</option>';
         }
         ?>
      </select>
      <p>select thumbnail <span>*</span></p>
      <input type="file" name="thumb" accept="image/*" required class="box">
      <input type="submit" value="upload post" name="submit" class="btn">
      
   </form>

</section>
















<?php include 'components/footer.php'; ?>
<script src="js/admin_script.js"></script>

</body>
</html>