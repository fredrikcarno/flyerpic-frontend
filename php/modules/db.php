<?php

/**
 * @name		DB Module
 * @author		Tobias Reich
 * @copyright	2014 by Tobias Reich
 */

class Database {

	private $source = null;

	function __construct($dbUser, $dbPassword, $dbHost, $dbName) {

		$database = new mysqli($dbHost, $dbUser, $dbPassword);

		if ($database->connect_errno) exit('Error: ' . $database->connect_error);

		if (!$database->select_db($dbName)) exit('Error: Could not select database!');

		// Avoid sql injection on older MySQL versions
		if ($database->server_version<50500) $database->set_charset('GBK');

		// Save database
		$this->source = $database;

		return true;

	}

	function get() {

		if (!isset($this->source)) return null;
		else return $this->source;

	}

	function close() {

		if (!isset($this->source)) return false;

		$this->source->close();

		return true;

	}

}

?>