# Host: 127.0.0.1  (Version: 5.7.34)
# Date: 2021-10-14 00:45:53
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "xt_ddzglodcoins_log"
#

DROP TABLE IF EXISTS `xt_ddzglodcoins_log`;
CREATE TABLE `xt_ddzglodcoins_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `coins` bigint(11) NOT NULL DEFAULT '0' COMMENT '本次操作的金币数',
  `oldcoins` bigint(20) NOT NULL DEFAULT '0' COMMENT '操作前用户的金币数',
  `newcoins` bigint(20) NOT NULL DEFAULT '0' COMMENT '操作后用户的金币数',
  `acttp` tinyint(3) NOT NULL DEFAULT '0' COMMENT '操作金币的类型，1添加，2减少',
  `times` int(11) NOT NULL DEFAULT '0' COMMENT '场次ID',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

#
# Data for table "xt_ddzglodcoins_log"
#

INSERT INTO `xt_ddzglodcoins_log` VALUES (1,12,12800,0,0,2,1,1634036075),(2,13,12800,0,0,2,1,1634036075),(3,9,0,0,0,1,1,1634036075);

#
# Structure for table "xt_ddztable"
#

DROP TABLE IF EXISTS `xt_ddztable`;
CREATE TABLE `xt_ddztable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `times` int(11) NOT NULL DEFAULT '0' COMMENT 'state=1表示桌子当前的场次,state不等于1时表示上一次的场次',
  `state` tinyint(3) NOT NULL DEFAULT '0' COMMENT '桌子当前的状态，0空桌子，1对战中，2等待开始中',
  `nowuser_id` tinyint(3) DEFAULT '0' COMMENT '轮到那个位置了',
  `lastdef_num` tinyint(3) DEFAULT '1' COMMENT '上把默认的地主开始位置，1第一个，2第二个，3第三个',
  `stepstate` tinyint(3) NOT NULL DEFAULT '0' COMMENT '进行中的状态，0未准备全，1抢地主阶段，2加倍阶段，3打牌阶段，4已结束',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='五子棋桌子';

#
# Data for table "xt_ddztable"
#

INSERT INTO `xt_ddztable` VALUES (1,1,0,0,0,0),(2,0,0,0,2,0),(3,0,0,0,2,0),(4,0,0,0,1,0),(5,0,0,0,1,0),(6,0,0,0,1,0),(7,0,0,0,1,0),(8,0,0,0,1,0),(9,0,0,0,1,0),(10,0,0,0,1,0),(11,0,0,0,1,0),(12,0,0,0,1,0),(13,0,0,0,1,0),(14,0,0,0,1,0),(15,0,0,0,1,0),(16,0,0,0,1,0),(17,0,0,0,1,0),(18,0,0,0,1,0),(19,0,0,0,1,0),(20,0,0,0,1,0);

#
# Structure for table "xt_ddztabulist"
#

DROP TABLE IF EXISTS `xt_ddztabulist`;
CREATE TABLE `xt_ddztabulist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `addtime` int(11) NOT NULL DEFAULT '0',
  `uptime` int(11) NOT NULL DEFAULT '0',
  `nowpk` text COMMENT '当前Poke',
  `times` int(11) NOT NULL DEFAULT '0' COMMENT '当前场次',
  `table_id` int(11) NOT NULL DEFAULT '0' COMMENT '当前所在桌子ID',
  `state` tinyint(3) DEFAULT '0' COMMENT '当前是否有效，0无效，1有效',
  `startpk` varchar(255) DEFAULT '' COMMENT '初始化的pk',
  `is_dz` tinyint(3) DEFAULT '0' COMMENT '是否地主,0不是，1地主',
  `is_readly` tinyint(3) DEFAULT '0' COMMENT '是否准备好，0未准备，1已准备',
  `tabposnum` tinyint(3) NOT NULL DEFAULT '0' COMMENT '用户在桌子上的位置，1，2，3',
  `is_roblld` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否抢地主，0未操作，1抢地主，2不抢',
  `is_double` tinyint(3) DEFAULT '0' COMMENT '是否加倍,1加倍，2不加倍',
  PRIMARY KEY (`id`),
  KEY `table_id` (`table_id`,`tabposnum`,`state`),
  KEY `user_id` (`user_id`,`state`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

#
# Data for table "xt_ddztabulist"
#

INSERT INTO `xt_ddztabulist` VALUES (1,1,1634136291,1634136291,'',0,1,0,'',0,0,3,0,0),(2,1,1634136291,1634136291,'',0,1,0,'',0,0,3,0,0),(3,1,1634136291,1634136291,'',0,1,0,'',0,0,1,0,0),(4,1,1634136291,1634136291,'',0,9,0,'',0,0,1,0,0),(5,1,1634136291,1634136291,'',0,5,0,'',0,0,1,0,0),(6,1,1634136291,1634136291,'',0,1,0,'',0,0,1,0,0),(7,9,1634136291,1634136291,'',0,5,0,'',0,0,1,0,0),(8,9,1634136291,1634136291,'',0,20,0,'',0,0,2,0,0),(9,9,1634136291,1634136291,'',0,8,0,'',0,0,1,0,0),(10,1,1634136291,1634136291,'',0,1,0,'',0,0,3,0,0),(11,9,1634136291,1634136291,'a:0:{}',1,1,0,'a:17:{i:0;s:2:\"c7\";i:1;s:3:\"c10\";i:2;s:3:\"c11\";i:3;s:2:\"d1\";i:4;s:2:\"d4\";i:5;s:2:\"d7\";i:6;s:2:\"d8\";i:7;s:2:\"d9\";i:8;s:3:\"d10\";i:9;s:3:\"d12\";i:10;s:3:\"d13\";i:11;s:2:\"h4\";i:12;s:3:\"h12\";i:13;s:3:\"h13\";i:14;s:2:\"s9\";i:15;s:3:\"s10\";i:16;s:2:\"w2\";}',1,1,2,1,1),(12,12,1634136291,1634136291,'a:17:{i:0;s:2:\"c2\";i:1;s:2:\"c9\";i:2;s:3:\"c12\";i:3;s:3:\"c13\";i:4;s:2:\"d2\";i:5;s:2:\"d3\";i:6;s:3:\"d11\";i:7;s:2:\"h1\";i:8;s:2:\"h2\";i:9;s:2:\"h7\";i:10;s:2:\"h8\";i:11;s:3:\"h11\";i:12;s:2:\"s1\";i:13;s:2:\"s2\";i:14;s:2:\"s6\";i:15;s:2:\"s8\";i:16;s:3:\"s13\";}',1,1,0,'a:17:{i:0;s:2:\"c2\";i:1;s:2:\"c9\";i:2;s:3:\"c12\";i:3;s:3:\"c13\";i:4;s:2:\"d2\";i:5;s:2:\"d3\";i:6;s:3:\"d11\";i:7;s:2:\"h1\";i:8;s:2:\"h2\";i:9;s:2:\"h7\";i:10;s:2:\"h8\";i:11;s:3:\"h11\";i:12;s:2:\"s1\";i:13;s:2:\"s2\";i:14;s:2:\"s6\";i:15;s:2:\"s8\";i:16;s:3:\"s13\";}',0,1,1,1,1),(13,13,1634136291,1634136291,'a:17:{i:0;s:2:\"c1\";i:1;s:2:\"c3\";i:2;s:2:\"c5\";i:3;s:2:\"c6\";i:4;s:2:\"c8\";i:5;s:2:\"d5\";i:6;s:2:\"d6\";i:7;s:2:\"h3\";i:8;s:2:\"h5\";i:9;s:2:\"h6\";i:10;s:2:\"h9\";i:11;s:3:\"h10\";i:12;s:2:\"s3\";i:13;s:2:\"s4\";i:14;s:2:\"s7\";i:15;s:3:\"s12\";i:16;s:2:\"w1\";}',1,1,0,'a:17:{i:0;s:2:\"c1\";i:1;s:2:\"c3\";i:2;s:2:\"c5\";i:3;s:2:\"c6\";i:4;s:2:\"c8\";i:5;s:2:\"d5\";i:6;s:2:\"d6\";i:7;s:2:\"h3\";i:8;s:2:\"h5\";i:9;s:2:\"h6\";i:10;s:2:\"h9\";i:11;s:3:\"h10\";i:12;s:2:\"s3\";i:13;s:2:\"s4\";i:14;s:2:\"s7\";i:15;s:3:\"s12\";i:16;s:2:\"w1\";}',0,1,3,1,1),(14,12,1634136291,1634136291,'',0,1,0,'',0,0,1,0,0),(15,9,1634136291,1634136291,'',0,1,0,'',0,0,2,0,0),(16,13,1634136291,1634136291,'',0,1,0,'',0,0,3,0,0);

#
# Structure for table "xt_ddztimes"
#

DROP TABLE IF EXISTS `xt_ddztimes`;
CREATE TABLE `xt_ddztimes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tableid` int(11) DEFAULT '0' COMMENT '桌子ID',
  `state` tinyint(3) DEFAULT '0' COMMENT '1进行中，2已结束',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `uptime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `lastsduser_id` int(11) DEFAULT NULL COMMENT '最后一次的有效出牌用户，只有算牌面符合时更新',
  `lastactuser_id` int(11) DEFAULT '0' COMMENT '最后一次动作的用户id,每次操作都会更新',
  `lastsendpk` varchar(255) DEFAULT NULL COMMENT '最后一次出牌',
  `send_record` text COMMENT '已经出过的牌,出牌记录',
  `nowuser_id` int(11) NOT NULL DEFAULT '0' COMMENT '当前用户ID',
  `dzuser_id` int(11) NOT NULL DEFAULT '0' COMMENT '地主的uid',
  `startuser_id` int(11) NOT NULL DEFAULT '0',
  `stepstate` tinyint(3) DEFAULT '0' COMMENT '进行中的状态，0未准备全，1抢地主阶段，2加倍阶段，3打牌阶段，4已结束',
  `dzpoker` varchar(255) DEFAULT NULL COMMENT '地主的三张牌',
  `rob_num` tinyint(3) NOT NULL DEFAULT '0' COMMENT '抢地主次数，小于3以前轮询，第四次确认',
  `robuser_id` int(11) NOT NULL DEFAULT '0' COMMENT '抢地主的ID',
  `double_num` tinyint(3) DEFAULT '0' COMMENT '加倍操作次数',
  `sendpk_num` tinyint(3) DEFAULT '0' COMMENT '第几次出牌了',
  `winuser_id` int(11) NOT NULL DEFAULT '0' COMMENT '赢提比赛的用户ID',
  `base_coins` int(11) NOT NULL DEFAULT '0' COMMENT '金币基数',
  `multiple_num` int(11) NOT NULL DEFAULT '1' COMMENT '加倍数，初始值是1，每加一次倍*2,每次炸弹*2',
  `is_cla` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否已经计算过了，0未计算过，1计算过',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "xt_ddztimes"
#

INSERT INTO `xt_ddztimes` VALUES (1,1,1,1634136291,1634136291,9,9,'a:1:{i:0;s:2:\"w2\";}','a:19:{i:1;a:2:{s:7:\"user_id\";s:1:\"9\";s:6:\"sendpk\";a:4:{i:0;s:2:\"s5\";i:1;s:2:\"c4\";i:2;s:2:\"h4\";i:3;s:2:\"d4\";}}i:2;a:2:{s:7:\"user_id\";s:2:\"13\";s:6:\"sendpk\";s:7:\"notsend\";}i:3;a:2:{s:7:\"user_id\";s:2:\"12\";s:6:\"sendpk\";s:7:\"notsend\";}i:4;a:2:{s:7:\"user_id\";s:1:\"9\";s:6:\"sendpk\";a:5:{i:0;s:3:\"s10\";i:1;s:3:\"c10\";i:2;s:3:\"d10\";i:3;s:2:\"d7\";i:4;s:2:\"c7\";}}i:5;a:2:{s:7:\"user_id\";s:2:\"13\";s:6:\"sendpk\";s:7:\"notsend\";}i:6;a:2:{s:7:\"user_id\";s:2:\"12\";s:6:\"sendpk\";s:7:\"notsend\";}i:7;a:2:{s:7:\"user_id\";s:1:\"9\";s:6:\"sendpk\";a:6:{i:0;s:3:\"h13\";i:1;s:3:\"d13\";i:2;s:3:\"d12\";i:3;s:3:\"h12\";i:4;s:3:\"c11\";i:5;s:3:\"s11\";}}i:8;a:2:{s:7:\"user_id\";s:2:\"13\";s:6:\"sendpk\";s:7:\"notsend\";}i:9;a:2:{s:7:\"user_id\";s:2:\"12\";s:6:\"sendpk\";s:7:\"notsend\";}i:10;a:2:{s:7:\"user_id\";s:1:\"9\";s:6:\"sendpk\";a:2:{i:0;s:2:\"s9\";i:1;s:2:\"d9\";}}i:11;a:2:{s:7:\"user_id\";s:2:\"13\";s:6:\"sendpk\";s:7:\"notsend\";}i:12;a:2:{s:7:\"user_id\";s:2:\"12\";s:6:\"sendpk\";s:7:\"notsend\";}i:13;a:2:{s:7:\"user_id\";s:1:\"9\";s:6:\"sendpk\";a:1:{i:0;s:2:\"d8\";}}i:14;a:2:{s:7:\"user_id\";s:2:\"13\";s:6:\"sendpk\";s:7:\"notsend\";}i:15;a:2:{s:7:\"user_id\";s:2:\"12\";s:6:\"sendpk\";s:7:\"notsend\";}i:16;a:2:{s:7:\"user_id\";s:1:\"9\";s:6:\"sendpk\";a:1:{i:0;s:2:\"d1\";}}i:17;a:2:{s:7:\"user_id\";s:2:\"13\";s:6:\"sendpk\";s:7:\"notsend\";}i:18;a:2:{s:7:\"user_id\";s:2:\"12\";s:6:\"sendpk\";s:7:\"notsend\";}i:19;a:2:{s:7:\"user_id\";s:1:\"9\";s:6:\"sendpk\";a:1:{i:0;s:2:\"w2\";}}}',13,9,9,4,'a:3:{i:3;s:2:\"c4\";i:43;s:2:\"s5\";i:49;s:3:\"s11\";}',4,9,3,19,9,100,128,1);

#
# Structure for table "xt_ddzulist"
#

DROP TABLE IF EXISTS `xt_ddzulist`;
CREATE TABLE `xt_ddzulist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `state` tinyint(3) NOT NULL DEFAULT '0' COMMENT '用户在列表中的状态，0表示退出/无效，1处于界面的列给中，没有进入任何桌子，2已进入某个桌子的状态',
  `tableid` int(11) NOT NULL DEFAULT '0' COMMENT '用户状态为2时，对应的桌子ID',
  `tableposid` int(11) NOT NULL DEFAULT '0' COMMENT '用户所在桌子里面的位置',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `lastuptime` int(11) NOT NULL DEFAULT '0' COMMENT '最后一次的更新时间',
  `glodcoins` bigint(20) NOT NULL DEFAULT '0' COMMENT '金币数',
  `total_matches` int(11) DEFAULT '0' COMMENT '玩游戏的总场数',
  `win_matches` int(11) NOT NULL DEFAULT '0' COMMENT '赢得比赛的场次',
  `fail_matches` int(11) NOT NULL DEFAULT '0' COMMENT '失败场数',
  PRIMARY KEY (`id`),
  KEY `tableid` (`tableid`,`state`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='进入五子棋的用户列表';

#
# Data for table "xt_ddzulist"
#

INSERT INTO `xt_ddzulist` VALUES (1,1,1,0,0,1634136291,1634136291,0,0,0,0),(2,9,1,0,0,1634136291,1634136291,0,1,1,0),(3,12,1,0,0,1634136291,1634136291,0,1,0,1),(4,13,1,0,0,1634136291,1634136291,0,1,0,1);

#
# Structure for table "xt_friend"
#

DROP TABLE IF EXISTS `xt_friend`;
CREATE TABLE `xt_friend` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `my_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `fri_uid` int(11) unsigned DEFAULT '0' COMMENT '好友ID',
  `diy_name` varchar(50) DEFAULT NULL COMMENT '呢称',
  `group_id` int(11) unsigned DEFAULT '0' COMMENT '群组ID',
  `state` tinyint(3) NOT NULL DEFAULT '1' COMMENT '用户状态，1正常，２加入黑名单',
  `cmtn_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '消息ID',
  `cmtn_cnt` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '未读的消息数',
  `sim_msg` varchar(50) DEFAULT NULL COMMENT '简短信息',
  `c_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '建立关联关系的时间',
  `uptime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `rel_type` tinyint(3) unsigned DEFAULT '0' COMMENT '关系类型，0是指朋友，1是指群组',
  PRIMARY KEY (`id`),
  KEY `myfirend` (`my_uid`,`state`),
  KEY `myfirtype` (`my_uid`,`state`,`rel_type`),
  KEY `groupid` (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

#
# Data for table "xt_friend"
#

INSERT INTO `xt_friend` VALUES (1,3,1,NULL,0,1,1,1,'发一条消息',1634136291,1634143411,0),(2,1,3,NULL,0,1,1,0,'发一条消息',1634136291,1634143411,0),(3,2,1,NULL,0,1,3,1,'HI',1634136291,1634143399,0),(4,1,2,NULL,0,1,3,0,'HI',1634136291,1634143399,0),(5,6,3,NULL,0,1,5,0,NULL,1634136291,1634136291,0),(6,3,6,NULL,0,1,5,0,NULL,1634136291,1634136291,0),(7,6,1,NULL,0,1,7,0,NULL,1634136291,1634136291,0),(8,1,6,NULL,0,1,7,0,NULL,1634136291,1634136291,0),(9,7,6,NULL,0,1,9,0,NULL,1634136291,1634136291,0),(10,6,7,NULL,0,1,9,0,NULL,1634136291,1634136291,0),(11,7,1,NULL,0,1,11,0,NULL,1634136291,1634136291,0),(12,1,7,NULL,0,1,11,0,NULL,1634136291,1634136291,0),(13,8,7,NULL,0,1,13,0,NULL,1634136291,1634136291,0),(14,7,8,NULL,0,1,13,0,NULL,1634136291,1634136291,0),(15,8,1,NULL,0,1,15,0,NULL,1634136291,1634136291,0),(16,1,8,NULL,0,1,15,0,NULL,1634136291,1634136291,0),(17,8,3,NULL,0,1,17,0,NULL,1634136291,1634136291,0),(18,3,8,NULL,0,1,17,0,NULL,1634136291,1634136291,0),(19,9,7,NULL,0,1,19,0,NULL,1634136291,1634136291,0),(20,7,9,NULL,0,1,19,0,NULL,1634136291,1634136291,0),(21,9,3,NULL,0,1,21,0,NULL,1634136291,1634136291,0),(22,3,9,NULL,0,1,21,0,NULL,1634136291,1634136291,0),(23,9,1,NULL,0,1,23,0,NULL,1634136291,1634136291,0),(24,1,9,NULL,0,1,23,0,NULL,1634136291,1634136291,0),(25,12,9,NULL,0,1,25,0,NULL,1634136291,1634136291,0),(26,9,12,NULL,0,1,25,0,NULL,1634136291,1634136291,0),(27,12,7,NULL,0,1,27,0,NULL,1634136291,1634136291,0),(28,7,12,NULL,0,1,27,0,NULL,1634136291,1634136291,0),(29,12,6,NULL,0,1,29,0,NULL,1634136291,1634136291,0),(30,6,12,NULL,0,1,29,0,NULL,1634136291,1634136291,0),(31,12,3,NULL,0,1,31,0,NULL,1634136291,1634136291,0),(32,3,12,NULL,0,1,31,0,NULL,1634136291,1634136291,0),(33,12,1,NULL,0,1,33,0,NULL,1634136291,1634136291,0),(34,1,12,NULL,0,1,33,0,NULL,1634136291,1634136291,0),(35,14,12,NULL,0,1,35,0,NULL,1634136291,1634136291,0),(36,12,14,NULL,0,1,35,0,NULL,1634136291,1634136291,0),(37,14,8,NULL,0,1,37,0,NULL,1634136291,1634136291,0),(38,8,14,NULL,0,1,37,0,NULL,1634136291,1634136291,0),(39,14,7,NULL,0,1,39,0,NULL,1634136291,1634136291,0),(40,7,14,NULL,0,1,39,0,NULL,1634136291,1634136291,0),(41,14,6,NULL,0,1,41,0,NULL,1634136291,1634136291,0),(42,6,14,NULL,0,1,41,0,NULL,1634136291,1634136291,0),(43,14,3,NULL,0,1,43,0,NULL,1634136291,1634136291,0),(44,3,14,NULL,0,1,43,0,NULL,1634136291,1634136291,0),(45,14,1,NULL,0,1,45,0,NULL,1634136291,1634136291,0),(46,1,14,NULL,0,1,45,0,NULL,1634136291,1634136291,0),(47,1,0,NULL,1,1,47,0,'这是一个群...',1634136291,1634143423,1),(48,3,0,NULL,1,1,47,2,'这是一个群...',1634136291,1634143423,1),(49,14,0,NULL,1,1,47,4,'这是一个群...',1634136291,1634143423,1),(50,12,0,NULL,1,1,47,4,'这是一个群...',1634136291,1634143423,1),(51,9,0,NULL,1,1,47,1,'这是一个群...',1634136291,1634143423,1),(52,8,0,NULL,1,1,47,4,'这是一个群...',1634136291,1634143423,1),(53,7,0,NULL,1,1,47,4,'这是一个群...',1634136291,1634143423,1),(54,6,0,NULL,1,1,47,4,'这是一个群...',1634136291,1634143423,1),(55,10,8,NULL,0,1,55,0,NULL,1634136291,1634136291,0),(56,8,10,NULL,0,1,55,0,NULL,1634136291,1634136291,0),(57,10,7,NULL,0,1,57,0,NULL,1634136291,1634136291,0),(58,7,10,NULL,0,1,57,0,NULL,1634136291,1634136291,0),(59,10,3,NULL,0,1,59,0,NULL,1634136291,1634136291,0),(60,3,10,NULL,0,1,59,0,NULL,1634136291,1634136291,0),(61,4,10,NULL,0,1,61,0,NULL,1634136291,1634136291,0),(62,10,4,NULL,0,1,61,0,NULL,1634136291,1634136291,0),(63,4,9,NULL,0,1,63,0,NULL,1634136291,1634136291,0),(64,9,4,NULL,0,1,63,0,NULL,1634136291,1634136291,0),(65,4,8,NULL,0,1,65,0,NULL,1634136291,1634136291,0),(66,8,4,NULL,0,1,65,0,NULL,1634136291,1634136291,0),(67,4,1,NULL,0,1,67,1,'1',1634136291,1634143430,0),(68,1,4,NULL,0,1,67,0,'1',1634136291,1634143430,0),(69,4,3,NULL,0,1,69,0,NULL,1634136291,1634136291,0),(70,3,4,NULL,0,1,69,0,NULL,1634136291,1634136291,0),(71,9,10,NULL,0,1,71,0,NULL,1634136291,1634136291,0),(72,10,9,NULL,0,1,71,0,NULL,1634136291,1634136291,0),(73,13,10,NULL,0,1,73,0,NULL,1634136291,1634136291,0),(74,10,13,NULL,0,1,73,0,NULL,1634136291,1634136291,0),(75,13,12,NULL,0,1,75,0,NULL,1634136291,1634136291,0),(76,12,13,NULL,0,1,75,0,NULL,1634136291,1634136291,0),(77,13,9,NULL,0,1,77,0,NULL,1634136291,1634136291,0),(78,9,13,NULL,0,1,77,0,NULL,1634136291,1634136291,0),(79,13,7,NULL,0,1,79,0,NULL,1634136291,1634136291,0),(80,7,13,NULL,0,1,79,0,NULL,1634136291,1634136291,0),(81,13,3,NULL,0,1,81,0,NULL,1634136291,1634136291,0),(82,3,13,NULL,0,1,81,0,NULL,1634136291,1634136291,0),(83,13,1,NULL,0,1,83,0,NULL,1634136291,1634136291,0),(84,1,13,NULL,0,1,83,0,NULL,1634136291,1634136291,0);

#
# Structure for table "xt_frireq"
#

DROP TABLE IF EXISTS `xt_frireq`;
CREATE TABLE `xt_frireq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_uid` int(11) NOT NULL DEFAULT '0' COMMENT '请求用户ID',
  `to_uid` int(11) NOT NULL DEFAULT '0' COMMENT '接受用户ID',
  `reqtp` tinyint(3) NOT NULL DEFAULT '1' COMMENT '消息类型，1请求通知，2返回通知',
  `intro` varchar(255) DEFAULT NULL,
  `state` tinyint(3) DEFAULT '0' COMMENT '0请求中，1同意加好友，2拒绝加好友，3忽略',
  `is_agree` tinyint(3) DEFAULT '0' COMMENT '0无意义，1表示同意，2表示拒绝,回馈消息时才有用',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `uptime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `form_reqid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8;

#
# Data for table "xt_frireq"
#

INSERT INTO `xt_frireq` VALUES (1,1,2,1,'加下好友',1,0,1634136291,1634136291,0),(2,1,3,1,'好友',1,0,1634136291,1634136291,0),(3,1,4,1,'加下好友',1,0,1634136291,1634136291,0),(4,3,1,2,NULL,0,1,1634136291,1634136291,2),(5,3,4,1,'二柱子加我',1,0,1634136291,1634136291,0),(6,2,1,2,NULL,0,1,1634136291,1634136291,1),(7,3,5,1,'',0,0,1634136291,1634136291,0),(8,3,6,1,'',1,0,1634136291,1634136291,0),(9,3,7,1,'',0,0,1634136291,1634136291,0),(10,3,8,1,'',1,0,1634136291,1634136291,0),(11,1,4,1,'',1,0,1634136291,1634136291,0),(12,1,6,1,'',1,0,1634136291,1634136291,0),(13,1,7,1,'',1,0,1634136291,1634136291,0),(14,1,8,1,'',1,0,1634136291,1634136291,0),(15,1,9,1,'',1,0,1634136291,1634136291,0),(16,1,10,1,'',0,0,1634136291,1634136291,0),(17,1,11,1,'',0,0,1634136291,1634136291,0),(18,1,12,1,'',1,0,1634136291,1634136291,0),(19,1,13,1,'',1,0,1634136291,1634136291,0),(20,1,14,1,'',1,0,1634136291,1634136291,0),(21,3,9,1,'',1,0,1634136291,1634136291,0),(22,3,6,1,'',1,0,1634136291,1634136291,0),(23,3,10,1,'',1,0,1634136291,1634136291,0),(24,3,12,1,'',1,0,1634136291,1634136291,0),(25,3,13,1,'',1,0,1634136291,1634136291,0),(26,3,14,1,'',1,0,1634136291,1634136291,0),(27,6,3,2,NULL,0,1,1634136291,1634136291,22),(28,6,1,2,NULL,0,1,1634136291,1634136291,12),(29,6,3,2,NULL,0,1,1634136291,1634136291,8),(30,6,7,1,'',1,0,1634136291,1634136291,0),(31,6,7,1,'',1,0,1634136291,1634136291,0),(32,6,11,1,'',0,0,1634136291,1634136291,0),(33,6,12,1,'',1,0,1634136291,1634136291,0),(34,6,14,1,'',1,0,1634136291,1634136291,0),(35,7,6,2,NULL,0,1,1634136291,1634136291,31),(36,7,6,2,NULL,0,1,1634136291,1634136291,30),(37,7,1,2,NULL,0,1,1634136291,1634136291,13),(38,7,13,1,'',1,0,1634136291,1634136291,0),(39,7,12,1,'',1,0,1634136291,1634136291,0),(40,7,8,1,'',1,0,1634136291,1634136291,0),(41,7,10,1,'',1,0,1634136291,1634136291,0),(42,7,9,1,'',1,0,1634136291,1634136291,0),(43,7,14,1,'',1,0,1634136291,1634136291,0),(44,8,7,2,NULL,0,1,1634136291,1634136291,40),(45,8,1,2,NULL,0,1,1634136291,1634136291,14),(46,8,3,2,NULL,0,1,1634136291,1634136291,10),(47,8,6,1,'',0,0,1634136291,1634136291,0),(48,8,14,1,'',1,0,1634136291,1634136291,0),(49,8,10,1,'',1,0,1634136291,1634136291,0),(50,8,4,1,'',1,0,1634136291,1634136291,0),(51,9,7,2,NULL,0,1,1634136291,1634136291,42),(52,9,3,2,NULL,0,1,1634136291,1634136291,21),(53,9,1,2,NULL,0,1,1634136291,1634136291,15),(54,9,4,1,'',1,0,1634136291,1634136291,0),(55,9,12,1,'',1,0,1634136291,1634136291,0),(56,9,13,1,'',1,0,1634136291,1634136291,0),(57,12,9,2,NULL,0,1,1634136291,1634136291,55),(58,12,7,2,NULL,0,1,1634136291,1634136291,39),(59,12,6,2,NULL,0,1,1634136291,1634136291,33),(60,12,3,2,NULL,0,1,1634136291,1634136291,24),(61,12,1,2,NULL,0,1,1634136291,1634136291,18),(62,12,13,1,'',1,0,1634136291,1634136291,0),(63,12,14,1,'',1,0,1634136291,1634136291,0),(64,12,8,1,'',0,0,1634136291,1634136291,0),(65,14,12,2,NULL,0,1,1634136291,1634136291,63),(66,14,8,2,NULL,0,1,1634136291,1634136291,48),(67,14,7,2,NULL,0,1,1634136291,1634136291,43),(68,14,6,2,NULL,0,1,1634136291,1634136291,34),(69,14,3,2,NULL,0,1,1634136291,1634136291,26),(70,14,1,2,NULL,0,1,1634136291,1634136291,20),(71,10,8,2,NULL,0,1,1634136291,1634136291,49),(72,10,7,2,NULL,0,1,1634136291,1634136291,41),(73,10,3,2,NULL,0,1,1634136291,1634136291,23),(74,10,6,1,'',0,0,1634136291,1634136291,0),(75,10,4,1,'',1,0,1634136291,1634136291,0),(76,10,9,1,'',1,0,1634136291,1634136291,0),(77,10,13,1,'',1,0,1634136291,1634136291,0),(78,10,14,1,'',0,0,1634136291,1634136291,0),(79,4,10,2,NULL,0,1,1634136291,1634136291,75),(80,4,9,2,NULL,0,1,1634136291,1634136291,54),(81,4,8,2,NULL,0,1,1634136291,1634136291,50),(82,4,1,2,NULL,0,1,1634136291,1634136291,11),(83,4,3,2,NULL,0,1,1634136291,1634136291,5),(84,4,1,2,NULL,0,1,1634136291,1634136291,3),(85,4,12,1,'',0,0,1634136291,1634136291,0),(86,4,14,1,'',0,0,1634136291,1634136291,0),(87,4,7,1,'',0,0,1634136291,1634136291,0),(88,9,10,2,NULL,0,1,1634136291,1634136291,76),(89,13,10,2,NULL,0,1,1634136291,1634136291,77),(90,13,12,2,NULL,0,1,1634136291,1634136291,62),(91,13,9,2,NULL,0,1,1634136291,1634136291,56),(92,13,7,2,NULL,0,1,1634136291,1634136291,38),(93,13,3,2,NULL,0,1,1634136291,1634136291,25),(94,13,1,2,NULL,0,1,1634136291,1634136291,19);

#
# Structure for table "xt_group"
#

DROP TABLE IF EXISTS `xt_group`;
CREATE TABLE `xt_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `gname` varchar(100) DEFAULT NULL,
  `gnote` varchar(255) DEFAULT NULL,
  `adduid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `addtime` int(11) unsigned NOT NULL DEFAULT '0',
  `uptime` int(11) unsigned NOT NULL DEFAULT '0',
  `cmtn_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

#
# Data for table "xt_group"
#

INSERT INTO `xt_group` VALUES (1,NULL,NULL,1,1634136291,1634136291,47);

#
# Structure for table "xt_member"
#

DROP TABLE IF EXISTS `xt_member`;
CREATE TABLE `xt_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `pic` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `telphone` varchar(150) DEFAULT NULL,
  `phone` varchar(150) DEFAULT NULL,
  `addtime` int(10) NOT NULL,
  `login_num` int(10) NOT NULL DEFAULT '0',
  `uptime` int(10) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0无效,1为有效,预留字段',
  `intro` varchar(255) DEFAULT '' COMMENT '个性签名',
  `notice_num` int(11) NOT NULL DEFAULT '0' COMMENT '新消息数',
  `allow_sch` tinyint(3) DEFAULT '1' COMMENT '是否允许搜索，1允许，2不允许',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "xt_member"
#

INSERT INTO `xt_member` VALUES (1,'admin','77d3b7ed9db7d236b9eac8262d27f6a5','管理员','20211011172018255.jpg','abc@qq.com','36695','15013581400',123,8,1634136291,1,'我是管理员',0,1),(2,'cs','77d3b7ed9db7d236b9eac8262d27f6a5','香克斯',NULL,'','1546456','3655485',123,5,1634136291,1,'你好朋友',0,1),(3,'mingr','77d3b7ed9db7d236b9eac8262d27f6a5','鸣人','20211011175724305.png',NULL,NULL,NULL,123,5,1634136291,1,NULL,3,1),(4,'zuoz','77d3b7ed9db7d236b9eac8262d27f6a5','佐助','20211012183704896.png',NULL,NULL,NULL,123,5,1634136291,1,NULL,0,1),(5,'xiaoy','77d3b7ed9db7d236b9eac8262d27f6a5','上缨',NULL,NULL,NULL,NULL,123,2,1634136291,1,NULL,1,1),(6,'kakax','77d3b7ed9db7d236b9eac8262d27f6a5','卡卡西','20211011172942247.png',NULL,NULL,NULL,123,5,1634136291,1,NULL,6,1),(7,'dashew','77d3b7ed9db7d236b9eac8262d27f6a5','大蛇丸','20211011173704669.png',NULL,NULL,NULL,123,1,1634136291,1,NULL,7,1),(8,'zilaiy','77d3b7ed9db7d236b9eac8262d27f6a5','自来也','20211011174048755.png','',NULL,'',123,5,1634136291,1,'',4,1),(9,'yzbb','77d3b7ed9db7d236b9eac8262d27f6a5','班','20211011174407339.png','',NULL,'',123,4,1634136291,1,'',0,1),(10,'yzby','77d3b7ed9db7d236b9eac8262d27f6a5','鼬','20211012182625361.png','',NULL,'',123,5,1634136291,1,'',3,1),(11,'yzbdt','77d3b7ed9db7d236b9eac8262d27f6a5','带土',NULL,NULL,NULL,NULL,123,4,1634136291,1,'',2,1),(12,'zhuj','77d3b7ed9db7d236b9eac8262d27f6a5','柱间','20211011174745258.png','',NULL,'',123,7,1634136291,1,'',3,1),(13,'feijian','77d3b7ed9db7d236b9eac8262d27f6a5','扉间','20211012185007839.png',NULL,NULL,NULL,123,1,1634136291,1,'',0,1),(14,'yuanf','77d3b7ed9db7d236b9eac8262d27f6a5','猿飞','20211011175603818.png',NULL,NULL,NULL,123,1,1634136291,1,'',2,1);

#
# Structure for table "xt_member_log"
#

DROP TABLE IF EXISTS `xt_member_log`;
CREATE TABLE `xt_member_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `member_id` int(10) NOT NULL DEFAULT '0',
  `login_time` int(10) NOT NULL,
  `ip` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "xt_member_log"
#

INSERT INTO `xt_member_log` VALUES (1,3,1634136291,'127.0.0.1'),(2,1,1634136291,'127.0.0.1'),(3,2,1634136291,'172.16.45.29'),(4,3,1634136291,'127.0.0.1'),(5,6,1634136291,'127.0.0.1'),(6,7,1634136291,'127.0.0.1'),(7,8,1634136291,'127.0.0.1'),(8,9,1634136291,'127.0.0.1'),(9,12,1634136291,'127.0.0.1'),(10,14,1634136291,'127.0.0.1'),(11,3,1634136291,'127.0.0.1'),(12,1,1634136291,'127.0.0.1'),(13,3,1634136291,'127.0.0.1'),(14,2,1634136291,'172.16.45.29'),(15,2,1634136291,'172.16.45.29'),(16,2,1634136291,'172.16.45.29'),(17,10,1634136291,'127.0.0.1'),(18,4,1634136291,'127.0.0.1'),(19,9,1634136291,'127.0.0.1'),(20,12,1634136291,'127.0.0.1'),(21,13,1634136291,'127.0.0.1'),(22,1,1634142892,'172.17.0.1'),(23,1,1634142953,'172.17.0.1'),(24,1,1634143463,'172.17.0.1');

#
# Structure for table "xt_message"
#

DROP TABLE IF EXISTS `xt_message`;
CREATE TABLE `xt_message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `form_uid` int(11) NOT NULL,
  `to_uid` int(11) NOT NULL DEFAULT '0' COMMENT '发送给谁的，0表示不是给个人的，有可能是群组',
  `msg_tp` tinyint(1) NOT NULL DEFAULT '1' COMMENT '消息类型，1发送给某人，2发送给群组，3发送的广播。。。',
  `to_group_id` int(11) NOT NULL DEFAULT '0' COMMENT '群组ID，发送给某个群组的标识，0表示非群组',
  `cmtn_id` int(11) NOT NULL DEFAULT '0' COMMENT 'communication 某两个私人之间的消息标识，第一次发消息时生成，用于两人之间的消息提取',
  `send_time` int(11) NOT NULL COMMENT '消息发送时间',
  `con_tp` tinyint(1) NOT NULL DEFAULT '0' COMMENT '内容类型，1为text,2图片,3混合体，4附件',
  `content` text COMMENT '消息内容',
  `exttp` varchar(255) NOT NULL DEFAULT 'txt' COMMENT '扩展类型，主要是给附件用',
  `realaddr` varchar(255) NOT NULL DEFAULT '0' COMMENT '真实文件地址',
  `exptime` int(11) NOT NULL DEFAULT '0' COMMENT '有效时间，主要给发送的附件时用',
  `filesize` int(11) DEFAULT '0',
  `msgstate` tinyint(1) DEFAULT '1' COMMENT '消息状态，1正常，2撤回',
  `uptime` int(11) DEFAULT '0' COMMENT '最后更新时间',
  PRIMARY KEY (`id`),
  KEY `cmtn_id` (`cmtn_id`) USING BTREE,
  KEY `to_group_id` (`to_group_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Data for table "xt_message"
#

INSERT INTO `xt_message` VALUES (1,1,2,1,0,3,1634143399,1,'HI','txt','0',0,0,1,0),(2,1,3,1,0,1,1634143411,1,'发一条消息','txt','0',0,0,1,0),(3,1,0,2,1,47,1634143423,1,'这是一个群消息','txt','0',0,0,1,0),(4,1,4,1,0,67,1634143430,1,'1','txt','0',0,0,1,0);

#
# Structure for table "xt_msts"
#

DROP TABLE IF EXISTS `xt_msts`;
CREATE TABLE `xt_msts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0',
  `goods_num` int(11) NOT NULL DEFAULT '0',
  `cs` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Data for table "xt_msts"
#


#
# Structure for table "xt_sys_config"
#

DROP TABLE IF EXISTS `xt_sys_config`;
CREATE TABLE `xt_sys_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `k_field` varchar(100) NOT NULL,
  `k_value` varchar(100) NOT NULL,
  `order_num` int(10) NOT NULL DEFAULT '0',
  `groupby` tinyint(2) NOT NULL DEFAULT '1',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1为TEXT类型',
  `uptime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "xt_sys_config"
#

INSERT INTO `xt_sys_config` VALUES (1,'公司名称','web_company','某某科技有限公司',0,1,1,1634136291);

#
# Structure for table "xt_temp_pic"
#

DROP TABLE IF EXISTS `xt_temp_pic`;
CREATE TABLE `xt_temp_pic` (
  `id` int(20) NOT NULL AUTO_INCREMENT COMMENT '这张表的储存上临时上传的图片,主要是为了实现没有被用到的图片在操作完成后再删除掉',
  `adduid` int(11) NOT NULL,
  `addtime` int(10) NOT NULL,
  `pic` varchar(50) NOT NULL,
  `pic_dir` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "xt_temp_pic"
#


#
# Structure for table "xt_wzchat"
#

DROP TABLE IF EXISTS `xt_wzchat`;
CREATE TABLE `xt_wzchat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `formuid` int(11) NOT NULL DEFAULT '0' COMMENT '发送消息的用户ID',
  `totableid` int(11) DEFAULT '0' COMMENT '目标的桌子ID',
  `chattxt` varchar(255) DEFAULT NULL COMMENT '消息内容',
  `addtime` int(11) DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='五子棋里面的聊天，五子棋里面的都是广播';

#
# Data for table "xt_wzchat"
#

INSERT INTO `xt_wzchat` VALUES (1,1,1,'是我先下吗？',1634136291),(2,3,1,'是的',1634136291);

#
# Structure for table "xt_wztable"
#

DROP TABLE IF EXISTS `xt_wztable`;
CREATE TABLE `xt_wztable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `times` int(11) NOT NULL DEFAULT '0' COMMENT '桌子当前的场次',
  `state` tinyint(3) NOT NULL DEFAULT '0' COMMENT '桌子当前的状态，0空桌子，1对战中，2等待开始中',
  `user1id` int(11) NOT NULL DEFAULT '0' COMMENT '位置1的用户ID',
  `user2id` int(11) NOT NULL DEFAULT '0' COMMENT '位置2的用户ID',
  `u1state` tinyint(3) NOT NULL DEFAULT '0' COMMENT '用户1的状态，0坐下状态，1准备状态',
  `u2state` tinyint(3) DEFAULT '0' COMMENT '用户2的状态，0刚坐下状态，1准备好状态',
  `nowposid` tinyint(3) DEFAULT '0' COMMENT '轮到那个位置了',
  `u1qz` tinyint(1) DEFAULT '0' COMMENT '用户1棋子颜色，1白色，2黑色',
  `u2qz` tinyint(1) NOT NULL DEFAULT '0' COMMENT '用户2棋子颜色，1白色，2黑色',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='五子棋桌子';

#
# Data for table "xt_wztable"
#

INSERT INTO `xt_wztable` VALUES (1,2,0,0,0,0,0,2,1,2),(2,5,0,0,0,0,0,1,1,2),(3,0,0,0,0,0,0,0,0,0),(4,0,0,0,0,0,0,0,0,0),(5,0,0,0,0,0,0,0,0,0),(6,0,0,0,0,0,0,0,0,0),(7,0,0,0,0,0,0,0,0,0),(8,0,0,0,0,0,0,0,0,0),(9,0,0,0,0,0,0,0,0,0),(10,0,0,0,0,0,0,0,0,0),(11,0,0,0,0,0,0,0,0,0),(12,0,0,0,0,0,0,0,0,0),(13,0,0,0,0,0,0,0,0,0),(14,0,0,0,0,0,0,0,0,0),(15,0,0,0,0,0,0,0,0,0),(16,0,0,0,0,0,0,0,0,0),(17,0,0,0,0,0,0,0,0,0),(18,0,0,0,0,0,0,0,0,0),(19,0,0,0,0,0,0,0,0,0),(20,0,0,0,0,0,0,0,0,0);

#
# Structure for table "xt_wztimes"
#

DROP TABLE IF EXISTS `xt_wztimes`;
CREATE TABLE `xt_wztimes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tableid` int(11) DEFAULT '0' COMMENT '桌子ID',
  `user1id` int(11) NOT NULL DEFAULT '0' COMMENT '用户1ID',
  `user2id` int(11) DEFAULT '0' COMMENT '用户2ID',
  `state` tinyint(3) DEFAULT '0' COMMENT '0默认值，1进行中，2已结束',
  `winposid` tinyint(3) NOT NULL DEFAULT '0' COMMENT '此把游戏的胜利方所在位置1和2',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `uptime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `u1qz` tinyint(1) NOT NULL DEFAULT '0' COMMENT '棋子颜色，1白色，2黑色',
  `u2qz` tinyint(1) NOT NULL DEFAULT '0' COMMENT '棋子颜色，1白色，2黑色',
  `chesscon` text,
  `lastch` varchar(11) DEFAULT NULL COMMENT '最后棋局',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

#
# Data for table "xt_wztimes"
#

INSERT INTO `xt_wztimes` VALUES (1,1,1,3,2,1,1634136291,1634136291,2,1,'a:15:{i:0;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:1;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:2;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:3;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:4;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:5;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:6;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:7;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:8;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:9;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:10;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:11;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:12;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:13;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:14;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}}','0'),(2,1,1,3,2,1,1634136291,1634136291,1,2,'a:15:{i:0;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:1;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:2;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:3;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:4;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:5;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:6;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:7;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:8;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:9;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:10;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:11;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:12;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:13;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:14;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}}','0'),(3,2,3,1,2,1,1634136291,1634136291,2,1,'a:15:{i:0;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:1;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:2;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:3;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:4;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:5;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:6;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:7;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:8;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:9;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:10;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:11;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:12;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:13;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:14;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}}','0'),(4,2,1,3,2,1,1634136291,1634136291,2,1,'a:15:{i:0;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:1;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:2;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:3;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:4;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:5;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:6;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:7;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:8;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:9;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:10;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:11;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:12;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:13;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:14;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}}','0'),(5,2,3,1,2,2,1634136291,1634136291,1,2,'a:15:{i:0;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:1;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:2;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:3;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:4;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:5;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;s:1:\"2\";i:6;s:1:\"1\";i:7;s:1:\"1\";i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:6;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;s:1:\"2\";i:6;s:1:\"1\";i:7;i:0;i:8;s:1:\"1\";i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:7;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;s:1:\"2\";i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:8;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;s:1:\"2\";i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:9;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;s:1:\"2\";i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:10;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:11;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:12;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:13;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}i:14;a:15:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;}}','9_5');

#
# Structure for table "xt_wzulist"
#

DROP TABLE IF EXISTS `xt_wzulist`;
CREATE TABLE `xt_wzulist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `state` tinyint(3) NOT NULL DEFAULT '0' COMMENT '用户在列表中的状态，0表示退出/无效，1处于界面的列给中，没有进入任何桌子，2已进入某个桌子的状态',
  `tableid` int(11) NOT NULL DEFAULT '0' COMMENT '用户状态为2时，对应的桌子ID',
  `tableposid` int(11) NOT NULL DEFAULT '0' COMMENT '用户所在桌子里面的位置',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `lastuptime` int(11) NOT NULL DEFAULT '0' COMMENT '最后一次的更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='进入五子棋的用户列表';

#
# Data for table "xt_wzulist"
#

INSERT INTO `xt_wzulist` VALUES (1,1,1,0,0,1634136291,1634136291),(2,3,1,0,0,1634136291,1634136291),(3,9,1,0,0,1634136291,1634136291);
