<?php
require __DIR__ . '/../vendor/autoload.php';

require '../helpers.php';

use Framework\Router;

//create custom outloader
// spl_autoload_register(function($class) {
//     $path = basePath('Framework/' . $class . '.php');
//     if(file_exists($path)) {
//         require $path;
//     }
// });

//Instatiating the router
$router = new Router();

//Get routes
$routes = require basePath('routes.php');

//Get the current uri and http method
//extract the path component from the URL, ?id= is the query string
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$method = $_SERVER['REQUEST_METHOD'];

//route the request
$router->route($uri, $method);
