<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'core/Rest_controller.php');

class Test_rest extends Rest_controller {
    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('test_model');
    }

    public function action_list(){
        $res = $this->test_model->select('id,name')->fetch();
        $this->succeed($res);
    }

    public function action_add(){
        $d = new Test_data($_GET);
        if(!$d->is_valid())return $this->failed('参数解析无效！');
        if($this->test_model->add($d)){
            $this->succeed();
        }else{
            $this->failed('添加失败！');
        }
    }
};

