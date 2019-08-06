<?php
namespace Admin\Controller;
use Think\Controller;
class FinanceController extends CommonController {
	public function assetsPage(){
		$Model = M('finance_assets');
		$assets = $Model ->select();
		$this->assign('assets',$assets);
		$this->display(T('admin/finance_assets_page'));

	}
	public function assetsAddPage(){
		echo "add";
		$this->display(T('admin/finance_assets_add'));

	}
	public function addAssets(){
		$data['name'] = I('post.name');//description
		$data['bank'] = I('post.bank');//sort id docurls
		$data['moneytype'] = I('post.moneytype');//sort id
		$data['amount'] = I('post.amount');//description
		$data['year_rate'] = I('post.yearrate');//sort id docurls
		$data['startdate'] = I('post.startdate');//sort id
		$data['enddate'] = I('post.enddate');
		$data['description'] = I('post.description');
		$Model = M('finance_assets');
		$maxid = $Model->max('id');
		$data['id'] = $maxid + 1;
		$Model->data($data)->add();
		print_r($data);
		//$this->success('Add Assets successfully!',U('Finance/assetsPage'),1);
	}
	public function editAssetsPage(){
		//finance_assets_edit.html
		$id = I('get.id');
		$Model = M('finance_assets');
		$cond['id'] = $id;
		$result = $Model->where($cond)->find($cond);
		//dump($result);
		$this->assign('assets',$result);
		$this->display(T('admin/finance_assets_edit'));
	}
	public function delassets(){
	}


}
