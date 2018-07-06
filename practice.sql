/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : practice

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2018-07-06 23:59:46
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ty_admin
-- ----------------------------
DROP TABLE IF EXISTS `ty_admin`;
CREATE TABLE `ty_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `password` varchar(32) DEFAULT '',
  `salt` varchar(8) NOT NULL,
  `nickname` varchar(30) DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '正常：1  禁用：0',
  `create_time` int(10) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `regfrom` tinyint(3) DEFAULT 0,
  `token` varchar(64) DEFAULT '' COMMENT 'app用户登陆的时间戳',
  `is_delete` tinyint(1) unsigned DEFAULT 0,
  `remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ty_admin
-- ----------------------------
INSERT INTO `ty_admin` VALUES ('2', 'admin', '8bdfbd80b8bcd726593115d46c6e8dcb', 'abcdefgh', '小志', '', '1', '1528104105', '', '59b65dc0be77b.jpg', '0', '1505123677', '0', '');
INSERT INTO `ty_admin` VALUES ('6', 'alex', '2f24997275d81848beef5bae1e0a85d9', '1E2olh0f', '亚历克斯', '', '1', '1528124281', '', null, '0', '', '0', null);
INSERT INTO `ty_admin` VALUES ('7', 'mina', '038b7501a7489208ab8c05ec7df545e0', 'ymlNMvyl', '美美', '', '1', '1528199372', '', null, '0', '', '0', null);
INSERT INTO `ty_admin` VALUES ('8', '阿斯蒂芬', 'fa6661e96a6f74e941074dded34ec9f7', 'XYFogCPs', '美美12', '', '1', '1528382947', '', null, '0', '', '1', null);

-- ----------------------------
-- Table structure for ty_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `ty_auth_group`;
CREATE TABLE `ty_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `rules` char(80) NOT NULL DEFAULT '',
  `description` varchar(20) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ty_auth_group
-- ----------------------------
INSERT INTO `ty_auth_group` VALUES ('1', '超级管理员', '1', '1,2,3,4,5,6,7,8,9,10', '拥用至高无上的权力');
INSERT INTO `ty_auth_group` VALUES ('2', '管理员', '1', '1,2,3,4,5,6,7', '网站管理员');

-- ----------------------------
-- Table structure for ty_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `ty_auth_group_access`;
CREATE TABLE `ty_auth_group_access` (
  `uid` mediumint(8) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ty_auth_group_access
-- ----------------------------
INSERT INTO `ty_auth_group_access` VALUES ('2', '1');
INSERT INTO `ty_auth_group_access` VALUES ('7', '1');
INSERT INTO `ty_auth_group_access` VALUES ('8', '1');
INSERT INTO `ty_auth_group_access` VALUES ('6', '2');

-- ----------------------------
-- Table structure for ty_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `ty_auth_rule`;
CREATE TABLE `ty_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(80) NOT NULL DEFAULT '',
  `pretitle` varchar(20) NOT NULL DEFAULT '',
  `title` char(20) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `condition` char(100) NOT NULL DEFAULT '',
  `cate` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ty_auth_rule
-- ----------------------------
INSERT INTO `ty_auth_rule` VALUES ('1', 'admin/index/systeminfo', '', '网站设置', '1', '', '1');
INSERT INTO `ty_auth_rule` VALUES ('2', 'admin/index/adminlist', '', '管理员列表', '1', '', '3');
INSERT INTO `ty_auth_rule` VALUES ('3', 'admin/index', '', '后台权限', '1', '', '2');
INSERT INTO `ty_auth_rule` VALUES ('4', 'admin/index/rule', '', '权限细项', '1', '', '2');
INSERT INTO `ty_auth_rule` VALUES ('5', 'admin/index/password', '', '修改密码', '1', '', '2');
INSERT INTO `ty_auth_rule` VALUES ('7', 'admin/index/index', '', '后台首页', '1', '', '3');
INSERT INTO `ty_auth_rule` VALUES ('8', 'Admin/Account/role', '', '角色权限', '1', '', '1');
INSERT INTO `ty_auth_rule` VALUES ('10', 'Admin/Member/member_list', '', '会员管理', '1', '', '1');

-- ----------------------------
-- Table structure for ty_config
-- ----------------------------
DROP TABLE IF EXISTS `ty_config`;
CREATE TABLE `ty_config` (
  `name` varchar(30) NOT NULL DEFAULT '',
  `value` varchar(128) DEFAULT NULL,
  `type` varchar(32) DEFAULT NULL,
  `sort` int(4) DEFAULT 0,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ty_config
-- ----------------------------
INSERT INTO `ty_config` VALUES ('site_title', '天源美容', 'site_info', '0');

-- ----------------------------
-- Table structure for ty_favorite
-- ----------------------------
DROP TABLE IF EXISTS `ty_favorite`;
CREATE TABLE `ty_favorite` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(8) NOT NULL,
  `item_id` int(8) unsigned NOT NULL,
  `add_time` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ty_favorite
-- ----------------------------

-- ----------------------------
-- Table structure for ty_item
-- ----------------------------
DROP TABLE IF EXISTS `ty_item`;
CREATE TABLE `ty_item` (
  `item_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `cate_id` int(4) unsigned NOT NULL,
  `cate_id2` int(4) unsigned NOT NULL,
  `title` varchar(64) CHARACTER SET utf8mb4 NOT NULL,
  `description` varchar(255) NOT NULL,
  `detail` text DEFAULT NULL,
  `prom_type` tinyint(1) unsigned DEFAULT 0 COMMENT '0正常，1',
  `is_recommend` tinyint(1) unsigned DEFAULT 0 COMMENT '1推荐',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
  `market_price` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
  `cost` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '成本',
  `origin_image` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `give_integral` int(8) DEFAULT 0 COMMENT '送多少积分',
  `click_count` int(8) DEFAULT 0,
  `favorite_count` int(8) DEFAULT 0,
  `on_sale` tinyint(1) DEFAULT 1 COMMENT '0下架，1在售中',
  `is_delete` tinyint(1) DEFAULT 0 COMMENT '0正常，1已删除',
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ty_item
-- ----------------------------
INSERT INTO `ty_item` VALUES ('1', '1', '5', '纹眉', '好吧，写描述', null, '0', '0', '100.00', '180.00', '0.00', '\\upload\\category\\20180613\\eba9e6656747be02f0b4b03cb2a42675.png', '0', '0', '0', '1', '0');
INSERT INTO `ty_item` VALUES ('2', '2', '5', '减肥', '减肥的描述还有什么好说的呢', null, '0', '0', '80.00', '160.00', '0.00', '\\upload\\category\\20180613\\eba9e6656747be02f0b4b03cb2a42675.png', '0', '0', '0', '1', '0');

-- ----------------------------
-- Table structure for ty_item_cate
-- ----------------------------
DROP TABLE IF EXISTS `ty_item_cate`;
CREATE TABLE `ty_item_cate` (
  `cate_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8mb4 DEFAULT NULL,
  `level` tinyint(1) unsigned DEFAULT 0,
  `parent_id` int(4) unsigned DEFAULT 0,
  `image` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `sort` int(4) unsigned DEFAULT 0,
  PRIMARY KEY (`cate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ty_item_cate
-- ----------------------------
INSERT INTO `ty_item_cate` VALUES ('1', '面部护理', '1', '0', '\\upload\\category\\20180613\\caee3d65516e85fe00f6561310096b6e.png', '0');
INSERT INTO `ty_item_cate` VALUES ('2', '身体塑形', '1', '0', '\\upload\\category\\20180611\\985f7a2e58565a36cf74c3cbc666752f.png', '0');
INSERT INTO `ty_item_cate` VALUES ('3', '减肥', '2', '2', '', '0');
INSERT INTO `ty_item_cate` VALUES ('5', '美眉', '2', '1', '', '0');
INSERT INTO `ty_item_cate` VALUES ('6', '瘦腿', '2', '2', '', '0');
INSERT INTO `ty_item_cate` VALUES ('13', '保湿按摩', '2', '1', '\\upload\\category\\20180613\\eba9e6656747be02f0b4b03cb2a42675.png', '0');
INSERT INTO `ty_item_cate` VALUES ('14', '纹眉', '2', '1', '\\upload\\category\\20180613\\4aeedb6de055859e76a72a8d9832dc21.png', '0');
INSERT INTO `ty_item_cate` VALUES ('15', '面部护理', '1', '0', '\\upload\\category\\20180611\\e005309e5a5318c0ce9b5c2a0d97fa11.jpg', '0');

-- ----------------------------
-- Table structure for ty_item_img
-- ----------------------------
DROP TABLE IF EXISTS `ty_item_img`;
CREATE TABLE `ty_item_img` (
  `img_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) unsigned NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `sort` tinyint(2) unsigned DEFAULT 0,
  PRIMARY KEY (`img_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ty_item_img
-- ----------------------------

-- ----------------------------
-- Table structure for ty_order
-- ----------------------------
DROP TABLE IF EXISTS `ty_order`;
CREATE TABLE `ty_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(8) unsigned NOT NULL,
  `status` tinyint(1) unsigned DEFAULT 0 COMMENT '0未支付，1已支付，2已退款，3已作废  ,4已过期',
  `type` tinyint(1) DEFAULT 0 COMMENT '0正常，1赠送，2',
  `confirm_id` int(4) unsigned DEFAULT NULL,
  `pay_amount` decimal(10,2) DEFAULT 0.00 COMMENT '支付金额',
  `order_no` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `transaction_no` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `add_time` int(10) unsigned DEFAULT NULL,
  `pay_time` int(10) unsigned DEFAULT NULL,
  `pay_status` tinyint(1) unsigned DEFAULT 0,
  `limit_time` int(10) unsigned DEFAULT 0 COMMENT '服务过期时间',
  `remark` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ty_order
-- ----------------------------

-- ----------------------------
-- Table structure for ty_order_item
-- ----------------------------
DROP TABLE IF EXISTS `ty_order_item`;
CREATE TABLE `ty_order_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) NOT NULL,
  `item_id` int(10) NOT NULL,
  `cate_id` int(4) unsigned DEFAULT NULL,
  `cate_id2` int(4) unsigned DEFAULT NULL,
  `title` varchar(64) CHARACTER SET utf8mb4 NOT NULL,
  `num` int(4) DEFAULT NULL COMMENT '项目服务次数',
  `price` decimal(10,2) unsigned DEFAULT NULL,
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `cost` decimal(10,2) unsigned DEFAULT NULL,
  `member_price` decimal(10,2) unsigned DEFAULT NULL,
  `give_integral` int(4) unsigned DEFAULT 0 COMMENT '赠送积分',
  `is_checkout` tinyint(1) unsigned DEFAULT 0 COMMENT '是否已对技师结算',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ty_order_item
-- ----------------------------

-- ----------------------------
-- Table structure for ty_users
-- ----------------------------
DROP TABLE IF EXISTS `ty_users`;
CREATE TABLE `ty_users` (
  `user_id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(11) DEFAULT NULL,
  `name` varchar(16) CHARACTER SET utf8mb4 DEFAULT NULL,
  `nickname` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `password` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `salt` varchar(8) CHARACTER SET utf8mb4 DEFAULT NULL,
  `sex` tinyint(1) unsigned DEFAULT 0,
  `avatar` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `birthday` int(10) unsigned DEFAULT NULL,
  `career` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '职业',
  `characteristic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_slovenian_ci DEFAULT NULL,
  `faverate` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `create_time` int(10) NOT NULL,
  `last_login_time` int(10) DEFAULT NULL,
  `wx_openid` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `qq_openid` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `union_id` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `token` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `level` tinyint(1) DEFAULT 1 COMMENT '会员等级',
  `address` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `remark` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ty_users
-- ----------------------------

-- ----------------------------
-- Table structure for ty_user_level
-- ----------------------------
DROP TABLE IF EXISTS `ty_user_level`;
CREATE TABLE `ty_user_level` (
  `level_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `level_name` varchar(30) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '头衔名称',
  `amount` decimal(10,2) DEFAULT NULL COMMENT '等级必要金额',
  `discount` smallint(4) DEFAULT NULL COMMENT '折扣',
  `describe` varchar(200) CHARACTER SET utf8mb4 DEFAULT '',
  PRIMARY KEY (`level_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ty_user_level
-- ----------------------------
INSERT INTO `ty_user_level` VALUES ('1', '时尚名媛', '1000.00', '100', '初级会员');
INSERT INTO `ty_user_level` VALUES ('2', '尊贵名媛', '5000.00', '98', '中级会员');
INSERT INTO `ty_user_level` VALUES ('3', '金卡会员', '20000.00', '95', '高级会员');
INSERT INTO `ty_user_level` VALUES ('4', '贵宾尊享', '100000.00', '93', '最高荣誉');
