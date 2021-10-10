# Host: 127.0.0.1:3356  (Version: 5.7.34)
# Date: 2021-10-10 18:20:32
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
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='五子棋桌子';

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
) ENGINE=InnoDB AUTO_INCREMENT=332 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

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
  `total_matches` int(11) NOT NULL DEFAULT '0' COMMENT '玩游戏的总场数',
  `win_matches` int(11) NOT NULL DEFAULT '0' COMMENT '赢得比赛的场次',
  `fail_matches` int(11) NOT NULL DEFAULT '0' COMMENT '失败场数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='进入五子棋的用户列表';

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
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

#
# Structure for table "xt_frireq"
#

DROP TABLE IF EXISTS `xt_frireq`;
CREATE TABLE `xt_frireq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_uid` int(11) NOT NULL DEFAULT '0' COMMENT '请求用户ID',
  `to_uid` int(11) NOT NULL DEFAULT '0' COMMENT '接受用户ID',
  `reqtp` tinyint(3) DEFAULT '1' COMMENT '1请求，2请求响应',
  `intro` varchar(255) DEFAULT NULL,
  `state` tinyint(3) DEFAULT '0' COMMENT '0请求中，1同意加好友，2拒绝加好友，3忽略',
  `is_agree` tinyint(3) DEFAULT '0' COMMENT '0无 意义，1同意，2拒绝加好友',
  `form_reqid` int(11) DEFAULT '0' COMMENT '来源ID',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `uptime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;

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
  `cmtn_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

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
  `intro` varchar(255) DEFAULT '',
  `notice_num` int(11) NOT NULL DEFAULT '0',
  `allow_sch` tinyint(3) DEFAULT '1' COMMENT '1允许，2不允许',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

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
) ENGINE=InnoDB AUTO_INCREMENT=325 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

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
  `con_tp` tinyint(1) NOT NULL COMMENT '内容类型，1为text,2图片',
  `content` text COMMENT '消息内容',
  `exttp` varchar(255) NOT NULL DEFAULT 'txt' COMMENT '扩展类型，主要是给附件用',
  `realaddr` varchar(255) NOT NULL DEFAULT '0' COMMENT '真实文件地址',
  `exptime` int(11) NOT NULL DEFAULT '0' COMMENT '有效时间，发送文件时的有效时间',
  `filesize` int(11) DEFAULT '0' COMMENT '附件大小',
  `msgstate` tinyint(3) DEFAULT '1' COMMENT '1有效，2撤回',
  `uptime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cmtn_id` (`cmtn_id`) USING BTREE,
  KEY `to_group_id` (`to_group_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COMMENT='五子棋里面的聊天，五子棋里面的都是广播';

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
  `lastch` varchar(255) DEFAULT NULL,
  `chesscon` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8;

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
