<?php
/**
 * Created by JetBrains PhpStorm.
 * User: fish
 * Date: 15-1-19
 * Time: 上午10:31
 *
 */
class Sitecollect extends CI_Controller
{
    /**
     * @var 采集站点名称
     */
    public $siteName;
    /**
     * @var 采集站点url
     */
    public $siteUrl;
    /**
     * @var 网站域名地址
     */
    public $baseUrl;
    /**
     * @var 采集编码
     */
    public $collectEncode;
    /**
     * @var string 文章分类ID
     */
    public $sysTagId = '';
    /**
     * @var string 博客会员ID
     */
    public $memberId = '';
    /**
     * @var string  博客用户Id
     */
    public $userId = '';

    /**
     * @var 采集的站点配置
     */
    public $siteConfig;
    /**
     * @var string 保留的标签
     */
    public $saveTag = '<div><p><img><style><table><tbody><thead><th><tr><td><wbr>';
    /**
     * @var string 默认文件跟数据库编码
     */
    public $fileEncode = 'UTF-8';

    /**
     * @var string 防止重复采集的日志文件
     */
    public $logFile = '';


    /**
     * @var string
     */
    public $logErrorFile = '';
    /**
     * 博主的配置文件
     * @var string
     */
    public $iniFilePath = '';
    /**
     * 配置文件后缀
     * @var string
     */
    public $iniFileExt = '.ini';

    public function __construct()
    {
      
        parent::__construct();
        $this->load->helper('common');
        $this->load->config('collection');
        $this->iniFilePath = APPPATH . '/ini/';
		$this->logErrorFile = APPPATH . 'logs/logErrorFile.log';
		if(!is_dir($this->iniFilePath))
		{
			mkdir($this->iniFilePath,0755,true);
		}
		error_reporting(E_ALL ^ E_NOTICE);
    }

    /**
     *
     */
    public function index()
    {
        echo __FILE__;
    }


    /**采集入口
     * @param $siteName  站点名称
     * @param int $singlePage 是否启用单页
     * @param int $debugTest 是否启用测试配置
     * @param int $debugList 是否调试列表页
     * @param int $debugCotent 是否调试文章页
     */
    public function collect($siteName,$singlePage=0, $debugTest = 0, $debugList = 0, $debugCotent = 0)
    {
        $this->benchmark->mark('code_start');

        $this->siteName = $siteName;

        if (empty($this->siteName)) {
            exit('stieurl no in collect sites');
        }
        if ($debugTest) {
            $blogerConfig = $this->parseIniFile($siteName . '.test');
        } else {
            $blogerConfig = $this->parseIniFile($siteName);
        }
         echo $this->siteName; 
        //包含采集的配置
        $this->siteConfig =   config_item($this->siteName);
		
        foreach ($blogerConfig as $bloger) {
            $this->siteUrl = $bloger['siteUrl'];
            $this->sysTagId = $bloger['sysTagId'];
            $this->userId = $bloger['userId'];
            $this->memberId = $bloger['memberId'];
            $this->Recommend = $bloger['Recommend'];

            if($singlePage){
                $linkUrlsArr[] = $this->siteUrl;
            }else{

                /*采集日志 防止重复采集*/
                // $md5SiteUrl = substr(md5($this->siteUrl), 0, 6);
                $this->logFile = APPPATH . '/logs/' . $this->siteName . '_' . $this->userId . '_' . date('Ym') . '.log';
                if (file_exists(APPPATH . '/logs/' . $this->siteName . '_' . $this->userId . '_' . (date('Ym') - 1) . '.log')) { //移除上一月的日志
                    unlink(APPPATH . '/logs/' . $this->siteName . '_' . $this->userId . '_' . (date('Ym') - 1) . '.log');
                }

                /*对搜狐站点列表页的特殊处理*/
                if ($this->siteName == 'sohu') {
                    $this->parseSohuListUrl($this->siteUrl);
                }

                /*是否启用gzip压缩页面*/
                if (isset($this->siteConfig['gzip']) AND $this->siteConfig['gzip']) {
                    $listContent = curl_http_post($this->siteUrl, '', array('ssl' => 0, 'post' => 0, 'format' => 'html', 'followAction' => 1, 'gzip' => 1), $this->siteConfig['header']);
                } else {
                    $listContent = curl_http_post($this->siteUrl, '', array('ssl' => 0, 'post' => 0, 'format' => 'html', 'followAction' => 1), $this->siteConfig['header']);
                }


                /*内容转码*/
                if (strtoupper($this->siteConfig['collectEncode']) != $this->fileEncode) {
                    $listContent = iconv($this->siteConfig['collectEncode'], $this->fileEncode . '//IGNORE', $listContent);
                }


                preg_match($this->siteConfig['pregList'], $listContent, $matchList);
//            preg_match($this->siteConfig['pregPublishTime'],$listContent,$matchList);
//            preg_match($this->siteConfig['linkRule'],$listContent,$matchList);

                $collectArr = $this->_striplinks($matchList[1], $this->siteConfig['baseUrl'], $this->siteConfig['linkRule']);

                /* 提取日志里的数组 */
                $logArr = array();
                if (file_exists($this->logFile)) {
                    $logArr = explode(PHP_EOL, file_get_contents($this->logFile));
                }
                $linkUrlsArr = array_diff($collectArr, $logArr);
            }

            if ($debugList) {
                echo "<pre>";
                echo "siteName:{$this->siteName}\n";
                echo "siteUrl:{$this->siteUrl}\n";
                echo "----------listContent-----------\n";
                print_r($listContent);
                echo "----------matchList-------------\n";
                print_r($matchList[1]);
                echo "----------siteConfig------------\n";
                print_r($this->siteConfig);
                echo "---------linkUrlsArr-------------\n";
                print_r($linkUrlsArr);
                echo "---------debug list end---------------\n";
                exit();
            }


            if (!empty($linkUrlsArr)) {

                foreach ($linkUrlsArr as $linkUrl) {
                    if (isset($this->siteConfig['gzip']) AND $this->siteConfig['gzip']) {
                        $articleContent = curl_http_post($linkUrl, '', array('ssl' => 0, 'post' => 0, 'format' => 'html', 'followAction' => 1, 'gzip' => 1), $this->siteConfig['header']);
                    } else {
                        $articleContent = curl_http_post($linkUrl, '', array('ssl' => 0, 'post' => 0, 'format' => 'html', 'followAction' => 1), $this->siteConfig['header']);
                    }

                    /*内容转码*/
                    if (strtoupper($this->siteConfig['collectEncode']) != $this->fileEncode) {
                        $articleContent = iconv($this->siteConfig['collectEncode'], $this->fileEncode . '//IGNORE', $articleContent);
                    }


                    preg_match($this->siteConfig['pregReduceContent'], $articleContent, $matcharticleContent);

                    $articleContent = $matcharticleContent[1];

                    if (!empty($articleContent)) {
						/*
						if (isset($this->siteConfig['pregTime']) AND !empty($this->siteConfig['pregTime'])) {
                            preg_match($this->siteConfig['pregTime'], $articleContent, $matchTime);
                            if(empty($matchTime[1])){
                                $logMsg = date('Y-m-d H:i:s')."\t{$linkUrl}\t{$this->siteUrl}\t time";
                                error_log($logMsg.PHP_EOL,3,$this->logErrorFile);
                                continue;//时间没匹配到退出
                            } 
                            $articleTime = strtotime($matchTime[1]);
                            if((time()-$articleTime)>86400){
								$logMsg = date('Y-m-d H:i:s')."时间超过一天退出{$matchTime[1]}\t{$linkUrl}\t{$this->siteUrl}\t time";
                                error_log($logMsg.PHP_EOL,3,$this->logErrorFile);
                                continue;//超过一天的退出
                            }
                        }
						*/
						
					
						
                        preg_match($this->siteConfig['pregTitle'], $articleContent, $matchTitle);
                        $title = $matchTitle[1];

                        //过滤直播
                        if(strrpos($title,'同步')!= false OR strrpos($title,'实时')!= false OR strrpos($title,'直播')!= false )
                        {
                            continue;
                        }


                        // preg_match($this->siteConfig['pregAuthor'],$articleContent,$matchAuthor);
                        /*标签处理*/
                        if (isset($this->siteConfig['pregTags']) AND !empty($this->siteConfig['pregTags'])) {
                            preg_match_all($this->siteConfig['pregTags'], $articleContent, $matchTags);
                            if (!empty($matchTags[1])) {
                                $tags = implode(',', $matchTags[1]);
                                //$tagids = $this->tagHandle($this->userId, strip_tags($tags)); //标签处理
                            }
                        }
                        preg_match($this->siteConfig['pregContent'], $articleContent, $matchContent);
   
                        if(empty($matchContent[1]) AND isset($this->siteConfig['pregContentOne']) AND !empty($this->siteConfig['pregContentOne']))
                        {
                            preg_match($this->siteConfig['pregContentOne'], $articleContent, $matchContent);//多套模板规则识别
                        }



//                    preg_match($this->siteConfig['pregNextPage'], $articleContent, $matchNextPage);

                        $content = $matchContent[1];
//                    $nextpage = $matchNextPage[1];
                        /*图片本地化*/
                        if (isset($this->siteConfig['pregImageRule']) AND !empty($this->siteConfig['pregImageRule'])) {
                            $content = $this->imglLocalization($this->userId, $content, $this->siteConfig['pregImageRule']);
                        } else {
                            $content = $this->imglLocalization($this->userId, $content);
                        }
                        /*格式化处理*/
                        if (isset($this->siteConfig['contentFind']) AND !empty($this->siteConfig['contentFind'])) {
                            $content = preg_replace($this->siteConfig['contentFind'], $this->siteConfig['contentReplace'], $content);
                        }
                        if (isset($this->siteConfig['tailFind']) AND !empty($this->siteConfig['tailFind'])) {
                            $content = preg_replace($this->siteConfig['tailFind'], $this->siteConfig['tailFind'], $content);
                        }
					    /*****沙黾农 保留原有html****/
						if(in_array($this->userId,array(1311672))){
                                //
						}else{
                            $content = strip_tags($content, $this->saveTag);
                            /**************************程序自动对文章进行排版****************************/
                            /* P标签换成BR */
                            $content = preg_replace('/<\/?p[^>]*>/i','<br>',$content);
                            /* 换成2个BR */
                            $content = preg_replace('/<br[\s\/br><&nbsp;]*>(\s*|&nbsp;)*/i','<br><br>&nbsp;&nbsp;&nbsp;&nbsp;',$content);
                            /**************************排版结束**************************************/
                        }
						


                        $arr = array(
                            'Title' => trim(strip_tags($title)),
                            'TagIDs' => $tagids,
                            'SysTagID' => $this->sysTagId,
                            'Recommend'=> $this->Recommend ,
                            'Content' =>$content,
                            // 'Summary' =>  ,
                        );

                        if ($debugCotent) {
                            echo "<pre>";
                            echo "siteUrl:{$linkUrl}\n";
                            echo "----------articleContent-----------\n";
                            print_r($articleContent);
                            echo "----------matchTitle---------------\n";
                            print_r($matchTitle[1]);
                            echo "----------matchTime---------------\n";
                            print_r($matchTime[1]);
                            echo "----------matchTags----------------\n";
                            print_r($matchTags[1]);
                            echo "---------matchContent--------------\n";
                            print_r($content);
                            echo "---------dbArr---------------------\n";
                            echo "<pre>";
                            print_r($arr);
                            echo "---------debug content end---------------\n";
                            exit();
                        }

                        if ($this->memberId  AND !empty($arr['Title']) AND !empty($arr['SysTagID']) AND !empty($arr['Content'])) {
                            //写入gw
                            $this->addToGw($this->memberId, $arr);
						    /*采集的url记录日志 以防止重复采集*/
							error_log($linkUrl . PHP_EOL, 3, $this->logFile);
                        }
                    }else{
                        $logMsg = date('Y-m-d H:i:s')."\t{$linkUrl}\t{$this->siteUrl}\t content";
                        error_log($logMsg.PHP_EOL,3,$this->logErrorFile);
                        echo "empty content";
                    }


                }

            } else {
               // $logMsg = date('Y-m-d H:i:s')."\t{$this->siteUrl}\t  list...";
               // error_log($logMsg.PHP_EOL,3,$this->logErrorFile);
                echo "null list...";
            }
            $this->benchmark->mark('code_end');
            echo $this->benchmark->elapsed_time('code_start', 'code_end');

        }

    }

    /**
     * 往GW添加采集的数据
     * @param $memberId 会员id
     * @param $collectParams 采集的参数
    */

    private function addToGW($memberId, $collectParams)
    {
        error_log(var_export($collectParams,true),3,$this->logErrorFile);
    }
 
    /**
     * 获取站点配置
     * @param $siteName 站点名称
     * @return array|bool
     */
    public function parseIniFile($siteName)
    {
        $iniFileName = $this->iniFilePath . $siteName . $this->iniFileExt;

        if (file_exists($iniFileName)) {
		    /*
            if(time()-filemtime($iniFileName)>86400)
            {
                unlink($iniFileName);//数据库被删除 配置也相应移除
                $sites = FALSE;
            }else{
                $sites = parse_ini_file($iniFileName, TRUE);
            }*/
			$sites = parse_ini_file($iniFileName, TRUE);
        } else {
            $sites = FALSE;
        }
        return $sites;
    }

    /**
     *从数据库生成对应博主的采集的配置文件
     */
    public function generateIniFileFromDb()
    {
        $this->benchmark->mark('code_start');
        $this->dbcollect = $this->load->database('handwd', true);
        $siteNums = $this->dbcollect->count_all_results('blog_sitecollect');
        $iniArr = array();
        if ($siteNums > 0) {
            $this->dbcollect->select('*')->from('blog_sitecollect')->limit($siteNums);
            $userSites = $this->dbcollect->get();
            foreach ($userSites->result_array() as $siteInfo) {
                $this->parseSiteName($siteInfo['siteUrl']);
                $siteInfo['Recommend'] = $this->getRecommendByMemberid($siteInfo['memberId'],$siteInfo['sysTagId']);
                foreach ($siteInfo as $key => $val) {
                    $siteInfostr[] = $key . " = " . $val;
                }
                $iniArr[$this->siteName][] = "[{$siteInfo['nickname']}]" . PHP_EOL . implode(PHP_EOL, $siteInfostr);
                unset($siteInfostr);

            }
        }

        foreach ($iniArr as $siteName => $iniInfo) {
            file_put_contents($this->iniFilePath . $siteName . $this->iniFileExt, implode(PHP_EOL, $iniInfo));
        }
        $this->benchmark->mark('code_end');
        echo $this->benchmark->elapsed_time('code_start', 'code_end');

    }


    /**通过用户id去获取用户组信息
     * @param $memberid 用户id
     * @param $systTagID 系统分类
     * @return int
     */
    private function getRecommendByMemberid($memberid,$systTagID)
    {
        $this->load->model('memberblog_socket');
        $blogConfig = $this->memberblog_socket->getMemberBlogByMemberID(array('QryData' =>$memberid));
        //用户组信息的处理
        $groups = trim($blogConfig['GroupID'], ',');
        if ($groups != "") {
            $recommend = config_item('recommendgroup');
            $limittag = config_item('limittags');
            $groups = explode(',', $groups);
            $groups = (is_string($groups)) ? array(0 => $groups) : $groups;
            foreach ($groups as $grp) {
                if (isset($recommend[$grp])) {
                    if (!in_array($systTagID, $limittag)) {
                        $param['Recommend'] = $recommend[$grp];
                    }
                }
            }
        }
        return isset($param['Recommend'])?$param['Recommend']:0;
    }



    /**
     * 解析搜狐列表页的真实地址
     * @param $sohuListUrl
     */
    private function parseSohuListUrl($sohuListUrl)
    {
        $sohuListContent = curl_http_post($sohuListUrl, '', array('ssl' => 0, 'post' => 0, 'format' => 'html', 'followAction' => 1, 'gzip' => 1), $this->siteConfig['header']);
        $sohuListContent = iconv("GBK", $this->fileEncode . "//IGNORE", $sohuListContent);
        $pregBaseurl = '/_blog_base_url = \'(.*?)\'/';
        $pregEbi = '/_ebi = \'(.*?)\'/';
        if (preg_match($pregBaseurl, $sohuListContent, $matchBaseUrl) AND  preg_match($pregEbi, $sohuListContent, $mathEbi)) {
            $this->siteUrl = $matchBaseUrl[1] . '/sff/entries/' . $mathEbi[1] . '.html';
        }
    }

    /**
     * 解析站点的配置名称
     * @param $collectUrl 采集的url地址
     */
    private function parseSiteName($collectUrl)
    {

        // 新浪博客/微博、和讯、搜狐、网易、华夏时报
        if (strrpos($collectUrl, 'blog.sina.com.cn/u/') != false) {
            $this->siteName = 'sinau';
        } else if (strrpos($collectUrl, 'blog.sina.com.cn/s/') != false) {
            $this->siteName = 'sinas';
        } else if (strrpos($collectUrl, 'finance.sina.com.cn/stock/cpbd') != false) {
            $this->siteName = 'sinaSinglePage';
        } else if (strrpos($collectUrl, 'blog.sina.com.cn') != false) {
            $this->siteName = 'sina';
        }else if (strrpos($collectUrl, 'weibo.com') != false) {
            $this->siteName = 'weibo';
        } else if (strrpos($collectUrl, 'blog.hexun.com') != false) {
            $this->siteName = 'hexun';
        } else if (strrpos($collectUrl, 'blog.sohu.com') != false) {
            $this->siteName = 'sohu';
        } else if (strrpos($collectUrl, 'blog.163.com') != false) {
            $this->siteName = '163blog';
        } else if (strrpos($collectUrl, 'www.chinatimes.cc') != false) {
            $this->siteName = 'chinatimes';
        } else {
            $this->siteName = '';
        }

    }

    /**
     * 图片本地化
     * @param $userid 用户id
     * @param $content 文章内容的html
     * @param string $pregImgRule 启用了自定义图片规则
     * @return mixed
     */
    public function imglLocalization($userid, $content, $pregImgRule = '')
    {
        preg_match_all('/(<img[\s\S]*?(><\/img>|>))/i', $content, $matchImg);

        if (!empty($matchImg[1])) {
            foreach ($matchImg[1] as $key => $img) {
                /*匹配图片 src width height*/
                $src = $width = $height = array();
                $s = $w = $h = '';
                if ($pregImgRule) {
                    preg_match($pregImgRule, $img, $src); //自定义规则
                } else {
                    preg_match('/src=["\']?(.+?)("|\'| |>|\/>){1}/i', $img, $src); //默认规则
                }

                preg_match('/width=["\']?(.*?)("|\'| |>|\/>){1}/i', $img, $width);
                preg_match('/height=["\']?(.*?)("|\'| |>|\/>){1}/i', $img, $height);
                $remoteFile = $src[1];
                if ($width[1]) {
                    $w = ' width=' . $width[1];
                }
                if ($height[1]) {
                    $h = ' height=' . $height[1] . ' ';
                }

                $imgExt = $this->GetFileExt($remoteFile);
                if (!preg_match('/^(jpg|gif|png|jpeg)$/i', $imgExt)) {
                    $imgExt = 'jpg';
                }
                $imgFileName = date('YmdHis').substr(md5($remoteFile),0,6). '.' . $imgExt;

                /*获取上传路径*/
                $folder = '/images';
                $path = getUploadPath($userid, $folder);
                /*本地临时存放的文件*/
                $dir = config_item('img_file_path');
                $newfilename = getImgName($imgFileName);
                $localFile = $dir . $newfilename;
                /*图片本地化*/
                $imgcontent = file_get_contents($remoteFile);
                file_put_contents($localFile, $imgcontent);

                $ftpremoteFile =  $path.$newfilename;
                //同步到服务器
                $this->ftp_article_img($ftpremoteFile, $localFile);
                //替换图片链接
                $matchImg[3][$key] = '<img src=' . config_item('img_base_url').$ftpremoteFile . $w . $h . ' />';
                sleep(1);

            }
            $content = str_replace($matchImg[1], $matchImg[3], $content);
        }
        return $content;
    }

    /**
     * 获取文件后缀
     * @param $filePath 文件路径
     * @return string
     */
    public function GetFileExt($filePath)
    {
        return (trim(strtolower(substr(strrchr($filePath, '.'), 1))));
    }


    /**
     * 获取列表页所有url
     * @param $document 缩减后的html列表页
     * @param $baseUrl  页面链接
     * @param string $linkRule 启用特殊规则的链接
     * @return array
     */
    public function _striplinks($document, $baseUrl, $linkRule = '')
    {
        if ($linkRule) {
            preg_match_all(
                $linkRule,
                $document,
                $links
            );

            while (list($key, $val) = each($links[1])) {
                if (!empty($val) && strpos($val, $baseUrl) != FALSE) {
                    $match[] = $val;
                } else if (!empty($val)) {
                    if (substr($val, 0, 7) == 'http://') {
                        continue;
                    } elseif (substr($val, 0, 1) == '/') {
                        $match[] = $baseUrl . $val;
                    } elseif (substr($val, 0, 2) == './') {
                        $match[] = $baseUrl . substr($val, 1);
                    } elseif (substr($val, 0, 3) == '../') {
                        $match[] = $baseUrl . substr($val, 2);
                    } else {
                        $match[] = $baseUrl . '/' . $val;
                    }
                }

            }
        } else {
            preg_match_all("'<\s*a\s.*?href\s*=\s*		# find <a href=
                            ([\"\'])?					# find single or double quote
                            (?(1) (.*?)\\1 | ([^\s\>]+))# if quote found, match up to next matching
                                                        # quote, otherwise match up to next space
                            'isx", $document, $links);
            // catenate the non-empty matches from the conditional subpattern
            while (list($key, $val) = each($links[2])) {
                if (!empty($val) && strpos($val, $baseUrl) != FALSE)
                    $match[] = $val;
            }

            while (list($key, $val) = each($links[3])) {
                if (!empty($val) && strpos($val, $baseUrl) != FALSE)
                    $match[] = $val;
            }
        }
        // return the links
        return array_unique($match);
    }



    function ftp_article_img($remoteFile,$localFile)
    {
        $rometeFileDir = dirname($remoteFile);
        $this->load->library('ftp');
        $this->ftp->connect(config_item('ftp'));
        $mkdirStatus = $this->ftp->ftp_mksubdirs('',$rometeFileDir);
        if($mkdirStatus){
            if($this->ftp->upload($localFile,$remoteFile))
            {
                echo "upload suecess...";
            }else{
                echo "upload fail...";
            }
        }else{
            echo "fail to upload file";
        }

    }
}
