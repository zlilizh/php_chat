# PHP 聊天 五子棋 斗地主
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
###  源代码地址：
 __https://github.com/zlilizh/php_chat__
### 开发环境（基于docker布署）:
php7.2  （docker pull zlilizh/phpfpm7.2 ）  
swoole 4.7(swoole环境的PHP单独布署)  （docker pull zlilizh/swlphp7.2）  
nginx latest  （docker pull nginx ）  
mysql 5.7  （docker pull mysql:5.7.34）  
redis latest  （docker pull redis）  
### 各功能的系统界面 
* 聊天功能界面
  ![在线聊天](https://img-blog.csdnimg.cn/97ef181782cd4593ab8916125bde66d6.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
* 五子棋界面  
  ![五子棋](https://img-blog.csdnimg.cn/5646b2062fc549c480a501ab512fd6d7.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)  
* 斗地主界面
  ![在线斗地主](https://img-blog.csdnimg.cn/8209fab3910d4d849f49dadb2aaba97f.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)


### 下面是以在win10系统的docker中布署此项目的例子
##### 1,拉取镜像
docker pull zlilizh/swlphp7.2  
docker pull zlilizh/phpfpm7.2  
docker pull nginx  
docker pull mysql:5.7.34  
docker pull redis

##### 2,创建项目目录与文件+拉取代码:
D:\dkml\phpfpm72_conf *#PHP7.2配置目录*  
D:\dkml\mysql5734_data *#mysql5.7数据目录*  
D:\dkml\xm\ *#代码存放目录 **把代码拉取到此目录，项目地址 https://github.com/zlilizh/php_chat.git** 拉完后的目录就是D:\dkml\xm\php_chat (chat_cli与chat_ser都放在此目录下)*  
D:\dkml\nginx *#nginx相关配置目录  在nginx目录下面创建 nginx.conf 默认配置如下：*
```bash
user  nginx;
worker_processes  auto;
error_log  /var/log/nginx/error.log notice;
pid        /var/run/nginx.pid;

events {
    worker_connections  1024;
}

http {
    include       /etc/nginx/mime.types;
    default_type  application/octet-stream;
    log_format  main  '$remote_addr - $remote_user [$time_local] "$request" '
                      '$status $body_bytes_sent "$http_referer" '
                      '"$http_user_agent" "$http_x_forwarded_for"';
    access_log  /var/log/nginx/access.log  main;
    sendfile        on;
    keepalive_timeout  65;
    include /etc/nginx/conf.d/*.conf;
}

```
##### 3,启动容器&安装依赖:
*  启动nginx:
```bash
docker run -d --name mynginx -p 80:80 -v d/dkml/nginx/nginx.conf:/etc/nginx/nginx.conf -v d/dkml/nginx/logs:/var/log/nginx -v d/dkml/xm:/usr/share/nginx/html -v d/dkml/nginx/conf/:/etc/nginx/conf.d --privileged=true nginx
```

* 启动mysql:
```bash
docker run -p 3306:3306 --name mysql5734 -v d/dkml/mysql5734_data/:/var/lib/mysql/ -e MYSQL_ROOT_PASSWORD=123456 -d mysql:5.7.34
```

* 启动swooles:
```bash
docker run -it --name myswoole -p 9501:9501 -v d/dkml/xm:/usr/share/swl zlilizh/swlphp7.2 sh
```

* 启动php7.2:
```bash
docker run -d --name myphp72fpm --restart always --privileged=true -p 9000:9000 -v d/dkml/xm:/usr/share/nginx/html -v d/dkml/phpfpm72_conf:/usr/local/etc/php/conf.d zlilizh/phpfpm7.2
```
* 启动redis
```bash
docker run -d --name myredis -p 6379:6379 redis:latest
```
* php7.2的容器都需要额外配置下pdo_mysql gd redis(swoole镜像已经安装了)这三个扩展,在在D:\dkml\phpfpm72_conf\ 目录下面创建 docker-php-ext.ini文件，添加如下配置:
    
  ```bash
  extension=pdo_mysql.so
  extension=gd.so
  extension=redis.so
  date.timezone=Asia/Shanghai
  ```

##### 4，确定域名,配置nginx虚拟主机
* 确定域名
  当前配置以 www.csct.com 为例，修改本地host 配置域名
  ```bash
  127.0.0.1 www.csct.com
  ```
* 配置nginx虚拟主机
  在D:\dkml\nginx\conf\ 目录下面创建 default.conf文件，添加如下配置
  ```bash
  server {
      listen       80;
      server_name  www.csct.com;
      root   /usr/share/nginx/html/php_chat/chat_cli;
  
      location / {
          index  index.php index.html index.htm;
          autoindex  off;
      }
  
      error_page   500 502 503 504  /50x.html;
      location = /50x.html {
          root   /usr/share/nginx/html;
      }
     location ~ ^/assets/.*\.php$ {
          deny all;
      }
  
      location ~ \.php(.*)$ {
          root           html;
          fastcgi_pass   主机IP:9000; #把主机IP换成自己的IP
          fastcgi_index  index.php;
          fastcgi_split_path_info  ^((?U).+\.php)(/?.+)$;
          fastcgi_param  SCRIPT_FILENAME  /usr/share/nginx/html/php_chat/chat_cli$fastcgi_script_name;
          fastcgi_param  PATH_INFO  $fastcgi_path_info;
          fastcgi_param  PATH_TRANSLATED  /usr/share/nginx/html/php_chat/chat_cli$fastcgi_path_info;
          include        fastcgi_params;
      }
  
     location ~* /\.ht {
          deny all;
      }
  }
  ```
##### 5,配置代码
* 修改chat_cli的配置文件:  
  重命名 __chat_cli/config/__ 目录下 __db_config_bast.php__ 为 __db_config.php__
  并修改对应的配置  
  **WEB_URL** 本地访问URL，与nginx里面的配置要一致  
  **$db_config** #DB相关配置  
  __$web_config['ws_addr']__ #是websockt地址 __主机IP:9501__  
  __$r_cof__ #Redis的相关配置
* 修改chat_ser的配置文件:  
  重命名 __chat_ser/config/__ 目录下 __main_bast.php__ 为 __main.php__
  修改 $db_config g与 $r_cof即可

##### 6,导入数据库
数据库名：**chat**,导入 __D:\dkml\xm\php_chat\chat.sql__ 文件
到此项目都已经配置完成

### 启动项目

进入swoole容器并启动监听
```bash
cd /usr/share/swl/php_chat/chat_ser  
php index.php 
```
浏览器里面输入
www.csct.com

默认账号
admin, cs,mingr,kakax,yzbb,yzby,zhuj等，其它账号可以表 __xt_member__ 中查看，密码统一是123456

###  系统部分界面
![在这里插入图片描述](https://img-blog.csdnimg.cn/216df5119aa84ad08394e045d30d2260.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/55443fa4e1b04fc0a8af3321d6b0c3d0.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/bddd5370b5be498eab18b4b4da6f0dce.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/e3f5a49abf5445689a65d475d61fad51.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/d4a614d128664b6d909113334087f5b7.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/f5662fec6ebf4162bd565cdb9c8748b2.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/e00c4a1fdc77479584b55953d849bc28.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/e25d7a84d09e4af79dddec825bb71c04.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/97ef181782cd4593ab8916125bde66d6.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/5cb141380509461296c22f6c022fab6d.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/a63af82785f641ce971822f07bc8057b.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/1c045210a3254cfe91a0e0adcb8b2ed8.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/37f432b3f9354a7cae2a1f8ec51517ca.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/854f810940594e6e979665ffe8132ea7.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)

![在这里插入图片描述](https://img-blog.csdnimg.cn/7f6a83d4f94f439f8edf331fdb5ade35.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/f756336c43ba498a844544f7c5f4d261.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/3182326607a544ecbf4444f4bda655a8.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/21e9e1971175414d8de3f6112fc302cc.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/5646b2062fc549c480a501ab512fd6d7.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/c177c2b614fa4c789274eae0f304a0fc.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/0066ab2de0374f1d97e1d5a53b7da1ce.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/2d420863f58244d9b2b171eea16102f6.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/0b84c0475cf04178a3bfb4c2c94d1afe.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/9856b3da6b0347ebbb4869b48ca93a97.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/ae39fffe496a45e2ac6650bb35e1e571.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/642d448e7ba645a1aeea66ca3253c06b.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/786ceedf9695423192191cd760188dab.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/d69b68c6aeb04dd2afb612a547f13a47.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/c56689e78bf44121b7d354de4a98a869.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/b997f59bebb04159aadb7ea92bb959bf.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/8209fab3910d4d849f49dadb2aaba97f.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/dd518e425dc24274adda2c39da079c5a.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/16b65141bcdc49548775f72c6cb71ff7.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/39dd88179716404b973310bb2f93a35f.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
![在这里插入图片描述](https://img-blog.csdnimg.cn/1d4f1c794d274b75a8b461014b003cbf.png?x-oss-process=image/watermark,type_ZHJvaWRzYW5zZmFsbGJhY2s,shadow_50,text_Q1NETiBAemxpemg=,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)
