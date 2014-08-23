<?php

###
# @name			Photo Module
# @author		Tobias Reich
# @copyright	2014 by Tobias Reich
###

class Photo {

	private $database	= null;
	private $photoID	= null;

	public function __construct($database, $photoID) {

		$this->database	= $database;
		$this->photoID	= $photoID;

	}

	public function getID() {

		if (!isset($this->photoID)) return false;
		return $this->photoID;

	}

	public function getUserID() {

		if (!isset($this->database, $this->photoID)) exit('Error: Database or photoID missing');

		$query	= "SELECT * FROM lychee_photos WHERE id = '$this->photoID' LIMIT 1;";
		$result	= $this->database->query($query);
		$return	= $result->fetch_assoc();

		$album		= new Album($this->database, $return['album']);
		$albumID	= $album->getUserID();

		return $albumID;

	}

	public function setPayment() {

		if (!isset($this->database, $this->photoID)) return false;

		$query	= "SELECT tags FROM lychee_photos WHERE id = '$this->photoID';";
		$result	= $this->database->query($query);
		$result	= $result->fetch_assoc();
		$tags	= @split(',', $result['tags']);
		$tag	= @$tags[0];

		if (!isset($tag)||$tag==='') return false;

		$query	= "UPDATE lychee_photos SET tags = CONCAT(tags, ',payed') WHERE tags LIKE '$tag%';";
		$result	= $this->database->query($query);

		if (!$result) return false;
		return true;

	}

}

?>