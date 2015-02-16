<?php
/**
 * Created by JetBrains PhpStorm.
 * User: fish
 * Date: 14-10-9
 * Time: 上午9:47
 * 
 */

function jsoncallback($data=array(),$callback=''){
    $return = json_encode($data);
    if($callback){
        echo $callback.'('.$return.')';
    }else{
        echo $return;
    }
}


/**
 * 获取客户端ip
 * @return mixed
 */
function get_client_ip() {
    $ip = $_SERVER['REMOTE_ADDR'];
    if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
        foreach ($matches[0] AS $xip) {
            if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                $ip = $xip;
                break;
            }
        }
    }
    return $ip;
}



/* curl模拟post
 * @param $url url
 * @param $param  请求参数
 * @param array $extra  ssl 是否启用ssl,是否post
 * @param array $header 头部信息
 * @return mixed
 */
function curl_http_post($url,$param,$extra=array('ssl'=>1,'post'=>1,'format'=>'json','followAction'=>NULL,'gzip'=>NULL),$header=array())
{
    $ch = curl_init();
    if (is_array($param)) {
        $urlparam = http_build_query($param);
    }else if(is_string($param)){//json字符串
        $urlparam = $param;
    }
    if(!$extra['post']){
        $url = $url.'?'.$urlparam;
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_TIMEOUT, 120 ); //设置超时时间
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//返回原生的（Raw）输出
    if($extra['ssl']){
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ;//将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
    }

    if($extra['post']){
        curl_setopt($ch, CURLOPT_POST, 1);//POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $urlparam);//post数据
    }

    if(isset($extra['followAction']))
    {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);//允许被抓取的链接跳转
    }

    if(isset($extra['gzip']))
    {
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Accept-Encoding: gzip, deflate'));
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
    }

    if($header)
    {
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
    }

    $data = curl_exec($ch);
    if($extra['format']=='json')
    {
        $data = json_decode( $data,true);
    }
    curl_close($ch);
    return $data;
}


 

//获取图片的名字
function getImgName($imgname) {
    $begin = strlen($imgname) - strrpos($imgname, '.');
    $ext = substr($imgname, -$begin);
    srand(time());
    return date('Ymdhis') . rand(1000, 9000) . $ext;
}


/*
  根据根据用户ID获取相册图片上传目录
 */

function getUploadPath($userid, $basepath = '') {
    if ($basepath) {
        $path = $basepath;
    } else {
        $path = config_item('uploadpath');
    }
    return $path . getIdFolder($userid);
}

/**
 * 功能：获取用户上传文件的目录
 * @id 用户ID
 * @return array
 */
function getIdFolder($id = 0) {
    $id_encode = md5($id);
    $folder_1 = substr($id_encode, 0, 2);
    $folder_2 = substr($id_encode, 2, 2);
    return '/' . $folder_1 . '/' . $folder_2 . '/' . $id . '/';
}