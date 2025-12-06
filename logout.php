<?php
$PAGE_ID = "LOGOUT_PAGE";
include_once "includes/baseIncludes.php";

$_SESSION[IsLoggedIn] = false;
$_SESSION[LoggedInUser] = null;

unset($_SESSION[IsLoggedIn]);
unset($_SESSION[LoggedInUser]);


// Step 2: Destroy the session
session_destroy();

// Step 3: Delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to the login page or home page
header("Location: /");
exit;