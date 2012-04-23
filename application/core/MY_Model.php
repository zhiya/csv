<?php if(!defined('BASEPATH'))exit('Direct script access denied!');

/*
 *带有ID的实体对象数据模型基类
 */
class MY_Model extends CI_Model{
    protected $tbname = '';
    protected $select = 'id';
    protected $orderby = 'id';
    protected $orderasc = true;
    protected $distinct = false;

    public function __construct($tbname=''){
        parent::__construct();
        $this->tbname = $tbname;
    }

    /**
     * 
     * 抓取要取得的数据信息字段，逗号分隔
     * @param $select
     */
    public function select($select){
        $this->select = $select;
        return $this;
    }

    /**
     * 
     * 抓取信息时的排序设置
     * @param $by 以哪个字段为线索
     * @param $asc 是不是按递增顺序
     */
    public function order_by($by,$asc=true){
        $this->orderby = $by;
        $this->orderasc = $asc;
        return $this;
    }

    /**
     * 是否在获取数据时除去冗余
     */
    public function distinct($dist=true){
        $this->distinct = $dist;
        return $this;
    }

    /**
     * 
     * 从指定索引开始获取一页数据
     * @param $from 起始索引>=0
     * @param $cond 条件数组
     */
    public function fetch($from=0,$cond=null){
        $q = $this->db->select($this->select);
        if($cond && is_array($cond))$q->where($cond);

        if($this->distinct)$q->distinct();

        $q->limit(DB_FETCH_PAGESIZE,$from)
            ->order_by($this->orderby,$this->orderasc?'ASC':'DESC');

        $this->distinct = false;//不常用！
        return $this->db->get($this->tbname)->result();
    }

    /**
     * 
     * 根据指定条件获取单一记录
     * @param $mixed 资源ID或者条件数组
     */
    public function get($mixed){
        $q = $this->db->select($this->select);
        if(is_array($mixed)){
            $q->where($mixed);
        }else{
            $q->where('id',$mixed);
        }
        $q->limit(1);
        return $this->db->get($this->tbname)->row_object(0);
    }

    /**
     * 确保数据记录存在，返回本记录ID
     */
    public function ensure($data){
        $this->db->select('id')
            ->where($data)
            ->limit(1);
        $res = $this->db->get($this->tbname);
        $id = 0;
        if($res->num_rows()>0){
            $id = $res->row_object(0)->id;
        }else if($this->add($data)){
            $id = $this->db->insert_id();
        }
        return $id;
    }

    /**
     * 检查指定数据是否存在，返回本条数据ID(多个只返回第一个)
     * @param $mixed 指定数据条件(ID或条件数组)
     */
    public function check($mixed){
        $q = $this->db->select('id');
        if(is_array($mixed)){
            $q->where($mixed);
        }else{
            $q->where('id',$mixed);
        }
        $res = $q->limit(1)->get($this->tbname);
        if($res->num_rows()>0){
            return $res->row_object(0)->id;
        }
        return false;
    }

    /**
     * 检查数组数据是否可以添加到数据库
     * 需要子类继承，根据不同表格字段进行判断
     *
     * @param @data 数组数据
     */
    public function is_addable($data){
        if(!$data || !is_array($data)) return false;
        return true;
    }

    /**
     * 
     * 添加数组数据
     * @param $data 数组，子类提供is_addable接口
     */
    public function add($data){
        if( !$data || !$this->is_addable($data) ){
            return false;
        }
        if($this->db->insert($this->tbname,$data)){
            return $this->db->insert_id();
        }
        return false;
    }

    /**
     * 
     * 删除一条数据记录
     * @param $mixed 指定删除条件或记录ID
     */
    public function remove($mixed){
        if(is_array($mixed)){
            $this->db->where($mixed);
        }else{
            $this->db->where('id',$mixed);
        }
        return $this->db->limit(1)->delete($this->tbname);
    }

    /**
     * 
     * 更新模型数据
     * @param $mixed 指定记录ID或判定条件数组
     * @param $data 更新内容数组
     */
    public function update($mixed,$data){
        if(!$mixed || !$data) return false;
        if(is_array($mixed)){
            $this->db->where($mixed);
        }else{
            $this->db->where('id',$mixed);
        }
        return $this->db->limit(1)->update($this->tbname,$data);
    }
}


