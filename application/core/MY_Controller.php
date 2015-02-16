<?php
/**
 * Created by JetBrains PhpStorm.
 * User: fish
 * Date: 14-9-23
 * Time: 下午1:47
 * 
 */
use PhpRbac\Rbac;

/**
 * Class MY_Controller
 */
class MY_Controller extends CI_Controller{
    /**
     * rbac管理器
     * @var PhpRbac\Rbac
     */
    public $rbac_manager;
    /**
     * 当前控制器_方法名称
     * @var string
     */
    public $controller_method;
    /**
     * 权限id
     * @var mixed
     */
    public $permissionid;
    /**
     * 用户id
     * @var int
     */
    public $userid;

    /**
     *
     */
    public function __construct(){
        session_start();
        header('Content-type:text/html;charset:utf8');
        parent::__construct();
        $this->rbac_manager = new Rbac();
        $c =  $this->uri->segment(1);
        $m = $this->uri->segment(2);
        $this->controller_method = $c.'_'.$m;
        $this->permissionid = $this->returnId();

        if(check_rbac_islogin()){
            if(!empty($this->permissionid)){
                $this->userid = $_COOKIE['cookie']['rbac']['uid'];
                var_dump($this->permissionid);
                var_dump($this->checkPermission());
                if($this->checkPermission()){
                    echo $this->controller_method.$this->userid;
                }else{
                    exit('没有权限');
                }
            }else{
               // exit('该模块不存在权限');
            }
        }else{
            redirect(USER_LOGIN_URL);
        }

    }

    /**
     * 返回权限id
     * @return mixed
     */
    private  function returnId(){
        return $this->rbac_manager->Permissions->returnId($this->controller_method);
    }


    /**
     * 检查权限
     * @return bool
     */
    private  function checkPermission()
    {
        return $this->rbac_manager->check($this->controller_method,$this->userid);
    }

}