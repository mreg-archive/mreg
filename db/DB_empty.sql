-- MySQL dump 10.13  Distrib 5.5.28, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: mreg_empty
-- ------------------------------------------------------
-- Server version	5.5.28-0ubuntu0.12.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Template`
--

DROP TABLE IF EXISTS `Template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Template` (
  `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tCreated` int(11) NOT NULL DEFAULT '0',
  `tModified` int(11) NOT NULL DEFAULT '0',
  `owner` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'root',
  `group` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'root',
  `mode` smallint(5) unsigned NOT NULL DEFAULT '504',
  `etag` char(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `modifiedBy` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'sys',
  `headline` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  `tmpl` text COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`name`),
  KEY `owner` (`owner`),
  KEY `modifiedBy` (`modifiedBy`),
  KEY `group` (`group`),
  CONSTRAINT `Template_ibfk_1` FOREIGN KEY (`owner`) REFERENCES `sys__User` (`uname`) ON UPDATE CASCADE,
  CONSTRAINT `Template_ibfk_2` FOREIGN KEY (`group`) REFERENCES `sys__Group` (`name`) ON UPDATE CASCADE,
  CONSTRAINT `Template_ibfk_3` FOREIGN KEY (`modifiedBy`) REFERENCES `sys__User` (`uname`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Template`
--

LOCK TABLES `Template` WRITE;
/*!40000 ALTER TABLE `Template` DISABLE KEYS */;
INSERT INTO `Template` VALUES ('password',1331132866,1331132866,'root','root',504,'08c259109fede7c92b0af3cad1f6dbd1','sys','Ditt nya lösenord','<h1>Ditt nya lösenord</h1>\r\n\r\n<p><strong>{{password}}</strong></p>\r\n			\r\n<p>Logga in med det användarnamn du angav vid registreringen. Där kan du ändra lösenordet till någonting du lättare kommer ihåg.</p>\r\n\r\n<p>mvh</p>'),('password-short',1331132866,1331132866,'root','root',504,'9d181eee707f8da4c612b064ef21a69f','sys','Ditt nya lösenord','Ditt nya lösenord: {{password}}');
/*!40000 ALTER TABLE `Template` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `Template_before_insert` BEFORE INSERT ON `Template`
FOR EACH ROW
BEGIN
	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`owner`, NEW.`group`, NEW.`mode`, NEW.`headline`, NEW.`tmpl`));
	IF NEW.`tCreated` = '0' THEN
	    SET NEW.`tCreated` = UNIX_TIMESTAMP();
	END IF;
	SET NEW.`tModified`=NEW.`tCreated`;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `Template_before_update` BEFORE UPDATE ON `Template`
FOR EACH ROW
BEGIN
	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`owner`, NEW.`group`, NEW.`mode`, NEW.`headline`, NEW.`tmpl`));
	SET NEW.`tModified` = UNIX_TIMESTAMP();
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `Template_after_update` AFTER UPDATE ON `Template`
FOR EACH ROW
BEGIN
	IF ( OLD.`owner` != NEW.`owner` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='Template',
			`ref_id`=NEW.`name`,
			`ref_column`='owner',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`owner`,
			`new_value`=NEW.`owner`;
	END IF;
	IF ( OLD.`group` != NEW.`group` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='Template',
			`ref_id`=NEW.`name`,
			`ref_column`='group',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`group`,
			`new_value`=NEW.`group`;
	END IF;
	IF ( OLD.`mode` != NEW.`mode` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='Template',
			`ref_id`=NEW.`name`,
			`ref_column`='mode',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`mode`,
			`new_value`=NEW.`mode`;
	END IF;
	IF ( OLD.`headline` != NEW.`headline` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='Template',
			`ref_id`=NEW.`name`,
			`ref_column`='headline',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`headline`,
			`new_value`=NEW.`headline`;
	END IF;
	IF ( OLD.`tmpl` != NEW.`tmpl` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='Template',
			`ref_id`=NEW.`name`,
			`ref_column`='tmpl',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`tmpl`,
			`new_value`=NEW.`tmpl`;
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `aux__Address`
--

DROP TABLE IF EXISTS `aux__Address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aux__Address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tCreated` int(11) NOT NULL DEFAULT '0',
  `tModified` int(11) NOT NULL DEFAULT '0',
  `etag` char(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `modifiedBy` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'sys',
  `ref_table` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ref_id` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_swedish_ci NOT NULL,
  `mailee_role_descriptor` varchar(3) COLLATE utf8_swedish_ci NOT NULL DEFAULT '' COMMENT 'Ex: c/o',
  `mailee` varchar(32) COLLATE utf8_swedish_ci NOT NULL DEFAULT '' COMMENT 'Name of mailee',
  `thoroughfare` varchar(36) COLLATE utf8_swedish_ci NOT NULL DEFAULT '' COMMENT 'Streetname or equivalent',
  `plot` varchar(9) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  `littera` char(2) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  `stairwell` varchar(3) COLLATE utf8_swedish_ci NOT NULL DEFAULT '' COMMENT 'Ex: UH, UV, U1 ... U99',
  `floor` varchar(7) COLLATE utf8_swedish_ci NOT NULL DEFAULT '' COMMENT 'Only used if door is not specified',
  `door` varchar(4) COLLATE utf8_swedish_ci NOT NULL DEFAULT '' COMMENT 'Lagen om lägenhetsregister (2006:378)',
  `supplementary_delivery_point_data` varchar(36) COLLATE utf8_swedish_ci NOT NULL DEFAULT '' COMMENT 'Free form supplementary data',
  `delivery_service` varchar(30) COLLATE utf8_swedish_ci NOT NULL DEFAULT '' COMMENT 'Type of service. Ex: ''Box'', ''Bryggpost'', ''Poste restante''',
  `alternate_delivery_service` varchar(5) COLLATE utf8_swedish_ci NOT NULL DEFAULT '' COMMENT 'Service address. Ex: box-number',
  `postcode` varchar(6) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  `town` varchar(29) COLLATE utf8_swedish_ci NOT NULL DEFAULT '' COMMENT '29 chars so that town + postcode will not exceed  36 chars',
  `country_code` char(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'SE' COMMENT 'ISO 3166-1 alpha-2 country code',
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference` (`ref_table`,`ref_id`,`name`),
  KEY `modifiedBy` (`modifiedBy`),
  CONSTRAINT `aux__Address_ibfk_2` FOREIGN KEY (`modifiedBy`) REFERENCES `sys__User` (`uname`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci COMMENT='Swedish postal addresses as per SS 613401:2011 ed. 3';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aux__Address`
--

LOCK TABLES `aux__Address` WRITE;
/*!40000 ALTER TABLE `aux__Address` DISABLE KEYS */;
/*!40000 ALTER TABLE `aux__Address` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `aux__Address_before_insert` BEFORE INSERT ON `aux__Address`
FOR EACH ROW
BEGIN
	IF NEW.`mailee` IS NOT NULL AND (NEW.`mailee_role_descriptor` IS NULL OR NEW.`mailee_role_descriptor`='') THEN
	    SET NEW.`mailee_role_descriptor` = 'c/o';
	END IF;
	IF NEW.`alternate_delivery_service` IS NOT NULL AND NEW.`alternate_delivery_service` != '' AND (NEW.`delivery_service` IS NULL OR NEW.`delivery_service`='') THEN
	    SET NEW.`delivery_service` = 'Box';
	END IF;

	SET NEW.`delivery_service` = CONCAT(UPPER(SUBSTRING(NEW.`delivery_service`, 1, 1)), LOWER(SUBSTRING(NEW.`delivery_service`, 2)));
	SET NEW.`littera` = UPPER(NEW.`littera`);
	SET NEW.`stairwell` = UPPER(NEW.`stairwell`);
	SET NEW.`floor` = UPPER(NEW.`floor`);
	SET NEW.`town` = CONCAT(UPPER(SUBSTRING(NEW.`town`, 1, 1)), LOWER(SUBSTRING(NEW.`town`, 2)));
	SET NEW.`country_code` = UPPER(NEW.`country_code`);

	IF CHAR_LENGTH(NEW.`postcode`) = 5 THEN
	    SET NEW.`postcode` = CONCAT_WS(' ', SUBSTRING(NEW.`postcode`, 1, 3), SUBSTRING(NEW.`postcode`, 4));
	END IF;

	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`mailee`, NEW.`mailee_role_descriptor`, NEW.`thoroughfare`, NEW.`plot`, NEW.`littera`, NEW.`stairwell`, NEW.`floor`, NEW.`door`, NEW.`supplementary_delivery_point_data`, NEW.`delivery_service`, NEW.`alternate_delivery_service`, NEW.`postcode`, NEW.`town`, NEW.`country_code`));
	IF NEW.`tCreated` = '0' THEN
	    SET NEW.`tCreated` = UNIX_TIMESTAMP();
	END IF;
	SET NEW.`tModified`=NEW.`tCreated`;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `aux__Address_before_update` BEFORE UPDATE ON `aux__Address`
FOR EACH ROW
BEGIN
	IF NEW.`mailee` IS NOT NULL AND (NEW.`mailee_role_descriptor` IS NULL OR NEW.`mailee_role_descriptor`='') THEN
	    SET NEW.`mailee_role_descriptor` = 'c/o';
	END IF;
	IF NEW.`alternate_delivery_service` IS NOT NULL AND NEW.`alternate_delivery_service` != '' AND (NEW.`delivery_service` IS NULL OR NEW.`delivery_service`='') THEN
	    SET NEW.`delivery_service` = 'Box';
	END IF;

	SET NEW.`delivery_service` = CONCAT(UPPER(SUBSTRING(NEW.`delivery_service`, 1, 1)), LOWER(SUBSTRING(NEW.`delivery_service`, 2)));
	SET NEW.`littera` = UPPER(NEW.`littera`);
	SET NEW.`stairwell` = UPPER(NEW.`stairwell`);
	SET NEW.`floor` = UPPER(NEW.`floor`);
	SET NEW.`town` = CONCAT(UPPER(SUBSTRING(NEW.`town`, 1, 1)), LOWER(SUBSTRING(NEW.`town`, 2)));
	SET NEW.`country_code` = UPPER(NEW.`country_code`);

	IF CHAR_LENGTH(NEW.`postcode`) = 5 THEN
	    SET NEW.`postcode` = CONCAT_WS(' ', SUBSTRING(NEW.`postcode`, 1, 3), SUBSTRING(NEW.`postcode`, 4));
	END IF;

	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`mailee`, NEW.`mailee_role_descriptor`, NEW.`thoroughfare`, NEW.`plot`, NEW.`littera`, NEW.`stairwell`, NEW.`floor`, NEW.`door`, NEW.`supplementary_delivery_point_data`, NEW.`delivery_service`, NEW.`alternate_delivery_service`, NEW.`postcode`, NEW.`town`, NEW.`country_code`));
	SET NEW.`tModified` = UNIX_TIMESTAMP();
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `aux__Address_after_update` AFTER UPDATE ON `aux__Address`
FOR EACH ROW
BEGIN
	IF ( OLD.`mailee` != NEW.`mailee` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='aux__Address',
			`ref_id`=NEW.`id`,
			`ref_column`= 'mailee',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`mailee`,
			`new_value`=NEW.`mailee`;
	END IF;
	IF ( OLD.`mailee_role_descriptor` != NEW.`mailee_role_descriptor` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='aux__Address',
			`ref_id`=NEW.`id`,
			`ref_column`= 'mailee_role_descriptor',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`mailee_role_descriptor`,
			`new_value`=NEW.`mailee_role_descriptor`;
	END IF;
	IF ( OLD.`thoroughfare` != NEW.`thoroughfare` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='aux__Address',
			`ref_id`=NEW.`id`,
			`ref_column`= 'thoroughfare',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`thoroughfare`,
			`new_value`=NEW.`thoroughfare`;
	END IF;
	IF ( OLD.`plot` != NEW.`plot` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='aux__Address',
			`ref_id`=NEW.`id`,
			`ref_column`= 'plot',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`plot`,
			`new_value`=NEW.`plot`;
	END IF;
	IF ( OLD.`littera` != NEW.`littera` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='aux__Address',
			`ref_id`=NEW.`id`,
			`ref_column`= 'littera',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`littera`,
			`new_value`=NEW.`littera`;
	END IF;
	IF ( OLD.`stairwell` != NEW.`stairwell` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='aux__Address',
			`ref_id`=NEW.`id`,
			`ref_column`= 'stairwell',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`stairwell`,
			`new_value`=NEW.`stairwell`;
	END IF;
	IF ( OLD.`floor` != NEW.`floor` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='aux__Address',
			`ref_id`=NEW.`id`,
			`ref_column`= 'floor',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`floor`,
			`new_value`=NEW.`floor`;
	END IF;
	IF ( OLD.`door` != NEW.`door` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='aux__Address',
			`ref_id`=NEW.`id`,
			`ref_column`= 'door',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`door`,
			`new_value`=NEW.`door`;
	END IF;
	IF ( OLD.`supplementary_delivery_point_data` != NEW.`supplementary_delivery_point_data` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='aux__Address',
			`ref_id`=NEW.`id`,
			`ref_column`= 'supplementary_delivery_point_data',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`supplementary_delivery_point_data`,
			`new_value`=NEW.`supplementary_delivery_point_data`;
	END IF;
	IF ( OLD.`delivery_service` != NEW.`delivery_service` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='aux__Address',
			`ref_id`=NEW.`id`,
			`ref_column`= 'delivery_service',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`delivery_service`,
			`new_value`=NEW.`delivery_service`;
	END IF;
	IF ( OLD.`alternate_delivery_service` != NEW.`alternate_delivery_service` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='aux__Address',
			`ref_id`=NEW.`id`,
			`ref_column`= 'alternate_delivery_service',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`alternate_delivery_service`,
			`new_value`=NEW.`alternate_delivery_service`;
	END IF;
	IF ( OLD.`postcode` != NEW.`postcode` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='aux__Address',
			`ref_id`=NEW.`id`,
			`ref_column`= 'postcode',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`postcode`,
			`new_value`=NEW.`postcode`;
	END IF;
	IF ( OLD.`town` != NEW.`town` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='aux__Address',
			`ref_id`=NEW.`id`,
			`ref_column`= 'town',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`town`,
			`new_value`=NEW.`town`;
	END IF;
	IF ( OLD.`country_code` != NEW.`country_code` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='aux__Address',
			`ref_id`=NEW.`id`,
			`ref_column`= 'country_code',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`country_code`,
			`new_value`=NEW.`country_code`;
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `aux__Mail`
--

DROP TABLE IF EXISTS `aux__Mail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aux__Mail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tCreated` int(11) NOT NULL DEFAULT '0',
  `tModified` int(11) NOT NULL DEFAULT '0',
  `etag` char(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `modifiedBy` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'sys',
  `ref_table` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ref_id` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_swedish_ci NOT NULL,
  `mail` varchar(200) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference` (`ref_table`,`ref_id`,`name`),
  KEY `modifiedBy` (`modifiedBy`),
  CONSTRAINT `aux__Mail_ibfk_1` FOREIGN KEY (`modifiedBy`) REFERENCES `sys__User` (`uname`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aux__Mail`
--

LOCK TABLES `aux__Mail` WRITE;
/*!40000 ALTER TABLE `aux__Mail` DISABLE KEYS */;
/*!40000 ALTER TABLE `aux__Mail` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `aux__Mail_before_insert` BEFORE INSERT ON `aux__Mail`
FOR EACH ROW
BEGIN
	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`mail`, NEW.`name`));
	IF NEW.`tCreated` = '0' THEN
	    SET NEW.`tCreated` = UNIX_TIMESTAMP();
	END IF;
	SET NEW.`tModified`=NEW.`tCreated`;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `aux__Mail_before_update` BEFORE UPDATE ON `aux__Mail`
FOR EACH ROW
BEGIN
	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`mail`, NEW.`name`));
	SET NEW.`tModified` = UNIX_TIMESTAMP();
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `aux__Mail_after_update` AFTER UPDATE ON `aux__Mail`
FOR EACH ROW
BEGIN
	IF ( OLD.`mail` != NEW.`mail` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='aux__Mail',
			`ref_id`=NEW.`id`,
			`ref_column`=NEW.`name`,
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`mail`,
			`new_value`=NEW.`mail`;
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `aux__Phone`
--

DROP TABLE IF EXISTS `aux__Phone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aux__Phone` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tCreated` int(11) NOT NULL DEFAULT '0',
  `tModified` int(11) NOT NULL DEFAULT '0',
  `etag` char(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `modifiedBy` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'sys',
  `ref_table` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ref_id` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_swedish_ci NOT NULL,
  `cc` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Country code',
  `ndc` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Natinal destination code (or destination network)',
  `sn` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Subscriber number',
  `carrier` varchar(50) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference` (`ref_table`,`ref_id`,`name`),
  KEY `modifiedBy` (`modifiedBy`),
  CONSTRAINT `aux__Phone_ibfk_1` FOREIGN KEY (`modifiedBy`) REFERENCES `sys__User` (`uname`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci COMMENT='E164 phone numbers';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aux__Phone`
--

LOCK TABLES `aux__Phone` WRITE;
/*!40000 ALTER TABLE `aux__Phone` DISABLE KEYS */;
/*!40000 ALTER TABLE `aux__Phone` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `aux__Phone_before_insert` BEFORE INSERT ON `aux__Phone`
FOR EACH ROW
BEGIN
	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`cc`, NEW.`ndc`, NEW.`sn`, NEW.`carrier`, NEW.`name`));
	IF NEW.`tCreated` = '0' THEN
	    SET NEW.`tCreated` = UNIX_TIMESTAMP();
	END IF;
	SET NEW.`tModified`=NEW.`tCreated`;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `aux__Phone_before_update` BEFORE UPDATE ON `aux__Phone`
FOR EACH ROW
BEGIN
	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`cc`, NEW.`ndc`, NEW.`sn`, NEW.`carrier`, NEW.`name`));
	SET NEW.`tModified` = UNIX_TIMESTAMP();
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `aux__Phone_after_update` AFTER UPDATE ON `aux__Phone`
FOR EACH ROW
BEGIN
	IF ( OLD.`cc`!=NEW.`cc` OR OLD.`ndc`!=NEW.`ndc` OR OLD.`sn`!=NEW.`sn` ) THEN
		INSERT INTO `aux__Revision` SET
		    `tModified`=UNIX_TIMESTAMP(),
		    `ref_table` = 'aux__Phone',
		    `ref_id` = NEW.`id`,
		    `ref_column` = NEW.`name`,
		    `modifiedBy` = NEW.`modifiedBy`,
		    `old_value` = CONCAT('+', OLD.`cc`, OLD.`ndc`, OLD.`sn`),
		    `new_value` = CONCAT('+', NEW.`cc`, NEW.`ndc`, NEW.`sn`);
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `aux__Revision`
--

DROP TABLE IF EXISTS `aux__Revision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aux__Revision` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tModified` int(11) NOT NULL DEFAULT '0',
  `modifiedBy` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'sys',
  `ref_table` varchar(50) COLLATE utf8_swedish_ci NOT NULL,
  `ref_id` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ref_column` varchar(50) COLLATE utf8_swedish_ci NOT NULL,
  `old_value` text COLLATE utf8_swedish_ci NOT NULL,
  `new_value` text COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ref_table` (`ref_table`,`ref_id`,`ref_column`),
  KEY `modifiedBy` (`modifiedBy`),
  CONSTRAINT `aux__Revision_ibfk_1` FOREIGN KEY (`modifiedBy`) REFERENCES `sys__User` (`uname`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aux__Revision`
--

LOCK TABLES `aux__Revision` WRITE;
/*!40000 ALTER TABLE `aux__Revision` DISABLE KEYS */;
INSERT INTO `aux__Revision` VALUES (1,-3600,'sys','dir__Faction','318','name','Jutis','Arjeplogs'),(2,978303600,'sys','dir__Faction','4','name','Guldsmedshyttans samt Örebro','Bergslagens'),(3,315529200,'sys','dir__Faction','60','name','Korsnäs','Falu-Korsnäs'),(4,599612400,'sys','dir__Faction','382','name','Visby','Gotlands'),(5,410223600,'sys','dir__Faction','31','name','Malmbergets','Gällivare-Malmbergets'),(6,-63162000,'sys','dir__Faction','100','name','Dala-Finnhyttans','Hedemora'),(7,-94698000,'sys','dir__Faction','44','name','Rämsö','Hedesunda'),(8,-157770000,'sys','dir__Faction','217','name','Lillpite','Piteortens'),(9,1262300400,'sys','dir__Faction','129','name','Tyresö','Tyresö-Haninge'),(10,567990000,'sys','dir__Faction','425','name','Lovikka','Tärendö'),(11,1143846000,'sys','dir__Faction','26','name','Halmstads','Hallands');
/*!40000 ALTER TABLE `aux__Revision` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dir__Employer`
--

DROP TABLE IF EXISTS `dir__Employer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dir__Employer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tCreated` int(11) NOT NULL DEFAULT '0',
  `tModified` int(11) NOT NULL DEFAULT '0',
  `owner` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'root',
  `group` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'mem-edit',
  `mode` smallint(5) unsigned NOT NULL DEFAULT '504',
  `etag` char(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `modifiedBy` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'sys',
  `name` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  `parentId` int(10) unsigned NOT NULL DEFAULT '1' COMMENT 'Parent employer id',
  `corporateId` varchar(13) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Swedish corporate identity number',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `modifiedBy` (`modifiedBy`),
  KEY `parentId` (`parentId`),
  KEY `owner` (`owner`),
  KEY `group` (`group`),
  CONSTRAINT `dir__Employer_ibfk_5` FOREIGN KEY (`owner`) REFERENCES `sys__User` (`uname`) ON UPDATE CASCADE,
  CONSTRAINT `dir__Employer_ibfk_6` FOREIGN KEY (`group`) REFERENCES `sys__Group` (`name`) ON UPDATE CASCADE,
  CONSTRAINT `dir__Employer_ibfk_7` FOREIGN KEY (`modifiedBy`) REFERENCES `sys__User` (`uname`) ON UPDATE CASCADE,
  CONSTRAINT `dir__Employer_ibfk_8` FOREIGN KEY (`parentId`) REFERENCES `dir__Employer` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dir__Employer`
--

LOCK TABLES `dir__Employer` WRITE;
/*!40000 ALTER TABLE `dir__Employer` DISABLE KEYS */;
INSERT INTO `dir__Employer` VALUES (1,0,0,'root','mem-edit',504,'cd853f23b2abf7f279d3b4c75ff67b1b','sys','root employer',1,'');
/*!40000 ALTER TABLE `dir__Employer` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `dir__Employer_before_insert` BEFORE INSERT ON `dir__Employer`
FOR EACH ROW
BEGIN
	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`owner`, NEW.`group`, NEW.`mode`, NEW.`name`, NEW.`parentId`, NEW.`corporateId`));
	IF NEW.`tCreated` = '0' THEN
	    SET NEW.`tCreated` = UNIX_TIMESTAMP();
	END IF;
	SET NEW.`tModified`=NEW.`tCreated`;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `dir__Employer_before_update` BEFORE UPDATE ON `dir__Employer`
FOR EACH ROW
BEGIN
	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`owner`, NEW.`group`, NEW.`mode`, NEW.`name`, NEW.`parentId`, NEW.`corporateId`));
	SET NEW.`tModified` = UNIX_TIMESTAMP();
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `dir__Employer_after_update` AFTER UPDATE ON `dir__Employer`
FOR EACH ROW
BEGIN
	IF ( OLD.`owner` != NEW.`owner` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Employer',
			`ref_id`=NEW.`id`,
			`ref_column`='owner',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`owner`,
			`new_value`=NEW.`owner`;
	END IF;
	IF ( OLD.`group` != NEW.`group` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Employer',
			`ref_id`=NEW.`id`,
			`ref_column`='group',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`group`,
			`new_value`=NEW.`group`;
	END IF;
	IF ( OLD.`mode` != NEW.`mode` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Employer',
			`ref_id`=NEW.`id`,
			`ref_column`='mode',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`mode`,
			`new_value`=NEW.`mode`;
	END IF;
	IF ( OLD.`name` != NEW.`name` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Employer',
			`ref_id`=NEW.`id`,
			`ref_column`='name',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`name`,
			`new_value`=NEW.`name`;
	END IF;
	IF ( OLD.`parentId` != NEW.`parentId` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Employer',
			`ref_id`=NEW.`id`,
			`ref_column`='parentId',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`parentId`,
			`new_value`=NEW.`parentId`;
	END IF;
	IF ( OLD.`corporateId` != NEW.`corporateId` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Employer',
			`ref_id`=NEW.`id`,
			`ref_column`='corporateId',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`corporateId`,
			`new_value`=NEW.`corporateId`;
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `dir__Faction`
--

DROP TABLE IF EXISTS `dir__Faction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dir__Faction` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tCreated` int(11) NOT NULL DEFAULT '0',
  `tModified` int(11) NOT NULL DEFAULT '0',
  `owner` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'root',
  `group` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'group-edit',
  `mode` smallint(5) unsigned NOT NULL DEFAULT '504',
  `etag` char(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `modifiedBy` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'sys',
  `parentId` int(10) unsigned NOT NULL DEFAULT '1000' COMMENT 'Parent faction id',
  `name` varchar(100) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  `type` enum('ANNAN','DISTRIKT','LS','ORTSSEKTION','FEDERATION','SYNDIKAT','DRIFTSSEKTION','AVDELNING','KONCERNFACK','TEMP','TEKNISK') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'ANNAN',
  `description` varchar(100) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  `notes` text COLLATE utf8_swedish_ci,
  `accountantId` int(11) unsigned DEFAULT NULL COMMENT 'Foreign key to eco__Accountant',
  `plusgiro` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `bankgiro` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `avatar` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `accountantId` (`accountantId`),
  KEY `modifiedBy` (`modifiedBy`),
  KEY `owner` (`owner`),
  KEY `group` (`group`),
  KEY `parentId` (`parentId`),
  CONSTRAINT `dir__Faction_ibfk_19` FOREIGN KEY (`owner`) REFERENCES `sys__User` (`uname`) ON UPDATE CASCADE,
  CONSTRAINT `dir__Faction_ibfk_20` FOREIGN KEY (`group`) REFERENCES `sys__Group` (`name`) ON UPDATE CASCADE,
  CONSTRAINT `dir__Faction_ibfk_21` FOREIGN KEY (`modifiedBy`) REFERENCES `sys__User` (`uname`) ON UPDATE CASCADE,
  CONSTRAINT `dir__Faction_ibfk_22` FOREIGN KEY (`parentId`) REFERENCES `dir__Faction` (`id`),
  CONSTRAINT `dir__Faction_ibfk_23` FOREIGN KEY (`accountantId`) REFERENCES `eco__Accountant` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1001 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci COMMENT='organisational groups';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dir__Faction`
--

LOCK TABLES `dir__Faction` WRITE;
/*!40000 ALTER TABLE `dir__Faction` DISABLE KEYS */;
INSERT INTO `dir__Faction` VALUES (1000,1321207113,1340719189,'root','group-edit',504,'b443074dcfef68dce3e56a14f466e5d7','sys',1000,'CENTRAL','ANNAN','','',1,'','','','');
/*!40000 ALTER TABLE `dir__Faction` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `dir__Faction_before_insert` BEFORE INSERT ON `dir__Faction`
FOR EACH ROW
BEGIN
	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`owner`, NEW.`group`, NEW.`mode`, NEW.`parentId`, NEW.`name`, NEW.`type`, NEW.`description`, NEW.`notes`, NEW.`plusgiro`, NEW.`bankgiro`, NEW.`url`));
	IF NEW.`tCreated` = '0' THEN
	    SET NEW.`tCreated` = UNIX_TIMESTAMP();
	END IF;
	SET NEW.`tModified`=NEW.`tCreated`;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `dir__Faction_before_update` BEFORE UPDATE ON `dir__Faction`
FOR EACH ROW
BEGIN
	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`owner`, NEW.`group`, NEW.`mode`, NEW.`parentId`, NEW.`name`, NEW.`type`, NEW.`description`, NEW.`notes`, NEW.`plusgiro`, NEW.`bankgiro`, NEW.`url`));
	SET NEW.`tModified` = UNIX_TIMESTAMP();
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `dir__Faction_after_update` AFTER UPDATE ON `dir__Faction`
FOR EACH ROW
BEGIN
	IF ( OLD.`owner` != NEW.`owner` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Faction',
			`ref_id`=NEW.`id`,
			`ref_column`='owner',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`owner`,
			`new_value`=NEW.`owner`;
	END IF;
	IF ( OLD.`group` != NEW.`group` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Faction',
			`ref_id`=NEW.`id`,
			`ref_column`='group',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`group`,
			`new_value`=NEW.`group`;
	END IF;
	IF ( OLD.`mode` != NEW.`mode` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Faction',
			`ref_id`=NEW.`id`,
			`ref_column`='mode',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`mode`,
			`new_value`=NEW.`mode`;
	END IF;
	IF ( OLD.`parentId` != NEW.`parentId` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Faction',
			`ref_id`=NEW.`id`,
			`ref_column`='parentId',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`parentId`,
			`new_value`=NEW.`parentId`;
	END IF;
	IF ( OLD.`name` != NEW.`name` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Faction',
			`ref_id`=NEW.`id`,
			`ref_column`='name',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`name`,
			`new_value`=NEW.`name`;
	END IF;
	IF ( OLD.`type` != NEW.`type` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Faction',
			`ref_id`=NEW.`id`,
			`ref_column`='type',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`type`,
			`new_value`=NEW.`type`;
	END IF;
	IF ( OLD.`description` != NEW.`description` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Faction',
			`ref_id`=NEW.`id`,
			`ref_column`='description',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`description`,
			`new_value`=NEW.`description`;
	END IF;
	IF ( OLD.`notes` != NEW.`notes` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Faction',
			`ref_id`=NEW.`id`,
			`ref_column`='notes',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`notes`,
			`new_value`=NEW.`notes`;
	END IF;
	IF ( OLD.`plusgiro` != NEW.`plusgiro` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Faction',
			`ref_id`=NEW.`id`,
			`ref_column`='plusgiro',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`plusgiro`,
			`new_value`=NEW.`plusgiro`;
	END IF;
	IF ( OLD.`bankgiro` != NEW.`bankgiro` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Faction',
			`ref_id`=NEW.`id`,
			`ref_column`='bankgiro',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`bankgiro`,
			`new_value`=NEW.`bankgiro`;
	END IF;
	IF ( OLD.`url` != NEW.`url` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Faction',
			`ref_id`=NEW.`id`,
			`ref_column`='url',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`url`,
			`new_value`=NEW.`url`;
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `dir__Member`
--

DROP TABLE IF EXISTS `dir__Member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dir__Member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'medlemsnummer',
  `tCreated` int(11) NOT NULL DEFAULT '0',
  `tModified` int(11) NOT NULL DEFAULT '0',
  `owner` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'root',
  `group` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'mem-edit',
  `mode` smallint(5) unsigned NOT NULL DEFAULT '504',
  `etag` char(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `modifiedBy` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'sys',
  `personalId` varchar(13) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'Swedish personal identity number',
  `dob` date NOT NULL DEFAULT '0000-00-00' COMMENT 'Date of birth',
  `sex` enum('M','F','O') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'O' COMMENT 'M: male, F: female, O: other',
  `names` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  `surname` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  `avatar` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `workCondition` enum('ANSTALLD','PENSIONAR','STUDERANDE','EGENFORETAGARE','ARBETSLOS','ANNAN') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'ANNAN',
  `salary` decimal(10,2) NOT NULL DEFAULT '0.00',
  `debitClass` varchar(10) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  `paymentType` enum('AG','BAG','BAG-V','MAG','MAG-V','PAG','LS') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'LS',
  `bankAccount` varchar(20) COLLATE utf8_swedish_ci NOT NULL DEFAULT '0000,',
  `notes` text COLLATE utf8_swedish_ci,
  `LS` varchar(100) COLLATE utf8_swedish_ci NOT NULL DEFAULT '' COMMENT 'Cache of name. xref value precedes.',
  `invoiceFlag` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 if member is flaged for expired invoices',
  PRIMARY KEY (`id`),
  KEY `modifiedBy` (`modifiedBy`),
  KEY `group` (`group`),
  KEY `owner` (`owner`),
  CONSTRAINT `dir__Member_ibfk_1` FOREIGN KEY (`owner`) REFERENCES `sys__User` (`uname`) ON UPDATE CASCADE,
  CONSTRAINT `dir__Member_ibfk_2` FOREIGN KEY (`group`) REFERENCES `sys__Group` (`name`) ON UPDATE CASCADE,
  CONSTRAINT `dir__Member_ibfk_3` FOREIGN KEY (`modifiedBy`) REFERENCES `sys__User` (`uname`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dir__Member`
--

LOCK TABLES `dir__Member` WRITE;
/*!40000 ALTER TABLE `dir__Member` DISABLE KEYS */;
/*!40000 ALTER TABLE `dir__Member` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `dir__Member_before_insert` BEFORE INSERT ON `dir__Member`
FOR EACH ROW
BEGIN
	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`owner`, NEW.`group`, NEW.`mode`, NEW.`personalId`, NEW.`names`, NEW.`surname`, NEW.`paymentType`, NEW.`bankAccount`, NEW.`notes`, NEW.`salary`));
	IF NEW.`tCreated` = '0' THEN
	    SET NEW.`tCreated` = UNIX_TIMESTAMP();
	END IF;
	SET NEW.`tModified`=NEW.`tCreated`;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `dir__Member_before_update` BEFORE UPDATE ON `dir__Member`
FOR EACH ROW
BEGIN
	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`owner`, NEW.`group`, NEW.`mode`, NEW.`personalId`, NEW.`names`, NEW.`surname`, NEW.`paymentType`, NEW.`bankAccount`, NEW.`notes`, NEW.`salary`));
	SET NEW.`tModified` = UNIX_TIMESTAMP();
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `dir__Member_after_update` AFTER UPDATE ON `dir__Member`
FOR EACH ROW
BEGIN
	IF ( OLD.`owner` != NEW.`owner` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Member',
			`ref_id`=NEW.`id`,
			`ref_column`='owner',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`owner`,
			`new_value`=NEW.`owner`;
	END IF;
	IF ( OLD.`group` != NEW.`group` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Member',
			`ref_id`=NEW.`id`,
			`ref_column`='group',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`group`,
			`new_value`=NEW.`group`;
	END IF;
	IF ( OLD.`mode` != NEW.`mode` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Member',
			`ref_id`=NEW.`id`,
			`ref_column`='mode',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`mode`,
			`new_value`=NEW.`mode`;
	END IF;
	IF ( OLD.`personalId` != NEW.`personalId` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Member',
			`ref_id`=NEW.`id`,
			`ref_column`='personalId',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`personalId`,
			`new_value`=NEW.`personalId`;
	END IF;
	IF ( OLD.`names` != NEW.`names` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Member',
			`ref_id`=NEW.`id`,
			`ref_column`='names',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`names`,
			`new_value`=NEW.`names`;
	END IF;
	IF ( OLD.`surname` != NEW.`surname` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Member',
			`ref_id`=NEW.`id`,
			`ref_column`='surname',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`surname`,
			`new_value`=NEW.`surname`;
	END IF;
	IF ( OLD.`paymentType` != NEW.`paymentType` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Member',
			`ref_id`=NEW.`id`,
			`ref_column`='paymentType',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`paymentType`,
			`new_value`=NEW.`paymentType`;
	END IF;
	IF ( OLD.`bankAccount` != NEW.`bankAccount` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Member',
			`ref_id`=NEW.`id`,
			`ref_column`='bankAccount',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`bankAccount`,
			`new_value`=NEW.`bankAccount`;
	END IF;
	IF ( OLD.`notes` != NEW.`notes` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Member',
			`ref_id`=NEW.`id`,
			`ref_column`='notes',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`notes`,
			`new_value`=NEW.`notes`;
	END IF;
	IF ( OLD.`salary` != NEW.`salary` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Member',
			`ref_id`=NEW.`id`,
			`ref_column`='salary',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`salary`,
			`new_value`=NEW.`salary`;
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `dir__Workplace`
--

DROP TABLE IF EXISTS `dir__Workplace`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dir__Workplace` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tCreated` int(11) NOT NULL DEFAULT '0',
  `tModified` int(11) NOT NULL DEFAULT '0',
  `owner` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'root',
  `group` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'mem-edit',
  `mode` smallint(5) unsigned NOT NULL DEFAULT '504',
  `etag` char(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `modifiedBy` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'sys',
  `parentId` int(10) unsigned NOT NULL DEFAULT '1' COMMENT 'Workplace parent id',
  `name` varchar(100) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  `unions` varchar(500) COLLATE utf8_swedish_ci NOT NULL DEFAULT '' COMMENT 'Other unions on workplace',
  `collectiveAgreements` varchar(500) COLLATE utf8_swedish_ci NOT NULL DEFAULT '' COMMENT 'Collective agreements on workplace',
  `employees` enum('1-10','11-30','31-99','100-','okänt') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'okänt' COMMENT 'Number of employees on workplace',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `owner` (`owner`),
  KEY `group` (`group`),
  KEY `modifiedBy` (`modifiedBy`),
  KEY `parentId` (`parentId`),
  CONSTRAINT `dir__Workplace_ibfk_10` FOREIGN KEY (`owner`) REFERENCES `sys__User` (`uname`) ON UPDATE CASCADE,
  CONSTRAINT `dir__Workplace_ibfk_11` FOREIGN KEY (`group`) REFERENCES `sys__Group` (`name`) ON UPDATE CASCADE,
  CONSTRAINT `dir__Workplace_ibfk_12` FOREIGN KEY (`modifiedBy`) REFERENCES `sys__User` (`uname`) ON UPDATE CASCADE,
  CONSTRAINT `dir__Workplace_ibfk_13` FOREIGN KEY (`parentId`) REFERENCES `dir__Workplace` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dir__Workplace`
--

LOCK TABLES `dir__Workplace` WRITE;
/*!40000 ALTER TABLE `dir__Workplace` DISABLE KEYS */;
INSERT INTO `dir__Workplace` VALUES (1,1321045329,1331567939,'root','mem-edit',504,'94589e94438f78ea233176b55b4aa61d','sys',1,'root workplace','','','okänt');
/*!40000 ALTER TABLE `dir__Workplace` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `dir__Workplace_before_insert` BEFORE INSERT ON `dir__Workplace`
FOR EACH ROW
BEGIN
	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`owner`, NEW.`group`, NEW.`mode`, NEW.`parentId`, NEW.`name`, NEW.`unions`, NEW.`collectiveAgreements`, NEW.`employees`));
	IF NEW.`tCreated` = '0' THEN
	    SET NEW.`tCreated` = UNIX_TIMESTAMP();
	END IF;
	SET NEW.`tModified`=NEW.`tCreated`;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `dir__Workplace_before_update` BEFORE UPDATE ON `dir__Workplace`
FOR EACH ROW
BEGIN
	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`owner`, NEW.`group`, NEW.`mode`, NEW.`parentId`, NEW.`name`, NEW.`unions`, NEW.`collectiveAgreements`, NEW.`employees`));
	SET NEW.`tModified` = UNIX_TIMESTAMP();
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `dir__Workplace_after_update` AFTER UPDATE ON `dir__Workplace`
FOR EACH ROW
BEGIN
	IF ( OLD.`owner` != NEW.`owner` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Workplace',
			`ref_id`=NEW.`id`,
			`ref_column`='owner',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`owner`,
			`new_value`=NEW.`owner`;
	END IF;
	IF ( OLD.`group` != NEW.`group` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Workplace',
			`ref_id`=NEW.`id`,
			`ref_column`='group',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`group`,
			`new_value`=NEW.`group`;
	END IF;
	IF ( OLD.`mode` != NEW.`mode` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Workplace',
			`ref_id`=NEW.`id`,
			`ref_column`='mode',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`mode`,
			`new_value`=NEW.`mode`;
	END IF;
	IF ( OLD.`parentId` != NEW.`parentId` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Workplace',
			`ref_id`=NEW.`id`,
			`ref_column`='parentId',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`parentId`,
			`new_value`=NEW.`parentId`;
	END IF;
	IF ( OLD.`name` != NEW.`name` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Workplace',
			`ref_id`=NEW.`id`,
			`ref_column`='name',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`name`,
			`new_value`=NEW.`name`;
	END IF;
	IF ( OLD.`unions` != NEW.`unions` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Workplace',
			`ref_id`=NEW.`id`,
			`ref_column`='unions',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`unions`,
			`new_value`=NEW.`unions`;
	END IF;
	IF ( OLD.`collectiveAgreements` != NEW.`collectiveAgreements` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Workplace',
			`ref_id`=NEW.`id`,
			`ref_column`='collectiveAgreements',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`collectiveAgreements`,
			`new_value`=NEW.`collectiveAgreements`;
	END IF;
	IF ( OLD.`employees` != NEW.`employees` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='dir__Workplace',
			`ref_id`=NEW.`id`,
			`ref_column`='employees',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`employees`,
			`new_value`=NEW.`employees`;
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `eco__Accountant`
--

DROP TABLE IF EXISTS `eco__Accountant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eco__Accountant` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accounts` blob NOT NULL COMMENT 'Serialized and Base64 encoded \\itbz\\STB\\Accounting\\ChartOfAccounts-object',
  `channels` blob NOT NULL COMMENT 'Serialized and Base64 encoded \\mreg\\Economy\\Channels-object',
  `templates` blob NOT NULL COMMENT 'Serialized and Base64 encoded \\itbz\\STB\\Accounting\\ChartOfTemplates-object',
  `debits` blob NOT NULL COMMENT 'Serialized and Base64 encoded \\itbz\\STB\\Accounting\\TableOfDebits-object',
  `parent` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`),
  CONSTRAINT `eco__Accountant_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `eco__Accountant` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eco__Accountant`
--

LOCK TABLES `eco__Accountant` WRITE;
/*!40000 ALTER TABLE `eco__Accountant` DISABLE KEYS */;
INSERT INTO `eco__Accountant` VALUES (1,'TzozNToiaXRielxTVEJcQWNjb3VudGluZ1xDaGFydE9mQWNjb3VudHMiOjI6e3M6NDY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXENoYXJ0T2ZBY2NvdW50cwBfYWNjb3VudHMiO2E6NjU6e2k6MTUxMDtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIxNTEwIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IlQiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTc6Ik1lZGxlbXNmb3JkcmluZ2FyIjt9aToxOTEwO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjE5MTAiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiVCI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czo1OiJLYXNzYSI7fWk6MTkyMDtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIxOTIwIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IlQiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6ODoiUGx1c0dpcm8iO31pOjE5MzA7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMTkzMCI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJUIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjg6IkJhbmtnaXJvIjt9aToxOTM1O086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjE5MzUiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiVCI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czo4OiJBdXRvZ2lybyI7fWk6MjA3MTtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIyMDcxIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IlMiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTA6IlN0cmlkc2ZvbmQiO31pOjIwNzI7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMjA3MiI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJTIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjEwOiJTdHVkaWVmb25kIjt9aToyMjEwO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjIyMTAiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiUyI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czozMDoiU2t1bGQgZsO2ciBVdGJpbGRuaW5nc3N5bmRpa2F0Ijt9aToyMjIwO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjIyMjAiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiUyI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czozNjoiU2t1bGQgZsO2ciBTb2NpYWwtIG9jaCBWw6VyZHN5bmRpa2F0Ijt9aToyMjMwO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjIyMzAiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiUyI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czozODoiU2t1bGQgZsO2ciBIYW5kZWwtIG9jaCBTZXJ2aWNlc3luZGlrYXQiO31pOjIyNDA7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMjI0MCI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJTIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjI3OiJTa3VsZCBmw7ZyIEluZHVzdHJpc3luZGlrYXQiO31pOjIyNTA7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMjI1MCI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJTIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjI4OiJTa3VsZCBmw7ZyIFRyYW5zcG9ydHN5bmRpa2F0Ijt9aToyMjYwO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjIyNjAiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiUyI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoyMzoiU2t1bGQgZsO2ciBCeWdnc3luZGlrYXQiO31pOjIyNzA7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMjI3MCI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJTIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjM2OiJTa3VsZCBmw7ZyIEt1bHR1ci0gb2NoIE1lZGllc3luZGlrYXQiO31pOjIyODA7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMjI4MCI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJTIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjUwOiJTa3VsZCBmw7ZyIEtvbW11bmFsLSBvY2ggU3RhdHNhbnN0w6RsbGRhcyBzeW5kaWthdCI7fWk6MjI5MDtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIyMjkwIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IlMiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MjQ6IlNrdWxkIGbDtnIgc3luZGlrYXRsw7ZzYSI7fWk6MjQyMTtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIyNDIxIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IlMiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MjM6IlNrdWxkIGbDtnIgU0FDLWF2Z2lmdGVyIjt9aToyNDIyO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjI0MjIiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiUyI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoyODoiU2t1bGQgZsO2ciBkaXN0cmlrdHNhdmdpZnRlciI7fWk6MjQyNTtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIyNDI1IjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IlMiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MjI6IkbDtnJpbmJldGFsZGEgYXZnaWZ0ZXIiO31pOjMwMDA7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzAwMCI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjExOiJBQS1hdmdpZnRlciI7fWk6MzAwMTtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzMDAxIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTI6IkFBMS1hdmdpZnRlciI7fWk6MzAwMjtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzMDAyIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTI6IkFBMi1hdmdpZnRlciI7fWk6MzAwMztPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzMDAzIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTI6IkFBMy1hdmdpZnRlciI7fWk6MzAwNDtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzMDA0IjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTI6IkFBNC1hdmdpZnRlciI7fWk6MzAwNTtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzMDA1IjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTI6IkFBNS1hdmdpZnRlciI7fWk6MzAwNjtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzMDA2IjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTI6IkFBNi1hdmdpZnRlciI7fWk6MzAwNztPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzMDA3IjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTI6IkFBNy1hdmdpZnRlciI7fWk6MzEwMDtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzMTAwIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTA6IkEtYXZnaWZ0ZXIiO31pOjMxMDE7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzEwMSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjExOiJBMS1hdmdpZnRlciI7fWk6MzEwMjtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzMTAyIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTE6IkEyLWF2Z2lmdGVyIjt9aTozMTAzO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjMxMDMiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMToiQTMtYXZnaWZ0ZXIiO31pOjMxMDQ7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzEwNCI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjExOiJBNC1hdmdpZnRlciI7fWk6MzEwNTtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzMTA1IjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTE6IkE1LWF2Z2lmdGVyIjt9aTozMTA2O086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjMxMDYiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMToiQTYtYXZnaWZ0ZXIiO31pOjMxMDc7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzEwNyI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjExOiJBNy1hdmdpZnRlciI7fWk6MzIwMDtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzMjAwIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTA6IkItYXZnaWZ0ZXIiO31pOjMyMDE7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzIwMSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjExOiJCMS1hdmdpZnRlciI7fWk6MzIwMjtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzMjAyIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTE6IkIyLWF2Z2lmdGVyIjt9aTozMjAzO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjMyMDMiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMToiQjMtYXZnaWZ0ZXIiO31pOjMyMDQ7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzIwNCI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjExOiJCNC1hdmdpZnRlciI7fWk6MzIwNTtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzMjA1IjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTE6IkI1LWF2Z2lmdGVyIjt9aTozMjA2O086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjMyMDYiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMToiQjYtYXZnaWZ0ZXIiO31pOjMyMDc7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzIwNyI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjExOiJCNy1hdmdpZnRlciI7fWk6MzMwMDtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzMzAwIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTA6IkMtYXZnaWZ0ZXIiO31pOjMzMDE7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzMwMSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjExOiJDMS1hdmdpZnRlciI7fWk6MzMwMjtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzMzAyIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTE6IkMyLWF2Z2lmdGVyIjt9aTozMzAzO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjMzMDMiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMToiQzMtYXZnaWZ0ZXIiO31pOjMzMDQ7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzMwNCI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjExOiJDNC1hdmdpZnRlciI7fWk6MzMwNTtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzMzA1IjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTE6IkM1LWF2Z2lmdGVyIjt9aTozMzA2O086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjMzMDYiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMToiQzYtYXZnaWZ0ZXIiO31pOjMzMDc7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzMwNyI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjExOiJDNy1hdmdpZnRlciI7fWk6MzQwMDtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzNDAwIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTA6IkQtYXZnaWZ0ZXIiO31pOjM0MDE7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzQwMSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjExOiJEMS1hdmdpZnRlciI7fWk6MzQwMjtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzNDAyIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTE6IkQyLWF2Z2lmdGVyIjt9aTozNDAzO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjM0MDMiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMToiRDMtYXZnaWZ0ZXIiO31pOjM0MDQ7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzQwNCI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjExOiJENC1hdmdpZnRlciI7fWk6MzQwNTtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzNDA1IjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTE6IkQ1LWF2Z2lmdGVyIjt9aTozNDA2O086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjM0MDYiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMToiRDYtYXZnaWZ0ZXIiO31pOjM0MDc7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzQwNyI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjExOiJENy1hdmdpZnRlciI7fWk6Mzk5MDtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzOTkwIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6NjoiR8Oldm9yIjt9aTo0MTEwO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjQxMTAiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSyI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMjoiU0FDLWF2Z2lmdGVyIjt9aTo0MTIwO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjQxMjAiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSyI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxNzoiRGlzdHJpa3RzYXZnaWZ0ZXIiO31pOjQxMzA7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiNDEzMCI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJLIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjI5OiJTdHJpZHNmw7ZyYmVyZWRhbmRlIGtvc3RuYWRlciI7fWk6NDE0MDtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiI0MTQwIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IksiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6Mjk6IlN0dWRpZWbDtnJiZXJlZGFuZGUga29zdG5hZGVyIjt9aTo0MTUwO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjQxNTAiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSyI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxNjoiU3luZGlrYXRhdmdpZnRlciI7fX1zOjQyOiIAaXRielxTVEJcQWNjb3VudGluZ1xDaGFydE9mQWNjb3VudHMAX3R5cGUiO3M6NzoiRVVCQVM5NyI7fQ==','TzoyMToibXJlZ1xFY29ub215XENoYW5uZWxzIjoxOntzOjMyOiIAbXJlZ1xFY29ub215XENoYW5uZWxzAF9jaGFubmVscyI7YTo0NTp7czoxOiJLIjtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIxOTEwIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IlQiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6NToiS2Fzc2EiO31zOjI6IlBHIjtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIxOTIwIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IlQiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6ODoiUGx1c0dpcm8iO31zOjI6IkJHIjtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIxOTMwIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IlQiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6ODoiQmFua2dpcm8iO31zOjI6IkFHIjtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIxOTM1IjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IlQiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6ODoiQXV0b2dpcm8iO31zOjEzOiJzeW5kaWthdGzDtnNhIjtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIyMjkwIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IlMiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MjQ6IlNrdWxkIGbDtnIgc3luZGlrYXRsw7ZzYSI7fXM6MjoiQUEiO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjMwMDAiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMToiQUEtYXZnaWZ0ZXIiO31zOjM6IkFBMSI7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzAwMSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjEyOiJBQTEtYXZnaWZ0ZXIiO31zOjM6IkFBMiI7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzAwMiI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjEyOiJBQTItYXZnaWZ0ZXIiO31zOjM6IkFBMyI7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzAwMyI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjEyOiJBQTMtYXZnaWZ0ZXIiO31zOjM6IkFBNCI7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzAwNCI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjEyOiJBQTQtYXZnaWZ0ZXIiO31zOjM6IkFBNSI7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzAwNSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjEyOiJBQTUtYXZnaWZ0ZXIiO31zOjM6IkFBNiI7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzAwNiI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjEyOiJBQTYtYXZnaWZ0ZXIiO31zOjM6IkFBNyI7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzAwNyI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjEyOiJBQTctYXZnaWZ0ZXIiO31zOjE6IkEiO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjMxMDAiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMDoiQS1hdmdpZnRlciI7fXM6MjoiQTEiO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjMxMDEiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMToiQTEtYXZnaWZ0ZXIiO31zOjI6IkEyIjtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzMTAyIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTE6IkEyLWF2Z2lmdGVyIjt9czoyOiJBMyI7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzEwMyI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjExOiJBMy1hdmdpZnRlciI7fXM6MjoiQTQiO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjMxMDQiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMToiQTQtYXZnaWZ0ZXIiO31zOjI6IkE1IjtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzMTA1IjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTE6IkE1LWF2Z2lmdGVyIjt9czoyOiJBNiI7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzEwNiI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjExOiJBNi1hdmdpZnRlciI7fXM6MjoiQTciO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjMxMDciO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMToiQTctYXZnaWZ0ZXIiO31zOjE6IkIiO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjMyMDAiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMDoiQi1hdmdpZnRlciI7fXM6MjoiQjEiO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjMyMDEiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMToiQjEtYXZnaWZ0ZXIiO31zOjI6IkIyIjtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzMjAyIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTE6IkIyLWF2Z2lmdGVyIjt9czoyOiJCMyI7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzIwMyI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjExOiJCMy1hdmdpZnRlciI7fXM6MjoiQjQiO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjMyMDQiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMToiQjQtYXZnaWZ0ZXIiO31zOjI6IkI1IjtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzMjA1IjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTE6IkI1LWF2Z2lmdGVyIjt9czoyOiJCNiI7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzIwNiI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjExOiJCNi1hdmdpZnRlciI7fXM6MjoiQjciO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjMyMDciO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMToiQjctYXZnaWZ0ZXIiO31zOjE6IkMiO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjMzMDAiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMDoiQy1hdmdpZnRlciI7fXM6MjoiQzEiO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjMzMDEiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMToiQzEtYXZnaWZ0ZXIiO31zOjI6IkMyIjtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzMzAyIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTE6IkMyLWF2Z2lmdGVyIjt9czoyOiJDMyI7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzMwMyI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjExOiJDMy1hdmdpZnRlciI7fXM6MjoiQzQiO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjMzMDQiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMToiQzQtYXZnaWZ0ZXIiO31zOjI6IkM1IjtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzMzA1IjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTE6IkM1LWF2Z2lmdGVyIjt9czoyOiJDNiI7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzMwNiI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjExOiJDNi1hdmdpZnRlciI7fXM6MjoiQzciO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjMzMDciO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMToiQzctYXZnaWZ0ZXIiO31zOjE6IkQiO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjM0MDAiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMDoiRC1hdmdpZnRlciI7fXM6MjoiRDEiO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjM0MDEiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMToiRDEtYXZnaWZ0ZXIiO31zOjI6IkQyIjtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzNDAyIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTE6IkQyLWF2Z2lmdGVyIjt9czoyOiJEMyI7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzQwMyI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjExOiJEMy1hdmdpZnRlciI7fXM6MjoiRDQiO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjM0MDQiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMToiRDQtYXZnaWZ0ZXIiO31zOjI6IkQ1IjtPOjI3OiJpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQiOjM6e3M6MzY6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX251bWJlciI7czo0OiIzNDA1IjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF90eXBlIjtzOjE6IkkiO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX25hbWUiO3M6MTE6IkQ1LWF2Z2lmdGVyIjt9czoyOiJENiI7TzoyNzoiaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50IjozOntzOjM2OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9udW1iZXIiO3M6NDoiMzQwNiI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfdHlwZSI7czoxOiJJIjtzOjM0OiIAaXRielxTVEJcQWNjb3VudGluZ1xBY2NvdW50AF9uYW1lIjtzOjExOiJENi1hdmdpZnRlciI7fXM6MjoiRDciO086Mjc6Iml0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudCI6Mzp7czozNjoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbnVtYmVyIjtzOjQ6IjM0MDciO3M6MzQ6IgBpdGJ6XFNUQlxBY2NvdW50aW5nXEFjY291bnQAX3R5cGUiO3M6MToiSSI7czozNDoiAGl0YnpcU1RCXEFjY291bnRpbmdcQWNjb3VudABfbmFtZSI7czoxMToiRDctYXZnaWZ0ZXIiO319fQ==','[KontMall0]\r\nid=MAVG\r\nnamn=Bet. medlemsavgift\r\ntext=Medl.fakt. {F-NR} [M:{M-NR}] [OCR:{OCR}] {SPEC}\r\nRad0_radnr=1\r\nRad0_konto={AVGIFTSKANAL}\r\nRad0_belopp=-{AVGIFT}\r\nRad1_radnr=2\r\nRad1_konto=4110\r\nRad1_belopp={CENTRAL}\r\nRad2_radnr=3\r\nRad2_konto=2421\r\nRad2_belopp=-{CENTRAL}\r\nRad3_radnr=4\r\nRad3_konto=4120\r\nRad3_belopp={DISTRIKT}\r\nRad4_radnr=5\r\nRad4_konto=2422\r\nRad4_belopp=-{DISTRIKT}\r\nRad5_radnr=6\r\nRad5_konto=4130\r\nRad5_belopp={SF}\r\nRad6_radnr=7\r\nRad6_konto=2071\r\nRad6_belopp=-{SF}\r\nRad7_radnr=8\r\nRad7_konto=4150\r\nRad7_belopp={SYNDIKAT}\r\nRad8_radnr=9\r\nRad8_konto={SYNDIKATKANAL}\r\nRad8_belopp=-{SYNDIKAT}\r\nRad9_radnr=10\r\nRad9_konto={BETKANAL}\r\nRad9_belopp={SUMMA}\r\nRad10_radnr=11\r\nRad10_konto=3990\r\nRad10_belopp=-{REST}\r\nRad11_radnr=12\r\nRad11_konto=1510\r\nRad11_belopp={FORDRAN}\r\n[KontMall1]\r\nid=MSKULD\r\nnamn=Bet. medlemsfordran\r\ntext=Inbetald medlemsfordring fakt. {F-NR} [M:{M-NR}] [OCR:{OCR}]\r\nRad0_radnr=1\r\nRad0_konto=1510\r\nRad0_belopp=-{AVGIFT}\r\nRad1_radnr=2\r\nRad1_konto={BETKANAL}\r\nRad1_belopp={SUMMA}\r\nRad2_radnr=3\r\nRad2_konto=3990\r\nRad2_belopp=-{REST}\r\nRad3_radnr=4\r\nRad3_konto=1510\r\nRad3_belopp={FORDRAN}\r\n[KontMall2]\r\nid=CENTRAL\r\nnamn=Bet. central skuld\r\ntext=Bet.fakt. {F-NR} till centralt\r\nRad0_radnr=1\r\nRad0_konto=2421\r\nRad0_belopp={SUMMA}\r\nRad1_radnr=2\r\nRad1_konto=1920\r\nRad1_belopp=-{SUMMA}\r\n[KontMall3]\r\nid=\r\nnamn=Tom mall\r\ntext=Tom mall\r\n','TzoyNjoibXJlZ1xFY29ub215XFRhYmxlT2ZEZWJpdHMiOjI6e3M6NDE6IgBtcmVnXEVjb25vbXlcVGFibGVPZkRlYml0cwBfcmF3Q2xhc3NEYXRhIjthOjU6e3M6MjoiQUEiO2E6Mjp7czo0OiJiYXNlIjthOjM6e3M6ODoidGVtcGxhdGUiO3M6MDoiIjtzOjE0OiJpbnRlcnZhbF9zdGFydCI7TzoyMToiaXRielxTVEJcVXRpbHNcQW1vdW50IjoxOntzOjMwOiIAaXRielxTVEJcVXRpbHNcQW1vdW50AF9hbW91bnQiO2Q6MTkwMDA7fXM6NjoiY2hhcmdlIjtPOjIxOiJpdGJ6XFNUQlxVdGlsc1xBbW91bnQiOjE6e3M6MzA6IgBpdGJ6XFNUQlxVdGlsc1xBbW91bnQAX2Ftb3VudCI7ZDoxOTA7fX1zOjY6ImRlYml0cyI7YToxOntzOjM6IlNBQyI7TzoyMToiaXRielxTVEJcVXRpbHNcQW1vdW50IjoxOntzOjMwOiIAaXRielxTVEJcVXRpbHNcQW1vdW50AF9hbW91bnQiO2Q6MTkwO319fXM6MToiQSI7YToyOntzOjQ6ImJhc2UiO2E6Mzp7czo4OiJ0ZW1wbGF0ZSI7czowOiIiO3M6MTQ6ImludGVydmFsX3N0YXJ0IjtPOjIxOiJpdGJ6XFNUQlxVdGlsc1xBbW91bnQiOjE6e3M6MzA6IgBpdGJ6XFNUQlxVdGlsc1xBbW91bnQAX2Ftb3VudCI7ZDoxMzAwMDt9czo2OiJjaGFyZ2UiO086MjE6Iml0YnpcU1RCXFV0aWxzXEFtb3VudCI6MTp7czozMDoiAGl0YnpcU1RCXFV0aWxzXEFtb3VudABfYW1vdW50IjtkOjEyMDt9fXM6NjoiZGViaXRzIjthOjE6e3M6MzoiU0FDIjtPOjIxOiJpdGJ6XFNUQlxVdGlsc1xBbW91bnQiOjE6e3M6MzA6IgBpdGJ6XFNUQlxVdGlsc1xBbW91bnQAX2Ftb3VudCI7ZDoxMjA7fX19czoxOiJCIjthOjI6e3M6NDoiYmFzZSI7YTozOntzOjg6InRlbXBsYXRlIjtzOjA6IiI7czoxNDoiaW50ZXJ2YWxfc3RhcnQiO086MjE6Iml0YnpcU1RCXFV0aWxzXEFtb3VudCI6MTp7czozMDoiAGl0YnpcU1RCXFV0aWxzXEFtb3VudABfYW1vdW50IjtkOjYwMDA7fXM6NjoiY2hhcmdlIjtPOjIxOiJpdGJ6XFNUQlxVdGlsc1xBbW91bnQiOjE6e3M6MzA6IgBpdGJ6XFNUQlxVdGlsc1xBbW91bnQAX2Ftb3VudCI7ZDo1MDt9fXM6NjoiZGViaXRzIjthOjE6e3M6MzoiU0FDIjtPOjIxOiJpdGJ6XFNUQlxVdGlsc1xBbW91bnQiOjE6e3M6MzA6IgBpdGJ6XFNUQlxVdGlsc1xBbW91bnQAX2Ftb3VudCI7ZDo1MDt9fX1zOjE6IkMiO2E6Mjp7czo0OiJiYXNlIjthOjM6e3M6ODoidGVtcGxhdGUiO3M6MDoiIjtzOjE0OiJpbnRlcnZhbF9zdGFydCI7TzoyMToiaXRielxTVEJcVXRpbHNcQW1vdW50IjoxOntzOjMwOiIAaXRielxTVEJcVXRpbHNcQW1vdW50AF9hbW91bnQiO2Q6MDt9czo2OiJjaGFyZ2UiO086MjE6Iml0YnpcU1RCXFV0aWxzXEFtb3VudCI6MTp7czozMDoiAGl0YnpcU1RCXFV0aWxzXEFtb3VudABfYW1vdW50IjtkOjA7fX1zOjY6ImRlYml0cyI7YTowOnt9fXM6MToiRCI7YToyOntzOjQ6ImJhc2UiO2E6Mzp7czo4OiJ0ZW1wbGF0ZSI7czowOiIiO3M6MTQ6ImludGVydmFsX3N0YXJ0IjtPOjIxOiJpdGJ6XFNUQlxVdGlsc1xBbW91bnQiOjE6e3M6MzA6IgBpdGJ6XFNUQlxVdGlsc1xBbW91bnQAX2Ftb3VudCI7ZDowO31zOjY6ImNoYXJnZSI7TzoyMToiaXRielxTVEJcVXRpbHNcQW1vdW50IjoxOntzOjMwOiIAaXRielxTVEJcVXRpbHNcQW1vdW50AF9hbW91bnQiO2Q6MDt9fXM6NjoiZGViaXRzIjthOjA6e319fXM6Mzk6IgBtcmVnXEVjb25vbXlcVGFibGVPZkRlYml0cwBfZGViaXROYW1lcyI7YToxOntpOjA7czozOiJTQUMiO319',1);
/*!40000 ALTER TABLE `eco__Accountant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `eco__MemberInvoice`
--

DROP TABLE IF EXISTS `eco__MemberInvoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eco__MemberInvoice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'fakturanummer',
  `tCreated` int(11) NOT NULL DEFAULT '0',
  `tModified` int(11) NOT NULL DEFAULT '0',
  `owner` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'root',
  `group` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'treasurer',
  `mode` smallint(5) unsigned NOT NULL DEFAULT '504',
  `etag` char(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `modifiedBy` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'sys',
  `title` varchar(220) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  `recipientId` int(10) unsigned NOT NULL COMMENT 'Foreign key to dir__Faction::id',
  `payerId` int(10) unsigned NOT NULL COMMENT 'Foreign key to dir__Member::id',
  `ocr` varchar(23) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tExpire` int(10) unsigned NOT NULL,
  `tPrinted` int(10) unsigned NOT NULL DEFAULT '0',
  `tPaid` int(10) unsigned NOT NULL DEFAULT '0',
  `tExported` int(10) unsigned NOT NULL DEFAULT '0',
  `amount` decimal(10,2) NOT NULL,
  `isAutogiro` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 if registered for autogiro',
  `paidVia` enum('AG','BG','PG','K','') COLLATE utf8_swedish_ci NOT NULL DEFAULT '' COMMENT 'Payment channel used',
  `locked` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 if locked for editing',
  `description` varchar(500) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  `template` blob,
  `verification` blob,
  PRIMARY KEY (`id`),
  KEY `modifiedBy` (`modifiedBy`),
  KEY `group` (`group`),
  KEY `owner` (`owner`),
  KEY `recipientId` (`recipientId`),
  KEY `payerId` (`payerId`),
  KEY `paidVia` (`paidVia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci COMMENT='Invoices from Factions to Members';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eco__MemberInvoice`
--

LOCK TABLES `eco__MemberInvoice` WRITE;
/*!40000 ALTER TABLE `eco__MemberInvoice` DISABLE KEYS */;
/*!40000 ALTER TABLE `eco__MemberInvoice` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `eco__MemberInvoice_before_insert` BEFORE INSERT ON `eco__MemberInvoice`
FOR EACH ROW
BEGIN
	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`owner`, NEW.`group`, NEW.`mode`, NEW.`recipientId`, NEW.`payerId`, NEW.`amount`, NEW.`ocr`, NEW.`isAutogiro`, NEW.`paidVia`, NEW.`tExpire`, NEW.`tPrinted`, NEW.`tPaid`, NEW.`tExported`, NEW.`description`, NEW.`template`, NEW.`verification`, NEW.`title`));
	IF NEW.`tCreated` = '0' THEN
	    SET NEW.`tCreated` = UNIX_TIMESTAMP();
	END IF;
	SET NEW.`tModified`=NEW.`tCreated`;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `eco__MemberInvoice_before_update` BEFORE UPDATE ON `eco__MemberInvoice`
FOR EACH ROW
BEGIN
	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`owner`, NEW.`group`, NEW.`mode`, NEW.`recipientId`, NEW.`payerId`, NEW.`amount`, NEW.`ocr`, NEW.`isAutogiro`, NEW.`paidVia`, NEW.`tExpire`, NEW.`tPrinted`, NEW.`tPaid`, NEW.`tExported`, NEW.`description`, NEW.`template`, NEW.`verification`, NEW.`title`));
	SET NEW.`tModified` = UNIX_TIMESTAMP();
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `eco__MemberInvoice_after_update` AFTER UPDATE ON `eco__MemberInvoice`
FOR EACH ROW
BEGIN
	IF ( OLD.`owner` != NEW.`owner` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='eco__MemberInvoice',
			`ref_id`=NEW.`id`,
			`ref_column`='owner',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`owner`,
			`new_value`=NEW.`owner`;
	END IF;
	IF ( OLD.`group` != NEW.`group` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='eco__MemberInvoice',
			`ref_id`=NEW.`id`,
			`ref_column`='group',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`group`,
			`new_value`=NEW.`group`;
	END IF;
	IF ( OLD.`mode` != NEW.`mode` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='eco__MemberInvoice',
			`ref_id`=NEW.`id`,
			`ref_column`='mode',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`mode`,
			`new_value`=NEW.`mode`;
	END IF;
	IF ( OLD.`recipientId` != NEW.`recipientId` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='eco__MemberInvoice',
			`ref_id`=NEW.`id`,
			`ref_column`='recipientId',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`recipientId`,
			`new_value`=NEW.`recipientId`;
	END IF;
	IF ( OLD.`payerId` != NEW.`payerId` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='eco__MemberInvoice',
			`ref_id`=NEW.`id`,
			`ref_column`='payerId',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`payerId`,
			`new_value`=NEW.`payerId`;
	END IF;
	IF ( OLD.`amount` != NEW.`amount` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='eco__MemberInvoice',
			`ref_id`=NEW.`id`,
			`ref_column`='amount',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`amount`,
			`new_value`=NEW.`amount`;
	END IF;
	IF ( OLD.`isAutogiro` != NEW.`isAutogiro` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='eco__MemberInvoice',
			`ref_id`=NEW.`id`,
			`ref_column`='isAutogiro',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`isAutogiro`,
			`new_value`=NEW.`isAutogiro`;
	END IF;
	IF ( OLD.`paidVia` != NEW.`paidVia` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='eco__MemberInvoice',
			`ref_id`=NEW.`id`,
			`ref_column`='paidVia',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`paidVia`,
			`new_value`=NEW.`paidVia`;
	END IF;
	IF ( OLD.`description` != NEW.`description` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='eco__MemberInvoice',
			`ref_id`=NEW.`id`,
			`ref_column`='description',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`description`,
			`new_value`=NEW.`description`;
	END IF;
	IF ( OLD.`title` != NEW.`title` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='eco__MemberInvoice',
			`ref_id`=NEW.`id`,
			`ref_column`='title',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`title`,
			`new_value`=NEW.`title`;
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `lookup__Jobs`
--

DROP TABLE IF EXISTS `lookup__Jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lookup__Jobs` (
  `name` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  `count` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`name`),
  KEY `count` (`count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lookup__Jobs`
--

LOCK TABLES `lookup__Jobs` WRITE;
/*!40000 ALTER TABLE `lookup__Jobs` DISABLE KEYS */;
INSERT INTO `lookup__Jobs` VALUES ('A-kasse handläggare',0),('Administrativ koordinator',0),('Administrativarbetare',0),('Administratör',0),('Affärsbiträde',0),('Aktivitetsledare',0),('Alkohol &-drogterapeut',0),('Alkohol- och drogteapeut',0),('Anläggare',0),('Anläggningsarbetare',0),('Antikvarie',0),('Arbetsledare',0),('Arbetsterapeut',0),('Arkeolog',0),('Arkitekt',0),('Arkivarbetare',0),('Arkivarie',0),('Artist',0),('Assistent',0),('Barnskötare',0),('Bartender',0),('Behandlare',0),('Behandlingsassistent',0),('Bibliotekarie',0),('Biblioteksarbetare',0),('Biblioteksarbetare bibliotekarie',0),('Biblioteksassistent',0),('Bibliotikarie',0),('Bild- och webredaktör',0),('Biografanställd',0),('Biografarbetare',0),('Bitr. Rektor',0),('Boassistent',0),('Boendestödjare',0),('Bokbindare',0),('Bredbandsinstallatör',0),('Brevbärare',0),('Bryggeriarbetare',0),('Bud',0),('Bussarbetare',0),('Busschafför',0),('Bussförare',0),('Bussförare skolbuss',0),('Butiksansvarig',0),('Butiksarbetare',0),('Butiksarbetare biträde',0),('Butiksarbetare säljare',0),('Butiksbiträde',0),('Byggarbetare',0),('Byggnadsarbetare',0),('Byråsekreterare',0),('Cafearbetare',0),('Chaufför',0),('Chefredaktör',0),('Cirkelledare',0),('Civilingengör',0),('CNC-operatör',0),('Controller',0),('Data/IT',0),('Data/IT administratör',0),('Data/IT programmerare',0),('Data/IT speldesigner',0),('Data/IT tekniker',0),('Demokratiambassadör',0),('Diskare',0),('Doktorand',0),('Doktorand Genusvetenskap',0),('Dramapedagog',0),('Dödgrävare',0),('Ekonom',0),('Ekonomiassistent',0),('Elevassistent',0),('Enhetschef',0),('Expeditionsvakt',0),('Expeditör',0),('Fabriksarbetare',0),('Fabriksarbetare operatör',0),('Fabriksarbetare svetsare',0),('Familjeterapeut',0),('Fastighetsskötare',0),('Filmare',0),('Filmpedagog',0),('Formgivare',0),('Forskare',0),('Fotograf',0),('Frilansjournalist',0),('Fritidsarbetare',0),('Fritidsarbetare fritidsledare',0),('Fritidsassistent',0),('Fritidsledare',0),('Fritidspedagog',0),('Fältarbetare',0),('Färdtjänstförare',0),('Föreståndare',0),('Författare',0),('Förskollärare',0),('Försäljare',0),('Gitarrtekniker',0),('Grafiker',0),('Grafisk formgivare',0),('Grovarbetare',0),('Guide',0),('Gårdskarl',0),('Habiliteringshandledare',0),('Handformare',0),('Handledare',0),('Handläggare',0),('Hantverkare',0),('Hemstädare',0),('Hemtjänsten',0),('Hundskötare',0),('Håltagare',0),('Högskolelektor',0),('Illustratör',0),('Informationssekreterare',0),('Informatör',0),('Ingenjör',0),('Innesäljare',0),('Insamling',0),('IT-konsult',0),('IT-support',0),('IT-Systemadministratör',0),('Journalist',0),('Kanslist',0),('Kassabiträde',0),('Kassaföreståndare',0),('Kassör',0),('Klädinsamlare',0),('Kock',0),('Kommunalarbetare',0),('Kommunikatör',0),('Konsult',0),('Kontorist',0),('Kontrollrumsingenjör',0),('Koordinator',0),('Kulturkritiker',0),('Kulturproducent',0),('Kundservicevärd',0),('Kundtjänst',0),('Kundtjänsthandläggare',0),('Kurator',0),('Kvalitetsansvarig',0),('Kvinnojourssamordnare',0),('Kyltekniker',0),('Kyrkoarbetare',0),('Kyrkoarbetare kyrkogård',0),('Kyrkogårdsarbetare',0),('Lagerarbetare',0),('Lastbilschufför',0),('Layoutare',0),('Lektor',0),('Linjemontör',0),('Livsmedelsarbetare',0),('Ljudtekniker',0),('Lokalredaktör',0),('Lokalvårdare',0),('Lokförare',0),('Låssmed',0),('Lärarassistent',0),('Lärare',0),('Lärare data',0),('Lärare folkhögskola',0),('Lärare högstadiet',0),('Lärare media',0),('Lärare musik',0),('Lärare slöjd',0),('Lärare textil',0),('Lärarvikarie',0),('Lärling',0),('Läxhjälpare',0),('Manusskrivare',0),('Marknadsförare',0),('Maskinoperatör',0),('Massör',0),('Matros',0),('Medarbetare',0),('Mediaarbetare',0),('Mediaarbetare journalist',0),('Mediaarbetare producent',0),('Mediaarbetare redigerare',0),('Mediaarbetare reporter',0),('Mediaarbetare skribent',0),('Mediapedagog',0),('Medlemstödsutvecklare',0),('Medlemsvärvare',0),('Mentalskötare',0),('Miljökonsult',0),('Montör',0),('Museiarbetare pedagog',0),('Museumarbetare',0),('Musiker',0),('Möbeldesign',0),('Nätverkstekniker',0),('Ombud',0),('Ombudsperson',0),('Områdesutvecklare',0),('Operatör',0),('Ordningsvakt',0),('Parkeringsvakt',0),('Pedagog',0),('Pedagog medie',0),('Personlig assistent',0),('Plockare',0),('Politisk sekretare',0),('Politisk sekreterare',0),('Postarbetare brevbärare',0),('Postarbetare postiljon',0),('Postarbetare sorterare',0),('Postarbetare terminalarbetare',0),('Pressekreterare',0),('Producent',0),('Produktassistent',0),('Produktutvecklare',0),('Programmerare',0),('Projektassistent',0),('Projektkoordinatör',0),('Projektledare',0),('Projektmedarbetare',0),('Projektsamordnare',0),('Projektsekreterare',0),('Präst',0),('Psykolog',0),('Receptionist',0),('Redaktör',0),('Registratör',0),('Rehab assistent',0),('Reklamationsarbetare',0),('Reparatör',0),('Reporter',0),('Reseledare',0),('Restaurangarbetare',0),('Restaurangbiträde',0),('Retuschör',0),('Riggare',0),('Rivningsarbetare',0),('Rörmokare',0),('Rörmontör',0),('Sagoberättare',0),('Satellitförsäljare',0),('Sekreterare',0),('Servicetekniker',0),('Servitris',0),('Sjukgymnast',0),('Sjuksköterska',0),('Skoladministratör',0),('Skolvaktmästare',0),('Skorstensfejare',0),('Skönlitterär redaktör',0),('Skötare',0),('Smed',0),('Snickare',0),('Socialarbetare',0),('Socialarbetare biståndshandläggare',0),('Socialarbetare sekreterare',0),('Socialarbetare socionom',0),('Socialarbetare utredare',0),('Socialsekreterare',0),('Sociolog',0),('Socionom',0),('Solidaritetsarbetare',0),('Sotare',0),('Spårstädare',0),('Spärrvakt',0),('Steward',0),('Student',0),('Studieorganisatör',0),('Städare',0),('Supervisor',0),('Svetsare',0),('Systemoperatör',0),('Systemtekniker',0),('Systemutvecklare',0),('Systemutvecklare (konsult)',0),('Säljare',0),('Taxiförare',0),('Teaterarbetare',0),('Teaterarbetare belysningsmästare',0),('Teaterarbetare koreograf',0),('Teaterarbetare kostymör kostymteknare',0),('Teaterarbetare ljussättare',0),('Teaterarbetare scendekor',0),('Teaterarbetare skådespelare',0),('Teaterarbetare snickare',0),('Teaterarbetare tekniker',0),('Tecknare',0),('Tekniker',0),('Teknisk redaktör',0),('Teknisk samordnare',0),('Telefonförsäljare',0),('Telefonist',0),('Telefonkommunikatör',0),('Terminalarbetare',0),('Textilarbetare',0),('Textilslöjdlärare',0),('Tidningsbud',0),('Tidningsbärare',0),('Tjänsteman',0),('Trafikvärd',0),('Truckförare',0),('Tryckare',0),('Tryckeriarbetare',0),('Trädgårdsarbetare',0),('Trädgårsarbetare',0),('Tunnelbanearbetare',0),('Tunneltågförare',0),('Tågarbetare förare',0),('Tågarbetare konduktör',0),('Tågarbetare reparatör',0),('Tågarbetare spärrvakt',0),('Tågarbetarevärd',0),('Tågförare',0),('Tågvärd',0),('Undersköterska',0),('Universitetsarbetare',0),('Universitetsarbetare docent',0),('Universitetsarbetare forskare',0),('Universitetsarbetare Lärare',0),('Universitetsarbetare professor',0),('Universitetsarbetare vaktmästare',0),('Universitetslektor',0),('Universitetslektor docent',0),('Urmakare',0),('Utbildningsledare',0),('Utredare',0),('Utställningsintendent',0),('Utvecklare',0),('Vaktmästare',0),('Verksamhetsutvecklare',0),('Verkstadsarbetare',0),('Verkstadsarbetare mekaniker',0),('Verkstadsarbetare slipare',0),('Verkstadsarbetare svarvare',0),('Verkstadsmekaniker',0),('VVS-konsult',0),('Vårdarbetare',0),('Vårdarbetare biträde',0),('Vårdarbetare handledare',0),('Vårdarbetare heminstruktör',0),('Vårdarbetare hemvårdare',0),('Vårdarbetare kontaktperson',0),('Vårdarbetare läkare',0),('Vårdarbetare mentalskötare',0),('Vårdarbetare sjuksköterska',0),('Vårdarbetare vårdare',0),('Vårdarbetare överläkare',0),('Vårdare',0),('Vårdbehovsutredare',0),('Vårdbiträde',0),('Värvare',0),('Växttekniker',0),('Web',0),('Återvinningsarbetare',0),('Översättare',0);
/*!40000 ALTER TABLE `lookup__Jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `search__active`
--

DROP TABLE IF EXISTS `search__active`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `search__active` (
  `uri` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  `title` varchar(200) COLLATE utf8_swedish_ci NOT NULL,
  `id` varchar(20) COLLATE utf8_swedish_ci NOT NULL,
  `type` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  `description` varchar(200) COLLATE utf8_swedish_ci NOT NULL,
  `misc` longtext COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`uri`),
  FULLTEXT KEY `title` (`title`,`description`,`misc`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `search__active`
--

LOCK TABLES `search__active` WRITE;
/*!40000 ALTER TABLE `search__active` DISABLE KEYS */;
/*!40000 ALTER TABLE `search__active` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sys__Group`
--

DROP TABLE IF EXISTS `sys__Group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys__Group` (
  `name` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tCreated` int(11) NOT NULL DEFAULT '0',
  `tModified` int(11) NOT NULL DEFAULT '0',
  `owner` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'root',
  `group` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'user-edit',
  `mode` smallint(5) unsigned NOT NULL DEFAULT '504',
  `etag` char(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `modifiedBy` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'sys',
  `description` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`name`),
  KEY `owner` (`owner`),
  KEY `group` (`group`),
  KEY `modifiedBy` (`modifiedBy`),
  CONSTRAINT `sys__Group_ibfk_1` FOREIGN KEY (`owner`) REFERENCES `sys__User` (`uname`) ON UPDATE CASCADE,
  CONSTRAINT `sys__Group_ibfk_2` FOREIGN KEY (`group`) REFERENCES `sys__Group` (`name`) ON UPDATE CASCADE,
  CONSTRAINT `sys__Group_ibfk_3` FOREIGN KEY (`modifiedBy`) REFERENCES `sys__User` (`uname`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sys__Group`
--

LOCK TABLES `sys__Group` WRITE;
/*!40000 ALTER TABLE `sys__Group` DISABLE KEYS */;
INSERT INTO `sys__Group` VALUES ('group-edit',1320879600,1331206713,'root','user-edit',504,'c758e6fc36ee690392528aa5c642a458','sys','Arbeta med grupper'),('malmols',1321977711,1331206714,'root','user-edit',504,'1b0d16e3371c52116e641d977433392a','sys','Generisk grupp för Malmö'),('mem-edit',1320879600,1331206714,'root','user-edit',504,'81a74411c4060524432cbe357f226735','sys','Arbeta med medlemmar'),('root',1320879600,1331206714,'root','root',504,'b40a1746d68f4c836e241e533ea455c3','sys','system administrators'),('sthlmls',1321977726,1331206714,'root','user-edit',504,'a7f7f445b5fe5a94f5382867496fe502','sys','Generisk grupp för Stockholms'),('treasurer',1320879600,1331206714,'root','user-edit',504,'f8e4ad58638b580ab7a15a6b73618edf','sys','Arbeta med transaktioner'),('user-edit',1320879600,1331206714,'root','root',504,'251c0403366d591ef3f80019c287ccc7','sys','Arbeta med systemanvändare och systemgrupper');
/*!40000 ALTER TABLE `sys__Group` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `sys__Group_before_insert` BEFORE INSERT ON `sys__Group`
FOR EACH ROW
BEGIN
	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`owner`, NEW.`group`, NEW.`mode`, NEW.`description`));
	IF NEW.`tCreated` = '0' THEN
	    SET NEW.`tCreated` = UNIX_TIMESTAMP();
	END IF;
	SET NEW.`tModified`=NEW.`tCreated`;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `sys__Group_before_update` BEFORE UPDATE ON `sys__Group`
FOR EACH ROW
BEGIN
	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`owner`, NEW.`group`, NEW.`mode`, NEW.`description`));
	SET NEW.`tModified` = UNIX_TIMESTAMP();
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `sys__Group_after_update` AFTER UPDATE ON `sys__Group`
FOR EACH ROW
BEGIN
	IF ( OLD.`owner` != NEW.`owner` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='sys__Group',
			`ref_id`=NEW.`name`,
			`ref_column`='owner',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`owner`,
			`new_value`=NEW.`owner`;
	END IF;
	IF ( OLD.`group` != NEW.`group` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='sys__Group',
			`ref_id`=NEW.`name`,
			`ref_column`='group',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`group`,
			`new_value`=NEW.`group`;
	END IF;
	IF ( OLD.`mode` != NEW.`mode` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='sys__Group',
			`ref_id`=NEW.`name`,
			`ref_column`='mode',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`mode`,
			`new_value`=NEW.`mode`;
	END IF;
	IF ( OLD.`description` != NEW.`description` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='sys__Group',
			`ref_id`=NEW.`name`,
			`ref_column`='description',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`description`,
			`new_value`=NEW.`description`;
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `sys__Log`
--

DROP TABLE IF EXISTS `sys__Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys__Log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `level` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `user` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ip` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `http_method` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(300) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=ARCHIVE DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sys__Log`
--

LOCK TABLES `sys__Log` WRITE;
/*!40000 ALTER TABLE `sys__Log` DISABLE KEYS */;
/*!40000 ALTER TABLE `sys__Log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sys__Session`
--

DROP TABLE IF EXISTS `sys__Session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys__Session` (
  `id` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `updated` int(11) NOT NULL DEFAULT '1',
  `user` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `error` varchar(200) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  `data` varchar(500) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sys__Session`
--

LOCK TABLES `sys__Session` WRITE;
/*!40000 ALTER TABLE `sys__Session` DISABLE KEYS */;
/*!40000 ALTER TABLE `sys__Session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sys__Setting`
--

DROP TABLE IF EXISTS `sys__Setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys__Setting` (
  `name` varchar(30) COLLATE utf8_swedish_ci NOT NULL,
  `tCreated` int(11) NOT NULL DEFAULT '0',
  `tModified` int(11) NOT NULL DEFAULT '0',
  `owner` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'root',
  `group` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'root',
  `mode` smallint(5) unsigned NOT NULL DEFAULT '484',
  `etag` char(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `modifiedBy` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'sys',
  `value` text COLLATE utf8_swedish_ci NOT NULL,
  `comment` text COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`name`),
  KEY `owner` (`owner`),
  KEY `group` (`group`),
  KEY `modifiedBy` (`modifiedBy`),
  CONSTRAINT `sys__Setting_ibfk_1` FOREIGN KEY (`owner`) REFERENCES `sys__User` (`uname`) ON UPDATE CASCADE,
  CONSTRAINT `sys__Setting_ibfk_2` FOREIGN KEY (`group`) REFERENCES `sys__Group` (`name`) ON UPDATE CASCADE,
  CONSTRAINT `sys__Setting_ibfk_3` FOREIGN KEY (`modifiedBy`) REFERENCES `sys__User` (`uname`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci COMMENT='System setings NOTE: changing values may damage the system!';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sys__Setting`
--

LOCK TABLES `sys__Setting` WRITE;
/*!40000 ALTER TABLE `sys__Setting` DISABLE KEYS */;
INSERT INTO `sys__Setting` VALUES ('auth.blockAfterFailures',1331132334,1331206850,'root','root',484,'8fcf7eb46c9015bf7aab64f47dcfa502','sys','3','Block user after number of successive failed auth attempts, 0 disables feature.'),('auth.blockAfterInactive',1331132334,1331206850,'root','root',484,'27515b376824f55b7a991870dcdaaa2d','sys','3','Block user after number of inactive months (no auths), 0 disables feature.'),('auth.checkSingeSession',1331132334,1331206850,'root','root',484,'94fa762e7400a3b15cc85f5d0060a554','sys','0','If set to 1 users can have only one active session.'),('auth.enableFakeAuth',1336569870,1336569870,'root','root',484,'3ef0980b86b15095ee32bf77b30a41b7','sys','1','Enable automatic root authentication when there is no referer header and user ip matches server ip'),('auth.pswdLifeSpan',1331132334,1348679958,'root','root',484,'9d3ec9303956dd1120ed0060ac499915','sys','6','Block passwords older than number of months, 0 disables feature.'),('auth.pswdMinEntropy',1331132334,1348576662,'root','root',484,'d76b8cfb7c0604e8b63a9416c01de417','sys','100','Minimal entropy-points for created passwords. Lower values allows simpler passwords.'),('revision.maxNrOfPosts',1331132334,1331206850,'root','root',484,'43816993aa9a0c750604622976a1cb8e','sys','5','Antal ändringar av enskilda värden som ska sparas.');
/*!40000 ALTER TABLE `sys__Setting` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `sys__Setting_before_insert` BEFORE INSERT ON `sys__Setting`
FOR EACH ROW
BEGIN
	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`owner`, NEW.`group`, NEW.`mode`, NEW.`value`, NEW.`comment`));
	IF NEW.`tCreated` = '0' THEN
	    SET NEW.`tCreated` = UNIX_TIMESTAMP();
	END IF;
	SET NEW.`tModified`=NEW.`tCreated`;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `sys__Setting_before_update` BEFORE UPDATE ON `sys__Setting`
FOR EACH ROW
BEGIN
	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`owner`, NEW.`group`, NEW.`mode`, NEW.`value`, NEW.`comment`));
	SET NEW.`tModified` = UNIX_TIMESTAMP();
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `sys__Setting_after_update` AFTER UPDATE ON `sys__Setting`
FOR EACH ROW
BEGIN
	IF ( OLD.`owner` != NEW.`owner` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='sys__Setting',
			`ref_id`=NEW.`name`,
			`ref_column`='owner',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`owner`,
			`new_value`=NEW.`owner`;
	END IF;
	IF ( OLD.`group` != NEW.`group` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='sys__Setting',
			`ref_id`=NEW.`name`,
			`ref_column`='group',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`group`,
			`new_value`=NEW.`group`;
	END IF;
	IF ( OLD.`mode` != NEW.`mode` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='sys__Setting',
			`ref_id`=NEW.`name`,
			`ref_column`='mode',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`mode`,
			`new_value`=NEW.`mode`;
	END IF;
	IF ( OLD.`value` != NEW.`value` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='sys__Setting',
			`ref_id`=NEW.`name`,
			`ref_column`='value',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`value`,
			`new_value`=NEW.`value`;
	END IF;
	IF ( OLD.`comment` != NEW.`comment` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='sys__Setting',
			`ref_id`=NEW.`name`,
			`ref_column`='comment',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`comment`,
			`new_value`=NEW.`comment`;
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `sys__User`
--

DROP TABLE IF EXISTS `sys__User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys__User` (
  `uname` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tCreated` int(11) NOT NULL DEFAULT '0',
  `tModified` int(11) NOT NULL DEFAULT '0',
  `owner` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'root',
  `group` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'user-edit',
  `mode` smallint(5) unsigned NOT NULL DEFAULT '504',
  `etag` char(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `modifiedBy` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'sys',
  `password` char(60) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  `tPswdCreated` int(11) NOT NULL DEFAULT '0',
  `groups` varchar(200) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  `status` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0 equals active user. Anything else is an error code.',
  `nInvalidAuths` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Number of sequential invalid auths',
  `tLogin` int(11) NOT NULL DEFAULT '0' COMMENT 'Timestamp for last login',
  `tLastLogin` int(11) NOT NULL DEFAULT '0' COMMENT 'Timestamp for login before last',
  `nLogins` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Number of logins',
  `fullname` varchar(200) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  `notes` text COLLATE utf8_swedish_ci,
  `accountingFor` int(10) unsigned DEFAULT NULL COMMENT 'Foreign key to dir__Faction::id',
  PRIMARY KEY (`uname`),
  KEY `owner` (`owner`),
  KEY `group` (`group`),
  KEY `modifiedBy` (`modifiedBy`),
  KEY `accountingFor` (`accountingFor`),
  CONSTRAINT `sys__User_ibfk_1` FOREIGN KEY (`owner`) REFERENCES `sys__User` (`uname`) ON UPDATE CASCADE,
  CONSTRAINT `sys__User_ibfk_2` FOREIGN KEY (`group`) REFERENCES `sys__Group` (`name`) ON UPDATE CASCADE,
  CONSTRAINT `sys__User_ibfk_3` FOREIGN KEY (`modifiedBy`) REFERENCES `sys__User` (`uname`) ON UPDATE CASCADE,
  CONSTRAINT `sys__User_ibfk_4` FOREIGN KEY (`accountingFor`) REFERENCES `dir__Faction` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sys__User`
--

LOCK TABLES `sys__User` WRITE;
/*!40000 ALTER TABLE `sys__User` DISABLE KEYS */;
INSERT INTO `sys__User` VALUES ('cli',1340834983,1340834983,'root','root',504,'dec0b3dd5f677abfdfc6c817421f4c9c','sys','',0,'root',3,0,0,0,0,'cli','Systemets användare för att köra cli-skript',NULL),('kobe75',1321978036,1348580764,'root','sthlmls',504,'d4282da9435a296f0346ed6f1f1b8b20','test','$2a$12$7GS.YU5v5bdY4D1anOt/k.ehzgneWDPXpnc1wFGCW6xVcuNbCLo5m',1348578832,'sthlmls,mem-edit,group-edit,user-edit,treasurer',0,0,1341271190,1341271190,0,'Klas Bäckelin','',NULL),('malmols',1321977916,1348580764,'root','malmols',504,'3c04f743b1ac52c22615f1cc14a10691','sys','$2a$12$QrQQShkaGXtymrUUL6HImerggVqsp9SOiAquVu5zhW0WIpF6r/J4i',1348578832,'malmols,mem-edit,group-edit,user-edit,treasurer',0,0,1341271190,1341271190,0,'Generisk användare för Malmö',NULL,NULL),('root',1313156181,1348580764,'root','root',288,'0cbfb8a784256ab8418f6459c1279b3d','sys','$2a$12$WBKUOvcRZeaxZ1dKfLpDqecE3m/YXUTqN2NQ.S7Y0Me701iZQwDiu',1348578832,'group-edit',0,1,1332219600,1332219600,4,'root','Systemets root-användare. Ska endast användas för att skapa sysadmins (dvs. användare i gruppen root). Använd ej för det dagliga arbetet.',NULL),('sys',1320916780,1348575386,'root','root',288,'d9dabb49d8746fbf74f519c4cfff7fca','sys','',0,'root',3,0,1332219600,1332219600,0,'tecnical user','Teknisk användare som används för att spåra ändringar i databasen initierade av systemet. Används ej för inloggningar.',NULL),('test',1970,1348580764,'root','user-edit',504,'86f8f2c13da6fb64d7786e75b0235e46','sys','$2a$12$dnVj0oNE58gstwbkn.bXTODD8aNq.tBH0Dz4wDc5MC7hQJYn25LNu',1348578832,'root,group-edit,mem-edit,user-edit',0,0,1332219600,1332219600,1323,'test','test',NULL);
/*!40000 ALTER TABLE `sys__User` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `sys__User_before_insert` BEFORE INSERT ON `sys__User`
FOR EACH ROW
BEGIN
	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`owner`, NEW.`group`, NEW.`mode`, NEW.`status`, NEW.`password`, NEW.`fullname`, NEW.`notes`));
	IF NEW.`tCreated` = '0' THEN
	    SET NEW.`tCreated` = UNIX_TIMESTAMP();
	END IF;
	SET NEW.`tModified`=NEW.`tCreated`;
	SET NEW.`tLogin` = UNIX_TIMESTAMP();
	SET NEW.`tLastLogin` = UNIX_TIMESTAMP();
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `sys__User_before_update` BEFORE UPDATE ON `sys__User`
FOR EACH ROW
BEGIN
	SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`owner`, NEW.`group`, NEW.`mode`, NEW.`status`, NEW.`password`, NEW.`fullname`, NEW.`notes`));
	SET NEW.`tModified` = UNIX_TIMESTAMP();
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `sys__User_after_update` AFTER UPDATE ON `sys__User`
FOR EACH ROW
BEGIN
	IF ( OLD.`owner` != NEW.`owner` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='sys__User',
			`ref_id`=NEW.`uname`,
			`ref_column`='owner',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`owner`,
			`new_value`=NEW.`owner`;
	END IF;
	IF ( OLD.`group` != NEW.`group` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='sys__User',
			`ref_id`=NEW.`uname`,
			`ref_column`='group',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`group`,
			`new_value`=NEW.`group`;
	END IF;
	IF ( OLD.`mode` != NEW.`mode` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='sys__User',
			`ref_id`=NEW.`uname`,
			`ref_column`='mode',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`mode`,
			`new_value`=NEW.`mode`;
	END IF;
	IF ( OLD.`status` != NEW.`status` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='sys__User',
			`ref_id`=NEW.`uname`,
			`ref_column`='status',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`status`,
			`new_value`=NEW.`status`;
	END IF;
	IF ( OLD.`fullname` != NEW.`fullname` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='sys__User',
			`ref_id`=NEW.`uname`,
			`ref_column`='fullname',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`fullname`,
			`new_value`=NEW.`fullname`;
	END IF;
	IF ( OLD.`notes` != NEW.`notes` ) THEN
		INSERT INTO `aux__Revision` SET
			`tModified`=UNIX_TIMESTAMP(),
			`ref_table`='sys__User',
			`ref_id`=NEW.`uname`,
			`ref_column`='notes',
			`modifiedBy`=NEW.`modifiedBy`,
			`old_value`=OLD.`notes`,
			`new_value`=NEW.`notes`;
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `xref__Faction_Faction`
--

DROP TABLE IF EXISTS `xref__Faction_Faction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xref__Faction_Faction` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tSince` int(11) NOT NULL DEFAULT '0',
  `tUnto` int(11) NOT NULL DEFAULT '0',
  `master_id` int(10) unsigned NOT NULL COMMENT 'container faction',
  `foreign_id` int(10) unsigned NOT NULL COMMENT 'member faction',
  `state` enum('OK','HISTORIC') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'OK' COMMENT 'Value other then OK signifies ''not currently a member of''',
  `stateComment` varchar(50) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`tUnto`,`master_id`,`foreign_id`,`state`),
  KEY `foreign_id` (`foreign_id`),
  KEY `master_id` (`master_id`,`foreign_id`),
  CONSTRAINT `xref__Faction_Faction_ibfk_1` FOREIGN KEY (`master_id`) REFERENCES `dir__Faction` (`id`),
  CONSTRAINT `xref__Faction_Faction_ibfk_2` FOREIGN KEY (`foreign_id`) REFERENCES `dir__Faction` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xref__Faction_Faction`
--

LOCK TABLES `xref__Faction_Faction` WRITE;
/*!40000 ALTER TABLE `xref__Faction_Faction` DISABLE KEYS */;
/*!40000 ALTER TABLE `xref__Faction_Faction` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `xref__Faction_Faction_before_insert` BEFORE INSERT ON `xref__Faction_Faction`
FOR EACH ROW
BEGIN
	IF NEW.`tSince` = '0' THEN
	    SET NEW.`tSince` = UNIX_TIMESTAMP();
	END IF;
	IF NEW.`state` != 'OK' AND NEW.`tUnto` = '0' THEN
	    SET NEW.`tUnto` = UNIX_TIMESTAMP();
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `xref__Faction_Faction_before_update` BEFORE UPDATE ON `xref__Faction_Faction`
FOR EACH ROW
BEGIN
	IF OLD.`state` = 'OK' AND NEW.`state` != 'OK' AND NEW.`tUnto` = '0' THEN
	    SET NEW.`tUnto` = UNIX_TIMESTAMP();
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `xref__Faction_Member`
--

DROP TABLE IF EXISTS `xref__Faction_Member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xref__Faction_Member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tSince` int(11) NOT NULL DEFAULT '0',
  `tUnto` int(11) NOT NULL DEFAULT '0',
  `master_id` int(10) unsigned NOT NULL COMMENT 'Containing faction',
  `foreign_id` int(10) unsigned NOT NULL COMMENT 'Member',
  `state` enum('OK','UTTRÄDD','UTESLUTEN','ÖVERGÅNG','AVLIDEN','PENSION','ANNAT') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'OK' COMMENT 'Value other then OK signifies ''not currently a member of''',
  `stateComment` varchar(50) COLLATE utf8_swedish_ci NOT NULL DEFAULT '' COMMENT 'Used with UTTRÄDD, UTESLUTEN, ÖVERGÅNG and ANNAT',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`tUnto`,`master_id`,`foreign_id`,`state`),
  KEY `foreign_id` (`foreign_id`),
  KEY `master_id` (`master_id`,`foreign_id`),
  CONSTRAINT `xref__Faction_Member_ibfk_1` FOREIGN KEY (`master_id`) REFERENCES `dir__Faction` (`id`),
  CONSTRAINT `xref__Faction_Member_ibfk_2` FOREIGN KEY (`foreign_id`) REFERENCES `dir__Member` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xref__Faction_Member`
--

LOCK TABLES `xref__Faction_Member` WRITE;
/*!40000 ALTER TABLE `xref__Faction_Member` DISABLE KEYS */;
/*!40000 ALTER TABLE `xref__Faction_Member` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `xref__Faction_Member_before_insert` BEFORE INSERT ON `xref__Faction_Member`
FOR EACH ROW
BEGIN
	IF NEW.`tSince` = '0' THEN
	    SET NEW.`tSince` = UNIX_TIMESTAMP();
	END IF;
	IF NEW.`state` != 'OK' AND NEW.`tUnto` = '0' THEN
	    SET NEW.`tUnto` = UNIX_TIMESTAMP();
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `xref__Faction_Member_after_insert` AFTER INSERT ON `xref__Faction_Member`
FOR EACH ROW
BEGIN
	DECLARE factionType VARCHAR(20);
	DECLARE factionName VARCHAR(100);
	SELECT `type`, `name` FROM `dir__Faction`
	    WHERE `id`= NEW.`master_id`
	    INTO factionType, factionName;
	IF factionType = 'LS' AND NEW.`state` = 'OK' THEN
	    UPDATE `dir__Member`
	    SET `LS` = factionName
	    WHERE `id`=NEW.`foreign_id`;
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `xref__Faction_Member_before_update` BEFORE UPDATE ON `xref__Faction_Member`
FOR EACH ROW
BEGIN
	IF OLD.`state` = 'OK' AND NEW.`state` != 'OK' AND NEW.`tUnto` = '0' THEN
	    SET NEW.`tUnto` = UNIX_TIMESTAMP();
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `xref__Faction_Member_after_update` AFTER UPDATE ON `xref__Faction_Member`
FOR EACH ROW
BEGIN
	DECLARE factionType VARCHAR(20);
	DECLARE factionName VARCHAR(100);
	SELECT `type`, `name` FROM `dir__Faction`
	    WHERE `id`= NEW.`master_id`
	    INTO factionType, factionName;
	IF factionType = 'LS' AND (NEW.`state` = 'OK' OR OLD.`state` = 'OK') THEN
	    UPDATE `dir__Member`
	    SET `LS` = factionName
	    WHERE `id`=NEW.`foreign_id`;
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `xref__Faction_Workplace`
--

DROP TABLE IF EXISTS `xref__Faction_Workplace`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xref__Faction_Workplace` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tSince` int(11) NOT NULL DEFAULT '0',
  `tUnto` int(11) NOT NULL DEFAULT '0',
  `master_id` int(10) unsigned NOT NULL COMMENT 'Containing faction',
  `foreign_id` int(10) unsigned NOT NULL COMMENT 'Workplace',
  `state` enum('OK','HISTORIC') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'OK' COMMENT 'Value other then OK signifies ''not currently a member of''',
  `stateComment` varchar(50) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`tUnto`,`master_id`,`foreign_id`,`state`),
  KEY `master_id` (`master_id`,`foreign_id`),
  KEY `foreign_id` (`foreign_id`),
  CONSTRAINT `xref__Faction_Workplace_ibfk_1` FOREIGN KEY (`master_id`) REFERENCES `dir__Faction` (`id`),
  CONSTRAINT `xref__Faction_Workplace_ibfk_2` FOREIGN KEY (`foreign_id`) REFERENCES `dir__Workplace` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xref__Faction_Workplace`
--

LOCK TABLES `xref__Faction_Workplace` WRITE;
/*!40000 ALTER TABLE `xref__Faction_Workplace` DISABLE KEYS */;
/*!40000 ALTER TABLE `xref__Faction_Workplace` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `xref__Faction_Workplace_before_insert` BEFORE INSERT ON `xref__Faction_Workplace`
FOR EACH ROW
BEGIN
	IF NEW.`tSince` = '0' THEN
	    SET NEW.`tSince` = UNIX_TIMESTAMP();
	END IF;
	IF NEW.`state` != 'OK' AND NEW.`tUnto` = '0' THEN
	    SET NEW.`tUnto` = UNIX_TIMESTAMP();
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `xref__Faction_Workplace_before_update` BEFORE UPDATE ON `xref__Faction_Workplace`
FOR EACH ROW
BEGIN
	IF OLD.`state` = 'OK' AND NEW.`state` != 'OK' AND NEW.`tUnto` = '0' THEN
	    SET NEW.`tUnto` = UNIX_TIMESTAMP();
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `xref__Workplace_Member`
--

DROP TABLE IF EXISTS `xref__Workplace_Member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xref__Workplace_Member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tSince` int(11) NOT NULL DEFAULT '0',
  `tUnto` int(11) NOT NULL DEFAULT '0',
  `master_id` int(10) unsigned NOT NULL COMMENT 'Containing workplace',
  `foreign_id` int(10) unsigned NOT NULL COMMENT 'member',
  `state` enum('OK','HISTORIC') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'OK' COMMENT 'Value other then OK signifies ''not currently a member of''',
  `stateComment` varchar(50) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`tUnto`,`master_id`,`foreign_id`,`state`),
  KEY `foreign_id` (`foreign_id`),
  KEY `master_id` (`master_id`,`foreign_id`),
  CONSTRAINT `xref__Workplace_Member_ibfk_1` FOREIGN KEY (`master_id`) REFERENCES `dir__Workplace` (`id`),
  CONSTRAINT `xref__Workplace_Member_ibfk_2` FOREIGN KEY (`foreign_id`) REFERENCES `dir__Member` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xref__Workplace_Member`
--

LOCK TABLES `xref__Workplace_Member` WRITE;
/*!40000 ALTER TABLE `xref__Workplace_Member` DISABLE KEYS */;
/*!40000 ALTER TABLE `xref__Workplace_Member` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `xref__Workplace_Member_before_insert` BEFORE INSERT ON `xref__Workplace_Member`
FOR EACH ROW
BEGIN
	IF NEW.`tSince` = '0' THEN
	    SET NEW.`tSince` = UNIX_TIMESTAMP();
	END IF;
	IF NEW.`state` != 'OK' AND NEW.`tUnto` = '0' THEN
	    SET NEW.`tUnto` = UNIX_TIMESTAMP();
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `xref__Workplace_Member_before_update` BEFORE UPDATE ON `xref__Workplace_Member`
FOR EACH ROW
BEGIN
	IF OLD.`state` = 'OK' AND NEW.`state` != 'OK' AND NEW.`tUnto` = '0' THEN
	    SET NEW.`tUnto` = UNIX_TIMESTAMP();
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Dumping events for database 'mreg_empty'
--
/*!50106 SET @save_time_zone= @@TIME_ZONE */ ;
/*!50106 DROP EVENT IF EXISTS `clear_session` */;
DELIMITER ;;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;;
/*!50003 SET character_set_client  = utf8 */ ;;
/*!50003 SET character_set_results = utf8 */ ;;
/*!50003 SET collation_connection  = utf8_general_ci */ ;;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;;
/*!50003 SET @saved_time_zone      = @@time_zone */ ;;
/*!50003 SET time_zone             = 'SYSTEM' */ ;;
/*!50106 CREATE*/ /*!50117 DEFINER=`root`@`localhost`*/ /*!50106 EVENT `clear_session` ON SCHEDULE EVERY 5 MINUTE STARTS '2012-06-25 18:36:58' ON COMPLETION NOT PRESERVE ENABLE COMMENT 'Remove hour old sessions, logout 30 min old.' DO BEGIN
		
		DELETE FROM `mreg`.`sys__Session`
			WHERE UNIX_TIMESTAMP() - 36000 > `updated`;
		
		
		UPDATE `mreg`.`sys__Session`
			SET `error` = 'Du loggades ut på grund av över 30 minuters inaktivitet'
			WHERE UNIX_TIMESTAMP() - 1800 > `updated`;
	END */ ;;
/*!50003 SET time_zone             = @saved_time_zone */ ;;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;;
/*!50003 SET character_set_client  = @saved_cs_client */ ;;
/*!50003 SET character_set_results = @saved_cs_results */ ;;
/*!50003 SET collation_connection  = @saved_col_connection */ ;;
DELIMITER ;
/*!50106 SET TIME_ZONE= @save_time_zone */ ;

--
-- Dumping routines for database 'mreg_empty'
--
/*!50003 DROP FUNCTION IF EXISTS `in_csv` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 FUNCTION `in_csv`( needle VARCHAR(100), haystack TEXT ) RETURNS tinyint(1)
    DETERMINISTIC
    SQL SECURITY INVOKER
    COMMENT 'True if needle exists in csv haystack'
BEGIN
		
		DECLARE delim CHAR(1) DEFAULT ',';

		DECLARE pos_start INT DEFAULT 1;
		DECLARE pos_end INT;
		DECLARE len INT;
		DECLARE teststr VARCHAR(100);

		SET haystack = CONCAT(haystack, delim);

		haystackloop: LOOP
			SET pos_end = LOCATE(delim, haystack, pos_start);
			IF pos_end = 0 THEN
				RETURN 0;
			END IF;
			SET len = pos_end - pos_start;
			SET teststr = SUBSTR(haystack, pos_start, len);
			IF teststr = needle THEN
				RETURN 1;
			END IF;
			SET pos_start = pos_end + 1;
		END LOOP;
	END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `isAllowed` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 FUNCTION `isAllowed`( action CHAR(1), owner VARCHAR(10), owner_grp VARCHAR(10), access_mode SMALLINT(2), uname VARCHAR(10), ugrps VARCHAR(200) ) RETURNS tinyint(1)
    DETERMINISTIC
    SQL SECURITY INVOKER
    COMMENT 'Returns 1 if action is allowed, 0 otherwise.'
BEGIN
		DECLARE mask SMALLINT(2) DEFAULT 7;

		
		IF uname = 'root' OR in_csv('root', ugrps) THEN
			RETURN 1;
		END IF;

		
		IF action = 'r' THEN
			SET mask = 4;
		ELSEIF action = 'w' THEN
			SET mask = 2;
		ELSEIF action = 'x' THEN
			SET mask = 1;
		END IF;

		
		IF uname = owner THEN
			SET mask = mask << 6;
		ELSEIF in_csv(owner_grp, ugrps) THEN
			SET mask = mask << 3;
		END IF;

		
		IF access_mode & mask = mask THEN
			RETURN 1;
		ELSE
			RETURN 0;
		END IF;
	END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-12-14  0:03:01
