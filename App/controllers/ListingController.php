<?php

namespace App\Controllers;

use Framework\Database;

class ListingController {
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
        $listings = $this->db->query('SELECT * FROM listings')->fetchAll();

        //make the listing variable accessible in view
        loadView('home',[
            'listings' => $listings
        ]);
    }

    public function create() {
        loadView('listings/create');
    }
    
    public function show() {
        $id = $_GET['id'] ?? '';

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();


        loadView('listings/show', [
            'listing' => $listing
        ]);
    }

}