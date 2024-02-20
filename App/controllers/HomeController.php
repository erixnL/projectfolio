<?php

namespace App\Controllers;

use Framework\Database;

class HomeController {
    protected $db;

    public function __construct()
    {
        //get config of the database
        $config = require basePath('config/db.php');

        //instantiate databse
        $this->db = new Database($config);
    }

    public function index() {
        //fetch data with a query
        $listings = $this->db->query('SELECT * FROM listings ORDER BY created_at DESC LIMIT 6')->fetchAll();

        //make the listing variable accessible in view
        loadView('home',[
            'listings' => $listings
]);
        
    }
}