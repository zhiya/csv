<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//默认每次获取数据库的页面大小
define('DB_FETCH_PAGESIZE', 20);

//REST API默认输出格式
$config['default_format'] = 'json';

//微博开发APPKEY/APPSECRETKEY
define(WEIBO_APPKEY, '0');
define(WEIBO_APPSECRETKEY, '0');
define(WEIBO_CALLBACK_URL, 'http://mysite/test_weibo/callback');

