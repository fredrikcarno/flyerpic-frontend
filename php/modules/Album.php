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
	public function get() {

		# Check dependencies
		if (!isset($this->database, $this->albumID)) exit('Error: Database or albumID missing');

		# Get album information
		$albums = $this->database->query("SELECT * FROM lychee_albums WHERE id = '$this->albumID' LIMIT 1;");
		$return = $albums->fetch_assoc();
		$return['sysdate']		= date('d M. Y', $return['sysstamp']);
		$return['password']		= ($return['password']=='' ? false : true);

		# Get photos
		$photos				= $this->database->query("SELECT id, title, tags, public, star, album, thumbUrl FROM lychee_photos WHERE album = '$this->albumID'");
		$previousPhotoID	= '';
		while ($photo = $photos->fetch_assoc()) {

			# Parse
			$photo['sysdate']			= date('d F Y', substr($photo['id'], 0, -4));
			$photo['previousPhoto']		= $previousPhotoID;
			$photo['nextPhoto']		= '';

			if ($previousPhotoID!=='') $return['content'][$previousPhotoID]['nextPhoto'] = $photo['id'];
			$previousPhotoID = $photo['id'];

			# Add to return
			$return['content'][$photo['id']] = $photo;

		}

		if ($photos->num_rows===0) {

			# Album empty
			$return['content'] = false;

		} else {

			# Enable next and previous for the first and last photo
			$lastElement	= end($return['content']);
			$lastElementId	= $lastElement['id'];
			$firstElement	= reset($return['content']);
			$firstElementId	= $firstElement['id'];

			if ($lastElementId!==$firstElementId) {
				$return['content'][$lastElementId]['nextPhoto']			= $firstElementId;
				$return['content'][$firstElementId]['previousPhoto']	= $lastElementId;
			}

		}

		$return['id']	= $this->albumID;
		$return['num']	= $photos->num_rows;

		return $return;

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