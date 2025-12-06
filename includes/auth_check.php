<?php
if(!isset($_SESSION[IsLoggedIn]) || !$_SESSION[IsLoggedIn] )
{
    // Save the intended URL for redirect after login
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    
    header("location: /login");
    exit();
}