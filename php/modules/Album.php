<?php

###
# @name			Album Module
# @author		Tobias Reich
# @copyright	2014 by Tobias Reich
###

class Album {

	private $database	= null;
	private $albumID	= null;

	public function __construct($database, $albumID) {

		$this->database	= $database;
		$this->albumID	= $albumID;

	}

	public function getID() {

		if (!isset($this->albumID)) return false;
		return $this->albumID;

	}

	public function getUserID() {

		if (!isset($this->database, $this->albumID)) exit('Error: Database or albumID missing');

		$query	= "SELECT * FROM lychee_albums WHERE id = '$this->albumID' LIMIT 1;";
		$result	= $this->database->query($query);
		$return	= $result->fetch_assoc();

		if (!isset($return['title'])) exit('Error: Album title not found');
		else return substr($return['title'], 0, 2);

	}

	public function setPayment() {

		if (!isset($this->database, $this->albumID)) exit('Error: Database or albumID missing');

		$error	= false;

		$query	= "SELECT id, tags FROM lychee_photos WHERE album = '$this->albumID';";
		$result	= $this->database->query($query);

		# For each photo
		while ($photo = $result->fetch_object()) {

			$photoObj	= new Photo($this->database, $photo->id);
			$setResult	= $photoObj->setPayment();

			if ($setResult!==true) $error = true;

		}

		if ($error===true) return false;
		return true;

	}

}

?>