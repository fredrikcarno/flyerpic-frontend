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
		$query	= Database::prepare($this->database, "SELECT id, title, description FROM ? WHERE id = '?' LIMIT 1", array(LYCHEE_TABLE_ALBUMS, $this->albumID));
		$albums = $this->database->query($query);
		$return = $albums->fetch_assoc();
		$return['userID']	= $this->getUserID();
		$return['content']	= [];

		# Get photos
		$query				= Database::prepare($this->database, "SELECT id, title, tags, public, star, album, thumbUrl FROM ? WHERE album = '?'", array(LYCHEE_TABLE_PHOTOS, $this->albumID));
		$photos				= $this->database->query($query);
		$previousPhotoID	= '';

		# Catch empty albums
		# Valid album must at least contain two photos, otherwise it's empty and not available
		if ($photos->num_rows<2) {

			Log::error($this->database, __METHOD__, __LINE__, 'Customer tried to take a look at an empty album (' . $this->albumID . ')');
			exit('Warning: Album empty');

		}

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

	public function getByCode($code) {

		# Check dependencies
		if (!isset($this->database)) {

			Log::error($this->database, __METHOD__, __LINE__, 'Database missing');
			exit('Error: Database missing');

		}

		# Search code
		$query	= Database::prepare($this->database, "SELECT id FROM ? WHERE title = '?' LIMIT 1", array(LYCHEE_TABLE_ALBUMS, $code));
		$result = $this->database->query($query);
		$return = $result->fetch_array();
		$id		= $return['id'];

		# Check if albumID valid
		if (!isset($id)||$id==='') return false;

		# Catch empty albums
		# Valid album must at least contain two photos, otherwise it's empty and not available
		$query	= Database::prepare($this->database, "SELECT id FROM ? WHERE album = '?'", array(LYCHEE_TABLE_PHOTOS, $id));
		$photos	= $this->database->query($query);

		if ($photos->num_rows<2) {

			Log::error($this->database, __METHOD__, __LINE__, 'Customer tried to take a look at an empty album (' . $this->albumID . ')');
			exit('Warning: Album empty');

		}

		return $id;

	}

	public function getID() {

		# Check dependencies
		if (!isset($this->albumID)) {

			Log::error($this->database, __METHOD__, __LINE__, 'albumID missing');
			return false;

		}

		return $this->albumID;

	}

	public function getUserID() {

		# Check dependencies
		if (!isset($this->database, $this->albumID)) {

			Log::error($this->database, __METHOD__, __LINE__, 'Database or albumID missing');
			exit('Error: Database or albumID missing');

		}

		$query	= Database::prepare($this->database, "SELECT * FROM ? WHERE id = '?' LIMIT 1", array(LYCHEE_TABLE_ALBUMS, $this->albumID));
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

		# Check dependencies
		if (!isset($this->database, $this->albumID)) {

			Log::error($this->database, __METHOD__, __LINE__, 'Database or albumID missing');
			exit('Error: Database or albumID missing');

		}

		$error	= false;

		# Set photos of album to payed
		$query	= Database::prepare($this->database, "SELECT id, tags FROM ? WHERE album = '?'", array(LYCHEE_TABLE_PHOTOS, $this->albumID));
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

		# Set album to payed
		$query	= Database::prepare($this->database, "UPDATE ? SET description = 'payed' WHERE id = '?'", array(LYCHEE_TABLE_ALBUMS, $this->albumID));
		$result	= $this->database->query($query);

		if (!$result||$error===true) {

			Log::notice($this->database, __METHOD__, __LINE__, 'Could not mark all photos as payed');
			return false;

		}

		return true;

	}

	public function download() {

		# Check dependencies
		if (!isset($this->database, $this->albumID)) {

			Log::error($this->database, __METHOD__, __LINE__, 'Database or albumID missing');
			exit('Error: Database or albumID missing');

		}

		# Photos query
		$photos		= Database::prepare($this->database, "SELECT title, url, tags FROM ? WHERE album = '?'", array(LYCHEE_TABLE_PHOTOS, $this->albumID));
		$photos		= $this->database->query($photos);

		# Set title
		$zipTitle	= 'Photo Session';
		$filename = LYCHEE_DATA . md5(microtime(true)) . '.zip';

		# Create zip
		$zip = new ZipArchive();
		if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
			Log::error($this->database, __METHOD__, __LINE__, 'Could not create ZipArchive');
			return false;
		}

		# Check if album empty
		if ($photos->num_rows==0) {
			Log::error($this->database, __METHOD__, __LINE__, 'Could not create ZipArchive without images');
			return false;
		}

		# Parse each path
		$i = 1;
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

			# Set title for photo
			$zipFileName = $zipTitle . '/' . $i . $extension;

			# Add photo to zip
			$zip->addFile(LYCHEE_UPLOADS_BIG . $photo->url, $zipFileName);

			# Increase photo number
			$i++;

		}

		# Finish zip
		$zip->close();

		# Send zip
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=\"$zipTitle.zip\"");
		header("Content-Length: " . filesize($filename));
		readfile($filename);

		# Delete zip
		unlink($filename);

		return true;

	}

}

?>