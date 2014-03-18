<?php

/**
 * @name		User Module
 * @author		Tobias Reich
 * @copyright	2014 by Tobias Reich
 */

function getUser($userID) {

	global $database;

	$query	= "SELECT * FROM lychee_users WHERE id = '$userID';";
	$result = $database->query($query);
	$return = $result->fetch_array();

	return $return;

}