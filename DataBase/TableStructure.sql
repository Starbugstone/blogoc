-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: blogoc2
-- ------------------------------------------------------
-- Server version	5.5.5-10.1.31-MariaDB

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
-- Table structure for table `boc_categories`
--

DROP TABLE IF EXISTS `boc_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `boc_categories` (
  `idcategories` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `categories_slug` varchar(255) NOT NULL,
  PRIMARY KEY (`idcategories`),
  UNIQUE KEY `categories_slug_UNIQUE` (`categories_slug`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boc_categories`
--

LOCK TABLES `boc_categories` WRITE;
/*!40000 ALTER TABLE `boc_categories` DISABLE KEYS */;
INSERT INTO `boc_categories` VALUES (1,'Dev','dev'),(2,'Stories','stories');
/*!40000 ALTER TABLE `boc_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `boc_comments`
--

DROP TABLE IF EXISTS `boc_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `boc_comments` (
  `idcomments` int(11) NOT NULL AUTO_INCREMENT,
  `users_idusers` int(11) NOT NULL,
  `posts_idposts` int(11) NOT NULL,
  `comment` text,
  `approved` tinyint(4) NOT NULL DEFAULT '0',
  `comment_date` datetime,
  PRIMARY KEY (`idcomments`),
  KEY `fk_comment_post1_idx` (`posts_idposts`),
  KEY `fk_comments_users1_idx` (`users_idusers`),
  CONSTRAINT `fk_comment_post1` FOREIGN KEY (`posts_idposts`) REFERENCES `boc_posts` (`idposts`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_comments_users1` FOREIGN KEY (`users_idusers`) REFERENCES `boc_users` (`idusers`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boc_comments`
--

LOCK TABLES `boc_comments` WRITE;
/*!40000 ALTER TABLE `boc_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `boc_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `boc_configs`
--

DROP TABLE IF EXISTS `boc_configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `boc_configs` (
  `idconfigs` int(11) NOT NULL AUTO_INCREMENT,
  `configs_name` varchar(255) NOT NULL,
  `configs_value` varchar(255) NOT NULL,
  `configs_class_idconfigsclass` int(11) NOT NULL,
  `configs_type_idconfigs_type` int(11) NOT NULL,
  `order` int(11) DEFAULT NULL,
  PRIMARY KEY (`idconfigs`),
  UNIQUE KEY `configName_UNIQUE` (`configs_name`),
  UNIQUE KEY `idconfigs_UNIQUE` (`idconfigs`),
  KEY `fk_config_config_class1_idx` (`configs_class_idconfigsclass`),
  KEY `fk_configs_configs_type1_idx` (`configs_type_idconfigs_type`),
  CONSTRAINT `fk_config_config_class1` FOREIGN KEY (`configs_class_idconfigsclass`) REFERENCES `boc_configs_class` (`idconfigsclass`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_configs_configs_type1` FOREIGN KEY (`configs_type_idconfigs_type`) REFERENCES `boc_configs_type` (`idconfigs_type`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boc_configs`
--

LOCK TABLES `boc_configs` WRITE;
/*!40000 ALTER TABLE `boc_configs` DISABLE KEYS */;
INSERT INTO `boc_configs` VALUES (1,'front_text_1','',1,1,1),(2,'front_text_2','',1,1,10),(3,'front_text_3','',1,1,20),(4,'social_icons_linkedin','',2,2,10),(5,'social_icons_github','',2,2,20),(6,'social_icons_twitter','',2,2,30),(7,'social_icons_facebook','',2,2,40),(8,'social_icons_website','',2,2,50),(9,'site_name','Blog OC',4,1,10),(10,'about_me_image','/config_images/camera-no-image-1.jpg',3,3,10),(11,'front_text_1_subtext','',1,1,2),(12,'front_text_2_subtext','',1,1,11),(13,'front_text_3_subtext','',1,1,21),(14,'no_image_replacement','',4,3,20),(15,'SMTP_server','smtp.mailtrap.io',5,1,1),(16,'SMTP_port','465',5,1,2),(17,'SMTP_user','6f39ae281f4565',5,1,3),(18,'SMTP_pass','77ac508b1b88fe',5,4,4),(19,'SMTP_from','noreply@localhost.com',5,1,5),(20,'admin_email_address','admin@me.com',4,1,30);
/*!40000 ALTER TABLE `boc_configs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `boc_configs_class`
--

DROP TABLE IF EXISTS `boc_configs_class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `boc_configs_class` (
  `idconfigsclass` int(11) NOT NULL AUTO_INCREMENT,
  `class` varchar(255) NOT NULL,
  PRIMARY KEY (`idconfigsclass`),
  UNIQUE KEY `configName_UNIQUE` (`idconfigsclass`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boc_configs_class`
--

LOCK TABLES `boc_configs_class` WRITE;
/*!40000 ALTER TABLE `boc_configs_class` DISABLE KEYS */;
INSERT INTO `boc_configs_class` VALUES (1,'20_front_page_text'),(2,'21_front_page_social_icons'),(3,'22_front_page_other'),(4,'30_global_site_configuration'),(5,'40_smtp_configuration');
/*!40000 ALTER TABLE `boc_configs_class` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `boc_configs_type`
--

DROP TABLE IF EXISTS `boc_configs_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `boc_configs_type` (
  `idconfigs_type` int(11) NOT NULL AUTO_INCREMENT,
  `configs_type_name` varchar(255) NOT NULL,
  PRIMARY KEY (`idconfigs_type`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boc_configs_type`
--

LOCK TABLES `boc_configs_type` WRITE;
/*!40000 ALTER TABLE `boc_configs_type` DISABLE KEYS */;
INSERT INTO `boc_configs_type` VALUES (1,'text'),(2,'url'),(3,'image'),(4,'password');
/*!40000 ALTER TABLE `boc_configs_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `boc_posts`
--

DROP TABLE IF EXISTS `boc_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `boc_posts` (
  `idposts` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) DEFAULT NULL,
  `post_image` varchar(255) DEFAULT NULL,
  `categories_idcategories` int(11) DEFAULT NULL,
  `article` text COMMENT 'The excerpt will be created',
  `author_iduser` int(11) NOT NULL,
  `last_update` datetime NOT NULL,
  `creation_date` datetime NOT NULL,
  `published` tinyint(4) NOT NULL,
  `on_front_page` tinyint(4) NOT NULL,
  `posts_slug` varchar(255) NOT NULL,
  PRIMARY KEY (`idposts`),
  UNIQUE KEY `posts_slug_UNIQUE` (`posts_slug`),
  KEY `fk_post_category1_idx` (`categories_idcategories`),
  KEY `fk_posts_users1_idx` (`author_iduser`),
  CONSTRAINT `fk_post_category1` FOREIGN KEY (`categories_idcategories`) REFERENCES `boc_categories` (`idcategories`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `fk_posts_users1` FOREIGN KEY (`author_iduser`) REFERENCES `boc_users` (`idusers`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boc_posts`
--

LOCK TABLES `boc_posts` WRITE;
/*!40000 ALTER TABLE `boc_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `boc_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `boc_posts_has_tags`
--

DROP TABLE IF EXISTS `boc_posts_has_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `boc_posts_has_tags` (
  `post_idposts` int(11) NOT NULL,
  `tag_idtags` int(11) NOT NULL,
  PRIMARY KEY (`post_idposts`,`tag_idtags`),
  KEY `fk_post_has_tag_tag1_idx` (`tag_idtags`),
  KEY `fk_post_has_tag_post1_idx` (`post_idposts`),
  CONSTRAINT `fk_post_has_tag_post1` FOREIGN KEY (`post_idposts`) REFERENCES `boc_posts` (`idposts`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_post_has_tag_tag1` FOREIGN KEY (`tag_idtags`) REFERENCES `boc_tags` (`idtags`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boc_posts_has_tags`
--

LOCK TABLES `boc_posts_has_tags` WRITE;
/*!40000 ALTER TABLE `boc_posts_has_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `boc_posts_has_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `boc_remembered_logins`
--

DROP TABLE IF EXISTS `boc_remembered_logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `boc_remembered_logins` (
  `token_hash` varchar(64) NOT NULL,
  `users_idusers` int(11) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`token_hash`),
  KEY `fk_remembered_logins_users1_idx` (`users_idusers`),
  CONSTRAINT `fk_remembered_logins_users1` FOREIGN KEY (`users_idusers`) REFERENCES `boc_users` (`idusers`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boc_remembered_logins`
--

LOCK TABLES `boc_remembered_logins` WRITE;
/*!40000 ALTER TABLE `boc_remembered_logins` DISABLE KEYS */;
/*!40000 ALTER TABLE `boc_remembered_logins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `boc_roles`
--

DROP TABLE IF EXISTS `boc_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `boc_roles` (
  `idroles` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) DEFAULT NULL,
  `role_level` int(11) DEFAULT NULL,
  PRIMARY KEY (`idroles`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boc_roles`
--

LOCK TABLES `boc_roles` WRITE;
/*!40000 ALTER TABLE `boc_roles` DISABLE KEYS */;
INSERT INTO `boc_roles` VALUES (1,'User',1),(2,'Admin',2);
/*!40000 ALTER TABLE `boc_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `boc_tags`
--

DROP TABLE IF EXISTS `boc_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `boc_tags` (
  `idtags` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(255) NOT NULL,
  PRIMARY KEY (`idtags`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boc_tags`
--

LOCK TABLES `boc_tags` WRITE;
/*!40000 ALTER TABLE `boc_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `boc_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `boc_users`
--

DROP TABLE IF EXISTS `boc_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `boc_users` (
  `idusers` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creation_date` datetime NOT NULL,
  `last_update` datetime DEFAULT NULL,
  `roles_idroles` int(11) NOT NULL,
  `bad_login_time` datetime DEFAULT NULL,
  `locked_out` tinyint(4) NOT NULL DEFAULT '0',
  `bad_login_tries` int(2) NOT NULL DEFAULT '0',
  `reset_password_hash` varchar(255) DEFAULT NULL,
  `reset_password_hash_generation_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`idusers`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  UNIQUE KEY `reset_password_hash_UNIQUE` (`reset_password_hash`),
  KEY `fk_user_role_idx` (`roles_idroles`),
  CONSTRAINT `fk_user_role` FOREIGN KEY (`roles_idroles`) REFERENCES `boc_roles` (`idroles`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boc_users`
--

LOCK TABLES `boc_users` WRITE;
/*!40000 ALTER TABLE `boc_users` DISABLE KEYS */;
INSERT INTO `boc_users` VALUES (1,'admin','','admin@me.com','$2y$10$KXMBSKpQeDholrhEJKLnV.dzL5LCGYHNurvJon0IeS3KFd5/kdtr6','Admin','Super','2018-10-30 00:00:00','2018-10-31 11:48:49',2,'2018-10-31 11:29:41',0,0,'5df11fa0b1b9419ff09c8f24163c116d6c800db3bc72a4330ec2100b0036eefd','2018-10-31 11:29:53');
/*!40000 ALTER TABLE `boc_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'blogoc2'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-10-31 12:16:39
