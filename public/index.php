<?php

require '../helpers.php';

require basePath('Database.php');

require basePath('Router.php');

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
