<?php

/**
 * @name		Config
 * @author		Tobias Reich
 * @copyright	2014 Tobias Reich
*/

// Version
$configVersion = '0.0.1';

// Lychee
$lychee = 'http://lychee.example.com'; # URL to the root of Lychee
$lychee_path = __DIR__ . '/../../lychee/'; # Path to the root of Lychee

// Database
$dbCredentials = array(
    'host'  	=> 'localhost', # Host of the Database
    'user'  	=> '', # Username of the database
    'pass'  	=> '', # Password of the Database
    'name'  	=> 'lychee', # Database name
    'prefix'	=> '' # Table prefix
);

// PayPal
$apiCredentials = array(
    'username'  => '',
    'password'  => '',
    'signature' => '',
    'appID'     => ''
);

?>