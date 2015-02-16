<?php
/**
 * Created by JetBrains PhpStorm.
 * User: fish
 * Date: 15-1-19
 * Time: 上午11:05
 * 
 */
 
//sina 站点配置// 新浪博客/微博、
//$config['sina']['logFile'] = 'sina';
 
$config['sina']['pregList']	= '/<div id="column_2"[^>]*>(.*)<div id="column_3"/is';
$config['sina']['pregPublishTime']	= '';
$config['sina']['baseUrl']	= 'blog.sina.com';//符合规则的域名
$config['sina']['linkRule']	= '/<a href="([^>]*?)" target="_blank">.*?<\/a>/is';//符合规则的链接规则

$config['sina']['collectEncode'] = 'utf8';
$config['sina']['pregReduceContent']	= '/<div id="articlebody"[^>]*>(.*)<div id=\'share\'/is';
$config['sina']['pregTitle']	= '/<h2[^>]*>(.*)<\/h2>/';
$config['sina']['pregAuthor']	= '';
$config['sina']['pregTime']	= '/<span class="time SG_txtc">\((.*)\)<\/span>/';
$config['sina']['pregTags']	= '/<h3>(.*?)<\/h3>/';
$config['sina']['pregContent']	= '/<!-- 正文开始 -->(.*)<!-- 正文结束 -->/is';
$config['sina']['pregImageRule'] = '/real_src\s*=\s*["\']?(.+?)("|\'| |>|\/>){1}/i';
$config['sina']['pregNextPage']	= '';
$config['sina']['contentFind']	= '';
$config['sina']['contentReplace']	= '';
$config['sina']['tailFind']	= '';
$config['sina']['tailReplace']	= '';
$config['sina']['header'] = array('Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                                 'Accept-Encoding:gzip, deflate, sdch',
                                 'Accept-Language:zh,en-US;q=0.8,en;q=0.6,zh-TW;q=0.4',
                                 'Cache-Control:max-age=0',
                                 'Connection:keep-alive',
                                 'Host:blog.sina.com.cn',
                                 'User-Agent:Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.65 Safari/537.36');
				

//和讯、

//搜狐、

//网易、


//华夏时报






