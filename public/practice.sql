-- MySQL dump 10.13  Distrib 8.0.11, for Linux (x86_64)
--
-- Host: localhost    Database: practice
-- ------------------------------------------------------
-- Server version	8.0.11

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 SET NAMES utf8mb4 ;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ty_admin`
--

DROP TABLE IF EXISTS `ty_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `ty_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `password` varchar(32) DEFAULT '',
  `salt` varchar(8) NOT NULL,
  `nickname` varchar(30) DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '正常：1  禁用：0',
  `create_time` int(10) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `regfrom` tinyint(3) DEFAULT '0',
  `token` varchar(64) DEFAULT '' COMMENT 'app用户登陆的时间戳',
  `is_delete` tinyint(1) unsigned DEFAULT '0',
  `remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ty_admin`
--

LOCK TABLES `ty_admin` WRITE;
/*!40000 ALTER TABLE `ty_admin` DISABLE KEYS */;
INSERT INTO `ty_admin` VALUES (2,'admin','8bdfbd80b8bcd726593115d46c6e8dcb','abcdefgh','小志','',1,1528104105,'','59b65dc0be77b.jpg',0,'1505123677',0,''),(6,'alex','2f24997275d81848beef5bae1e0a85d9','1E2olh0f','亚历克斯','',1,1528124281,'',NULL,0,'',0,NULL),(7,'mina','038b7501a7489208ab8c05ec7df545e0','ymlNMvyl','美美','',1,1528199372,'',NULL,0,'',0,NULL),(8,'阿斯蒂芬','fa6661e96a6f74e941074dded34ec9f7','XYFogCPs','美美12','',1,1528382947,'',NULL,0,'',1,NULL);
/*!40000 ALTER TABLE `ty_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ty_auth_group`
--

DROP TABLE IF EXISTS `ty_auth_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `ty_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `rules` char(80) NOT NULL DEFAULT '',
  `description` varchar(20) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ty_auth_group`
--

LOCK TABLES `ty_auth_group` WRITE;
/*!40000 ALTER TABLE `ty_auth_group` DISABLE KEYS */;
INSERT INTO `ty_auth_group` VALUES (1,'超级管理员',1,'1,2,3,4,5,6,7,8,9,10','拥用至高无上的权力'),(2,'管理员',1,'1,2,3,4,5,6,7','网站管理员');
/*!40000 ALTER TABLE `ty_auth_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ty_auth_group_access`
--

DROP TABLE IF EXISTS `ty_auth_group_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `ty_auth_group_access` (
  `uid` mediumint(8) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ty_auth_group_access`
--

LOCK TABLES `ty_auth_group_access` WRITE;
/*!40000 ALTER TABLE `ty_auth_group_access` DISABLE KEYS */;
INSERT INTO `ty_auth_group_access` VALUES (2,1),(7,1),(8,1),(6,2);
/*!40000 ALTER TABLE `ty_auth_group_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ty_auth_rule`
--

DROP TABLE IF EXISTS `ty_auth_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `ty_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(80) NOT NULL DEFAULT '',
  `pretitle` varchar(20) NOT NULL DEFAULT '',
  `title` char(20) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `condition` char(100) NOT NULL DEFAULT '',
  `cate` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ty_auth_rule`
--

LOCK TABLES `ty_auth_rule` WRITE;
/*!40000 ALTER TABLE `ty_auth_rule` DISABLE KEYS */;
INSERT INTO `ty_auth_rule` VALUES (1,'admin/index/systeminfo','','网站设置',1,'',1),(2,'admin/index/adminlist','','管理员列表',1,'',3),(3,'admin/index','','后台权限',1,'',2),(4,'admin/index/rule','','权限细项',1,'',2),(5,'admin/index/password','','修改密码',1,'',2),(7,'admin/index/index','','后台首页',1,'',3),(8,'Admin/Account/role','','角色权限',1,'',1),(10,'Admin/Member/member_list','','会员管理',1,'',1);
/*!40000 ALTER TABLE `ty_auth_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ty_config`
--

DROP TABLE IF EXISTS `ty_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `ty_config` (
  `name` varchar(30) NOT NULL DEFAULT '',
  `value` varchar(128) DEFAULT NULL,
  `type` varchar(32) DEFAULT NULL,
  `sort` int(4) DEFAULT '0',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ty_config`
--

LOCK TABLES `ty_config` WRITE;
/*!40000 ALTER TABLE `ty_config` DISABLE KEYS */;
INSERT INTO `ty_config` VALUES ('site_title','天源美容','site_info',0);
/*!40000 ALTER TABLE `ty_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ty_favorite`
--

DROP TABLE IF EXISTS `ty_favorite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `ty_favorite` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(8) NOT NULL,
  `item_id` int(8) unsigned NOT NULL,
  `add_time` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ty_favorite`
--

LOCK TABLES `ty_favorite` WRITE;
/*!40000 ALTER TABLE `ty_favorite` DISABLE KEYS */;
/*!40000 ALTER TABLE `ty_favorite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ty_item`
--

DROP TABLE IF EXISTS `ty_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `ty_item` (
  `item_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `cate_id` int(4) unsigned NOT NULL,
  `cate_id2` int(4) unsigned NOT NULL,
  `title` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` varchar(255) NOT NULL,
  `detail` text,
  `prom_type` tinyint(1) unsigned DEFAULT '0' COMMENT '0正常，1',
  `is_recommend` tinyint(1) unsigned DEFAULT '0' COMMENT '1推荐',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `market_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `cost` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '成本',
  `origin_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `give_integral` int(8) DEFAULT '0' COMMENT '送多少积分',
  `click_count` int(8) DEFAULT '0',
  `favorite_count` int(8) DEFAULT '0',
  `on_sale` tinyint(1) DEFAULT '1' COMMENT '0下架，1在售中',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '0正常，1已删除',
  `prom_id` int(10) unsigned DEFAULT '0',
  `is_package` tinyint(3) unsigned DEFAULT '0',
  `sort` int(10) unsigned DEFAULT '0',
  `service_count` int(4) DEFAULT '0',
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ty_item`
--

LOCK TABLES `ty_item` WRITE;
/*!40000 ALTER TABLE `ty_item` DISABLE KEYS */;
INSERT INTO `ty_item` VALUES (1,1,5,'纹眉','好吧，写描述',NULL,0,0,100.00,180.00,0.00,'\\upload\\category\\20180613\\eba9e6656747be02f0b4b03cb2a42675.png',0,0,0,1,0,0,0,12,0),(2,2,5,'减肥','减肥的描述还有什么好说的呢',NULL,0,1,80.00,160.00,0.00,'\\upload\\category\\20180613\\eba9e6656747be02f0b4b03cb2a42675.png',0,0,0,1,0,0,0,6,0),(3,1,14,'隆鼻','隆鼻是使用硅胶假体增加鼻子形体美感的最好方式',NULL,0,1,350.00,480.00,0.00,'http://oseihxzg8.bkt.clouddn.com/Fm0L1d-qf7A8GzDn3YUajOT4zdok',0,0,0,1,0,0,0,0,0),(4,1,5,'双眼皮','丹凤眼、双眼皮，流波利转顾盼生辉自不必说，天源专业美容医师采用韩国纯进口无痛微创双眼皮医疗美容设备，配合顶级恢复神水为您塑造独一无二的美丽。。','',0,0,800.00,1180.00,0.00,'http://oseihxzg8.bkt.clouddn.com/FpZIazS6fnUkxe_CqP8eZOdo0ktS',0,0,0,0,0,0,0,0,0),(5,1,3,'七牛云存储 | 上传凭证','阿斯顿法斯蒂芬','',0,0,234.00,800.00,0.00,'http://oseihxzg8.bkt.clouddn.com/FpZIazS6fnUkxe_CqP8eZOdo0ktS',0,0,0,1,0,0,0,0,0),(6,1,3,'七牛云存储 | 上传凭证','asdfasdf','',0,0,234.00,800.00,0.00,'http://oseihxzg8.bkt.clouddn.com/FgcZ2oQS2yOazsqPrjxZjdkyNwq7',0,0,0,0,0,0,0,9,0),(7,1,5,'七牛云存储','sdfasdf','',0,0,350.00,899.00,0.00,'http://oseihxzg8.bkt.clouddn.com/For_fBnP13rPagQDIVZ7Bnyw8LS8',0,0,0,1,0,0,0,12,0),(8,1,3,'七牛云存储 | 上传凭证','asdf','',0,0,350.00,800.00,0.00,'http://oseihxzg8.bkt.clouddn.com/FgcZ2oQS2yOazsqPrjxZjdkyNwq7',0,0,0,1,0,0,0,5,0),(9,2,3,'产品详情','阿斯蒂芬asdf','<p>阿斯顿法斯蒂芬44444444444凤飞飞ffffffffffffffffffffffffffffff</p>',0,0,100.00,300.00,0.00,'http://oseihxzg8.bkt.clouddn.com/FlQwL3OhvwRcsCvv7iVTuSctgmAK',0,0,0,1,0,0,0,0,0),(10,1,3,'beer','asasgggg','<p>gadgdafd</p>',0,1,15.00,35.00,0.00,'http://oseihxzg8.bkt.clouddn.com/FpNkeMzRzCCd3-pAWRQieUQjfJk6',0,0,0,1,0,0,0,0,0),(11,2,13,'细嫩肌肤','好吧，写点简介。','<p>asdf</p>',0,0,3600.00,4800.00,0.00,'http://oseihxzg8.bkt.clouddn.com/FkwADAwhd9qylfgLZyU0SdceS3gC',0,0,0,1,0,0,0,0,10);
/*!40000 ALTER TABLE `ty_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ty_item_cate`
--

DROP TABLE IF EXISTS `ty_item_cate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `ty_item_cate` (
  `cate_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `level` tinyint(1) unsigned DEFAULT '0',
  `parent_id` int(4) unsigned DEFAULT '0',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `sort` int(4) unsigned DEFAULT '0',
  PRIMARY KEY (`cate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ty_item_cate`
--

LOCK TABLES `ty_item_cate` WRITE;
/*!40000 ALTER TABLE `ty_item_cate` DISABLE KEYS */;
INSERT INTO `ty_item_cate` VALUES (1,'面部护理',1,0,'\\upload\\category\\20180613\\caee3d65516e85fe00f6561310096b6e.png',0),(2,'身体塑形',1,0,'\\upload\\category\\20180611\\985f7a2e58565a36cf74c3cbc666752f.png',0),(3,'减肥',2,2,'',0),(5,'美眉',2,1,'',0),(6,'瘦腿',2,2,'',0),(13,'保湿按摩',2,1,'\\upload\\category\\20180613\\eba9e6656747be02f0b4b03cb2a42675.png',0),(14,'纹眉',2,1,'\\upload\\category\\20180613\\4aeedb6de055859e76a72a8d9832dc21.png',0),(15,'面部护理',1,0,'\\upload\\category\\20180611\\e005309e5a5318c0ce9b5c2a0d97fa11.jpg',0);
/*!40000 ALTER TABLE `ty_item_cate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ty_item_img`
--

DROP TABLE IF EXISTS `ty_item_img`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `ty_item_img` (
  `img_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) unsigned NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `sort` tinyint(2) unsigned DEFAULT '0',
  PRIMARY KEY (`img_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ty_item_img`
--

LOCK TABLES `ty_item_img` WRITE;
/*!40000 ALTER TABLE `ty_item_img` DISABLE KEYS */;
INSERT INTO `ty_item_img` VALUES (3,10,'http://oseihxzg8.bkt.clouddn.com/FpNkeMzRzCCd3-pAWRQieUQjfJk6',1),(4,10,'http://oseihxzg8.bkt.clouddn.com/For_fBnP13rPagQDIVZ7Bnyw8LS8',2),(14,9,'http://oseihxzg8.bkt.clouddn.com/FlQwL3OhvwRcsCvv7iVTuSctgmAK',2),(15,11,'http://oseihxzg8.bkt.clouddn.com/FkwADAwhd9qylfgLZyU0SdceS3gC',1);
/*!40000 ALTER TABLE `ty_item_img` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ty_order`
--

DROP TABLE IF EXISTS `ty_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `ty_order` (
  `order_id` int(10) NOT NULL,
  `user_id` int(8) unsigned NOT NULL,
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '0未支付，1已支付，2已退款，3已作废  ,4已过期',
  `type` tinyint(1) DEFAULT '0' COMMENT '0正常，1赠送，2',
  `confirm_id` int(4) unsigned DEFAULT NULL,
  `pay_amount` decimal(10,2) DEFAULT '0.00' COMMENT '支付金额',
  `order_sn` varchar(32) DEFAULT NULL,
  `transaction_no` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `add_time` int(10) unsigned DEFAULT NULL,
  `pay_time` int(10) unsigned DEFAULT NULL,
  `pay_status` tinyint(1) unsigned DEFAULT '0',
  `limit_time` int(10) unsigned DEFAULT '0' COMMENT '服务过期时间',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `order_amount` decimal(10,2) DEFAULT '0.00',
  `order_prom_amount` decimal(10,2) DEFAULT '0.00',
  `points_amount` decimal(10,2) DEFAULT '0.00',
  `coupon_amount` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ty_order`
--

LOCK TABLES `ty_order` WRITE;
/*!40000 ALTER TABLE `ty_order` DISABLE KEYS */;
INSERT INTO `ty_order` VALUES (1,2,1,0,7,1500.00,'o201808123301',NULL,NULL,1534073249,1,0,NULL,1500.00,0.00,0.00,0.00);
/*!40000 ALTER TABLE `ty_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ty_order_item`
--

DROP TABLE IF EXISTS `ty_order_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `ty_order_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) NOT NULL,
  `item_id` int(10) NOT NULL,
  `cate_id` int(4) unsigned DEFAULT NULL,
  `cate_id2` int(4) unsigned DEFAULT NULL,
  `title` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `num` int(4) DEFAULT NULL COMMENT '项目服务次数',
  `price` decimal(10,2) unsigned DEFAULT NULL,
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `cost` decimal(10,2) unsigned DEFAULT NULL,
  `member_price` decimal(10,2) unsigned DEFAULT NULL,
  `give_integral` int(4) unsigned DEFAULT '0' COMMENT '赠送积分',
  `is_checkout` tinyint(1) unsigned DEFAULT '0' COMMENT '是否已对技师结算',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ty_order_item`
--

LOCK TABLES `ty_order_item` WRITE;
/*!40000 ALTER TABLE `ty_order_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `ty_order_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ty_user_level`
--

DROP TABLE IF EXISTS `ty_user_level`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `ty_user_level` (
  `level_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `level_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '' COMMENT '头衔名称',
  `amount` decimal(10,2) DEFAULT NULL COMMENT '等级必要金额',
  `discount` smallint(4) DEFAULT NULL COMMENT '折扣',
  `describe` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '',
  PRIMARY KEY (`level_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ty_user_level`
--

LOCK TABLES `ty_user_level` WRITE;
/*!40000 ALTER TABLE `ty_user_level` DISABLE KEYS */;
INSERT INTO `ty_user_level` VALUES (1,'时尚名媛',1000.00,100,'初级会员'),(2,'尊贵名媛',5000.00,98,'中级会员'),(3,'金卡会员',20000.00,95,'高级会员'),(4,'贵宾尊享',100000.00,93,'最高荣誉');
/*!40000 ALTER TABLE `ty_user_level` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ty_users`
--

DROP TABLE IF EXISTS `ty_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `ty_users` (
  `user_id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(11) DEFAULT NULL,
  `name` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `nickname` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `password` varchar(128) DEFAULT NULL,
  `sex` tinyint(1) unsigned DEFAULT '0',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `birthday` int(10) unsigned DEFAULT NULL,
  `career` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT '职业',
  `characteristic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_slovenian_ci DEFAULT NULL,
  `faverate` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `create_time` int(10) NOT NULL,
  `last_login_time` int(10) DEFAULT NULL,
  `wx_openid` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `qq_openid` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `union_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `token` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `level` tinyint(1) DEFAULT '1' COMMENT '会员等级',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `is_delete` int(10) unsigned DEFAULT '0',
  `is_valid` tinyint(1) unsigned DEFAULT '1',
  `last_come` int(10) unsigned DEFAULT NULL,
  `total_recharge` decimal(10,2) DEFAULT '0.00',
  `points` int(10) DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ty_users`
--

LOCK TABLES `ty_users` WRITE;
/*!40000 ALTER TABLE `ty_users` DISABLE KEYS */;
INSERT INTO `ty_users` VALUES (1,'13007686112','小志',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,1533491023,1533491023,NULL,NULL,NULL,NULL,1,'金水区景峰国际中心8楼',NULL,0,1,1533491023,0.00,0),(2,'13007686113','柏宇娜','旮旯村QQ',NULL,1,NULL,NULL,NULL,NULL,'',1533591023,1533591023,NULL,NULL,NULL,NULL,1,'杨金路新庄',NULL,0,1,1533591023,1500.00,0),(3,'13007686114','美美','','$2y$10$yGEwd37.J2.kK/yGHHQOK.WjSWBocwGPihOz0wAHW1ggCi4vFKWbu',0,NULL,NULL,NULL,NULL,NULL,1534057530,NULL,NULL,NULL,NULL,NULL,1,'',NULL,0,1,NULL,0.00,0),(4,'13007686115','','宝宝','$2y$10$pRaaJgkL6kclMn9IeqVEpOcDelSdoGy1S0Ha9U/1Ah4HnlYhYq/ea',0,NULL,NULL,NULL,NULL,NULL,1534058613,NULL,NULL,NULL,NULL,NULL,1,'',NULL,0,1,NULL,0.00,0),(5,'13007686116','丁1','丁丁','$2y$10$Yer3x64BEMPixUilWdyLGeWY00x71pMxsbVO4h/gCzJZ2sq75lP0i',1,NULL,NULL,NULL,NULL,NULL,1534059406,NULL,NULL,NULL,NULL,NULL,1,'',NULL,0,0,NULL,0.00,0);
/*!40000 ALTER TABLE `ty_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-08-13 23:44:12
