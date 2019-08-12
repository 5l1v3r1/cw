<?php
namespace Admin\Controller;
use Think\Controller;
use Org\Util\CategoryTree;
use Think\Upload;
use Think\Image;
use Think\Storage\Driver\File;
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
		$Model = M('security_taobao');
		$goods = $Model ->select();
		$this->assign('goods',$goods);// 赋值分页输出

		$this->display(T('admin/security_taobao_list'));
	}
	public function addTaobaopage(){
		$this->display(T('admin/security_taobao_add'));
	}
	public function addTaobaoGood(){
		$data['goodid'] = I('post.goodid');//description
		$data['goodname'] = I('post.goodname');//description
		$data['addtime'] = I('post.addtime');//sort id
		$data['description'] = I('post.description');
		$Model = M('security_taobao');
		$Model->data($data)->add();
		//print_r($data);
		$this->success('Add Goods successfully!',U('Security/taobaoRecordPage'),1);

	}
	public function editTaobaoGoodsPage(){
		$goodid = I('get.goodid');
		$Model = M('security_taobao');
		$cond['goodid'] = $goodid;
		$result = $Model->where($cond)->find();
		if(!empty($result)){
			$this->assign('goods',$result);
			$this->display(T('admin/security_taobao_edit'));
		}
		else{
			$this->error('Goods #'.$goodid. ' no exist!',U('Security/taobaoRecordPage'),1);
		}
		//dump($result);

	}
	public function updateTaobaoGoods(){
		$cond['goodid'] = I('post.goodid');
		$data['goodname'] = I('post.goodname');//description
		$data['addtime'] = I('post.addtime');//sort id
		$data['description'] = I('post.description');
		$Model = M('security_taobao');
		$flag = $Model->where($cond)->save($data);
		$this->success('Update Goods  #'.$cond['goodid']. ' successfully!',U('Security/taobaoRecordPage'),1);

	}
	public function delTaobaoGood(){
		$cond['goodid'] = I('get.goodid');
		$Model = M('security_taobao');
		$result = $Model->field("imagename")->where($cond)->find();
		$Model->where($cond)->delete();
		//print_r($result);
		if($result["imagename"] != ""){
			$imageurl = "./".C('UPLOAD_PATH')."/".$result["imagename"];
			//$Model->where($cond)->delete();
			//echo $imageurl;
			if(file_exists($imageurl)){
				unlink($imageurl);
			}
		}

		$this->success('Delete Goods #'.$cond['goodid']. ' successfully!',U('Security/taobaoRecordPage'),1);
	}
	public function uploadTBGoodsPicPage(){
		$goodid = I('get.goodid');
		$Model = M('security_taobao');
		$cond['goodid'] = $goodid;
		$result = $Model->where($cond)->find();
		if(!empty($result)){
			$this->assign('goods',$result);
			$this->display(T('admin/security_taobao_addpic'));
		}
		else{
			$this->error('Goods #'.$goodid. ' no exist!',U('Security/taobaoRecordPage'),1);
		}
	}
	public function addTaobaoGoodPic(){
		$gid = I('get.gid');
		$config = array(
			'key' =>'file_upload',
      'maxSize' => 31457280,
      'rootPath'  =>"./".C('UPLOAD_PATH')."/",
      'savePath'  =>'',
      'saveName' => time().'_'.$gid,
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
			$cell["deleteUrl"] = $gid;
			$cell["name"] = $file["savename"];
			$cell["size"] = $file["size"];
			$cell["thumbnailUrl"] = __ROOT__."/".C('UPLOAD_PATH')."/".$file["savename"];
			$cell["type"] = $file["type"];
			$cell["url"] = __ROOT__."/".C('UPLOAD_PATH')."/".$file["savename"];
			array_push($dataset,$cell);
			$Model = M('security_taobao');
			$cond['goodid'] = $gid;
			$goods = $Model->where($cond)->find();
			$datas['imagename'] = $file["savename"];
			if(!empty($goods)){
				$Model->where($cond)->save($datas);
			}

		}
		$res = array();
		$res["files"]= $dataset;
		//$this->error($pic_src);
		$this->ajaxReturn($res);
		//$this->display(T('admin/security_taobao_add'));
	}


}
