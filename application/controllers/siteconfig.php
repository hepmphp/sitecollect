<?php
/**
 * Class Siteconfig
 */
class Siteconfig extends MY_Controller
{
    /*增删改查成功状态标志*/
    CONST CURD_FAIL_FLAG = '0000';
    CONST INSERT_SUCCESS_FLAG='0001';
    CONST DELETE_SUCCESS_FLAG='0002';
    CONST UPDATE_SUCCESS_FLAG='0003';
    CONST SELECT_SUCCESS_FLAG='0004';

	public function __construct()
	{
        parent::__construct();
        $this->load->model('siteconfig_model','siteconfig');
        error_reporting(E_ALL^E_NOTICE);
	}
    /**
     *列表
     */
    public function getList(){
	
		 //权限检测
        $permparam = array(
            'type' => 4,
            'node' => '',
        );
        $this->checkPurview(cur_page_url(), 'js', $permparam);

        $tpp = max(intval($this->input->get('tpp')),20);
        $page = max(intval($this->input->get('page')),1);
        $offset = ($page-1)*$tpp;
        $tpp_options = $this->getTppOptions($tpp);

        $where = '';
        $like = '';
        $siteconfig_count = $this->siteconfig->getTableList($where,$like,true);
		$order = array('item'=>'id','type'=>'asc');
        //list
         $stieconfig_list = $this->siteconfig->getTableList($where,$like,'',$tpp,$offset,$order);


        $search_arr = array();
        $searchstr = http_build_query($search_arr);
        $multipage = cmsmulti($tpp_options,$siteconfig_count, $tpp, $page,"?".$searchstr);


        $systaglist =  & $this->config->item('sysTagList');
        $opt_systagstr = $this->generateTagSelect($systaglist);

         $blogurl = & $this->config->item('BlogUrl');
         foreach($stieconfig_list as $key=>$siteconfig)
         {
             $blogerInfo = $this->getBlogInfoByNickname($siteconfig['nickname']);
             $stieconfig_list[$key]['blogurl'] = $blogurl.$blogerInfo['DomainName'];
         }

        $list = array(
            'list'=>$stieconfig_list,
            'search_arr'=>$search_arr,
            'tpp'=>$tpp,
            'pages'=>$multipage,
            'systaglist'=>$systaglist,
            'opt_systagstr'=>$opt_systagstr,
        );
		
		if(isset($_REQUEST['test']))
		{
			echo "<pre>";
			print_R($list);
		}

	     $this->load->view("siteconfig/getlist.html",$list);

    }


    /**
     * 获取每页显示多少条下拉框字符串
     * @param $tpp 每页显示多少条
     * @return string
     */
    public function getTppOptions($tpp){
        $select[$tpp] = $tpp? "selected='selected'" : '';
        $tpp_options = "<select id='tpp' name='pagesize' onchange='loactionstppurl()'>
        <option value='20' $select[20]>20</option>
        <option value='50' $select[50]>50</option>
        <option value='100' $select[100]>100</option>
        <option value='150' $select[150]>150</option>
        <option value='200' $select[200]>200</option>
        </select>";

        return $tpp_options;
    }
    /**获取标签下拉框
     * @param $syttaglist
     * @return string
     */
    private function generateTagSelect($syttaglist)
    {
        $opt_str = '';
        foreach($syttaglist as $key =>$tag){
            $opt_str .= "<option value ='{$key}'>{$tag}</option> ";
        }
        return $opt_str;

    }


    /**
     *ajax更新  添加更新删除
     */
    public function ajaxUpdate()
    {
        $operation = $this->input->get_post('operation');
        if($operation=='update' OR $operation=='add')
        {
			 //权限检测
			$permparam = array(
				'type' => 1,
				'node' => '',
			);
			$this->checkPurview(cur_page_url(), 'json', $permparam);
            $user_field = array('nickname','siteUrl','sysTagId');
            foreach($user_field as $field)
            {
                $table_field[$field] = trim($this->input->get_post("$field"));
            }

            //数据验证
            if(!$this->validSiteUrl($table_field['siteUrl']))
            {
                jsoncallback(array('flag'=>self::CURD_FAIL_FLAG,'msg'=>'请填写正确的url',));
            }else if( $this->checkSiteUlrExits($table_field['siteUrl']) ){
                jsoncallback(array('flag'=>self::CURD_FAIL_FLAG,'msg'=>'站点已经添加过了',));
            }else if( !$this->validsysTag($table_field['sysTagId']) ){
                jsoncallback(array('flag'=>self::CURD_FAIL_FLAG,'msg'=>'请选择采集的分类',));
            }else if( !($bloginfo=$this->getBlogInfoByNickname($table_field['nickname'])) )
            {
                jsoncallback(array('flag'=>self::CURD_FAIL_FLAG,'msg'=>'用户昵称有错',));
            }else if($operation=='update' AND !( $id = intval($this->input->get_post('id')) )){
                jsoncallback(array('flag'=>self::CURD_FAIL_FLAG,'msg'=>'id号有错',));
            } else{
				if($bloginfo['Nickname']==' ')
				{
					jsoncallback(array('flag'=>self::CURD_FAIL_FLAG,'msg'=>'用户昵称有错',));
				}

                if($operation=='add'){
                    //合法数据入库
                    $siteconfig_arr = array(
                        'siteUrl'=>$table_field['siteUrl'],
                        'sysTagId'=>$table_field['sysTagId'],
                        'userId'=>$bloginfo['UserID'],
                        'memberId'=> $bloginfo['MemberID'],
                        'nickname'=> $table_field['nickname'],
                    );

                    if($rs = $this->siteconfig->insert($siteconfig_arr))
                    {
                        $return = array('flag'=>$rs,'msg'=>'添加成功',);
                        jsoncallback($return);
                    }else
                    {
                        $return = array('flag'=>self::CURD_FAIL_FLAG,'msg'=>'添加失败',);
                        jsoncallback($return);
                    }
                }else{
                    $table_field['userId'] = $bloginfo['UserID'];
                    $table_field['memberId'] = $bloginfo['MemberID'];
                    $rs = $this->siteconfig->update($id,$table_field);
                    if($rs){
                        $return = array('flag'=>self::UPDATE_SUCCESS_FLAG,'msg'=>'更新成功',);
                    }else{
                        $return = array('flag'=>self::CURD_FAIL_FLAG,'msg'=>'更新失败',);
                    }
                    jsoncallback($return);
                }
            }
        }elseif($operation=='delete')
        {
			//权限检测
			$permparam = array(
				'type' => 2,
				'node' => '',
			);
			$this->checkPurview(cur_page_url(), 'json', $permparam);
            if( $id = intval($this->input->get_post('id',true))){
                $rs = $this->siteconfig->delete($id);
                if($rs){
                    $return = array('flag'=>self::DELETE_SUCCESS_FLAG,'msg'=>'删除成功',);
                }else{
                    $return = array('flag'=>self::CURD_FAIL_FLAG,'msg'=>'操作失败',);
                }
                jsoncallback($return);
            }else{
                jsoncallback(array('flag'=>self::CURD_FAIL_FLAG,'msg'=>'id号出错'));
            }


        }
    }
	
	 /**
     * 验证url
     * @param $collectUrl 采集的url地址
     */
    private  function validSiteUrl($url)
    {
        // 新浪博客/微博、和讯、搜狐、网易、华夏时报
        if(    (strrpos($url, 'blog.sina.com.cn/u/') != false)
			OR (strrpos($url, 'blog.sina.com.cn/s/') != false)
			OR (strrpos($url, 'blog.sina.com.cn') != false)
		    OR (strrpos($url, 'finance.sina.com.cn/stock/cpbd/') != false)
			OR (strrpos($url, 'weibo.com') != false)
			OR (strrpos($url, 'blog.hexun.com') != false)
			OR (strrpos($url, 'blog.sohu.com') != false)
			OR (strrpos($url, 'blog.163.com') != false)
			OR (strrpos($url, 'www.chinatimes.cc') != false)
		){
			return TRUE;
		 }else{
			return FALSE;
		 }
    }

    /**
     * 检查站点是否已经存在
     * @param $url
     * @return mixed
     */
    public function checkSiteUlrExits($url)
    {
        return $this->siteconfig->checkSiteUlrExits($url);
    }

    /**验证标签是否合法
     * @param $tagid 标签id
     * @return bool
     */
    private function validsysTag($tagid){
		$systagList =  & $this->config->item('sysTagList');
		$systagKeys = array_keys($systagList);
		if(in_array($tagid,$systagKeys)){
			return TRUE;
		}else{
			return FALSE;
		}
	}

    /**通过昵称或者博主信息
     * @param $nickname
     * @return mixed
     */
    public function getBlogInfoByNickname($nickname)
    {
        $blogerInfo = $this->siteconfig->getBlogerInfoByNickname($nickname);
        return $blogerInfo;
    }


	
	

}