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

		# Check dependencies
		if (!isset($this->database, $this->userID)) {

			Log::notice($this->database, __METHOD__, __LINE__, 'Database or userID missing');
			return null;

		}

		$id = intval($this->userID);

		$query	= Database::prepare($this->database, "SELECT * FROM lychee_users WHERE id = '?' LIMIT 1", array($id));
		$result	= $this->database->query($query);
		$return	= $result->fetch_assoc();

		# Remove password from return
		$return['password'] = null;

		return $return;

	}

	public function setMail($mail, $code) {

		# Check dependencies
		if (!isset($this->database)) {

			Log::error($this->database, __METHOD__, __LINE__, 'Database missing');
			exit('Error: Database missing');

		}

		$query	= Database::prepare($this->database, "INSERT INTO lychee_mails (mail, code) VALUES ('?', '?')", array($mail, $code));
		$result	= $this->database->query($query);

		if (!$result) {

			Log::notice($this->database, __METHOD__, __LINE__, $this->database->error);
			return false;

		}
		return true;

	}

}

?>