/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50553
Source Host           : 127.0.0.1:3306
Source Database       : practice

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-09-21 15:46:42
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
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `rules` char(80) NOT NULL DEFAULT '',
  `description` varchar(20) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

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
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `condition` char(100) NOT NULL DEFAULT '',
  `cate` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

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
  `sort` int(4) DEFAULT '0',
  `description` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ty_config
-- ----------------------------
INSERT INTO `ty_config` VALUES ('points_ratio', '100', 'pints', '0', '积分比率');
INSERT INTO `ty_config` VALUES ('site_title', '天源美容', 'site_info', '0', '网站名称');

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
  `detail` text,
  `prom_type` tinyint(1) unsigned DEFAULT '0' COMMENT '0正常，1',
  `is_recommend` tinyint(1) unsigned DEFAULT '0' COMMENT '1推荐',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `market_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `cost` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '成本',
  `origin_image` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `give_integral` int(8) DEFAULT '0' COMMENT '送多少积分',
  `click_count` int(8) DEFAULT '0',
  `favorite_count` int(8) DEFAULT '0',
  `on_sale` tinyint(1) DEFAULT '1' COMMENT '0下架，1在售中',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '0正常，1已删除',
  `prom_id` int(10) unsigned DEFAULT '0',
  `is_package` tinyint(3) unsigned DEFAULT '0',
  `sort` int(10) unsigned DEFAULT '0',
  `service_count` int(4) DEFAULT '0',
  `first_letter` char(1) DEFAULT NULL,
  `unit` varchar(10) CHARACTER SET utf8mb4 DEFAULT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ty_item
-- ----------------------------
INSERT INTO `ty_item` VALUES ('1', '1', '5', '纹眉', '好吧，写描述', null, '0', '0', '100.00', '180.20', '0.00', '\\upload\\category\\20180613\\eba9e6656747be02f0b4b03cb2a42675.png', '0', '0', '0', '1', '0', '0', '0', '15', '10', 'W', '次');
INSERT INTO `ty_item` VALUES ('2', '2', '5', '减肥', '减肥的描述还有什么好说的呢', null, '0', '1', '80.00', '160.05', '0.00', '\\upload\\category\\20180613\\eba9e6656747be02f0b4b03cb2a42675.png', '0', '0', '0', '1', '0', '0', '0', '6', '10', 'J', '次');
INSERT INTO `ty_item` VALUES ('3', '1', '14', '隆鼻', '隆鼻是使用硅胶假体增加鼻子形体美感的最好方式', null, '0', '1', '350.00', '480.00', '0.00', 'http://oseihxzg8.bkt.clouddn.com/Fm0L1d-qf7A8GzDn3YUajOT4zdok', '0', '0', '0', '1', '0', '0', '0', '0', '10', 'L', '次');
INSERT INTO `ty_item` VALUES ('4', '1', '5', '双眼皮', '丹凤眼、双眼皮，流波利转顾盼生辉自不必说，天源专业美容医师采用韩国纯进口无痛微创双眼皮医疗美容设备，配合顶级恢复神水为您塑造独一无二的美丽。。', '', '0', '0', '800.00', '1180.00', '0.00', 'http://oseihxzg8.bkt.clouddn.com/FpZIazS6fnUkxe_CqP8eZOdo0ktS', '0', '0', '0', '0', '0', '0', '0', '0', '10', 'S', '次');
INSERT INTO `ty_item` VALUES ('5', '1', '3', '阿斯顿法', '阿斯顿法斯蒂芬', '', '0', '0', '234.00', '800.00', '0.00', 'http://oseihxzg8.bkt.clouddn.com/FpZIazS6fnUkxe_CqP8eZOdo0ktS', '0', '0', '0', '1', '0', '0', '0', '0', '10', 'Q', '次');
INSERT INTO `ty_item` VALUES ('6', '1', '3', '单眼皮', 'asdfasdf', '', '0', '0', '234.00', '800.00', '0.00', 'http://oseihxzg8.bkt.clouddn.com/FgcZ2oQS2yOazsqPrjxZjdkyNwq7', '0', '0', '0', '0', '0', '0', '0', '9', '10', 'Q', '次');
INSERT INTO `ty_item` VALUES ('7', '1', '5', '七牛云', 'sdfasdf', '', '0', '0', '350.00', '899.00', '0.00', 'http://oseihxzg8.bkt.clouddn.com/For_fBnP13rPagQDIVZ7Bnyw8LS8', '0', '0', '0', '1', '0', '0', '0', '12', '10', 'Q', '疗程');
INSERT INTO `ty_item` VALUES ('8', '1', '3', '勲香', 'asdf', '', '0', '0', '350.00', '800.00', '0.00', 'http://oseihxzg8.bkt.clouddn.com/FgcZ2oQS2yOazsqPrjxZjdkyNwq7', '0', '0', '0', '1', '0', '0', '0', '5', '10', 'Q', '次');
INSERT INTO `ty_item` VALUES ('9', '2', '3', '产品详情', '阿斯蒂芬asdf', '<p>阿斯顿法斯蒂芬44444444444凤飞飞ffffffffffffffffffffffffffffff</p>', '0', '0', '100.00', '300.00', '0.00', 'http://oseihxzg8.bkt.clouddn.com/FlQwL3OhvwRcsCvv7iVTuSctgmAK', '0', '0', '0', '1', '0', '0', '0', '0', '10', 'C', '次');
INSERT INTO `ty_item` VALUES ('10', '1', '3', 'beer', 'asasgggg', '<p>gadgdafd</p>', '0', '1', '15.00', '35.00', '0.00', 'http://oseihxzg8.bkt.clouddn.com/FpNkeMzRzCCd3-pAWRQieUQjfJk6', '0', '0', '0', '1', '0', '0', '0', '0', '10', 'B', '次');
INSERT INTO `ty_item` VALUES ('11', '2', '13', '细嫩肌肤', '好吧，写点简介。', '<p>asdf</p>', '0', '0', '3600.00', '4800.00', '0.00', 'http://oseihxzg8.bkt.clouddn.com/FkwADAwhd9qylfgLZyU0SdceS3gC', '0', '0', '0', '1', '0', '0', '0', '0', '10', 'X', '次');

-- ----------------------------
-- Table structure for ty_item_cate
-- ----------------------------
DROP TABLE IF EXISTS `ty_item_cate`;
CREATE TABLE `ty_item_cate` (
  `cate_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8mb4 DEFAULT NULL,
  `level` tinyint(1) unsigned DEFAULT '0',
  `parent_id` int(4) unsigned DEFAULT '0',
  `image` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `sort` int(4) unsigned DEFAULT '0',
  PRIMARY KEY (`cate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

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
  `sort` tinyint(2) unsigned DEFAULT '0',
  PRIMARY KEY (`img_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ty_item_img
-- ----------------------------
INSERT INTO `ty_item_img` VALUES ('3', '10', 'http://oseihxzg8.bkt.clouddn.com/FpNkeMzRzCCd3-pAWRQieUQjfJk6', '1');
INSERT INTO `ty_item_img` VALUES ('4', '10', 'http://oseihxzg8.bkt.clouddn.com/For_fBnP13rPagQDIVZ7Bnyw8LS8', '2');
INSERT INTO `ty_item_img` VALUES ('14', '9', 'http://oseihxzg8.bkt.clouddn.com/FlQwL3OhvwRcsCvv7iVTuSctgmAK', '2');
INSERT INTO `ty_item_img` VALUES ('15', '11', 'http://oseihxzg8.bkt.clouddn.com/FkwADAwhd9qylfgLZyU0SdceS3gC', '1');

-- ----------------------------
-- Table structure for ty_order
-- ----------------------------
DROP TABLE IF EXISTS `ty_order`;
CREATE TABLE `ty_order` (
  `order_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(8) unsigned NOT NULL,
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '0未支付，1已支付，2已退款，3已作废  ,4已过期',
  `type` tinyint(1) DEFAULT '0' COMMENT '0正常，1赠送，2',
  `confirm_id` int(4) unsigned DEFAULT NULL,
  `pay_amount` decimal(10,2) DEFAULT '0.00' COMMENT '支付金额',
  `order_sn` varchar(32) DEFAULT NULL,
  `transaction_no` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `add_time` int(10) unsigned DEFAULT NULL,
  `pay_time` int(10) unsigned DEFAULT NULL,
  `pay_status` tinyint(1) unsigned DEFAULT '0',
  `limit_time` int(10) unsigned DEFAULT '0' COMMENT '服务过期时间',
  `remark` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `order_amount` decimal(10,2) DEFAULT '0.00',
  `order_prom_amount` decimal(10,2) DEFAULT '0.00',
  `points_amount` decimal(10,2) DEFAULT '0.00',
  `coupon_amount` decimal(10,2) DEFAULT '0.00',
  `manager_reduce` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ty_order
-- ----------------------------
INSERT INTO `ty_order` VALUES ('1', '2', '1', '0', '7', '1500.00', 'o201808123301', null, null, '1534073249', '1', '0', null, '1500.00', '0.00', '0.00', '0.00', '0.00');
INSERT INTO `ty_order` VALUES ('7', '1', '1', '1', '2', '200.00', 'An8o4QdMCQNrMsxS', null, '1536068097', '1536068097', '1', '0', null, '110.00', '0.00', '0.00', '0.00', '60.00');
INSERT INTO `ty_order` VALUES ('8', '3', '1', '1', '2', '5000.00', 'AhwLzyXnQWFpWIcV', null, '1536068491', '1536068491', '1', '0', null, '4527.00', '0.00', '0.00', '0.00', '123.00');
INSERT INTO `ty_order` VALUES ('10', '3', '1', '1', '2', '58.00', 'ALeNlw85RCT5o1dw', null, '1536069514', '1536069514', '1', '0', null, '53.00', '0.00', '0.00', '0.00', '4.00');
INSERT INTO `ty_order` VALUES ('11', '3', '1', '1', '2', '120.00', 'A5GbigCv5TGRJ6Ox', null, '1537351561', '1537351561', '1', '0', null, '100.00', '0.00', '0.00', '0.00', '0.00');

-- ----------------------------
-- Table structure for ty_order_action
-- ----------------------------
DROP TABLE IF EXISTS `ty_order_action`;
CREATE TABLE `ty_order_action` (
  `action_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` varchar(32) NOT NULL,
  `operater` varchar(32) NOT NULL,
  `msg` varchar(64) NOT NULL,
  `log_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`action_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ty_order_action
-- ----------------------------
INSERT INTO `ty_order_action` VALUES ('2', '7', 'admin-小志', '新增充值订单', '1536067922');
INSERT INTO `ty_order_action` VALUES ('3', '8', 'admin-小志', '新增充值订单', '1536068491');
INSERT INTO `ty_order_action` VALUES ('4', '10', 'admin-小志', '新增充值订单', '1536069514');
INSERT INTO `ty_order_action` VALUES ('5', '11', 'admin-小志', '新增充值订单', '1537351561');

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
  `give_integral` int(4) unsigned DEFAULT '0' COMMENT '赠送积分',
  `is_checkout` tinyint(1) unsigned DEFAULT '0' COMMENT '是否已对技师结算',
  `is_give` tinyint(1) unsigned DEFAULT '0' COMMENT '0正常，1赠送',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ty_order_item
-- ----------------------------
INSERT INTO `ty_order_item` VALUES ('4', '7', '1', '1', '5', '纹眉', '10', '100.00', '180.20', '0.00', null, '0', '0', '0');
INSERT INTO `ty_order_item` VALUES ('5', '7', '2', '2', '5', '减肥', '10', '80.00', '160.05', '0.00', null, '0', '0', '0');
INSERT INTO `ty_order_item` VALUES ('6', '7', '6', '1', '3', '单眼皮', '10', '234.00', '800.00', '0.00', null, '0', '0', '1');
INSERT INTO `ty_order_item` VALUES ('7', '8', '4', '1', '5', '双眼皮', '10', '800.00', '1180.00', '0.00', null, '0', '0', '0');
INSERT INTO `ty_order_item` VALUES ('8', '8', '8', '1', '3', '勲香', '10', '350.00', '800.00', '0.00', null, '0', '0', '0');
INSERT INTO `ty_order_item` VALUES ('9', '8', '11', '2', '13', '细嫩肌肤', '10', '3600.00', '4800.00', '0.00', null, '0', '0', '0');
INSERT INTO `ty_order_item` VALUES ('10', '8', '3', '1', '14', '隆鼻', '10', '350.00', '480.00', '0.00', null, '0', '0', '1');
INSERT INTO `ty_order_item` VALUES ('11', '10', '1', '1', '5', '纹眉', '10', '100.00', '180.20', '0.00', null, '0', '0', '0');
INSERT INTO `ty_order_item` VALUES ('12', '10', '2', '2', '5', '减肥', '10', '80.00', '160.05', '0.00', null, '0', '0', '0');
INSERT INTO `ty_order_item` VALUES ('13', '10', '8', '1', '3', '勲香', '10', '350.00', '800.00', '0.00', null, '0', '0', '0');
INSERT INTO `ty_order_item` VALUES ('14', '10', '4', '1', '5', '双眼皮', '10', '800.00', '1180.00', '0.00', null, '0', '0', '1');
INSERT INTO `ty_order_item` VALUES ('15', '11', '1', '1', '5', '纹眉', '10', '100.00', '180.20', '0.00', null, '0', '0', '0');
INSERT INTO `ty_order_item` VALUES ('16', '11', '1', '1', '5', '纹眉', '10', '100.00', '180.20', '0.00', null, '0', '0', '1');

-- ----------------------------
-- Table structure for ty_users
-- ----------------------------
DROP TABLE IF EXISTS `ty_users`;
CREATE TABLE `ty_users` (
  `user_id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(11) DEFAULT NULL,
  `name` varchar(16) CHARACTER SET utf8mb4 DEFAULT NULL,
  `nickname` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `password` varchar(128) DEFAULT NULL,
  `sex` tinyint(1) unsigned DEFAULT '0',
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
  `level` tinyint(1) DEFAULT '1' COMMENT '会员等级',
  `address` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `remark` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `is_delete` int(10) unsigned DEFAULT '0',
  `is_valid` tinyint(1) unsigned DEFAULT '1',
  `last_come` int(10) unsigned DEFAULT NULL,
  `total_recharge` decimal(10,2) DEFAULT '0.00',
  `points` int(10) DEFAULT '0',
  `money` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ty_users
-- ----------------------------
INSERT INTO `ty_users` VALUES ('1', '13007686112', '小志11', '', '$2y$10$rFTpVBu8mxANQHMqkhrNAuuA8.XR68a2rhKY6KtcspmknHvNTo4Xm', '0', null, null, null, null, null, '1533491023', '1533491023', null, null, null, null, '1', '金水区景峰国际中心8楼', null, '0', '1', '1533491023', '0.00', '0', '90.00');
INSERT INTO `ty_users` VALUES ('2', '13007686113', '柏宇娜', '旮旯村QQ', null, '1', null, null, null, null, '', '1533591023', '1533591023', null, null, null, null, '1', '杨金路新庄', null, '0', '1', '1533591023', '1500.00', '0', '0.00');
INSERT INTO `ty_users` VALUES ('3', '13007686114', '美美', '', '$2y$10$yGEwd37.J2.kK/yGHHQOK.WjSWBocwGPihOz0wAHW1ggCi4vFKWbu', '0', null, null, null, null, null, '1534057530', null, null, null, null, null, '1', '', null, '0', '1', '1537351561', '120.00', '-473', '498.00');
INSERT INTO `ty_users` VALUES ('4', '13007686115', '', '宝宝', '$2y$10$pRaaJgkL6kclMn9IeqVEpOcDelSdoGy1S0Ha9U/1Ah4HnlYhYq/ea', '0', null, null, null, null, null, '1534058613', null, null, null, null, null, '1', '', null, '0', '1', null, '0.00', '0', '0.00');
INSERT INTO `ty_users` VALUES ('5', '13007686116', '丁1', '丁丁', '$2y$10$Yer3x64BEMPixUilWdyLGeWY00x71pMxsbVO4h/gCzJZ2sq75lP0i', '1', null, null, null, null, null, '1534059406', null, null, null, null, null, '1', '', null, '0', '1', null, '0.00', '0', '0.00');

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ty_user_level
-- ----------------------------
INSERT INTO `ty_user_level` VALUES ('1', '时尚名媛', '1000.00', '100', '初级会员');
INSERT INTO `ty_user_level` VALUES ('2', '尊贵名媛', '5000.00', '98', '中级会员');
INSERT INTO `ty_user_level` VALUES ('3', '金卡会员', '20000.00', '95', '高级会员');
INSERT INTO `ty_user_level` VALUES ('4', '贵宾尊享', '100000.00', '93', '最高荣誉');
