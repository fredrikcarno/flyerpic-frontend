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
		if (!isset($this->database, $this->albumID)) {

			Log::error($this->database, __METHOD__, __LINE__, 'Database or albumID missing');
			exit('Error: Database or albumID missing');

		}

		# Get album information
		$albums = $this->database->query("SELECT id, title, description FROM lychee_albums WHERE id = '$this->albumID' LIMIT 1;");
		$return = $albums->fetch_assoc();
		$return['userID']	= $this->getUserID();
		$return['content']	= [];

		# Get photos
		$photos				= $this->database->query("SELECT id, title, tags, public, star, album, thumbUrl FROM lychee_photos WHERE album = '$this->albumID'");
		$previousPhotoID	= '';
		while ($photo = $photos->fetch_assoc()) {

			if (strpos($photo['tags'], 'watermarked')!==false) {

				# Is a watermarked photo
				# Check if user bought the photo. If so, don't show the watermarked-photo.
				if (strpos($photo['tags'], 'payed')!==false) {

					# Photo bought
					continue;

				}

			} else {

				# Is *not* a watermarked photo
				# Check if user bought the photo. If not, don't show the original-photo.
				if (strpos($photo['tags'], 'payed')===false) {

					# Photo *not* bought
					continue;

				}

			}

			# Parse
			$photo['previousPhoto']		= $previousPhotoID;
			$photo['nextPhoto']			= '';
			$photo['thumbUrl']			= 'uploads/thumb/' . $photo['thumbUrl'];

			if ($previousPhotoID!=='') $return['content'][$previousPhotoID]['nextPhoto'] = $photo['id'];
			$previousPhotoID = $photo['id'];

			# Add to return
			$return['content'][$photo['id']] = $photo;

		}

		if (count($return['content'])<=0) {

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

		if (!isset($this->albumID)) {

			Log::error($this->database, __METHOD__, __LINE__, 'albumID missing');
			return false;

		}

		return $this->albumID;

	}

	public function getUserID() {

		if (!isset($this->database, $this->albumID)) {

			Log::error($this->database, __METHOD__, __LINE__, 'Database or albumID missing');
			exit('Error: Database or albumID missing');

		}

		$query	= "SELECT * FROM lychee_albums WHERE id = '$this->albumID' LIMIT 1;";
		$result	= $this->database->query($query);
		$return	= $result->fetch_assoc();

		if (!isset($return['title'])) {

			Log::error($this->database, __METHOD__, __LINE__, 'Album title not found');
			exit('Error: Album title not found');

		} else {

			$title = substr($return['title'], 0, 2);

			$from = array("a", "b", "d", "e", "f", "g", "h", "j", "k", "m");
			$to = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");

			$title = str_replace($from, $to, $title);

			return $title;

		};

	}

	public function setPayment() {

		if (!isset($this->database, $this->albumID)) {

			Log::error($this->database, __METHOD__, __LINE__, 'Database or albumID missing');
			exit('Error: Database or albumID missing');

		}

		$error	= false;

		$query	= "SELECT id, tags FROM lychee_photos WHERE album = '$this->albumID';";
		$result	= $this->database->query($query);

		# For each photo
		while ($photo = $result->fetch_object()) {

			$photoObj	= new Photo($this->database, $photo->id);
			$setResult	= $photoObj->setPayment();

			if ($setResult!==true) {

				Log::notice($this->database, __METHOD__, __LINE__, 'Could not mark photo as payed');
				$error = true;

			}

		}

		$query	= "UPDATE lychee_albums SET description = 'payed' WHERE id = '$this->albumID';";
		$result	= $this->database->query($query);

		if (!$result||$error===true) {

			Log::notice($this->database, __METHOD__, __LINE__, 'Could not mark all photos as payed');
			return false;

		}

		return true;

	}

}

?>