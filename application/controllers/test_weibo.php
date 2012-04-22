<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test_weibo extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->library('weibo');
    }

    public function index(){
        if(isset($_SESSION['auth'])){
            return $this->auth_succeed();
        }
        $codeurl = $this->weibo->auth->getAuthorizeURL(WEIBO_CALLBACK_URL);
        redirect($codeurl);
    }

    public function callback(){
        $auth = null;
        if(isset($_REQUEST['code'])){
            $keys = array();
            $keys['code'] = $_REQUEST['code'];
            $keys['redirect_uri'] = WEIBO_CALLBACK_URL;
            try{
                $auth = $this->weibo->auth->getAccessToken('code',$keys);
            }catch(OAuthException $e){}
        }
        if($auth){
            $_SESSION['auth'] = $auth;
            setcookie('weibojs_'.$this->weibo->auth->client_id,http_build_query($auth));
            return $this->auth_succeed();
        }else{
            echo '授权失败！';
        }
    }

    public function show(){
        var_dump($this->weibo->client()->users_show($_SESSION['weibo_userid']));
    }

    protected function auth_succeed(){
        $res = $this->weibo->client()->get_uid();
        if(!isset($res['error_code'])){
            $_SESSION['weibo_userid'] = $res['uid'];
            $this->show();
        }else{
            unset($_SESSION['auth']);
            unset($_SESSION['weibo_userid']);
            $this->index();
        }
    }

};


