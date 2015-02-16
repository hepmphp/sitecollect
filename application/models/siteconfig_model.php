<?php
/**
 * Created by JetBrains PhpStorm.
 * User: fish
 * Date: 15-1-23
 * Time: 上午10:06
 * 
 */
class Siteconfig_model extends CI_Model{
    /**
     * db实例
     * @var
     */
    public $db='';
    /**
     * 表名
     * @var string
     */
    public $tablename='blog_sitecollect';
    /**
     * 表的主键
     * @var string
     */
    public $primary_key = 'id';
    /**
     *
     */
    public function __construct()
    {
//        $this->benchmark->mark('code_start');
        $this->db = $this->load->database('default',true);
    }
    /**
     * 插入数据
     * @param $filed_arr
     * @return mixed
     */
    public function insert($filed_arr)
    {
        $rs =  $this->db->insert($this->tablename,$filed_arr);
        return $rs;
    }
    /**
     * 通过主键更新数据
     * @param $id 主键
     * @param $filed_arr 更新的字段数组
     * @return mixed
     */
    public function update($id,$filed_arr)
    {
        $this->db->where("{$this->primary_key}", $id);
        $rs = $this->db->update($this->tablename,$filed_arr);
        return $rs;
    }
    /**
     * 通过主键删除数据
     * @param $id 主键
     * @return mixed
     */
    public function delete($id)
    {
        $rs = $this->db->delete($this->tablename,array("{$this->primary_key}"=>$id));
        return $rs;
    }
    /**
     * 获取列表
     * @param array $where 条件数组
     * @param array $like  like数组
     * @param bool $iscount 是否是统计
     * @param string $tpp 每页多少数据量
     * @param string $offset 当前偏移量
     * @param array $order 排序
     * @return mixed
     */
    public function getTableList($where=array(),$like=array(),$iscount=false,$tpp='10',$offset='0',$order=array())
    {
        if(!empty($where)){
            $this->db->where($where);
        }
        if(!empty($like)){
            $this->db->like($like);
        }
        if($iscount){
            $this->db->from($this->tablename);
            return $this->db->count_all_results();
        }else{
            if(!empty($order)){
                $this->db->order_by($order['item'],$order['type']);
            }
            $this->db->limit($tpp,$offset);
            $query = $this->db->get($this->tablename);
            return $query->result_array();
        }
    }
}