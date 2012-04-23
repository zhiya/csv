<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test_model extends MY_Model{
    public function __construct(){
        parent::__construct('test');
        $this->select('id,name,age,address');
    }

    public function is_valid($data){
        if(!isset($data['name'])) return false;
        if(!isset($data['age'])) return false;
        if(!isset($data['address'])) return false;
        return true;
    }
};

