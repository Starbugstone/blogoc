-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: blogoc
-- ------------------------------------------------------
-- Server version	5.5.5-10.1.30-MariaDB

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
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `idcategories` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `categories_slug` varchar(255) NOT NULL,
  PRIMARY KEY (`idcategories`),
  UNIQUE KEY `categories_slug_UNIQUE` (`categories_slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (3,'Dev','dev'),(4,'Stories','stories');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `users_idusers` int(11) NOT NULL,
  `posts_idposts` int(11) NOT NULL,
  `comment` text,
  `approuved` tinyint(4) NOT NULL,
  PRIMARY KEY (`users_idusers`,`posts_idposts`),
  KEY `fk_comment_post1_idx` (`posts_idposts`),
  CONSTRAINT `fk_comment_post1` FOREIGN KEY (`posts_idposts`) REFERENCES `posts` (`idposts`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_comment_user1` FOREIGN KEY (`users_idusers`) REFERENCES `users` (`idusers`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configs`
--

DROP TABLE IF EXISTS `configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configs` (
  `idconfigs` int(11) NOT NULL AUTO_INCREMENT,
  `configs_name` varchar(255) NOT NULL,
  `configs_value` varchar(255) NOT NULL,
  `configs_type` varchar(255) DEFAULT NULL,
  `configs_class_idconfigsclass` int(11) NOT NULL,
  PRIMARY KEY (`idconfigs`),
  UNIQUE KEY `configName_UNIQUE` (`configs_name`),
  UNIQUE KEY `idconfigs_UNIQUE` (`idconfigs`),
  KEY `fk_config_config_class1_idx` (`configs_class_idconfigsclass`),
  CONSTRAINT `fk_config_config_class1` FOREIGN KEY (`configs_class_idconfigsclass`) REFERENCES `configs_class` (`idconfigsclass`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configs`
--

LOCK TABLES `configs` WRITE;
/*!40000 ALTER TABLE `configs` DISABLE KEYS */;
INSERT INTO `configs` VALUES (1,'front_text_1','Developping tomorrows Web','1',1),(2,'front_text_2','If I can imagine it','1',1),(3,'front_text_3','Smoke me a kipper,','1',1),(4,'social_icons_linkedin','https://www.linkedin.com/in/matthew-clancy-024597ba/','2',2),(5,'social_icons_github','https://github.com/Starbugstone','2',2),(6,'social_icons_twitter','https://twitter.com/StarbugStone','2',2),(7,'social_icons_facebook','','2',2),(8,'social_icons_website','https://starbugstone.eu','2',2),(9,'site_name','Blog OC','1',4),(10,'about_me_image','https://pbs.twimg.com/profile_images/3676317209/243cf055afcb2baa9fd894e5305985fa_400x400.jpeg','3',3),(11,'front_text_1_subtext','The starbug way','1',1),(12,'front_text_2_subtext','I can probably code it !','1',1),(13,'front_text_3_subtext','I\'ll be back for breakfast','1',1);
/*!40000 ALTER TABLE `configs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configs_class`
--

DROP TABLE IF EXISTS `configs_class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configs_class` (
  `idconfigsclass` int(11) NOT NULL AUTO_INCREMENT,
  `class` varchar(255) NOT NULL,
  PRIMARY KEY (`idconfigsclass`),
  UNIQUE KEY `configName_UNIQUE` (`idconfigsclass`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configs_class`
--

LOCK TABLES `configs_class` WRITE;
/*!40000 ALTER TABLE `configs_class` DISABLE KEYS */;
INSERT INTO `configs_class` VALUES (1,'20_front_page_text'),(2,'21_front_page_social_icons'),(3,'22_front_page_other'),(4,'10_global_site_configuration');
/*!40000 ALTER TABLE `configs_class` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configs_type`
--

DROP TABLE IF EXISTS `configs_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configs_type` (
  `idconfigs_type` int(11) NOT NULL AUTO_INCREMENT,
  `configs_type_name` varchar(255) NOT NULL,
  PRIMARY KEY (`idconfigs_type`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configs_type`
--

LOCK TABLES `configs_type` WRITE;
/*!40000 ALTER TABLE `configs_type` DISABLE KEYS */;
INSERT INTO `configs_type` VALUES (1,'text'),(2,'url'),(3,'image');
/*!40000 ALTER TABLE `configs_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `idposts` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) DEFAULT NULL,
  `post_image` varchar(255) DEFAULT NULL,
  `categories_idcategories` int(11) NOT NULL,
  `article` text COMMENT 'The excerpt will be created',
  `author_iduser` int(11) NOT NULL,
  `last_update` datetime NOT NULL,
  `creation_date` datetime NOT NULL,
  `published` tinyint(4) NOT NULL,
  `on_front_page` tinyint(4) NOT NULL,
  `posts_slug` varchar(255) NOT NULL,
  PRIMARY KEY (`idposts`),
  UNIQUE KEY `posts_slug_UNIQUE` (`posts_slug`),
  KEY `fk_post_user1_idx` (`author_iduser`),
  KEY `fk_post_category1_idx` (`categories_idcategories`),
  CONSTRAINT `fk_post_category1` FOREIGN KEY (`categories_idcategories`) REFERENCES `categories` (`idcategories`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_post_user1` FOREIGN KEY (`author_iduser`) REFERENCES `users` (`idusers`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (1,'TEST POST','http://lorempixel.com/400/200/',3,'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam condimentum, erat id congue imperdiet, dolor magna porta ex, id faucibus justo nulla vel erat. Ut auctor, ligula et luctus vestibulum, purus mauris vulputate leo, id imperdiet felis odio eget augue. Aliquam ut sagittis sem. Nunc ut mi porttitor, aliquam leo nec, dignissim dui. Ut at dolor nisl. Cras congue at quam eu dapibus. Nullam id est vitae nunc volutpat auctor sit amet eget turpis.',1,'0000-00-00 00:00:00','0000-00-00 00:00:00',1,1,'bla'),(2,'TEST POST2','http://lorempixel.com/400/200/',3,'dolor sit amet, consectetur adipiscing elit. Nam condimentum, erat id congue imperdiet, dolor magna porta ex, id faucibus justo nulla vel erat. Ut auctor, ligula et luctus vestibulum, purus mauris vulputate leo, id imperdiet felis odio eget augue. Aliquam ut sagittis sem. Nunc ut mi porttitor, aliquam leo nec, dignissim dui. Ut at dolor nisl. Cras congue at quam eu dapibus. Nullam id est vitae nunc volutpat auctor sit amet eget turpis. Aenean eget lectus vel ante malesuada pellentesque quis aliquam justo. Nulla a vehicula libero. Curabitur nec accumsan leo. Nunc sagittis velit nec mollis luctus. Nam in suscipit velit, ut imperdiet dolor. Nullam nec elit congue, semper nisi quis, commodo magna. Vivamus sagittis, urna vel fringilla scelerisque, metus nulla mattis lorem, eget viverra justo ligula sit amet ex. In tempor consequat tortor. Morbi ullamcorper eros at velit accumsan, blandit tincidunt elit viverra. Aenean tincidunt mattis tortor. Phasellus id nisi ornare, fermentum nisi in, egestas dolor. Pellentesque purus nulla, congue sit amet libero dignissim, varius facilisis metus. Sed sed lacinia nunc. Aliquam quis gravida est. Morbi ut aliquam diam.',1,'0000-00-00 00:00:00','0000-00-00 00:00:00',1,1,'bla2'),(3,'TEST POST3','http://lorempixel.com/400/200/',3,'BLA Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam condimentum, erat id congue imperdiet, dolor magna porta ex, id faucibus justo nulla vel erat. Ut auctor, ligula et luctus vestibulum, purus mauris vulputate leo, id imperdiet felis odio eget augue. Aliquam ut sagittis sem. Nunc ut mi porttitor, aliquam leo nec, dignissim dui. Ut at dolor nisl. Cras congue at quam eu dapibus. Nullam id est vitae nunc volutpat auctor sit amet eget turpis.',1,'0000-00-00 00:00:00','0000-00-00 00:00:00',1,1,'bla3'),(4,'TEST POST4','http://lorempixel.com/400/200/',3,'BLA',1,'0000-00-00 00:00:00','0000-00-00 00:00:00',1,1,'bla4');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts_has_tags`
--

DROP TABLE IF EXISTS `posts_has_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts_has_tags` (
  `post_idposts` int(11) NOT NULL,
  `tag_idtags` int(11) NOT NULL,
  PRIMARY KEY (`post_idposts`,`tag_idtags`),
  KEY `fk_post_has_tag_tag1_idx` (`tag_idtags`),
  KEY `fk_post_has_tag_post1_idx` (`post_idposts`),
  CONSTRAINT `fk_post_has_tag_post1` FOREIGN KEY (`post_idposts`) REFERENCES `posts` (`idposts`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_post_has_tag_tag1` FOREIGN KEY (`tag_idtags`) REFERENCES `tags` (`idtags`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts_has_tags`
--

LOCK TABLES `posts_has_tags` WRITE;
/*!40000 ALTER TABLE `posts_has_tags` DISABLE KEYS */;
INSERT INTO `posts_has_tags` VALUES (1,1),(1,2);
/*!40000 ALTER TABLE `posts_has_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `idroles` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) DEFAULT NULL,
  `role_level` int(11) DEFAULT NULL,
  PRIMARY KEY (`idroles`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'User',1),(2,'Admin',2);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tags` (
  `idtags` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(255) NOT NULL,
  PRIMARY KEY (`idtags`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
INSERT INTO `tags` VALUES (1,'test'),(2,'lorem');
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `idusers` int(11) NOT NULL AUTO_INCREMENT,
  `pseudo` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creation_date` datetime NOT NULL,
  `last_update` datetime DEFAULT NULL,
  `roles_idroles` int(11) NOT NULL,
  `bad_login_time` datetime DEFAULT NULL,
  `locked_out` tinyint(4) NOT NULL DEFAULT '0',
  `bad_login_tries` int(2) NOT NULL DEFAULT '0',
  `reset_password_hash` varchar(255) DEFAULT NULL,
  `reset_password_hash generation_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`idusers`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  KEY `fk_user_role_idx` (`roles_idroles`),
  CONSTRAINT `fk_user_role` FOREIGN KEY (`roles_idroles`) REFERENCES `roles` (`idroles`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Test',NULL,'test@me.com',NULL,'Doe','John','0000-00-00 00:00:00',NULL,2,NULL,0,0,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'blogoc'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-09-10 13:49:21
