<?php
include_once __DIR__ . "/includes/baseIncludes.php";


//Redirect to Login page if not logged in
if(!isset($_SESSION[IsLoggedIn]) || !$_SESSION[IsLoggedIn])
{
    header("location: /login");
    exit();
}else{
    header("location: /dashboard");
}