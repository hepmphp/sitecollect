<?php
/**
 * Created by JetBrains PhpStorm.
 * User: fish
 * Date: 14-10-9
 * Time: 上午9:39
 * 
 */

class User_model extends MY_Model {
    public $db;
    public $tablename = 'rbac_user';

    public function __construct()
    {
        $this->db = $this->load->database('default',true);
    }

    public function insert($user_arr=array())
    {
       $rs =  $this->db->insert($this->tablename,$user_arr);
       return $rs;
    }

    public function deleteByUserId($uid){

        $rs = $this->db->delete($this->tablename,array('uid'=>$uid));
        return $rs;
    }

    public function updateByUserId($uid,$user_arr=array())
    {
        $this->db->where('uid', $uid);
        $rs = $this->db->update($this->tablename,$user_arr);
        return $rs;
    }

    public function getUserByUsername($username)
    {
        $this->db->select('*')->from($this->tablename)->where('username', $username)->limit(1);
        $user = $this->db->get();
        return $user->row_array();
    }

    public function getTableList($where=array(),$like=array(),$iscount=false,$tpp='10',$offset='0',$order=array()){
       return parent::getTableList($this->tablename,$where,$like,$iscount,$tpp,$offset,$order);
    }

}