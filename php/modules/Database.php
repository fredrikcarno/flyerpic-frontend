<?php

###
# @name			DB Module
# @author		Tobias Reich
# @copyright	2014 by Tobias Reich
###

class Database {

	private $source = null;

	public function __construct($dbCredentials) {

		$database = new mysqli($dbCredentials['host'], $dbCredentials['user'], $dbCredentials['pass']);

		if ($database->connect_errno) {

			Log::error($this->database, __METHOD__, __LINE__, $database->connect_error);
			exit('Error: ' . $database->connect_error);

		}

		if (!$database->select_db($dbCredentials['name'])) exit('Error: Could not select database!');

		# Avoid sql injection on older MySQL versions
		if ($database->server_version<50500) $database->set_charset('GBK');

		# Save database
		$this->source = $database;

		return true;

	}

	public function get() {

		if (!isset($this->source)) {

			Log::notice($this->database, __METHOD__, __LINE__, 'Could not get and return database');
			return null;

		}

		return $this->source;

	}

}

?>