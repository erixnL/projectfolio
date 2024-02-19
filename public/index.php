<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';

use Framework\Router;
use Framework\Session;

Session::start();

require '../helpers.php';

//Instatiating the router
$router = new Router();

//Get routes
$routes = require basePath('routes.php');

//Get the current uri and http method
//extract the path component from the URL, ?id= is the query string
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

//route the request
$router->route($uri);
