<?php

/**
 * @name		Photo Module
 * @author		Tobias Reich
 * @copyright	2014 by Tobias Reich
 */

class Photo {

	private $database = null;
	private $photoID = null;

	function __construct($database, $photoID) {

		$this->database = $database;
		$this->photoID = $photoID;

	}

	function getUserID() {

		if (!isset($this->database, $this->photoID)) exit('Error: Database or photoID missing');

		$query	= "SELECT * FROM lychee_photos WHERE id = '$this->photoID' LIMIT 1;";
		$result = $this->database->query($query);
		$return = $result->fetch_assoc();

		$album		= new Album($this->database, $return['album']);
		$albumID	= $album->getUserID();

		return $albumID;

	}

}

?>