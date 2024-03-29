<?php

###
# @name			API
# @author		Tobias Reich
# @copyright	2014 by Tobias Reich
###

if ((isset($_POST['function'])&&!empty($_POST['function']))||
	(isset($_GET['function'])&&!empty($_GET['function']))) {

	session_start();

	require('autoload.php');

	if (file_exists(__DIR__ . '/../data/config.php')) require(__DIR__ . '/../data/config.php');
	else exit('Error: Config not found!');

	if (file_exists($lychee_path  . 'php/define.php')) require($lychee_path  . 'php/define.php');
	else exit('Error: Define not found!');

	defineTablePrefix($dbCredentials['prefix']);

	# Connect to database
	$database = new Database($dbCredentials);

	# Function for switch statement
	if (isset($_POST['function'])) $fn = $_POST['function'];
	else $fn = $_GET['function'];

	switch ($fn) {

		case 'getLychee':		echo $lychee;
								break;

		case 'getUser':			if (isset($_POST['userID'])) {

									$user = new User($database->get(), $_POST['userID']);
									echo json_encode($user->get());

								}
								break;

		case 'getAlbum':		if (isset($_POST['albumID'])) {

									$album	= new Album($database->get(), $_POST['albumID']);
									echo json_encode($album->get());

								}
								break;

		case 'getAlbumArchive':	if (isset($_GET['albumID'])) {

									$album	= new Album($database->get(), $_GET['albumID']);
									$album->download();

								}
								break;

		case 'getPhotoArchive':	if (isset($_GET['photoID'])) {

									$photo	= new Photo($database->get(), $_GET['photoID']);
									$photo->download();

								}
								break;

		case 'getPayPalLink':	$_SESSION['url'] = $_SERVER['HTTP_REFERER'];

								if (isset($_POST['albumID'])&&!isset($_POST['photoID'])) {

									# Generate album buy link
									$album	= new Album($database->get(), $_POST['albumID']);
									$user	= new User($database->get(), $album->getUserID());
									$paypal	= new PayPal($database->get(), $apiCredentials);
									echo $paypal->getLink('album', $user->get(), $album->getID());

								} else if (isset($_POST['albumID'], $_POST['photoID'])) {

									# Generate photo buy link
									$album	= new Album($database->get(), $_POST['albumID']);
									$photo	= new Photo($database->get(), $_POST['photoID']);
									$user	= new User($database->get(), $photo->getUserID());
									$paypal	= new PayPal($database->get(), $apiCredentials);
									echo $paypal->getLink('photo', $user->get(), $album->getID(), $photo->getID());

								} else {

									exit('Error: AlbumID or PhotoID not found');

								}
								break;

		case 'setPayment':	if (isset($_SESSION['payKey'])&&$_SESSION['payKey']!=='') {

								if (!isset($_SESSION['payType']))
									exit('Error: Type of payment not found. Please contact the support with this message.');

								if ($_SESSION['payType']==='album') {

									$album	= new Album($database->get(), $_SESSION['payAlbumID']);
									$paypal	= new PayPal($database->get(), $apiCredentials);
									$payed	= $paypal->checkPayment($_SESSION['payKey']);

									if ($payed===true) $result = $album->setPayment();
									else $result = false;

									if ($payed===false) $status = 'unverified';
									if ($payed===true&&$result===false) $status = 'locked';
									if ($payed===true&&$result===true) $status = 'success';

									header('Location: ' . $_SESSION['url'] . '#' . $_SESSION['payAlbumID'] . '//' . $status);
									exit();

								} else if ($_SESSION['payType']==='photo') {

									$photo	= new Photo($database->get(), $_SESSION['payPhotoID']);
									$paypal	= new PayPal($database->get(), $apiCredentials);
									$payed	= $paypal->checkPayment($_SESSION['payKey']);

									if ($payed===true) $result = $photo->setPayment();
									else $result = false;

									if ($payed===false) $status = 'unverified';
									if ($payed===true&&$result===false) $status = 'locked';
									if ($payed===true&&$result===true) $status = 'success';

									header('Location: ' . $_SESSION['url'] . '#' . $_SESSION['payAlbumID'] . '/' . $_SESSION['payPhotoID'] . '/' . $status);
									exit();

								} else {

									exit('Error: AlbumID not found. Please contact the support with this message.');

								}

							} else {

								exit('Error: PaymentKey not found. Please contact the support with this message.');

							}
							break;

		case 'getCode':		if (isset($_POST['code'])) {

								$album = new Album($database->get(), null);
								echo $album->getByCode($_POST['code']);

							}
							break;

		case 'setMail':		if (isset($_POST['mail'], $_POST['code'])) {

								$user = new User($database->get(), null);
								echo $user->setMail($_POST['mail'], $_POST['code']);

							}
							break;

	}

} else {

	exit('Error: Parameter "function" not defined!');

}

?>