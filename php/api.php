<?php

/**
 * @name		API
 * @author		Tobias Reich
 * @copyright	2014 by Tobias Reich
 */

if (!empty($_POST['function'])) {

	if (file_exists('../data/config.php')) require('../data/config.php');
	else exit('Error: Config not found!');

	switch ($_POST['function']) {

		case 'getLychee':		echo $lychee;
								break;

	}

} else {

	exit('Error: No permission!');

}

?>