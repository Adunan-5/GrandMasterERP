<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
include_once __DIR__ . "/../autoload.php";
require __DIR__ . '/../vendor/autoload.php';  // Autoload dependencies

define("RUNNING_ON_SERVER", $_SERVER['SERVER_NAME']);

switch (RUNNING_ON_SERVER) {
    case "erp.ggm.com.co":
        define("IS_LIVE", true);
        define("IS_BETA", false);
        define("IS_LOCAL", false);
        break;

    case "grandmastererp.zeenara.com":
        define("IS_LIVE", false);
        define("IS_BETA", false);
        define("IS_LOCAL", true);
        break;

    case "gmerp.local":
        define("IS_LIVE", false);
        define("IS_BETA", false);
        define("IS_LOCAL", true);
        break;

    case "gmmnewerp.betaspace.dev":
        define("IS_LIVE", false);
        define("IS_BETA", true);
        define("IS_LOCAL", false);
        break;
}


//if (IS_LIVE) {
//Eyewitness Zeenara LIVE
//    \Sentry\init([
//        'dsn'                  => 'https://28922698f127cd71e1d532a9899e5ef7@eyewitness.zeenara.com/4',
//        // Specify a fixed sample rate
//        'traces_sample_rate'   => 1.0,
//        'profiles_sample_rate' => 1.0,
//    ]);
//}

//if (IS_LOCAL || IS_BETA) {
//Eyewitness Zeenara Local / Beta
//    \Sentry\init([
//        'dsn'                  => 'https://b62e9e034d3fd24c8f0f977c01c68d87@eyewitness.zeenara.com/2',
//        // Specify a fixed sample rate
//        'traces_sample_rate'   => 1.0,
//        'profiles_sample_rate' => 1.0,
//    ]);
//}


//Cloud Sentry
//\Sentry\init([
//    'dsn' => 'https://fbd68176cf2ed248a40b1873b5db04ac@o968529.ingest.us.sentry.io/4508767669256192',
//    // Specify a fixed sample rate
//    'traces_sample_rate' => 1.0,
//    // Set a sampling rate for profiling - this is relative to traces_sample_rate
//    'profiles_sample_rate' => 1.0,
//]);

session_start();
include_once __DIR__ . "/sessions.php";
include_once __DIR__ . "/globals.php";
include_once __DIR__ . "/inc_opendb.php";
include_once __DIR__ . "/helper.php";
include_once __DIR__ . "/debug_logger.php";
include_once __DIR__ . "/email_sender.php";
include_once __DIR__ . "/email_credentials.php";