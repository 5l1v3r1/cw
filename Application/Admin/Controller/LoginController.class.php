<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller {
  public function index(){
	  $redirect = I("get.redirect");
	  $this->assign('redirect',$redirect);
      $this->display(T('admin/login'));
  }
	public function checkLog()
	{
		$redirect = I("get.redirect");
    	$flag = checkSession();
		//dump($content);
		if($flag == 1)//exist
		{
			if(!empty($redirect)){
				$redirectdec = base64_decode(str_replace(array('-', '_'), array('+', '/'), $redirect));
				$this->success(C('LOGIN_SUCCESS'), U($redirectdec),3);
			}else{
				$this->success(C('LOGIN_SUCCESS'), U("Dashboard/index"),3);
			}
            


		}else
		{
			if(!empty($redirect)){
				$this->error(C('LOGIN_ERROR'), U('Login/index?redirect='.$redirect.''),3);
			}else{
				$this->error(C('LOGIN_ERROR'), U('Login/index'),3);
			}
			
		}
	}
	public function logout()
	{
		session('admin_uid',null);
		//$this->show('login');
		//$this->display(T('homepage/index'));
		R('Login/index');
	}
}
