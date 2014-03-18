<?php

/**
 * @name		API
 * @author		Tobias Reich
 * @copyright	2014 by Tobias Reich
 */

if (!empty($_POST['function'])) {

	if (file_exists('../data/config.php')) require('../data/config.php');
	else exit('Error: Config not found!');

	require('modules/db.php');
	require('modules/user.php');

	// Connect
	$database = dbConnect();

	switch ($_POST['function']) {

		case 'getLychee':		echo $lychee;
								break;

		case 'getUser':			if (isset($_POST['userID']))
									echo json_encode(getUser($_POST['userID']));
								break;

	}

} else {

	exit('Error: No permission!');

}

?>