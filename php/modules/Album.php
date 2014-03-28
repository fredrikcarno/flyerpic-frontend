<?php

/**
 * @name		Album Module
 * @author		Tobias Reich
 * @copyright	2014 by Tobias Reich
 */

class Album {

	private $database = null;
	private $albumID = null;

	function __construct($database, $albumID) {

		$this->database = $database;
		$this->albumID = $albumID;

	}

	function getUserID() {

		if (!isset($this->database, $this->albumID)) exit('Error: Database or albumID missing');

		$query	= "SELECT * FROM lychee_albums WHERE id = '$this->albumID' LIMIT 1;";
		$result = $this->database->query($query);
		$return = $result->fetch_assoc();

		if (!isset($return['title'])) exit('Error: Album title not found');
		else return substr($return['title'], 0, 2);

	}

}

?>