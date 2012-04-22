<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test_data{
    public $id = null;
    public $name = null;
    public $age = null;
    public $address = null;

    public function __construct($data=null){
        if(is_array($data)){
            foreach(array(
                'name','age','address') as $k=>$v){
                if(isset($data[$v])){
                    $this->$v = $data[$v];
                }
            }
        }
    }

    public function is_valid(){
        if(!$this->name) return false;
        if(!$this->age) return false;
        if(!$this->address) return false;
        return true;
    }
}

class Test_model extends MY_Model{
    public function __construct(){
        parent::__construct('test');
    }
};

