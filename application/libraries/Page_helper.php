<?php
/**
 * Created by JetBrains PhpStorm.
 * User: fish
 * Date: 14-12-4
 * Time: 下午2:51
 * 
 */

class Page_helper {
    /**
     * CI超级全局对象
     * @var
     */
    public $CI;
    /**
     *
     */
    public function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->library('Pagination');
	 
    }

    /**
     * 获取分页字符串
     * @param $url url地址
     * @param $total 总数
     * @param int $perpage 每页分页数
     * @return mixed
     */
    function getPageStr($url,$total,$perpage=10){

        $config['base_url'] = $url;
        $config['total_rows'] = $total;
        $config['per_page'] = $perpage;
        $config['num_links'] = '4';
        $config['uri_segment'] = '3';
        $config['first_link'] = '首页';
        $config['prev_link'] = '上一页';
        $config['next_link'] = '下一页';
        $config['last_link'] = '末页';
        $config['cur_tag_open'] = '<a class="Cur">';
        $config['cur_tag_close'] = '</a>';
        $config['full_tag_open'] = '<div class="Page Cf">';
        $config['full_tag_close'] = '</div>';
        $this->CI->pagination->initialize($config);
        $pages = $this->CI->pagination->create_links();
        return $pages;
    }
}