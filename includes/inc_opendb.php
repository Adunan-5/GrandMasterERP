<?php
include_once __DIR__ ."/../phplibs/safemysql-master/safemysql.class.php";
include_once __DIR__ ."/masterconfig.php";

error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

//include_once "vendor/autoload.php";
//\Sentry\init(['dsn' => 'https://da5daa19259240aa8b18814f488d30ad@o1399671.ingest.sentry.io/6727336']);
//\Sentry\init(['dsn' => 'http://deeb4e1148d74ac19204f8850c4feaf2@sentry.zeenara.com:9000/3']);
//\Sentry\init(['dsn' => 'http://deeb4e1148d74ac19204f8850c4feaf2@sentry.zeenara.com:9000/3']);

define('APP_VERSION', "0.1.0");

//$mysqli = mysqli_init();

$dbInfo = [
    'host' => $dbhost,
    'user' => $dbuser,
    'pass' => $dbpass,
    'db' => $dbdatabase,
    'port' => null,
    'socket' => null,
    'pconnect' => false,
    'charset' => 'utf8',
    'errmode' => 'exception', //error or exception
    'exception' => 'Exception', //Exception class name
];

$db = new SafeMySQL($dbInfo);

//Change the SQL MODE
$res = $db->query("SELECT @@sql_mode as modes");
$row = mysqli_fetch_array($res);
$currentModes = $row['modes'];
$modesArray = explode(",", $currentModes);
$del_val = "ONLY_FULL_GROUP_BY";
if (($key = array_search($del_val, $modesArray)) !== false) {
    unset($modesArray[$key]);
}
$newModes = implode(",", $modesArray);
$res = $db->query("SET sql_mode = ?s;", $newModes);


//Secondary DB
$db2 = "";

function initiateDB2(MailServerNode $mailServerNode)
{
    global $db2;
//    $mysqli2 = mysqli_init();

    $dbInfo2 = [
        'host' => $mailServerNode->dbHostName,
        'user' => $mailServerNode->dbUserName,
        'pass' => $mailServerNode->dbPassword,
        'db' => $mailServerNode->dbName,
        'port' => $mailServerNode->dbPort,
        'socket' => null,
        'pconnect' => false,
        'charset' => 'utf8',
        'errmode' => 'exception', //error or exception
        'exception' => 'Exception', //Exception class name
    ];

    $db2 = new SafeMySQL($dbInfo2);

//Change the SQL MODE
    $res2 = $db2->query("SELECT @@sql_mode as modes");
    $row2 = mysqli_fetch_array($res2);
    $currentModes2 = $row2['modes'];
    $modesArray2 = explode(",", $currentModes2);
    $del_val2 = "ONLY_FULL_GROUP_BY";
    if (($key2 = array_search($del_val2, $modesArray2)) !== false) {
        unset($modesArray2[$key2]);
    }
    $newModes2 = implode(",", $modesArray2);
    $res2 = $db2->query("SET sql_mode = ?s;", $newModes2);
}



$HOST_NAME = $_SERVER['HTTP_HOST'];
$FULL_HOST_NAME = "https://" . $HOST_NAME . "/";



//include_once "inc_general_functions.php";

//Language Selector
$langArray = array('en','ar');

if(!isset($_SESSION['lang'])){
    $_SESSION['lang'] = "en";
}

if(isset($_GET['lang']))
{
    $lang = filter_var($_GET['lang'], FILTER_SANITIZE_STRING);
    if(in_array($lang, $langArray))
    {
        $_SESSION['lang'] = $lang;
    }
}

if(isset($_POST['lang']))
{
    $lang = filter_var($_POST['lang'], FILTER_SANITIZE_STRING);
    if(in_array($lang, $langArray))
    {
        $_SESSION['lang'] = $lang;
    }
}

$lang = $_SESSION['lang'];

$langid = "";

if($lang == "ar") $langid = "_ar"; else $langid = "";

if ( $_SESSION['lang'] == "en" ) {
    $locale = "";
}else
{
    $locale = "ar_SA";
}