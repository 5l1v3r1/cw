<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
use Org\Util\Auth;
class CommonController extends Controller
{
	public function _initialize()
	{
		ini_set('date.timezone','Asia/Shanghai');

		if(empty(session('admin_uid')))
		{
			//echo __SELF__;
			//echo __APP__;
			$urltmp = str_replace(__APP__."/","",__SELF__);
			//echo $urltmp;
			//echo $urltmp;
			$urltmpenc = "";
			if(!empty($urltmp)){
				$urltmpenc = str_replace(array('+', '/'), array('-', '_'), base64_encode($urltmp));
				//echo $urltmpenc;
			}
			$this->error(C('LOGIN_TIPS'),U('Login/index?redirect='.$urltmpenc.''),3);
			//exit(0);
			return 0;
		}else//whether is superadmin or not
		{
			$pwdtxt = encryptDecrypt('3330', session('admin_uid'),0);
			//echo $pwdtxt;
			//exit();
			$auth =new Auth();
			//echo MODULE_NAME.'-'.CONTROLLER_NAME.'-'.ACTION_NAME;
			//echo cookie('admin_uid');
			//echo $pwdtxt."<br>";
			//echo substr($pwdtxt , 17);
			//exit(0);
			if(!$auth->check(strtolower(MODULE_NAME.'-'.CONTROLLER_NAME.'-'.ACTION_NAME),substr($pwdtxt , 17)))
			{
				//echo U('Login/index?modulename='.CONTROLLER_NAME.'');
				$this->error(C('PERMISSION_DENIED_WARNING'), U('Login/index'),3);
				return 0;
			}
			// incomplte orders num
			/*

				0. guest have no paid gurrentee
				1. guest have paid gurrentee
				2. guest have pain all money
				-->
				<!--
				0. no
				1. worker is doing
				2. worker has completed and no pay
				3. worker has completed and paid
			*/
			$Model = M('orders');
			$cin = $Model->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_guest_order.g_state != 2 OR db_worker_order.w_state != 3')->count();
			$cin1 = $Model->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('(db_guest_order.g_state != 2 OR db_worker_order.w_state != 3) AND curdate() >= date_sub(db_worker_order.w_deadlin,interval 1 day)')->count();
			$cin_g_nopaid = $Model->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_guest_order.g_state = 0')->count();
			$cin_g_paid = $Model->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_guest_order.g_state = 1')->count();
			//$cin_g_paidall = $Model->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_guest_order.g_state = 2 and db_worker_order.w_state != 3')->count();
			$cin_w_doing = $Model->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_worker_order.w_state = 1')->count();
			$cin_w_com_nopaid = $Model->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_worker_order.w_state = 2')->count();
			//$cin_w_com_paid = $Model->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_worker_order.w_state = 3')->count();
			$cin_w_unset = $Model->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_worker_order.wxid is null')->count();
			//echo $cin_w_unset;

			$cin_g_nopaid_per = round(($cin_g_nopaid/$cin)*100,2);
			$cin_g_paid_per = round(($cin_g_paid/$cin)*100,2);
			//$cin_g_paidall_per = round(($cin_g_paidall/$cin)*100,2);
			$cin_w_doing_per = round(($cin_w_doing/$cin)*100,2);
			$cin_w_com_nopaid_per = round(($cin_w_com_nopaid/$cin)*100,2);
			//$cin_w_com_paid_per = round(($cin_w_com_paid/$cin)*100,2);
			$cin_w_unset_per = round(($cin_w_unset/$cin)*100,2);

			$this->assign('cin',$cin);// incomplte orders num
			$this->assign('cin1',$cin1);// incomplte orders warning num
			$this->assign('cin_g_nopaid',$cin_g_nopaid);// incomplte orders warning num
			$this->assign('cin_g_nopaid_per',$cin_g_nopaid_per);// incomplte orders warning num

			$this->assign('cin_g_paid',$cin_g_paid);// incomplte orders warning num
			$this->assign('cin_g_paid_per',$cin_g_paid_per);// incomplte orders warning num

			$this->assign('cin_w_doing',$cin_w_doing);// incomplte orders warning num
			$this->assign('cin_w_doing_per',$cin_w_doing_per);// incomplte orders warning num

			$this->assign('cin_w_unset',$cin_w_unset);// incomplte orders warning num
			$this->assign('cin_w_unset_per',$cin_w_unset_per);// incomplte orders warning num

			$this->assign('cin_w_com_nopaid',$cin_w_com_nopaid);// incomplte orders warning num
			$this->assign('cin_w_com_nopaid_per',$cin_w_com_nopaid_per);// incomplte orders warning num

			//$this->assign('cin_w_com_nopaid',$cin_w_com_nopaid);// incomplte orders warning num
			//$this->assign('cin_w_com_paid',$cin_w_com_paid);// incomplte orders warning num
			//$this->assign('cin_w_unset',$cin_w_unset);// incomplte orders warning num


			/* currency types*/
			$Model = M('configure_exchange');
			$items = $Model->select();
			$this->assign('currencies',$items);
			/* get default date */
			$dateform = date("Y-m-d",time());
			$this->assign('dateform',$dateform);

			/*shortcut*/
			$sts = [];
			$Model = M('configure_shortcut');
			$sts = $Model->field("db_configure_shortcut.ID,db_configure_shortcut.sortid,db_configure_shortcut.icon,db_configure_shortcut.style,db_configure_shortcut.ruleid,db_configure_shortcut.description,db_auth_rule.name")->join('left join db_auth_rule on db_configure_shortcut.ruleid = db_auth_rule.id')->order('db_configure_shortcut.sortid asc')->select();
			foreach($sts as $k=>$v){
				$news = [];
				$news = explode("-",$v['name']);
				$v['name'] = "";
				$v['name'] = $news[1]."/".$news[2];
				$sts[$k] = $v;
			}
			$this->assign('g_sts',$sts);

		}
	}
}
