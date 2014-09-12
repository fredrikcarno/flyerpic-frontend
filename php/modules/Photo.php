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

		# Check dependencies
		if (!isset($this->photoID)) {

			Log::notice($this->database, __METHOD__, __LINE__, 'Could not return id of photo');
			return false;

		}

		return $this->photoID;

	}

	public function getUserID() {

		# Check dependencies
		if (!isset($this->database, $this->photoID)) {

			Log::error($this->database, __METHOD__, __LINE__, 'Database or photoID missing');
			exit('Error: Database or photoID missing');

		}

		$query	= Database::prepare($this->database, "SELECT * FROM ? WHERE id = '?' LIMIT 1", array(LYCHEE_TABLE_PHOTOS, $this->photoID));
		$result	= $this->database->query($query);
		$return	= $result->fetch_assoc();

		$album		= new Album($this->database, $return['album']);
		$albumID	= $album->getUserID();

		return $albumID;

	}

	public function setPayment() {

		# Check dependencies
		if (!isset($this->database, $this->photoID)) {

			Log::notice($this->database, __METHOD__, __LINE__, 'Missing database or photoID');
			return false;

		}

		# Get photos which belong together
		$query	= Database::prepare($this->database, "SELECT tags FROM ? WHERE id = '?'", array(LYCHEE_TABLE_PHOTOS, $this->photoID));
		$result	= $this->database->query($query);
		$result	= $result->fetch_assoc();
		$tags	= @split(',', $result['tags']);
		$tag	= @$tags[0];

		if (!isset($tag)||$tag==='') {

			Log::notice($this->database, __METHOD__, __LINE__, 'Could not find photos which belong together to mark them as payed');
			return false;

		}

		$query	= Database::prepare($this->database, "UPDATE ? SET tags = CONCAT(tags, ',payed') WHERE tags LIKE '?%' LIMIT 2", array(LYCHEE_TABLE_PHOTOS, $tag));
		$result	= $this->database->query($query);

		if (!$result) return false;
		return true;

	}

	public function download() {

		# Check dependencies
		if (!isset($this->database, $this->photoID)) {

			Log::notice($this->database, __METHOD__, __LINE__, 'Missing database or photoID');
			exit('Error: Missing database or photoID');

		}

		# Get photos which belong together
		$query	= Database::prepare($this->database, "SELECT tags FROM ? WHERE id = '?'", array(LYCHEE_TABLE_PHOTOS, $this->photoID));
		$result	= $this->database->query($query);
		$result	= $result->fetch_assoc();
		$tags	= @split(',', $result['tags']);
		$tag	= @$tags[0];

		if (!isset($tag)||$tag==='') {

			Log::notice($this->database, __METHOD__, __LINE__, 'Could not find photos which belong together');
			return false;

		}

		# Get photo
		$query	= Database::prepare($this->database, "SELECT url, tags FROM ? WHERE tags LIKE '?%' LIMIT 2", array(LYCHEE_TABLE_PHOTOS, $tag));
		$photos	= $this->database->query($query);

		while ($photo = $photos->fetch_object()) {

			# Check which version should be added to the ZIP and
			# which should be skipped
			if (strpos($photo->tags, 'watermarked')!==false) {

				# Is a watermarked photo
				# Check if user bought the photo. If so, don't show the watermarked-photo.
				if (strpos($photo->tags, 'payed')!==false) {

					# Photo bought
					continue;

				}

			} else {

				# Is *not* a watermarked photo
				# Check if user bought the photo. If not, don't show the original-photo.
				if (strpos($photo->tags, 'payed')===false) {

					# Photo *not* bought
					continue;

				}

			}

			# Get extension
			$extension = strpos($photo->url, '.') !== false
				? strrchr($photo->url, '.')
				: '';

			# Set title
			$photo->title = 'Photo Download';

			# Set headers
			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"" . $photo->title . $extension . "\"");
			header("Content-Length: " . filesize(LYCHEE_UPLOADS_BIG . $photo->url));

			# Send file
			readfile(LYCHEE_UPLOADS_BIG . $photo->url);

			return true;

		}

	}

}

?>