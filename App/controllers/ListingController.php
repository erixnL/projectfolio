<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Session;
use Framework\Validation;
use Framework\Authorization;

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
        $listings = $this->db->query('SELECT * FROM listings ORDER BY created_at DESC')->fetchAll();

        //make the listing variable accessible in view
        loadView('listings/index',[
            'listings' => $listings
        ]);
    }

    public function create() {
        loadView('listings/create');
    }
    /**
     * Show a single listing
     * 
     * @param array @params
     * @return void
     */
    public function show($params) {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();
        //check if the listing exits
        if(!$listing) {
            ErrorController::notFound('Listing not found.');
            return;
        }

        loadView('listings/show', [
            'listing' => $listing
        ]);
    }

    /**
     * Store data in database
     * 
     * @return void
     */
    public function store () {
        //all the data should be get in $_POST super global 
        $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city',
        'state', 'phone', 'email', 'requirements', 'benefits'];
        //takes in two arrays and return a new array as long as the keys are in both arrays
        //array flip reverse the values to be keys and keys to be values
        $newListingData = array_intersect_key($_POST, array_flip($allowedFields));
        $newListingData['user_id'] = Session::get('user')['id'];
        //run sanitize function on every item
        $newListingData = array_map('sanitize', $newListingData);

        $requireFields = ['title', 'description', 'salary','email', 'city', 'state'];

        $errors = [];
        
        foreach($requireFields as $field) {
            if(empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
                $errors[$field] = ucfirst($field). ' is required';
            };
        }

        if (!empty($errors)) {
            // Reload view with errors
            loadView('listings/create', [
                'errors'=> $errors,
                'listing' => $newListingData
            ]);
        } else {
            // Submit data
        //     $this->db->query('INSERT INTO listings (title, description, salary, tags, company, address,
        //     city, state, phone, email, requirements, benefits, user_id) VALUES (:title, :description, :salary, :tags, :company, :address,
        //     :city, :state, :phone, :email, :requirements, :benefits, :user_id)', $newListingData);
        // }
            $fields = [];
            foreach($newListingData as $field => $value) {
                $fields[] = $field;
            }

            //take an array and turn it into a string
            $fields = implode(', ', $fields);
            $values = [];

            foreach($newListingData as $field => $value) {
                // convert empty strings to null
                if($value === '') {
                    $newListingData[$field] = null;
                }
                $values[] = ':'. $field;
            }

            $values = implode(', ', $values);

            $query = "INSERT INTO listings ({$fields}) VALUES ({$values})";

            Session::setFlashMessage('success_message', 'Listing has been added successful');

            $this->db->query($query, $newListingData);
            // redirect to listings after submit the data
            redirect('/listings');
    }
    }
    /**
     * Delete a listing
     * 
     * @param array $params
     * @return void
     */
    public function destroy($params) {
        $id = $params['id'];
        $params = [
            'id'=>$id
        ];
        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        if(!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }
        //Authorization
        if(!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'You are not authorized to delete this listing');
            return redirect('/listings/' . $listing->id);
        }

        $this->db->query('DELETE FROM listings WHERE id = :id', $params);
        //Set flash message 
        Session::setFlashMessage('success_message', 'Listing deleted successful');
        redirect('/listings');
    }
    /**
     * Show the listing edit form
     *
     * @param array $params
     * @return void
     */
    public function edit($params) {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();
        //check if the listing exits
        if(!$listing) {
            ErrorController::notFound('Listing not found.');
            return;
        };

        //Authorization
        if(!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'You are not authorized to delete this listing');
            return redirect('/listings/' . $listing->id);
        }

        loadView('listings/edit', [
            'listing' => $listing
        ]);
    }

    /**
     * Update a listing
     * 
     * @param array $params
     * @return void
     */
    public function update($params) {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();
        //check if the listing exits
        if(!$listing) {
            ErrorController::notFound('Listing not found.');
            return;
        }

        //Authorization
        if(!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'You are not authorized to delete this listing');
            return redirect('/listings/' . $listing->id);
        }

        $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city',
        'state', 'phone', 'email', 'requirements', 'benefits'];

        $updatedValues = [];

        $updatedValues = array_intersect_key($_POST, array_flip($allowedFields));
        $updatedValues = array_map('sanitize', $updatedValues);

        $requireFields = ['title', 'description', 'salary', 'email','city', 'state'];

        $errors = [];
        foreach($requireFields as $field) {
            if (empty($updatedValues[$field]) || !Validation::string($updatedValues[$field])) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }

        if (!empty($errors)) {
            loadView('listings/edit', [
                'listing' => $listing,
                'errors' => $errors
            ]);
            exit;
        } else {
            $updateFields = [];
            foreach(array_keys($updatedValues) as $field) {
                $updateFields[] = "{$field} = :{$field}";
            }
            $updateFields = implode(', ', $updateFields);
            $updateQuery = "UPDATE listings SET $updateFields WHERE id = :id";
            $updatedValues['id'] = $id;
            $this->db->query($updateQuery, $updatedValues);

            Session::setFlashMessage('success_message', 'Listing updated');

            redirect('/listings/' . $id);
        }
        
    }
}