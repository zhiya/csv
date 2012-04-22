<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test_db extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('test_model');
    }

    public function index(){
        $res = $this->test_model->select('*')->fetch();
        echo var_dump($res) . '<hr>';
    }

    public function add_entry(){
        $d = new Test_data($_GET);
        if(!$d->is_valid()){
            echo "无效参数！";
            exit();
        }
        echo $this->test_model->add($d)?"添加成功！":"添加失败！";
        exit();
    }
}

