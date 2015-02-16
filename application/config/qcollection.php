<?php
/**
 * Created by JetBrains PhpStorm.
 * User: fish
 * Date: 15-1-19
 * Time: 上午11:05
 *
 */

//sina 站点配置  新浪博客/微博、 这种地址http://blog.sina.com.cn/caojianhai的规则
$config['qsina']['baseUrl']	= 'blog.sina.com';//符合规则的域名
$config['qsina']['linkRule']	= '#column_2 .blog_title >a';

$config['qsina']['queryTitle']	= '.articalTitle';
$config['qsina']['queryAuthor']	= '';
$config['qsina']['queryTime']	= '.time';
$config['qsina']['queryTags']	= '.blog_tag h3';
$config['qsina']['queryContent']	= '#sina_keyword_ad_area2';
$config['qsina']['pregImageRule'] = 'real_src';


$config['qoschina']['baseUrl']	= 'http://my.oschina.net/';//符合规则的域名
$config['qoschina']['linkRule']	= '.BlogTitle h2 >a';

$config['qoschina']['queryTitle']	= '.BlogTitle h1';
$config['qoschina']['removeTitle'][0]['html']	=  'span';
$config['qoschina']['removeTitle'][0]['class']	=  '.icon';

$config['qoschina']['queryAuthor']	= '';
$config['qoschina']['queryTime']	= '.BlogStat';
$config['qoschina']['queryTags']	= '.time';

$config['qoschina']['queryContent']	= '.BlogContent';
$config['qoschina']['RemoveContent'] = array(

);

$config['qsina']['pregImageRule'] = 'src';

