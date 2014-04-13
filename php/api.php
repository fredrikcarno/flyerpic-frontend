<?php

/**
 * @name		API
 * @author		Tobias Reich
 * @copyright	2014 by Tobias Reich
 */

if (!empty($_POST['function'])) {

	require(__DIR__ . '/autoload.php');

	if (file_exists(__DIR__ . '/../data/config.php')) require(__DIR__ . '/../data/config.php');
	else exit('Error: Config not found!');

	// Connect to database
	$database = new Database($dbCredentials);

	switch ($_POST['function']) {

		case 'getLychee':		echo $lychee;
								break;

		case 'getUser':			if (isset($_POST['userID'])) {

									$user = new User($database->get(), $_POST['userID']);
									echo json_encode($user->get());

								}
								break;

		case 'getPayPalLink':	if (isset($_POST['albumID'])) {

									$album	= new Album($database->get(), $_POST['albumID']);
									$user	= new User($database->get(), $album->getUserID());
									$paypal	= new PayPal($apiCredentials);
									echo $paypal->getLink('album', $user->get());

								} else if (isset($_POST['photoID'])) {

									$photo	= new Photo($database->get(), $_POST['photoID']);
									$user	= new User($database->get(), $photo->getUserID());
									$paypal	= new PayPal($apiCredentials);
									echo $paypal->getLink('photo', $user->get());

								}
								break;

	}

} else {

	exit('Error: Parameter "function" not defined!');

}

?>