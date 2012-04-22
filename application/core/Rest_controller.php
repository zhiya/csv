<?php if(!defined('BASEPATH')) exit('Direct script access denied!');

/**
 * REST API Controller
 */
class Rest_controller extends CI_Controller{

    protected $supported_formats = array(
        'xml' => 'text/xml',
        'json' => 'application/json',
        'jsonp' => 'application/javascript'
    );

    protected $methods = array();
    protected $request = NULL;
    protected $response = NULL;
    protected $rest_route = true;
    protected $rest_action_pre = 'action_';

    public function __construct(){
        parent::__construct();
        $this->_detect_method();
        $this->_dectec_output_format();
        $this->load->library('format');
    }

    /**
     * 
     * 需要登录权限
     */
    protected function need_login(){
        if( !$this->session->userdata('logined') ){
            $this->failed("Need login first!");
        }
    }

    /**
     * 需要管理员权限
     */
    protected function need_admin(){
        //TODO 管理员权限认证
        $this->failed("No permission!");
    }

    /**
     * 
     * 获取请求参数
     * @param $name 参数名称
     */
    public function param($name){
        if(isset($_GET[$name])){
            return $_GET[$name];
        }
        if(isset($_POST[$name])){
            return $_POST[$name];
        }
        return null;
    }

    /**
     * 
     * 参数是否存在
     * @param $name 参数名称
     */
    public function has_param($name){
        if(isset($_GET[$name])){
            return true;
        }
        if(isset($_POST[$name])){
            return true;
        }
        return false;
    }

    /**
     * 
     * 访问接口成功返回
     * @param $data 返回数据内容
     */
    public function succeed($data = array()){
        $this->result(null,$data);
    }

    /**
     * 
     * 接口访问失败
     * @param $error 返回失败描述
     */
    public function failed($error){
        $this->result($error, null);
    }

    /**
     * 
     * 接口访问结果
     * @param $error 返回错误描述
     * @param $data 返回数据内容
     */
    public function result($error,$data){
        if($error){
            $result['succeed'] = false;
            $result['data'] = $error;
        }else{
            $result['succeed'] = true;
            $result['data'] = $data;
        }
        if (method_exists($this, '_format_' . $this->response->format))
        {
            //先查找本地输出方法
            header('Content-Type: ' . $this->supported_formats[$this->response->format] . '; charset=UTF-8');
            $output = $this->{'_format_' . $this->response->format}($result);
        }elseif (method_exists($this->format, 'to_' . $this->response->format))
        {
            //再查找外部类输出方法
            header('Content-Type: ' . $this->supported_formats[$this->response->format] . '; charset=UTF-8');
            $output = $this->format->factory($result)->{'to_' . $this->response->format}();
        } else {
            //否则直接输出
            $output = $data;
        }

        //HTTP返回码？
        //$http_code = 200;
        //header('HTTP/1.1: ' . $http_code);
        //header('Status: ' . $http_code);
        header('Content-Length: ' . strlen($output));
        header('Access-Control-Allow-Origin: *');

        exit($output);
    }

    public function index(){
        exit();
    }

    /**
     * 
     * 搜集参数集合到数组
     * @param $keys 必须是数组！
     */
    protected function select_params($keys){
        $data = null;
        foreach( $keys as $i=>$k ){
            if( $this->has_param($k) ){
                $data[$k] = $this->param($k);
            }
        }
        return $data;
    }

    //本地方法输出JSON格式
    protected function _format_json($data = array())
    {
        return preg_replace("#\\\u([0-9a-f]+)#ie","iconv('UCS-2BE','UTF-8',pack('H4','\\1'))",json_encode($data));
    }

    //本地方法输出JSONP格式
    protected function _format_jsonp($data = array())
    {
        return $this->param('callback') . '(' . $this->_format_json($data) . ');';
    }

    /**
     * 
     * REST API访问URL重置
     * @param $object_called
     * @param $arguments
     */
    public function _remap($object_called, $arguments)
    {
        $this->_api_check();
        if( !$this->rest_route ) {
            call_user_func_array(array($this, $object_called), $arguments);
            return ;
        }

        $pattern = '/^(.*)\.(' . implode('|', array_keys($this->supported_formats)) . ')$/';
        if (preg_match($pattern, $object_called, $matches))
        {
            $object_called = $matches[1];
        }

        $controller_method = $this->rest_action_pre . $object_called;

        // Sure it exists, but can they do anything with it?
        if ( ! method_exists($this, $controller_method))
        {
            $this->failed("Invalid action!");
        }
        call_user_func_array(array($this, $controller_method), $arguments);
    }

    protected function _detect_input_format()
    {
        if ($this->input->server('CONTENT_TYPE'))
        {
            // Check all formats against the HTTP_ACCEPT header
            foreach ($this->_supported_formats as $format => $mime)
            {
                if (strpos($match = $this->input->server('CONTENT_TYPE'), ';'))
                {
                    $match = current(explode(';', $match));
                }

                if ($match == $mime)
                {
                    return $format;
                }
            }
        }

        return NULL;
    }

    protected function _dectec_output_format(){
        $this->response->format = $this->param('format');
        if ( !$this->response->format || !array_key_exists(
            $this->response->format,$this->supported_formats) ) {
                $this->response->format = config_item('default_format');
            }
    }

    protected function _detect_method()
    {
        $method = strtolower($this->input->server('REQUEST_METHOD'));

        if ($this->config->item('enable_emulate_request') && $this->input->post('_method'))
        {
            $method =  $this->input->post('_method');
        }

        if (in_array($method, array('get', 'delete', 'post', 'put')))
        {
            $this->request->method =  $method;
        }

        $this->request->method = 'post';
    }

    protected function _api_accessable(){
        if( ENVIRONMENT == 'production' && !isset($_POST['submit']) ){
            //产品上线必须限制访问
            return false;
        }
        return true;
    }

    protected function _api_check(){
        if( !$this->_api_accessable() ){
            exit();
        }
    }

}
