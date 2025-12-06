<?php
include_once __DIR__ . "/../includes/baseIncludes.php";

//$email = filter_var($_POST['email-username'], FILTER_VALIDATE_EMAIL);
//$email = filter_var($_POST['email-username'], FILTER_SANITIZE_STRING);
$username = htmlspecialchars($_POST['email-username'], ENT_QUOTES, 'UTF-8');

logActivity();

//if ($email === false) {
//    echo "Invalid email address.";
//    exit;
//}

// Password handling should avoid any form of sanitization. Instead, process it securely.
$password = $_POST['password'];

//$hashedPassword = generateSsha512Password($password);
//// Example output sanitization for display purposes (if needed)
//$sanitizedEmail = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
//echo "Sanitized Email: " . $sanitizedEmail;
//echo "  --  Pass: " . $hashedPassword;


$res = $db->query("SELECT * FROM users WHERE userName = ?s AND active =1 LIMIT 1", $username);
if ($db->numRows($res) > 0) {
    $row = mysqli_fetch_assoc($res);


    $providedPassword = $password; // Password from login form
    $storedHash = $row['password'];

    if (verifySsha512Password($providedPassword, $storedHash)) {

        $userData = $row;

        //Get the roles
        $rolesRes = $db->query("SELECT
                              `user_role_mapping`.*,
                              `user_roles`.`roleName`,
                              `user_roles`.`description`
                            FROM
                              `user_role_mapping`
                              LEFT JOIN `user_roles` ON `user_role_mapping`.`roleId` = `user_roles`.`roleId`
                            WHERE
                              `user_role_mapping`.`userId` = ?s", $userData['userID']);


        $roles = array();
        while($rolesRow=mysqli_fetch_assoc($rolesRes))
        {
            $roles[] = $rolesRow['roleId'];
        }

        $_SESSION[IsLoggedIn] = true;

        $authenticatedUser = new AuthenticatedUser($userData, $roles);
        $_SESSION[LoggedInUser] = $authenticatedUser;

        //Set the company
        $userCompanies = $authenticatedUser->getUserCompanies();
        if(empty($userCompanies))
        {
            echo "ERROR|No companies assigned to this user. Please check with the administrator.";
            exit();
        }
        $_SESSION[SES_SELECTED_COMPANY] = $userCompanies[0]['companyId'];

        // Get redirect URL (intended page or fallback to dashboard)
        $redirect_url = $_SESSION['redirect_after_login'] ?? '/dashboard';
        unset($_SESSION['redirect_after_login']); // Clean up after use

        echo 'SUCCESS|' . $redirect_url;

    } else {
        echo "ERROR|Invalid username or password combination. Please try again";
    }

} else {

    echo "ERROR|Invalid username or password combination. Please try again";
}