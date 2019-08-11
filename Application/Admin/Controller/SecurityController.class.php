<?php
namespace Admin\Controller;
use Think\Controller;
class SecurityController extends CommonController {
	public function orderRecordPage(){
		$pp = 0;
		if(isset($_GET["p"])){
			$pp = I('get.p');
		}
		$Model = M('security_order');
		$records = $Model->order('createtime desc')->page($pp.',20')->select();
		$count = $Model->count();
		//print_r($records);
		//exit();
		$Page = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
		$Page->setConfig('prev','last');
		$Page->setConfig('next','next');
		$Page->setConfig('first','first');
		$Page->setConfig('last','last');
		$Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% ');
		$show = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('records',$records);
		//print_r($rate);
		//$this->assign('currencies',$output);
		$this->display(T('admin/security_order_list'));

	}
	public function sshRecordPage(){
		$pp = 0;
		if(isset($_GET["p"])){
			$pp = I('get.p');
		}
		$Model = M('security_ssh');
		$records = $Model->order('createtime desc')->page($pp.',20')->select();
		$count = $Model->count();
		//print_r($records);
		//exit();
		$Page = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
		$Page->setConfig('prev','last');
		$Page->setConfig('next','next');
		$Page->setConfig('first','first');
		$Page->setConfig('last','last');
		$Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% ');
		$show = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('records',$records);
		//print_r($rate);
		//$this->assign('currencies',$output);
		$this->display(T('admin/security_ssh_list'));

	}
	public function taobaoRecordPage(){
		$this->display(T('admin/security_taobao_list'));
	}
	public function addTaobaopage(){
		$this->display(T('admin/security_taobao_add'));
	}


}
