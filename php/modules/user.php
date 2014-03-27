<?php

/**
 * @name		User Module
 * @author		Tobias Reich
 * @copyright	2014 by Tobias Reich
 */

class User {

	private $database = null;
	private $userID = null;

	function __construct($database, $userID) {

		$this->database = $database;
		$this->userID = $userID;

	}

	function get() {

		if (!isset($this->database, $this->userID)) return null;

		$query	= "SELECT * FROM lychee_users WHERE id = '$this->userID';";
		$result = $this->database->query($query);
		$return = $result->fetch_assoc();

		return $return;

	}

}

?>