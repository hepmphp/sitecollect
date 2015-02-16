##通用文章采集程序
**application/controllers/sitecollect.php【正则匹配】**

**application/controllers/sitecollectquery.php【dom操作】**

##博主或者发帖账号配置
**sitecollect.php对应的配置为xxx.ini** 

**sitecollectquery.php 对应的配置为qxxx.ini**

application/ini/`q`oschina.ini

	[苏培科]
	id = 5
	siteUrl = http://my.oschina.net/u/248080/blog 采集的站点
	sysTagId = 5000   分类id
	userId = 5000     用户id
	memberId = 5000   会员id
	nickname = 苏培科  昵称

##采集的站点配置
**sitecollect.php对应的配置为collection.php**

	$config['sina']['collectEncode'] = 'UTF-8';//采集的站点编码
	$config['sina']['pregList']	= '/<div id="column_2"[^>]*>(.*)<div id="column_3"/is';//列表页正则
	$config['sina']['pregPublishTime']	= '';
	$config['sina']['baseUrl']	= 'blog.sina.com';//符合规则的域名
	$config['sina']['linkRule']	= '/<a href="([^>]*?)" target="_blank">.*?<\/a>/is';//符合规则的链接规则
	
	$config['sina']['pregReduceContent']	= '/<div id="articlebody"[^>]*>(.*)<div[^>]*id=["|\']share["|\']/is';//缩减内容页
	$config['sina']['pregTitle']	= '/<h[12][^>]*>(.*)<\/h[12]>/';//标题正则
	$config['sina']['pregAuthor']	= '';//作者
	$config['sina']['pregTime']	= '/<span[^>]*class="time SG_txtc">[\(]?([^\)]*)[\)]?<\/span>/';时间正则
	$config['sina']['pregTags']	= '/<h3>(.*?)<\/h3>/';//标签正则
	$config['sina']['pregContent']	= '/<!-- 正文开始 -->(.*)<!-- 正文结束 -->/is';//内容正则
	$config['sina']['pregContentOne']	= '/<!-- 内容区 -->(.*)<!--\/内容区-->/is';//新的内容正则
	$config['sina']['pregImageRule'] = '/real_src\s*=\s*["\']?(.+?)("|\'| |>|\/>){1}/i';//图片规则
	$config['sina']['pregNextPage']	= '';
	$config['sina']['contentFind']	= '';//过滤的规则
	$config['sina']['contentReplace']	= '';
	$config['sina']['tailFind']	= '';
	$config['sina']['tailReplace']	= '';
	$config['sina']['header'] = array();//http头部
**sitecollectquery.php 对应的配置为qcollection.php**

application/config/qcollection.php

    $config['qoschina']['baseUrl']	= 'http://my.oschina.net/';//符合规则的域名暂时无用
    $config['qoschina']['linkRule']	= '.BlogTitle h2 >a';//列表页链接
    
    $config['qoschina']['queryTitle']	= '.BlogTitle h1';//标题
    $config['qoschina']['removeTitle'][0]['html']	=  'span';//标题要过滤的标签
    $config['qoschina']['removeTitle'][0]['class']	=  '.icon';//标题要标签带的样式
    
    $config['qoschina']['queryAuthor']	= '';//作者
    $config['qoschina']['queryTime']	= '.BlogStat';//博文发表时间
    $config['qoschina']['queryTags']	= '.time';//博文标签
    
    $config['qoschina']['queryContent']	= '.BlogContent';//内容
    $config['qoschina']['RemoveContent'] = array(
    
    );//内容过滤
    $config['qsina']['pregImageRule'] = 'src';

  
##ftp服务器配置及图片服务器域名设置
application/config/serverconf.php


	$config['img_base_url'] = 'http://img.cn-php.com/';//图片服务器域名
	$config['img_file_path'] = FCPATH.'attach/';//本地附件路径
	
	/*ftp服务器用户名密码*/
	$config['ftp']['hostname']='127.0.0.1';
	$config['ftp']['username']='123';
	$config['ftp']['password']='123';

采集列子
										
    php -f index.php sitecollectquery collect qoschina
	       入口       采集控制器        方法     参数1
    

    正式采集
	php -f index.php sitecollect collect sina 0 0 0 0
 	php -f index.php sitecollect collect sina 1 0 0 0 单页采集
    调试列表
    php -f index.php sitecollect collect sina 0 0 1 0
    调试文章
    php -f index.php sitecollect collect sina 0 0 1 0