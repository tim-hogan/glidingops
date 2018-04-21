-- MySQL dump 10.13  Distrib 5.5.50, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: gliding
-- ------------------------------------------------------
-- Server version 5.5.50-0ubuntu0.14.04.1

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
-- Table structure for table `address`
--

-- DROP TABLE IF EXISTS `address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `addr1` varchar(45) NOT NULL,
  `addr2` varchar(45) DEFAULT NULL,
  `addr3` varchar(45) DEFAULT NULL,
  `addr4` varchar(45) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `postcode` varchar(45) DEFAULT NULL,
  `country` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`,`type`),
  KEY `fk_address_address_type1_idx` (`type`),
  CONSTRAINT `fk_address_address_type1` FOREIGN KEY (`type`) REFERENCES `address_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `address_type`
--

-- DROP TABLE IF EXISTS `address_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `address_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aircraft`
--

-- DROP TABLE IF EXISTS `aircraft`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aircraft` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org` int(11) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `registration` varchar(6) DEFAULT NULL,
  `rego_short` varchar(3) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `make_model` varchar(30) DEFAULT NULL,
  `seats` int(11) DEFAULT NULL,
  `serial` varchar(30) DEFAULT NULL,
  `club_glider` int(11) DEFAULT NULL,
  `bookable` int(11) DEFAULT NULL,
  `charge_per_minute` decimal(5,2) DEFAULT NULL,
  `max_perflight_charge` decimal(6,2) DEFAULT NULL,
  `next_annual` datetime DEFAULT NULL,
  `next_supplementary` datetime DEFAULT NULL,
  `flarm_ICAO` varchar(6) DEFAULT NULL,
  `spot_id` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `registration` (`registration`),
  UNIQUE KEY `rego_short` (`rego_short`),
  KEY `idx_org` (`org`),
  KEY `idx_type` (`type`),
  CONSTRAINT `aircraft_ibfk_1` FOREIGN KEY (`org`) REFERENCES `organisations` (`id`),
  CONSTRAINT `aircraft_ibfk_2` FOREIGN KEY (`type`) REFERENCES `aircrafttype` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aircrafttype`
--

-- DROP TABLE IF EXISTS `aircrafttype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aircrafttype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org` int(11) DEFAULT NULL,
  `name` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `idx_org` (`org`),
  CONSTRAINT `aircrafttype_ibfk_1` FOREIGN KEY (`org`) REFERENCES `organisations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `airspace`
--

-- DROP TABLE IF EXISTS `airspace`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `airspace` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `region` varchar(20) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `class` varchar(10) DEFAULT NULL,
  `upper_height` int(11) DEFAULT NULL,
  `Lower_height` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `airspacecoords`
--

-- DROP TABLE IF EXISTS `airspacecoords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `airspacecoords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `airspace` int(11) DEFAULT NULL,
  `seq` int(11) DEFAULT NULL,
  `type` varchar(6) DEFAULT NULL,
  `lattitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `arclat` double DEFAULT NULL,
  `arclon` double DEFAULT NULL,
  `arcdist` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `idx_airspace` (`airspace`),
  CONSTRAINT `airspacecoords_ibfk_1` FOREIGN KEY (`airspace`) REFERENCES `airspace` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `audit`
--

-- DROP TABLE IF EXISTS `audit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eventtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userid` int(11) DEFAULT NULL,
  `memberid` int(11) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `idx_userid` (`userid`),
  KEY `idx_memberid` (`memberid`),
  CONSTRAINT `audit_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`id`),
  CONSTRAINT `audit_ibfk_2` FOREIGN KEY (`memberid`) REFERENCES `members` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8582 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `billingoptions`
--

-- DROP TABLE IF EXISTS `billingoptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billingoptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `bill_pic` int(11) DEFAULT NULL,
  `bill_p2` int(11) DEFAULT NULL,
  `bill_other` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bookings`
--

-- DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org` int(11) DEFAULT NULL,
  `made` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lastmodify` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `start` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `type` int(11) DEFAULT NULL,
  `description` varchar(80) DEFAULT NULL,
  `member` int(11) DEFAULT NULL,
  `aircraft` int(11) DEFAULT NULL,
  `instructor` int(11) DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `idx_org` (`org`),
  KEY `idx_type` (`type`),
  KEY `idx_member` (`member`),
  KEY `idx_aircraft` (`aircraft`),
  KEY `idx_instructor` (`instructor`),
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`org`) REFERENCES `organisations` (`id`),
  CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`type`) REFERENCES `bookingtypes` (`id`),
  CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`member`) REFERENCES `members` (`id`),
  CONSTRAINT `bookings_ibfk_4` FOREIGN KEY (`aircraft`) REFERENCES `aircraft` (`id`),
  CONSTRAINT `bookings_ibfk_5` FOREIGN KEY (`instructor`) REFERENCES `members` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bookingtypes`
--

-- DROP TABLE IF EXISTS `bookingtypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookingtypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org` int(11) DEFAULT NULL,
  `typename` varchar(20) DEFAULT NULL,
  `colour` varchar(10) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `idx_org` (`org`),
  CONSTRAINT `bookingtypes_ibfk_1` FOREIGN KEY (`org`) REFERENCES `organisations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `charges`
--

-- DROP TABLE IF EXISTS `charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `charges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org` int(11) DEFAULT NULL,
  `name` varchar(30) DEFAULT NULL,
  `location` varchar(40) DEFAULT NULL,
  `validfrom` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `amount` decimal(9,2) DEFAULT NULL,
  `every_flight` int(11) DEFAULT '0',
  `max_once_per_day` int(11) DEFAULT '0',
  `monthly` int(11) DEFAULT '0',
  `comments` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `idx_org` (`org`),
  CONSTRAINT `charges_ibfk_1` FOREIGN KEY (`org`) REFERENCES `organisations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `diag`
--

-- DROP TABLE IF EXISTS `diag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `diag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `data` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `duty`
--

-- DROP TABLE IF EXISTS `duty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `duty` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `localdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `member` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`),
  KEY `idx_member` (`member`),
  CONSTRAINT `duty_ibfk_1` FOREIGN KEY (`type`) REFERENCES `dutytypes` (`id`),
  CONSTRAINT `duty_ibfk_2` FOREIGN KEY (`member`) REFERENCES `members` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=683 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dutytypes`
--

-- DROP TABLE IF EXISTS `dutytypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dutytypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flights`
--

-- DROP TABLE IF EXISTS `flights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org` int(11) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `localdate` int(11) DEFAULT NULL,
  `updseq` int(11) DEFAULT NULL,
  `location` varchar(40) DEFAULT NULL,
  `seq` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `launchtype` int(11) DEFAULT NULL,
  `towplane` int(11) DEFAULT NULL,
  `glider` varchar(6) DEFAULT NULL,
  `towpilot` int(11) DEFAULT NULL,
  `pic` int(11) DEFAULT NULL,
  `p2` int(11) DEFAULT NULL,
  `start` bigint(20) DEFAULT NULL,
  `towland` bigint(20) DEFAULT NULL,
  `land` bigint(20) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `billing_option` int(11) DEFAULT NULL,
  `billing_member1` int(11) DEFAULT NULL,
  `billing_member2` int(11) DEFAULT NULL,
  `comments` varchar(140) DEFAULT NULL,
  `finalised` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `org` (`org`,`localdate`,`seq`),
  KEY `idx_org` (`org`),
  KEY `idx_type` (`type`),
  KEY `idx_launchtype` (`launchtype`),
  KEY `idx_towplane` (`towplane`),
  KEY `idx_towpilot` (`towpilot`),
  KEY `idx_pic` (`pic`),
  KEY `idx_p2` (`p2`),
  KEY `idx_billing_option` (`billing_option`),
  KEY `idx_billing_member1` (`billing_member1`),
  KEY `idx_billing_member2` (`billing_member2`),
  CONSTRAINT `flights_ibfk_1` FOREIGN KEY (`org`) REFERENCES `organisations` (`id`),
  CONSTRAINT `flights_ibfk_10` FOREIGN KEY (`billing_member2`) REFERENCES `members` (`id`),
  CONSTRAINT `flights_ibfk_2` FOREIGN KEY (`type`) REFERENCES `flighttypes` (`id`),
  CONSTRAINT `flights_ibfk_3` FOREIGN KEY (`launchtype`) REFERENCES `launchtypes` (`id`),
  CONSTRAINT `flights_ibfk_4` FOREIGN KEY (`towplane`) REFERENCES `aircraft` (`id`),
  CONSTRAINT `flights_ibfk_5` FOREIGN KEY (`towpilot`) REFERENCES `members` (`id`),
  CONSTRAINT `flights_ibfk_6` FOREIGN KEY (`pic`) REFERENCES `members` (`id`),
  CONSTRAINT `flights_ibfk_7` FOREIGN KEY (`p2`) REFERENCES `members` (`id`),
  CONSTRAINT `flights_ibfk_8` FOREIGN KEY (`billing_option`) REFERENCES `billingoptions` (`id`),
  CONSTRAINT `flights_ibfk_9` FOREIGN KEY (`billing_member1`) REFERENCES `members` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8690 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flighttypes`
--

-- DROP TABLE IF EXISTS `flighttypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flighttypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `group_member`
--

-- DROP TABLE IF EXISTS `group_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_member` (
  `gm_group_id` int(11) DEFAULT NULL,
  `gm_member_id` int(11) DEFAULT NULL,
  KEY `idx_gm_group_id` (`gm_group_id`),
  KEY `idx_gm_member_id` (`gm_member_id`),
  CONSTRAINT `group_member_ibfk_1` FOREIGN KEY (`gm_group_id`) REFERENCES `groups` (`id`),
  CONSTRAINT `group_member_ibfk_2` FOREIGN KEY (`gm_member_id`) REFERENCES `members` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `groups`
--

-- DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org` int(11) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `name` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `idx_org` (`org`),
  CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`org`) REFERENCES `organisations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `incentive_schemes`
--

-- DROP TABLE IF EXISTS `incentive_schemes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `incentive_schemes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org` int(11) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(40) DEFAULT NULL,
  `specific_glider_list` varchar(30) DEFAULT NULL,
  `rate_glider` decimal(5,2) DEFAULT NULL,
  `charge_tow` int(11) DEFAULT NULL,
  `charge_airways` int(11) DEFAULT NULL,
  `cost` decimal(6,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `idx_org` (`org`),
  CONSTRAINT `incentive_schemes_ibfk_1` FOREIGN KEY (`org`) REFERENCES `organisations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `launchtypes`
--

-- DROP TABLE IF EXISTS `launchtypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `launchtypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `acronym` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `members`
--

-- DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `org` int(11) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `effective_from` datetime DEFAULT NULL,
  `firstname` varchar(40) DEFAULT NULL,
  `surname` varchar(40) DEFAULT NULL,
  `displayname` varchar(80) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `mem_addr1` varchar(45) DEFAULT NULL,
  `mem_addr2` varchar(45) DEFAULT NULL,
  `mem_addr3` varchar(45) DEFAULT NULL,
  `mem_addr4` varchar(45) DEFAULT NULL,
  `mem_city` varchar(45) DEFAULT NULL,
  `mem_country` varchar(45) DEFAULT NULL,
  `mem_postcode` varchar(45) DEFAULT NULL,
  `emerg_addr1` varchar(45) DEFAULT NULL,
  `emerg_addr2` varchar(45) DEFAULT NULL,
  `emerg_addr3` varchar(45) DEFAULT NULL,
  `emerg_addr4` varchar(45) DEFAULT NULL,
  `emerg_city` varchar(45) DEFAULT NULL,
  `emerg_country` varchar(45) DEFAULT NULL,
  `emerg_postcode` varchar(45) DEFAULT NULL,
  `gnz_number` int(11) DEFAULT NULL,
  `qgp_number` int(11) DEFAULT NULL,
  `class` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `phone_home` varchar(30) DEFAULT NULL,
  `phone_mobile` varchar(30) DEFAULT NULL,
  `phone_work` varchar(30) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `gone_solo` int(11) DEFAULT NULL,
  `enable_text` int(11) DEFAULT NULL,
  `enable_email` int(11) DEFAULT NULL,
  `medical_expire` timestamp NULL DEFAULT NULL,
  `icr_expire` date DEFAULT NULL,
  `bfr_expire` timestamp NULL DEFAULT NULL,
  `official_observer` tinyint(1) DEFAULT NULL,
  `first_aider` tinyint(1) DEFAULT NULL,
  `localdate_lastemail` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `firstname` (`firstname`,`surname`,`email`),
  KEY `idx_org` (`org`),
  KEY `idx_class` (`class`),
  KEY `fk_members_membership_status1_idx` (`status`),
  CONSTRAINT `fk_members_membership_status1` FOREIGN KEY (`status`) REFERENCES `membership_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `members_ibfk_1` FOREIGN KEY (`org`) REFERENCES `organisations` (`id`),
  CONSTRAINT `members_ibfk_2` FOREIGN KEY (`class`) REFERENCES `membership_class` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4693 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `membership_class`
--

-- DROP TABLE IF EXISTS `membership_class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `membership_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org` int(11) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `class` varchar(30) DEFAULT NULL,
  `disp_message_broadcast` tinyint(1) DEFAULT NULL,
  `dailysheet_dropdown` tinyint(1) DEFAULT NULL,
  `email_broadcast` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `idx_org` (`org`),
  CONSTRAINT `membership_class_ibfk_1` FOREIGN KEY (`org`) REFERENCES `organisations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `membership_status`
--

-- DROP TABLE IF EXISTS `membership_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `membership_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status_name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `messages`
--

-- DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org` int(11) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `msg` varchar(160) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `idx_org` (`org`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`org`) REFERENCES `organisations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=658 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `organisations`
--

-- DROP TABLE IF EXISTS `organisations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organisations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `addr1` varchar(60) DEFAULT NULL,
  `addr2` varchar(60) DEFAULT NULL,
  `addr3` varchar(60) DEFAULT NULL,
  `addr4` varchar(60) DEFAULT NULL,
  `country` varchar(60) DEFAULT NULL,
  `contact_name` varchar(80) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `timezone` varchar(20) DEFAULT NULL,
  `aircraft_prefix` varchar(5) DEFAULT NULL,
  `tow_height_charging` int(11) DEFAULT NULL,
  `tow_time_based` int(11) DEFAULT NULL,
  `default_location` varchar(40) DEFAULT NULL,
  `name_othercharges` varchar(20) DEFAULT NULL,
  `def_launch_lat` double DEFAULT NULL,
  `def_launch_lon` double DEFAULT NULL,
  `map_centre_lat` double DEFAULT NULL,
  `map_centre_lon` double DEFAULT NULL,
  `twitter_consumerKey` varchar(60) DEFAULT NULL,
  `twitter_consumerSecret` varchar(60) DEFAULT NULL,
  `twitter_accessToken` varchar(60) DEFAULT NULL,
  `twitter_accessTokenSecret` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `role_member`
--

-- DROP TABLE IF EXISTS `role_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `role_id` (`role_id`,`member_id`),
  KEY `idx_org` (`org`),
  KEY `idx_role_id` (`role_id`),
  KEY `idx_member_id` (`member_id`),
  CONSTRAINT `role_member_ibfk_1` FOREIGN KEY (`org`) REFERENCES `organisations` (`id`),
  CONSTRAINT `role_member_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `role_member_ibfk_3` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=263 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles`
--

-- DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scheme_subs`
--

-- DROP TABLE IF EXISTS `scheme_subs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scheme_subs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org` int(11) DEFAULT NULL,
  `member` int(11) DEFAULT NULL,
  `start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `scheme` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `idx_org` (`org`),
  KEY `idx_member` (`member`),
  KEY `idx_scheme` (`scheme`),
  CONSTRAINT `scheme_subs_ibfk_1` FOREIGN KEY (`org`) REFERENCES `organisations` (`id`),
  CONSTRAINT `scheme_subs_ibfk_2` FOREIGN KEY (`member`) REFERENCES `members` (`id`),
  CONSTRAINT `scheme_subs_ibfk_3` FOREIGN KEY (`scheme`) REFERENCES `incentive_schemes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `spots`
--

-- DROP TABLE IF EXISTS `spots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org` int(11) DEFAULT NULL,
  `rego_short` varchar(3) DEFAULT NULL,
  `spotkey` varchar(40) DEFAULT NULL,
  `polltimelast` int(11) DEFAULT NULL,
  `polltimeall` int(11) DEFAULT NULL,
  `lastreq` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastlistreq` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `rego_short` (`rego_short`),
  UNIQUE KEY `spotkey` (`spotkey`),
  KEY `idx_org` (`org`),
  CONSTRAINT `spots_ibfk_1` FOREIGN KEY (`org`) REFERENCES `organisations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `testy`
--

-- DROP TABLE IF EXISTS `testy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Char10` varchar(20) DEFAULT NULL,
  `IReq` int(11) DEFAULT NULL,
  `IntNormal` int(11) DEFAULT NULL,
  `IntCheckbox` int(11) DEFAULT NULL,
  `DecimalVal` decimal(5,2) DEFAULT NULL,
  `Email` varchar(60) DEFAULT NULL,
  `Date1` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `DateTimeSpecial2` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `texts`
--

-- DROP TABLE IF EXISTS `texts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `texts` (
  `txt_id` int(11) NOT NULL AUTO_INCREMENT,
  `txt_unique` int(11) DEFAULT NULL,
  `txt_msg_id` int(11) DEFAULT NULL,
  `txt_member_id` int(11) DEFAULT NULL,
  `txt_to` varchar(20) DEFAULT NULL,
  `txt_status` int(11) DEFAULT NULL,
  `txt_timestamp_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `txt_timestamp_sent` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `txt_timestamp_recv` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`txt_id`),
  KEY `idx_txt_msg_id` (`txt_msg_id`),
  KEY `idx_txt_member_id` (`txt_member_id`),
  CONSTRAINT `texts_ibfk_1` FOREIGN KEY (`txt_msg_id`) REFERENCES `messages` (`id`),
  CONSTRAINT `texts_ibfk_2` FOREIGN KEY (`txt_member_id`) REFERENCES `members` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6200 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `towcharges`
--

-- DROP TABLE IF EXISTS `towcharges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `towcharges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org` int(11) DEFAULT NULL,
  `plane` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `club_glider` int(11) DEFAULT NULL,
  `member_class` int(11) DEFAULT NULL,
  `effective_from` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `cost` decimal(6,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `idx_org` (`org`),
  KEY `idx_plane` (`plane`),
  KEY `idx_member_class` (`member_class`),
  CONSTRAINT `towcharges_ibfk_1` FOREIGN KEY (`org`) REFERENCES `organisations` (`id`),
  CONSTRAINT `towcharges_ibfk_2` FOREIGN KEY (`plane`) REFERENCES `aircraft` (`id`),
  CONSTRAINT `towcharges_ibfk_3` FOREIGN KEY (`member_class`) REFERENCES `membership_class` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tracks`
--

-- DROP TABLE IF EXISTS `tracks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tracks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org` int(11) DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `trip_id` int(11) DEFAULT NULL,
  `glider` varchar(7) DEFAULT NULL,
  `point_id` int(11) DEFAULT NULL,
  `point_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `point_time_milli` int(11) DEFAULT '0',
  `lattitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `altitude` double DEFAULT NULL,
  `accuracy` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `glider` (`glider`,`point_time`,`point_time_milli`),
  KEY `idx_org` (`org`),
  KEY `idx_user` (`user`),
  CONSTRAINT `tracks_ibfk_1` FOREIGN KEY (`org`) REFERENCES `organisations` (`id`),
  CONSTRAINT `tracks_ibfk_2` FOREIGN KEY (`user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=126239519 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

-- DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `usercode` varchar(80) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `org` int(11) DEFAULT NULL,
  `expire` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `securitylevel` int(11) DEFAULT NULL,
  `member` int(11) DEFAULT NULL,
  `force_pw_reset` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usercode` (`usercode`),
  KEY `idx_org` (`org`),
  KEY `idx_member` (`member`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`org`) REFERENCES `organisations` (`id`),
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`member`) REFERENCES `members` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=194 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-02-15  6:52:21
