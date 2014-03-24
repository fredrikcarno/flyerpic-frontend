<?php

/**
 * @name		API
 * @author		Tobias Reich
 * @copyright	2014 by Tobias Reich
 */

if (!empty($_POST['function'])) {

	if (file_exists('../data/config.php')) require('../data/config.php');
	else exit('Error: Config not found!');

	define('PP_CONFIG_PATH', '../data/' . $ppConfig);

	require '../vendor/autoload.php';
	require 'modules/db.php';
	require 'modules/user.php';
	require 'modules/paypal.php';

	// Connect
	$database	= dbConnect();
	$ini			= parse_ini_file(PP_CONFIG_PATH . 'sdk_config.ini');

	switch ($_POST['function']) {

		case 'getLychee':		echo $lychee;
								break;

		case 'getUser':			if (isset($_POST['userID']))
									echo json_encode(getUser($_POST['userID']));
								break;

	}

	pay();

} else {

	exit('Error: No permission!');

}

?>