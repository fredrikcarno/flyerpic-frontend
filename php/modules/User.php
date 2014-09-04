<?php

###
# @name			User Module
# @author		Tobias Reich
# @copyright	2014 by Tobias Reich
###

class User {

	private $database	= null;
	private $userID		= null;

	public function __construct($database, $userID) {

		$this->database	= $database;
		$this->userID	= $userID;

	}

	public function get() {

		if (!isset($this->database, $this->userID)) {

			Log::notice($this->database, __METHOD__, __LINE__, 'Database or userID missing');
			return null;

		}

		$id = intval($this->userID);

		$query	= "SELECT * FROM lychee_users WHERE id = '$id' LIMIT 1;";
		$result	= $this->database->query($query);
		$return	= $result->fetch_assoc();

		return $return;

	}

}

?>