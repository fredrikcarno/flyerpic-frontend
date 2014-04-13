# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.6.14)
# Database: lychee_fredrik
# Generation Time: 2014-04-13 13:47:08 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table lychee_users
# ------------------------------------------------------------

CREATE TABLE `lychee_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `description` varchar(1000) CHARACTER SET latin1 DEFAULT NULL,
  `primarymail` varchar(100) CHARACTER SET latin1 NOT NULL,
  `secondarymail` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `service` varchar(30) CHARACTER SET latin1 NOT NULL DEFAULT 'paypal',
  `currencycode` varchar(3) CHARACTER SET latin1 NOT NULL DEFAULT 'USD',
  `currencysymbol` varchar(1) CHARACTER SET latin1 NOT NULL DEFAULT '$',
  `currencyposition` tinyint(1) NOT NULL DEFAULT '0',
  `priceperalbum` double(4,2) NOT NULL,
  `priceperphoto` double(4,2) NOT NULL,
  `percentperprice` int(11) NOT NULL DEFAULT '0',
  `watermark` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
