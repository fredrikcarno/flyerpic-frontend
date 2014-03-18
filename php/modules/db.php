<?php

/**
 * @name		DB Module
 * @author		Tobias Reich
 * @copyright	2014 by Tobias Reich
 */

function dbConnect() {

	global $dbUser, $dbPassword, $dbHost, $dbName;

	$database = new mysqli($dbHost, $dbUser, $dbPassword);

	if ($database->connect_errno) exit('Error: ' . $database->connect_error);

	if (!$database->select_db($dbName)) exit('Error: Could not select database!');

	// Avoid sql injection on older MySQL versions
	if ($database->server_version<50500) $database->set_charset('GBK');

	return $database;

}

function dbClose() {

	global $database;

	if (!$database->close()) exit('Error: Closing the connection failed!');

	return true;

}

?>