/*
Navicat MySQL Data Transfer

Source Server         : next
Source Server Version : 50638
Source Host           : localhost:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50638
File Encoding         : 65001

Date: 2018-10-25 19:36:55
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `SystemSliderTranslate`
-- ----------------------------
DROP TABLE IF EXISTS `SystemSliderTranslate`;
CREATE TABLE `SystemSliderTranslate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of SystemSliderTranslate
-- ----------------------------
INSERT INTO `SystemSliderTranslate` VALUES ('16', '4', '1', 'Summer flowers3', '4472573easter.jpg');
INSERT INTO `SystemSliderTranslate` VALUES ('17', '4', '9', 'Summer flowers2', null);
INSERT INTO `SystemSliderTranslate` VALUES ('18', '4', '10', 'Summer flowers2', null);
