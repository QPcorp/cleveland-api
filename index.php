<?php

//Set Access Origins (Production should be limited, local can be *)
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");

//Options handler for Javascript Calls
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']) && (   
       $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] == 'POST' || 
       $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] == 'DELETE' || 
       $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] == 'PUT' )) {
             header('Access-Control-Allow-Origin: *');
             header("Access-Control-Allow-Credentials: true"); 
             header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
             header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');
             header('Access-Control-Max-Age: 86400'); 
      }
  exit;
}

//Load Libraries
require 'libs/autoload.php';
require 'libs/functions.php';

//Application Setup
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$app->contentType('application/json');
$app->expires('-1000000');

//Global App Variables
$req = $app->request;
$rootUri = $req->getRootUri();
$resourceUri = $req->getResourceUri();

//Services
require_once 'services/mysql-connect.php';
require_once 'services/auth.php';

//Routes
require('routes/admin.php');
require('routes/login.php');
require('routes/status.php');
require('routes/employees.php');
require('routes/locations.php');
require('routes/reports.php');
require('routes/lpn.php');

$app->run();