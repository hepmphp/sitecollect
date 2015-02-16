<?php
/**
 * Created by JetBrains PhpStorm.
 * User: fish
 * Date: 15-1-19
 * Time: 上午11:05
 *
 */

//sina 站点配置  新浪博客/微博、 这种地址http://blog.sina.com.cn/caojianhai的规则
//$config['sina']['logFile'] = 'sina';
$config['sina']['collectEncode'] = 'UTF-8';
$config['sina']['pregList']	= '/<div id="column_2"[^>]*>(.*)<div id="column_3"/is';
$config['sina']['pregPublishTime']	= '';
$config['sina']['baseUrl']	= 'blog.sina.com';//符合规则的域名
$config['sina']['linkRule']	= '/<a href="([^>]*?)" target="_blank">.*?<\/a>/is';//符合规则的链接规则

$config['sina']['pregReduceContent']	= '/<div id="articlebody"[^>]*>(.*)<div[^>]*id=["|\']share["|\']/is';
$config['sina']['pregTitle']	= '/<h[12][^>]*>(.*)<\/h[12]>/';
$config['sina']['pregAuthor']	= '';
$config['sina']['pregTime']	= '/<span[^>]*class="time SG_txtc">[\(]?([^\)]*)[\)]?<\/span>/';
$config['sina']['pregTags']	= '/<h3>(.*?)<\/h3>/';
$config['sina']['pregContent']	= '/<!-- 正文开始 -->(.*)<!-- 正文结束 -->/is';
$config['sina']['pregContentOne']	= '/<!-- 内容区 -->(.*)<!--\/内容区-->/is';
$config['sina']['pregImageRule'] = '/real_src\s*=\s*["\']?(.+?)("|\'| |>|\/>){1}/i';
$config['sina']['pregNextPage']	= '';
$config['sina']['contentFind']	= '';
$config['sina']['contentReplace']	= '';
$config['sina']['tailFind']	= '';
$config['sina']['tailReplace']	= '';
$config['sina']['header'] = array();

//sina 站点配置// 新浪博客/微博  这种地址http://blog.sina.com.cn/u/1884026747的规则 大部分规则同上 列表链接规则做修改
$config['sinau']['collectEncode'] = 'UTF-8';
$config['sinau']['pregList']	= '/<div id="column_2"[^>]*>(.*)<div id="column_3"/is';
$config['sinau']['pregPublishTime']	= '';
$config['sinau']['baseUrl']	= 'blog.sina.com';//符合规则的域名
$config['sinau']['linkRule']	= '/<a href="([^>]*?)" target="_blank">.*?<\/a>/is';//符合规则的链接规则

$config['sinau']['pregReduceContent']	= '/<div id="articlebody"[^>]*>(.*)<div[^>]*id=["|\']share["|\']/is';
$config['sinau']['pregTitle']	= '/<h[12][^>]*>(.*)<\/h[12]>/';
$config['sinau']['pregAuthor']	= '';
$config['sinau']['pregTime']	= '/<span[^>]*class="time SG_txtc">[\(]?([^\)]*)[\)]?<\/span>/';
$config['sinau']['pregTags']	= '/<h3>(.*?)<\/h3>/';
$config['sinau']['pregContent']	= '/<!-- 正文开始 -->(.*)<!-- 正文结束 -->/is';
$config['sinau']['pregContentOne']	= '/<!-- 内容区 -->(.*)<!--\/内容区-->/is';
$config['sinau']['pregImageRule'] = '/real_src\s*=\s*["\']?(.+?)("|\'| |>|\/>){1}/i';
$config['sinau']['pregNextPage']	= '';
$config['sinau']['contentFind']	= '';
$config['sinau']['contentReplace']	= '';
$config['sinau']['tailFind']	= '';
$config['sinau']['tailReplace']	= '';
$config['sinau']['header'] = array();


//sina 站点配置// 新浪博客/微博  这种地址http://blog.sina.com.cn/s/articlelist_1504965870_0_1.html/的规则 大部分规则同上 列表链接规则做修改
$config['sinas']['collectEncode'] = 'UTF-8';//目标页页面编码
$config['sinas']['pregList']	= '/<div id="column_2"[^>]*>(.*)<div id="column_3"/is';//列表页缩减正则
$config['sinas']['pregPublishTime']	= '';//文章发布时间
$config['sinas']['baseUrl']	= 'blog.sina.com';//符合规则的域名
$config['sinas']['linkRule']	= '/<a title="[^>]*" target="_blank" href="([^>]*?)">.*?<\/a>/is';//符合规则的链接规则

$config['sinas']['pregReduceContent']	= '/<div id="articlebody"[^>]*>(.*)<div[^>]*id=["|\']share["|\']/is';//缩减内容正则
$config['sinas']['pregTitle']	= '/<h[12][^>]*>(.*)<\/h[12]>/';
$config['sinas']['pregAuthor']	= '';
$config['sinas']['pregTime']	= '/<span[^>]*class="time SG_txtc">[\(]?([^\)]*)[\)]?<\/span>/';
$config['sinas']['pregTags']	= '/<h3>(.*?)<\/h3>/';
$config['sinas']['pregContent']	= '/<!-- 正文开始 -->(.*)<!-- 正文结束 -->/is';
$config['sinas']['pregContentOne']	= '/<!-- 内容区 -->(.*)<!--\/内容区-->/is';
$config['sinas']['pregImageRule'] = '/real_src\s*=\s*["\']?(.+?)("|\'| |>|\/>){1}/i';
$config['sinas']['pregNextPage']	= '';
$config['sinas']['contentFind']	= '';
$config['sinas']['contentReplace']	= '';
$config['sinas']['tailFind']	= '';
$config['sinas']['tailReplace']	= '';
$config['sinas']['header'] = array();

//和讯、 http://hhhhhhhhhh.blog.hexun.com/
$config['hexun']['collectEncode'] = 'GBK';//目标页页面编码
$config['hexun']['pregList']	= '/<!--  文章列表(.*)<div class=\'PageSkip\'>/is';//列表页缩减正则
$config['hexun']['pregPublishTime']	= '';//文章发布时间
$config['hexun']['baseUrl']	= 'blog.hexun.com';//符合规则的域名
$config['hexun']['linkRule']	= '/<a href=\'(http:\/\/(\w)*\.blog\.hexun\.com\/\d+_d\.html)\'>(.*?)<\/a>/is';//符合规则的链接规则

$config['hexun']['pregReduceContent']	= '/id="ArticeTextID">(.*?)<!--  文章内容:结束  -->/is';//缩减内容正则
$config['hexun']['pregTitle']	= '/<span class="ArticleTitleText">(.*?)<\/span>/is';//
$config['hexun']['pregAuthor']	= '';
$config['hexun']['pregTime']	= '/ \d{4}-\d{1,2}-\d{1,2} \d{1,2}:\d{1,2}:\d{1,2}/';
$config['hexun']['pregTags']	= '/<A href=\'http:\/\/blog\.hexun\.com\/group\/commontag\.aspx\?searchTag=[^>]*\' target=\'_blank\'>(.*?)<\/A>/is';
$config['hexun']['pregContent']	= '/<div id="BlogArticleDetail"[^>]*>(.*?)<\/div>/is';
$config['hexun']['pregImageRule'] = '';
$config['hexun']['pregNextPage']	= '';
$config['hexun']['contentFind']	= '';
$config['hexun']['contentReplace']	= '';
$config['hexun']['tailFind']	= '';
$config['hexun']['tailReplace']	= '';
$config['hexun']['header'] = array('User-Agent:Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.65 Safari/537.36',);


//搜狐、
$config['sohu']['collectEncode'] = 'GBK';//目标页页面编码
$config['sohu']['pregList']	= '/(.*)/is';//列表页缩减正则
$config['sohu']['pregPublishTime']	= '';//文章发布时间
$config['sohu']['baseUrl']	= 'blog.sohu.com';//符合规则的域名
$config['sohu']['linkRule']	= '/<h3>.*<a href="([^>]*?)" target="_blank">.*?<\/a>/';//符合规则的链接规则

$config['sohu']['pregReduceContent']	= '/<div class="revoArtRight">(.*)<div id="entrycommentlist">/is';//缩减内容正则
$config['sohu']['pregTitle']	= '/<h2>(.*?)<\/h2>/is';//
$config['sohu']['pregAuthor']	= '';
$config['sohu']['pregTime']	= '/<span class="date">(.*?)<\/span>/';
$config['sohu']['pregTags']	= '';
$config['sohu']['pregContent']	= '/<div class="item-content" id="main-content">(.*?)<div class="newBlog-bom"/is';
$config['sohu']['pregImageRule'] = '';
$config['sohu']['pregNextPage']	= '';
$config['sohu']['contentFind']	= '';
$config['sohu']['contentReplace']	= '';
$config['sohu']['tailFind']	= '';
$config['sohu']['tailReplace']	= '';
$config['sohu']['header'] = array();
//网易、
$config['163blog']['collectEncode'] = 'GBK';//目标页页面编码
$config['163blog']['pregList']	= '/id="1">(.*)id="15">/is';//列表页缩减正则
$config['163blog']['pregPublishTime']	= '';//文章发布时间
$config['163blog']['baseUrl']	= 'blog.163.com';//符合规则的域名
$config['163blog']['linkRule']	= '/<a href="(http:\/\/blog\.163\.com\/[^>]*\/blog\/static\/\d+\/)"[^>]*>.*?<\/a>/';//符合规则的链接规则

$config['163blog']['pregReduceContent']	= '/<div class="left">(.*)<div class="editopbar"/is';//缩减内容正则
$config['163blog']['pregTitle']	= '/<h3[^>]*>(.*?)<\/h3>/is';//
$config['163blog']['pregAuthor']	= '';
$config['163blog']['pregTime']	= '/<span class="blogsep">\d{4}-\d{2}-\d{2} \d{1,2}:\d{1,2}:\d{1,2}<\/span>/';
$config['163blog']['pregTags']	= '';
$config['163blog']['pregContent']	= '/<div class="nbw-blog-start">(.*)<div class="nbw-blog-end">/is';
$config['163blog']['pregImageRule'] = '';
$config['163blog']['pregNextPage']	= '';
$config['163blog']['contentFind']	= '';
$config['163blog']['contentReplace']	= '';
$config['163blog']['tailFind']	= '';
$config['163blog']['tailReplace']	= '';
$config['163blog']['header'] = array();

//华夏时报
$config['chinatimes']['collectEncode'] = 'GBK';//目标页页面编码
$config['chinatimes']['pregList']	= '/<!--内容部分开始second-main  -->(.*)<!--内容部分结束second-main  -->/is';//列表页缩减正则
$config['chinatimes']['pregPublishTime']	= '';//文章发布时间
$config['chinatimes']['baseUrl']	= 'www.chinatimes.cc';//符合规则的域名
$config['chinatimes']['linkRule']	= '/<p class="f20 l26"><a href="([^>]*?)" target="_blank">.*?<\/a><\/p>/';//符合规则的链接规则

$config['chinatimes']['pregReduceContent']	= '/<div class="content_main">(.*)<!--内容右侧开始-->/is';//缩减内容正则
$config['chinatimes']['pregTitle']	= '/<h1>(.*)<\/h1>/';//
$config['chinatimes']['pregAuthor']	= '';
$config['chinatimes']['pregTime']	= '/<p id="pubtime_baidu">发布时间：(.*)<\/p>/';
$config['chinatimes']['pregTags']	= '';
$config['chinatimes']['pregContent']	= '/<div class="c_content">(.*)<div class="share">/is';
$config['chinatimes']['pregImageRule'] = '';
$config['chinatimes']['pregNextPage']	= '';
$config['chinatimes']['contentFind']	= array('/<p>查看更多华夏时报文章(.*)<\/p>/',);
$config['chinatimes']['contentReplace']	= array();
$config['chinatimes']['tailFind']	= '';
$config['chinatimes']['tailReplace']	= '';
$config['chinatimes']['header'] = array();


//finance.sina.com.cn/stock/cpbd/
$config['sinaSinglePage']['collectEncode'] = 'gb2312';//目标页页面编码
$config['sinaSinglePage']['pregList']	= '';//列表页缩减正则
$config['sinaSinglePage']['pregPublishTime']	= '';//文章发布时间
$config['sinaSinglePage']['baseUrl']	= '';//符合规则的域名
$config['sinaSinglePage']['linkRule']	= '';//符合规则的链接规则

$config['sinaSinglePage']['pregReduceContent']	= '/<div class="main">(.*)<div class="box_sc">/isU';//缩减内容正则
$config['sinaSinglePage']['pregTitle']	= '/<span class="title">(.*?)<\/span>/';//
$config['sinaSinglePage']['pregAuthor']	= '';
$config['sinaSinglePage']['pregTime']	= '//';
$config['sinaSinglePage']['pregTags']	= '';
$config['sinaSinglePage']['pregContent']	= '/<div class="content hg_content" data-sudaclick="stock_cpbd_left">(.*?)<div class="sideBar">/is';
$config['sinaSinglePage']['pregImageRule'] = '';
$config['sinaSinglePage']['pregNextPage']	= '';
$config['sinaSinglePage']['contentFind']	= array('/<style>(.*?)<\/style>/is');
$config['sinaSinglePage']['contentReplace']	= array();
$config['sinaSinglePage']['tailFind']	= '';
$config['sinaSinglePage']['tailReplace']	= '';
$config['sinaSinglePage']['header'] = array();






