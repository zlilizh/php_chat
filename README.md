# php_chat
基于swoole的聊天系统 包括聊天,五子棋,斗地主功能

### 聊天系统包括的功能:
1. 实时发送消息,消息的内容类型:纯文本消息,表情消息,图片消息(独发方式),发送附件(独发方式),发送粘帖板图片
2. 发送消息类型:私发消息,群发消息,撤回消息
3. 群组功能:创建群组,拉好友进群组,群组创建者删除群组成员
4. 好友功能:查找好友,实时发送好友请求,实时处理好友请求,实时返回处理结果
5. 个人信息:个人信息修改,头像上传,密码修改
6. 连接断开系统不会自动连接，需要手动连接(刷新页面即可重新连接)

### 五子棋的功能点：
1. 棋局列表,棋局人员实时状态
2. 棋局页实时状态
3. 下棋部分,目前只能下棋,直至分出胜负,不支持悔棋,不支持求和
4. 每次下棋后会创建定时器,超时未下系统会自动判断对方赢得此场比赛
5. 连接断开系统不会自动连接，需要手动连接(刷新页面即可重新连接)

### 斗地主功能点:
1. 牌局列表,棋局人员实时状态
2. 牌局页实时状态
3. 坐入桌子指定位置
4. 实时同步发牌,抢地主,加倍,打牌,打牌提醒,以及最终打牌结果
5. 倒计时显示(倒计时暂时只是显示,没有处理后台逻辑)  
6. 打牌结果显示,金币结算(金币结算结果还未在前台展示,有没金币暂时都可以打牌)

### 系统开发环境（都是基于docker布署）:
php7.2  
swoole 4.7(swoole环境的PHP单独布署)  
nginx latest  
mysql 5.7  
redis latest  

### 安装步骤
有时间了再补下系统的界面，安装步骤，初始化数据
