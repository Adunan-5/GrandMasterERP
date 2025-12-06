<?php
include_once __DIR__ . "/../includes/baseIncludes.php";
$selectedStateID = filter_var($_POST['selectedStateID'], FILTER_SANITIZE_NUMBER_INT);
$res = $db->query("SELECT `id`, `name` FROM cities WHERE state_id = ?s", $selectedStateID);

echo '<option value="">Select a city</option>';

while($row=mysqli_fetch_assoc($res))
{
    echo '<option value="'. $row['id']  .'">'. $row['name']  .'</option>';
}