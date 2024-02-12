<?php

use Framework\Database;

//get config of the database
$config = require basePath('config/db.php');

//instantiate databse
$db = new Database($config);

//fetch data with a query
$listings = $db->query('SELECT * FROM listings LIMIT 6')->fetchAll();

//make the listing variable accessible in view
loadView('listings/index',[
    'listings' => $listings
]);
