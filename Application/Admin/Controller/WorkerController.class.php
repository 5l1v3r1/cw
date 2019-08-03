<?php
namespace Admin\Controller;
use Think\Controller;
class WorkerController extends CommonController {
    public function workerlist(){
      $search =I('post.techsearch');
      $Model = M('workers');
      $techstr = "";
      $pp = 0;
      if(isset($_GET["p"])){
        $pp = $_GET["p"];
      }
      $type = I('get.type');// 0  all worker 1 Income = 0 worker 2 old worker 3 dead worker  4 free user order by income 4  category (C++ ...)
      if(!isset($type)){
      $type = 0;
      }
      //echo $type;
      $workersres = array();
      /*
      <option value="10">addtime desc</option>
      <option value="11">addtime asc</option>
      <option value="20">remark desc</option>
      <option value="21">remark asc</option>
      <option value="30">income desc</option>
      <option value="31">income asc</option>
      <option value="50">projects desc</option>
      <option value="51">projects asc</option>
      */
      $sortword = 'addtime';
      $sortlist = 'SORT_ASC';
      $sortby =I('post.sortby');
      switch($sortby){
        case 10:
          $sortword = 'addtime';
          $sortlist = 'SORT_DESC';
        break;
        case 11:
          $sortword = 'addtime';
          $sortlist = 'SORT_ASC';
        break;
        case 20:
          $sortword = 'remark';
          $sortlist = 'SORT_DESC';
        break;
        case 21:
          $sortword = 'remark';
          $sortlist = 'SORT_ASC';
        break;
        case 30:
          $sortword = 'income';
          $sortlist = 'SORT_DESC';
        break;
        case 31:
          $sortword = 'income';
          $sortlist = 'SORT_ASC';
        break;
        case 50:
          $sortword = 'orderall';
          $sortlist = 'SORT_DESC';
        break;
        case 51:
          $sortword = 'orderall';
          $sortlist = 'SORT_ASC';
        break;
        default:
          $sortword = 'addtime';
          $sortlist = 'SORT_ASC';
      }
      if(!empty($search))
      {

        $workers = $Model->order('addtime asc')->select();
        //dump($workers);
        $techstr = '';
        foreach($search as $v){
        	$techstr = $v.','.$techstr;
        }
        $techstr = rtrim($techstr,",");
        //dump($techstr);
        $worklist = [];
        foreach($workers as $k=>$v){
        	$MM = M('worker_tech');

        	$techeslist = $MM->join('left join db_technologies on db_worker_tech.techid = db_technologies.techid')->field('db_worker_tech.wxid')->where('db_worker_tech.wxid = "'.$v['wxid'].'" and db_worker_tech.techid in('.$techstr.')')->order('db_technologies.sortid asc')->select();
        	//dump($techeslist);
        	if($techeslist != NULL){
        		array_push($worklist ,$techeslist[0]['wxid']);
        	}
        }
        //dump($worklist);
        //$workliststr = rtrim($workliststr,",");
        //dump($workliststr);
        $workeroutput = [];
        foreach($workers as $k=>$v){
          $MM = M('worker_tech');
          if(in_array($v['wxid'],$worklist)){
          	$teches = $MM->join('left join db_technologies on db_worker_tech.techid = db_technologies.techid')->field('db_worker_tech.techid,db_technologies.content')->where('db_worker_tech.wxid = "'.$v['wxid'].'"')->order('db_technologies.sortid asc')->select();
          	$v['techarr'] = $teches;
          	$workitem = getWorkerInfo($v['wxid']);
          	$v['ordercomplete'] = $workitem[0];//$ordercomplete;
          	$v['orderonging'] = $workitem[3] ;//$orderonging;
          	$v['orderunpaid'] = $workitem[4] ;//$order unpaid;
          	$v['orderall'] = $workitem[5];//$orderonging + $ordercomplete;
          	$v['income'] = $workitem[2];
          	$v['remark'] = round($workitem[1],2);

          	$workers[$k] = $v;
          	//array_push($workeroutput ,$workers[$k]);
          	switch($type){
          		case 1:
          			if($v['income'] == 0.00 && $workers[$k]["status"] == 0){
          			array_push($workersres,$workers[$k]);
          			}
          			break;
          		case 2:
          			if($v['income'] > 0.00 && $workers[$k]["status"] == 0){
          			array_push($workersres,$workers[$k]);
          			}
          			break;
          		case 3:
          			if($workers[$k]["status"] == 1){
          			array_push($workersres,$workers[$k]);
          			}
          			break;
          		case 4:
          			if($workers[$k]["orderonging"] == 0 && $workers[$k]["status"] == 0){
          			array_push($workersres,$workers[$k]);
          			}
          			break;
          		case 5:
          			if($workers[$k]["orderonging"] > 0 && $workers[$k]["status"] == 0){
          			array_push($workersres,$workers[$k]);
          			}
          			break;
          		default:
          			if($workers[$k]["status"] == 0){
          			array_push($workersres,$workers[$k]);
          			}
          	}
          }
      	}

            //dump($workeroutput);
            /*
            $Mtech = M('technologies');
            $teches = $Mtech->select();
            $workeroutput = arraySequence($workeroutput, 'remark', $sort = 'SORT_DESC');
            $this->assign('workers',$workeroutput);
            $this->assign('techstr',$techstr);
            $this->assign('teches',$teches);
            //dump($techstr);
            $this->display(T('admin/workers_list'));
            */
      }else
      {
			$workers = $Model->order('addtime asc')->select();
			$count = $Model->order('addtime asc')->count();
			//dump($workers);
			foreach($workers as $k=>$v){
				$MM = M('worker_tech');
				$map['wxid'] = $v['wxid'];
				$teches = $MM->join('left join db_technologies on db_worker_tech.techid = db_technologies.techid')->field('db_worker_tech.techid,db_technologies.content')->where('db_worker_tech.wxid = "'.$v['wxid'].'"')->order('db_technologies.sortid asc')->select();
				$v['techarr'] = $teches;
				/*order info*/
				$ORDER = M('orders');
				$workitem = getWorkerInfo($v['wxid']);

				$v['ordercomplete'] = $workitem[0];//$ordercomplete;
				$v['orderonging'] = $workitem[3] ;//$orderonging;
				$v['orderunpaid'] = $workitem[4] ;//$order unpaid;
				$v['orderall'] = $workitem[5];//$orderonging + $ordercomplete;
				$v['income'] = $workitem[2];
				$v['remark'] = round($workitem[1],2);

				$workers[$k] = $v;
				//dump($workers[$k]);
				//echo $k;
				switch($type){
					case 1:
					  if($v['income'] == 0.00 && $workers[$k]["status"] == 0){
						array_push($workersres,$workers[$k]);
					  }
					  break;
					case 2:
					  if($v['income'] > 0.00 && $workers[$k]["status"] == 0){
						array_push($workersres,$workers[$k]);
					  }
					  break;
					case 3:
					  if($workers[$k]["status"] == 1){
						array_push($workersres,$workers[$k]);
					  }
					  break;
					case 4:
					  if($workers[$k]["orderonging"] == 0 && $workers[$k]["status"] == 0){
						array_push($workersres,$workers[$k]);
					  }
					  break;
					case 5:
					  if($workers[$k]["orderonging"] > 0 && $workers[$k]["status"] == 0){
						array_push($workersres,$workers[$k]);
					  }
					  break;
					default:
					  if($workers[$k]["status"] == 0){
						array_push($workersres,$workers[$k]);
					  }
          }

        }


      }
      /*$Page = new \Think\Page(count($workersres),C('PAGE_LIMIT_WORKERS'));// 实例化分页类 传入总记录数和每页显示的记录数
      $Page->setConfig('prev','last');
      $Page->setConfig('next','next');
      $Page->setConfig('first','first');
      $Page->setConfig('last','last');
      $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% ');
      $Page->parameter["techsearch"]   =   urlencode($search);
      $show = $Page->show();// 分页显示输出
      $this->assign('page',$show);// 赋值分页输出*/
      $workersres = arraySequence($workersres, $sortword, $sortlist);
      $workersres = array_slice($workersres, $Page->firstRow,$Page->listRows);
      //dump($workers);
      $Mtech = M('technologies');
      $teches = $Mtech->order('sortid asc')->select();
      $this->assign('workers',$workersres);
      $this->assign('teches',$teches);
      $this->assign('techstr',$techstr);
      $this->assign('type',$type);
      $this->display(T('admin/workers_list'));


    }
    /* add new worker */
    public function workernewpage(){
      $Model = M('technologies');
      $content = $Model->order('sortid asc')->select();
      $this->assign('teches',$content);
      $this->display(T('admin/workers_add'));
    }
    public function workernew(){
      $data_['wxid'] = trim(I('post.wxid'));
      $Model = M('workers');
      $content = $Model->where($data_)->find();
      $teches = [];
      $teches = I('post.tech','','htmlspecialchars');//
      $attrarr = [];
      $attrarr = I('post.attrs','','htmlspecialchars');//
      //dump($teches);
      if(empty($content))
      {
        $data['wxid'] = $data_['wxid'];
        $data['wxname'] = I('post.wxname','','htmlspecialchars');//get email
        $data['email'] = I('post.email','','htmlspecialchars');//get email
        $data['description'] = I('post.description','','htmlspecialchars');//get email
        $data['addtime'] = date('Y-m-d H:i:s',time());
        $data['attrs'] = implode(",",$attrarr);
        //dump($data);
        $Model->data($data)->add();
        /* add to worker_tech*/
        $Model = M('worker_tech');
        foreach($teches as $k=>$v){
        	$map['wxid'] = $data['wxid'];
        	$map['techid'] = $v;
        	$Model->data($map)->add();
        }
        $this->success('Add a new Worker successfully!',U('Worker/workerlist'),1);
      }
      else
      {
        $this->error('Worker has existed already!', U('Worker/workernewpage'),2);

      }

    }
    /* edit worker*/
    public function workereditpage(){
    	$data_['wxid'] = I('get.wxid');
    	$Model = M('workers');
    	$worker = $Model->where($data_)->find();
    	$Mtech = M('technologies');
    	$teches = $Mtech->order('sortid asc')->select();
    	//dump($worker);
    	if(!empty($worker))
    	{
    		//dump($worker);
    		/* add to worker_tech*/
    		$MM = M('worker_tech');
    		$techesexit = $MM->join('inner join db_technologies on db_worker_tech.techid = db_technologies.techid')->field('db_worker_tech.techid')->where('db_worker_tech.wxid = "'.$data_['wxid'].'"')->order('db_technologies.sortid asc')->select();

    		$techstr = '';
    		foreach($techesexit as $k=>$v){
    			$techstr = $v['techid'].",".$techstr;
    		}
    		//dump($techstr);
    		$attrsche = ["v2ex","student","cs","abroad","other"];
    		$this->assign('teches',$teches);
    		$this->assign('techarr',$techstr);
    		$this->assign('attrsche',$attrsche);
    		$this->assign('worker',$worker);
    		$this->display(T('admin/workers_edit'));
    	}
    	else
    	{
    		$this->error('Worker has no existed !', U('Worker/workerlist'),2);

    	}

    }
    public function workerupdate(){
    	$data_['wxid'] = I('post.wxid');
    	$Model = M('workers');
    	$content = $Model->where($data_)->find();
    	$teches = [];
    	$teches = I('post.tech','','htmlspecialchars');//
    	//dump($teches);
    	$attrarr = [];
    	$attrarr = I('post.attrs','','htmlspecialchars');//
    	if(!empty($content))
    	{
    		$data['wxname'] = I('post.wxname','','htmlspecialchars');//get email
    		$data['email'] = I('post.email','','htmlspecialchars');//get email
    		$data['description'] = I('post.description','','htmlspecialchars');//get email
    		$data['attrs'] = implode(",",$attrarr);
    		$data['status'] = I('post.status','','htmlspecialchars');
    		//dump($data);
    		$Model->where($data_)->save($data);
    		/* add to worker_tech*/
    		$Model = M('worker_tech');
    		$Model->where($data_)->delete();
    		foreach($teches as $k=>$v){
    			$map['wxid'] = $data_['wxid'];
    			$map['techid'] = $v;
    			$Model->data($map)->add();
    		}
    		$this->success('Update Worker ^ '.$data['wxname'].' ^ successfully!',U('Worker/workerlist'),3);
    	}
    	else
    	{
    	  $this->error('Worker has no existed !', U('Worker/workerlist'),2);

    	}

    }
    /* delete a worker */
    public function workerdelete(){
    	$data_['wxid'] = I('get.wxid');
    	$Model = M('workers');
    	$Model->where($data_)->delete();
    	$this->success('Worker has deleted successfully!', U('Worker/workerlist'),2);

    }
    /* view worker detail */
    public function workerdetailpage(){
    	$m['wxid'] = I('get.wxid');
    	$W = M('workers');
    	$v = $W->where($m)->find();
    	//dump($v);
    	$MM = M('worker_tech');
    	$teches = $MM->join('left join db_technologies on db_worker_tech.techid = db_technologies.techid')->field('db_technologies.content')->where('db_worker_tech.wxid = "'.$v['wxid'].'"')->order('db_technologies.sortid asc')->select();
    	$v['teches'] = $teches;
    	/*order info*/
    	$ORDER = M('orders');
    	$orderlist = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_workers.wxid = "'.$v['wxid'].'"')->select();
    	/* complete orders*/
    	$orderincomelist = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_workers.wxid = "'.$v['wxid'].'" AND (db_guest_order.g_state != 2 OR db_worker_order.w_state != 3)')->select();

    	$workitem = getWorkerInfo($v['wxid']);
    	//dump($ordercomplete);
    	//dump($orderremark);
    	//dump($income);
    	//dump($orderonging);
    	$v['ordercomplete'] = $workitem[0];//$ordercomplete;
    	$v['orderonging'] = $workitem[3] ;//$orderonging;
    	$v['orderunpaid'] = $workitem[4] ;//$order unpaid;
    	$v['orderall'] = $workitem[5];//$orderonging + $ordercomplete;
    	$v['income'] = $workitem[2];
    	$v['remark'] = round($workitem[1],2);
    	$v['orderlist'] = $orderlist;
    	$v['orderincomelist'] = $orderincomelist;
    	$this->assign('workerinfo',$v);
    	$this->display(T('admin/workers_detail'));
    }
    /* worker analysis */
    public function showDataAnalysisPage(){
    	$workertotal = [];
    	$workertotal = getAllWorkerData();
    	$this->assign('workertotal',$workertotal);
          $workerpiedata = [];
          $item = [];
          $item["label"] = "doing";
          $item["count"] = $workertotal["worker_doing"];
          array_push($workerpiedata,$item);
          $item = [];
          $item["label"] = "unpaid";
          $item["count"] = $workertotal["worker_unpaid"];
          array_push($workerpiedata,$item);
    	$item = [];
          $item["label"] = "free(-unpaid)";
          $item["count"] = $workertotal["worker_total"] - $workertotal["worker_unpaid"];
          array_push($workerpiedata,$item);
          $this->assign('workerpiedata',$workerpiedata);

    	$this->display(T('admin/workers_analysis'));

    }
    public function getTechWorkerDatas(){
    	$res = [];
      $res = getTechWorkerDatas();
      //print_r($res);
      $this->ajaxReturn($res);

    }
	/* add new worker */
    public function workerRecommandPage(){
      $Model = M('workers');
		$workers = $Model->field("wxid as email,wxname as name")->where("status = 0")->order('wxname asc')->select();
		$this->assign('workers',$workers);
		/* get technology list*/
		$Model = M('technologies');
		$teches = $Model->field("techid as email,content as name,attr as attr")->select();
		$this->assign('teches',$teches);
      $this->display(T('admin/workers_recommand'));
    }
}
