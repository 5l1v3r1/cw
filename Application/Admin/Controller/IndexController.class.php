<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
  public function showlogin(){
    cookie('admin_uid',null);
    $this->display(T('admin/login'));
  }
  public function index(){
      $this->display(T('admin/ad'));
  }

}
