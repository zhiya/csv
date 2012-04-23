<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test_db extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('test_model');
    }

    public function index(){
        $res = $this->test_model->fetch();
        echo var_dump($res) . '<hr>';
    }

    public function add_entry(){
        echo $this->test_model->add(array(
            'name'=> $_GET['name'],
            'age'=> $_GET['age'],
            'address'=> $_GET['address']
        ))?"添加成功！":"添加失败！";
    }
}

