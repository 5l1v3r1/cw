<?php
namespace Admin\Controller;
use Think\Controller;
class FinanceController extends CommonController {
	public function assetsPage(){
		$Model = M('finance_assets');
		$assets = $Model ->select();
		foreach($assets as $k=>$v){
			$datetime_start = new \DateTime($v["startdate"]);
	    $datetime_end = new \DateTime($v["enddate"]);
			$daystep = $datetime_start->diff($datetime_end)->days + 1;
			//print($daystep);
			$v["interest"] = round($v["amount"] * ($v["year_rate"] * 0.01/365) *$daystep,3);
			$assets[$k] = $v;
		}
		//print_r($assets[$k]);
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
		$data['attr'] = I('post.attr');
		$data['description'] = I('post.description');
		$Model = M('finance_assets');
		$maxid = $Model->max('id');
		$data['id'] = $maxid + 1;
		$Model->data($data)->add();
		//print_r($data);
		$this->success('Add Assets successfully!',U('Finance/assetsPage'),1);
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
	public function editAssets(){
		$cond['id'] = I('post.id');
		$data['name'] = I('post.name');//description
		$data['bank'] = I('post.bank');//sort id docurls
		$data['moneytype'] = I('post.moneytype');//sort id
		$data['amount'] = I('post.amount');//description
		$data['year_rate'] = I('post.yearrate');//sort id docurls
		$data['startdate'] = I('post.startdate');//sort id
		$data['enddate'] = I('post.enddate');
		$data['description'] = I('post.description');

		$Model = M('finance_assets');
		$flag = $Model->where($cond)->save($data);
		$this->success('Update Assets '. $data['name'] .' successfully!',U('Finance/assetsPage'),1);
	}
	public function delassets(){
		$cond['id'] = I('get.id');
		$Model = M('finance_assets');
		$Model->where($cond)->delete();
		$this->success('Delete Assets successfully!',U('Finance/assetsPage'),1);
	}


}
