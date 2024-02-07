<?php

require '../helpers.php';

require basePath('Database.php');

require basePath('Router.php');

//Instatiating the router
$router = new Router();

//Get routes
$routes = require basePath('routes.php');

//Get the current uri and http method
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

//route the request
$router->route($uri, $method);
