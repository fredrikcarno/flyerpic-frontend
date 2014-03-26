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
	$database	= new Database($dbUser, $dbPassword, $dbHost, $dbName);
	$ini		= parse_ini_file(PP_CONFIG_PATH . 'sdk_config.ini');

	switch ($_POST['function']) {

		case 'getLychee':		echo $lychee;
								break;

		case 'getUser':			if (isset($_POST['userID'])) {
									$user = new User($database->get(), $_POST['userID']);
									echo json_encode($user->get());
								}
								break;

		case 'getPayPalLink':	if (isset($_POST['albumID']))
									echo getPayPalLink($_POST['albumID']);
								break;

	}

} else {

	exit('Error: Parameter "function" not defined!');

}

?>