<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';

require '../helpers.php';

use Framework\Router;

//Instatiating the router
$router = new Router();

//Get routes
$routes = require basePath('routes.php');

//Get the current uri and http method
//extract the path component from the URL, ?id= is the query string
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

//route the request
$router->route($uri);
