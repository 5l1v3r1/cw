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
	public function addTaobaoGood(){
		$config = array(
			'key' =>'file_upload',
      'maxSize' => 31457280,
      'rootPath'  =>"./Public/uploads/",
      'savePath'  =>'',
      'saveName' => 'xxx',
      'exts' => array('jpg', 'gif', 'png', 'jpeg'),
      'autoSub' => false,
      'replace' => true,
      //'subName' => array('date','Ymd'),
    );
		$upload = new \Think\Upload($config);// 实例化上传类
		$info   =   $upload->upload();
		$dataset = [];
		foreach($info as $file)
		{
			/*deleteType: "DELETE"
deleteUrl: "http://127.0.0.1/jQuery-File-Upload/server/php/index.php?file=1.jpg"
name: "1.jpg"
size: 243000
thumbnailUrl: "http://127.0.0.1/jQuery-File-Upload/server/php/files/thumbnail/1.jpg"
type: "image/jpeg"
url: "http://127.0.0.1/jQuery-File-Upload/server/php/files/1.jpg"*/
			$cell = array();
			$cell["deleteType"] = "DELETE";
			$cell["deleteUrl"] = $file;
			$cell["name"] = $file["savename"];
			$cell["size"] = $file["size"];
			$cell["thumbnailUrl"] = __ROOT__."/Public/uploads/".$file["savename"];
			$cell["type"] = $file["type"];
			$cell["url"] = __ROOT__."/Public/uploads/".$file["savename"];
			array_push($dataset,$cell);
		}
		$res = array();
		$res["files"]= $dataset;
		//$this->error($pic_src);
		$this->ajaxReturn($res);
		//$this->display(T('admin/security_taobao_add'));
	}


}
