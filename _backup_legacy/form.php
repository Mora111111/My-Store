<?php

    $email = $_POST['email'];
    $password = $_POST['password'];

$connection= mysqli_connect('localhost','root','','e-website');
if(isset($_POST['send'])){
    $email = $_POST['email'];
    $password = $_POST['password'];


    $request= "insert into elogin(email,password)values
    ('$email','$password')";
    mysqli_query($connection,$request);
    header('location:signin.php');
}else{
    echo'somthing went wrong...';
}


?>