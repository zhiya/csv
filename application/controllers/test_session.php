<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test_session extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->session();
    }

    public function index(){
        if($this->session->userdata('logined')){
            echo "welcome back " . $this->session->userdata('username') . " !<br><hr><br>";
            var_dump($this->session->all_userdata());
        }else{
            echo "login first!";
        }
    }

    public function login(){
        if(!isset($_GET['username'])){
            echo "you need username to login!";
        }else{
            $this->session->set_userdata('logined',true);
            $this->session->set_userdata('username',$_GET['username']);
            echo "login succeed!";
        }
    }

    public function logout(){
        if($this->session->userdata('logined')){
            $this->session->unset_userdata('logined');
            $this->session->unset_userdata('username');
            echo "logout succeed!";
        }else{
            echo "you haven't logined yet!";
        }
    }
}

