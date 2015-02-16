<?php
/**
 * Created by JetBrains PhpStorm.
 * User: fish
 * Date: 15-1-19
 * Time: 上午10:31
 *
 */
require APPPATH . 'third_party/phpQuery/phpQuery.php';

/**
 * Class Sitecollectquery
 */
class Sitecollectquery extends CI_Controller
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

    /**
     *
     */
    public function test()
    {
		echo __FILE__;
    }

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        error_reporting(E_ALL ^ E_NOTICE);
        $this->load->helper('common');
        $this->load->config('qcollection');
        $this->load->config('serverconf');
        $this->iniFilePath = APPPATH . '/ini/';
		$this->logErrorFile = APPPATH . 'logs/logErrorFile.log';
        if (!is_dir($this->iniFilePath)) {
            mkdir($this->iniFileName, 0755, true);
        }

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
    public function collect($siteName, $singlePage = 0, $debugTest = 0, $debugList = 0, $debugCotent = 0)
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

        //包含采集的配置
        $this->siteConfig =   config_item($this->siteName);

        foreach ($blogerConfig as $bloger) {
            $this->siteUrl = $bloger['siteUrl'];
            $this->sysTagId = $bloger['sysTagId'];
            $this->userId = $bloger['userId'];
            $this->memberId = $bloger['memberId'];
            $this->Recommend = $bloger['Recommend'];

            if ($singlePage) {
                $linkUrlsArr[] = $this->siteUrl;
            } else {

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

                $collectArr = $this->_striplinks($this->siteUrl, $this->siteConfig['linkRule']);
                if($debugList)
                {
                    echo "debug list-----";
                    echo "<br/>";
                    print_r($this->siteConfig);
                    print_r($collectArr);
                    exit();
                }

                /* 提取日志里的数组 */
                $logArr = array();
                if (file_exists($this->logFile)) {
                    $logArr = explode(PHP_EOL, file_get_contents($this->logFile));
                }
                $linkUrlsArr = array_diff($collectArr, $logArr);
            }

            if (!empty($linkUrlsArr)) {
                foreach ($linkUrlsArr as $linkUrl) {

                    phpQuery::newDocumentFile($linkUrl);
                    if (isset($this->siteConfig['removeTitle']) AND !empty($this->siteConfig['removeTitle'])) {//过滤标题
                        foreach ($this->siteConfig['removeTitle'] as $rule) {
                            pq($this->siteConfig['queryTitle'])->find($rule['html'])->remove($rule['class']);
                        }
                    } 
                    $arr['title'] = trim(strip_tags(pq($this->siteConfig['queryTitle'])->html()));
                   
                    $arr['pubtime'] = preg_match('/(\d{4}-\d{1,2}-\d{1,2})/', pq($this->siteConfig['queryTime'])->html(), $matchTime) ? $matchTime[1] : '';
                    $arr['tag'] = pq($this->siteConfig['queryTags'])->html();
                    $this->imglLocalization(123, $this->siteConfig['queryContent'], $this->siteConfig['pregImageRule']); //图片本地化

                    if (isset($this->siteConfig['RemoveContent']) AND !empty($this->siteConfig['RemoveContent'])) {//过滤内容
                        foreach ($this->siteConfig['RemoveContent'] as $rule) {
                            pq($this->siteConfig['queryContent'])->find($rule['html'])->remove($rule['class']);
                        }
                      
                    }
                    $arr['content'] = pq($this->siteConfig['queryContent'])->html();
                    

                    /*检查编码 转码 检查有点问题*/
					/*
                    $this->collectEncode = $this->_getEncode($arr['title']);

                    if ($this->collectEncode != $this->fileEncode) {
                        $arr = $this->_arrayConvertEncoding($arr, $this->fileEncode, $this->collectEncode);
                    }
					*/

                    if($debugCotent)
                    {
                        echo "debug content-----";
                        echo "<br/>";
                        print_r($this->siteConfig);
                        var_dump($this->collectEncode);
                        print_r($arr);
                    }

                    echo "<pre>";
                    print_r($arr);
					error_log(var_export($arr,true),3,$this->logErrorFile);
                    /*采集的url记录日志 以防止重复采集*/
                    //  error_log($linkUrl . PHP_EOL, 3, $this->logFile);
                }

            } else {
                // $logMsg = date('Y-m-d H:i:s')."\t{$this->siteUrl}\t  list...";
                // error_log($logMsg.PHP_EOL,3,$this->logErrorFile);
                echo "null list...";
            }
            $this->benchmark->mark('code_end');
            echo $this->benchmark->elapsed_time('code_start', 'code_end').'秒';

        }

    }


    /**
     * @param $memberId
     * @param $collectParams
     */
    private function addToGW($memberId, $collectParams)
    {


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
//            if(time()-filemtime($iniFileName)>86400)
//            {
//                unlink($iniFileName);//数据库被删除 配置也相应移除
//                $sites = FALSE;
//            }else{
            $sites = parse_ini_file($iniFileName, TRUE);
//            }
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
                $siteInfo['Recommend'] = $this->getRecommendByMemberid($siteInfo['memberId'], $siteInfo['sysTagId']);
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
        } else if (strrpos($collectUrl, 'weibo.com') != false) {
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
     * @param $queryContent 内容页的选择器
     * @param string $imgSoruce 图片真实地址选择器
     */
    public function imglLocalization($userid, $queryContent, $imgSoruce = 'src')
    {
        //phpQuery::newDocumentFile('http://www.oschina.net/code/list');
        $imgs = pq($queryContent . ' img');
        foreach ($imgs as $imageUrl) {
            $matchImg[] = array(
                "src" => $imageUrl->getAttribute($imgSoruce),
                'width' => $imageUrl->getAttribute('width'),
                'height' => $imageUrl->getAttribute('height'),
            );

        }
        if (!empty($matchImg)) {
            foreach ($matchImg as $key => $imgFile) {
                if (empty($imgFile['src'])) continue;
                /*匹配图片 src width height*/
                $imgExt = $this->GetFileExt($imgFile['src']);
                if (!preg_match('/^(jpg|gif|png|jpeg)$/i', $imgExt)) {
                    $imgExt = 'jpg';
                }
                $imgFileName = date('YmdHis') . substr(md5($imgFile['src']), 0, 6) . '.' . $imgExt;

                /*获取上传路径*/
                $folder = '/images';
                $path = getUploadPath($userid, $folder);
                /*本地临时存放的文件*/
                $dir = config_item('img_file_path');
                $newfilename = getImgName($imgFileName);
                $localFile = $dir . $newfilename;
                /*图片本地化*/
                $imgcontent = file_get_contents($imgFile['src']);
                file_put_contents($localFile, $imgcontent);

                $ftpremoteFile = $path . $newfilename;
                //同步到服务器
                $this->ftp_article_img($ftpremoteFile, $localFile);
                //替换图片链接
                $replaceImg[$key] = config_item('img_base_url') . $ftpremoteFile;
                pq($queryContent.' img')->elements[$key]->setAttribute('src', $replaceImg[$key]);
                sleep(1);
            }
        }
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
     * 获取列表页的文章页url
     * @param $baseUrl 列表有地址
     * @param $linkRule url链接的选择器
     * @return array
     */
    public function _striplinks($baseUrl, $linkRule)
    {
        phpQuery::newDocumentFile($baseUrl);
        $list = pq($linkRule);
        $listArr = array();
        foreach ($list as $listlink) {
            $listArr[] = $listlink->getAttribute('href');
        }

        return array_unique($listArr);
    }


    /**
     * 获取文件编码
     * @param $string
     * @return string
     */
    private function _getEncode($string)
    {
        return mb_detect_encoding($string, array('ASCII', 'GB2312', 'GBK', 'UTF-8'));
    }

    /**
     * 转换数组值的编码格式
     * @param  array $arr
     * @param  string $toEncoding
     * @param  string $fromEncoding
     * @return array
     */
    private function _arrayConvertEncoding($arr, $toEncoding, $fromEncoding)
    {
        eval('$arr = ' . iconv($fromEncoding, $toEncoding . '//IGNORE', var_export($arr, TRUE)) . ';');
        return $arr;
    }


    /**
     * 上传ftp
     * @param $remoteFile ftp的完整路径
     * @param $localFile  本地临时图片文件
     */
    function ftp_article_img($remoteFile, $localFile)
    {
        $rometeFileDir = dirname($remoteFile);
        $this->load->library('ftp');
        $this->ftp->connect(config_item('ftp'));
        $mkdirStatus = $this->ftp->ftp_mksubdirs('', $rometeFileDir);
        if ($mkdirStatus) {
            if ($this->ftp->upload($localFile, $remoteFile)) {
                echo "upload suecess...";
                unlink($localFile);
            } else {
                echo "upload fail...";
            }
        } else {
            echo "fail to mkdir...";
        }

    }


}
