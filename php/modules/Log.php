<?php

###
# @name			Log Module
# @author		Tobias Reich
# @copyright	2014 by Tobias Reich
###

class Log {

	public static function notice($database, $function, $line, $text = '') {

		return Log::text($database, 'notice', $function, $line, $text);

	}

	public static function warning($database, $function, $line, $text = '') {

		return Log::text($database, 'warning', $function, $line, $text);

	}

	public static function error($database, $function, $line, $text = '') {

		return Log::text($database, 'error', $function, $line, $text);

	}

	public static function text($database, $type, $function, $line, $text = '') {

		# Get time
		$sysstamp = time();

		# Save in database
		$query	= Database::prepare($database, "INSERT INTO ? (time, type, function, line, text) VALUES ('?', '?', '?', '?', '?')", array(LYCHEE_TABLE_LOG, $sysstamp, $type, $function, $line, $text));
		$result	= $database->query($query);

		if (!$result) return false;
		return true;

	}

}

?>