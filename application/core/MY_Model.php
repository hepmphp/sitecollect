<?php
/**
 * Created by JetBrains PhpStorm.
 * User: fish
 * Date: 14-10-9
 * Time: 下午4:33
 * 
 */

class MY_Model extends CI_Model{

    public function __construct()
    {
        $this->db = $this->load->database('default',true);
    }

    public function getTableList($table,$where=array(),$like=array(),$iscount=false,$tpp='10',$offset='0',$order=array())
    {

        if(!empty($where)){
            $this->db->where($where);
        }

        if(!empty($like)){
            $this->db->like($like);
        }

        if($iscount){
            $this->db->from($table);
            return $this->db->count_all_results();
        }else{
            if(!empty($order)){
                $this->db->order_by($order['item'],$order['type']);
            }

            $this->db->limit($tpp,$offset);
            $query = $this->db->get($table);
            echo $this->db->last_query()."<br>";
            return $query->result_array();
        }
    }
}