<?php

###
# @name			DB Module
# @author		Tobias Reich
# @copyright	2014 by Tobias Reich
###

class Database {

	private $source = null;

	public function __construct($dbCredentials) {

		$database = new mysqli($dbCredentials['host'], $dbCredentials['user'], $dbCredentials['pass']);

		if ($database->connect_errno) {

			Log::error($this->database, __METHOD__, __LINE__, $database->connect_error);
			exit('Error: ' . $database->connect_error);

		}

		if (!$database->select_db($dbCredentials['name'])) exit('Error: Could not select database!');

		# Avoid sql injection on older MySQL versions
		if ($database->server_version<50500) $database->set_charset('GBK');
		else $database->set_charset('utf8');

		# Save database
		$this->source = $database;

		return true;

	}

	public function get() {

		if (!isset($this->source)) {

			Log::notice($this->database, __METHOD__, __LINE__, 'Could not get and return database');
			return null;

		}

		return $this->source;

	}

	static function prepare($database, $query, $data) {

		# Check dependencies
		if (!isset($database, $query, $data)) {

			Log::error($this->database, __METHOD__, __LINE__, 'Params for preparation missing');
			exit('Error: Params for preparation missing');

		}

		# Count the number of placeholders and compare it with the number of arguments
		# If it doesn't match, calculate the difference and skip this number of placeholders before starting the replacement
		# This avoids problems with placeholders in user-input
		# $skip = Number of placeholders which need to be skipped
		$skip	= 0;
		$num	= array(
			'placeholder'	=> substr_count($query, '?'),
			'data'			=> count($data)
		);

		if (($num['data']-$num['placeholder'])<0) Log::notice($database, __METHOD__, __LINE__, 'Could not completely prepare query. Query has more placeholders than values.');

		foreach ($data as $value) {

			# Escape
			$value = mysqli_real_escape_string($database, $value);

			# Recalculate number of placeholders
			$num['placeholder'] = substr_count($query, '?');

			# Calculate number of skips
			if ($num['placeholder']>$num['data']) $skip = $num['placeholder'] - $num['data'];

			if ($skip>0) {

				# Need to skip $skip placeholders, because the user input contained placeholders
				# Calculate a substring which does not contain the user placeholders
				# 1 or -1 is the length of the placeholder (placeholder = ?)

				$pos = -1;
				for ($i=$skip; $i>0; $i--) $pos = strpos($query, '?', $pos + 1);
				$pos++;

				$temp	= substr($query, 0, $pos); # First part of $query
				$query	= substr($query, $pos); # Last part of $query

			}

			# Replace
			$query = preg_replace('/\?/', $value, $query, 1);

			if ($skip>0) {

				# Reassemble the parts of $query
				$query = $temp . $query;

			}

			# Reset skip
			$skip = 0;

			# Decrease number of data elements
			$num['data']--;

		}

		return $query;

	}

}

?>