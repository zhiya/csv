<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'helpers/saetv2.ex.class.php');

class Weibo {
    public $auth = null;
    protected $client = null;

    public function __construct(){
        $this->auth = new SaeToAuthV2(WEIBO_APPKEY,WEIBO_APPSECRETKEY);
    }

    public function client(){
        if(!$this->client){
            $this->client = new SaeTClientV2(WEIBO_APPKEY,WEIBO_APPSECRETKEY,$_SESSION['auth']['access_token']);
        }
        return $this->client;
    }

};

