<?php
include_once __DIR__ . "/../includes/baseIncludes.php";
$selectedCountryID = filter_var($_POST['selectedCountryID'], FILTER_SANITIZE_NUMBER_INT);
$res = $db->query("SELECT `id`, `name` FROM states WHERE country_id = ?s", $selectedCountryID);

echo '<option value="">Select a state</option>';

while($row=mysqli_fetch_assoc($res))
{
    echo '<option value="'. $row['id']  .'">'. $row['name']  .'</option>';
}