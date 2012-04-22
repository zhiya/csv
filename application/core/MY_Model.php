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
     * 根据资源标志获取数据
     * @param $id
     */
    public function get($id){
        $this->db->select($this->select)
            ->where('id',$id)
            ->limit(1);
        return $this->db->get($this->tbname)->row_object(0);
    }

    /**
     * 
     * 模型数据是否存在
     * @param $id 指定模型数据标志
     */
    public function exists($id){
        $this->db->select('id')
            ->where('id',$id)
            ->limit(1);
        return ($this->db->get($this->tbname)->num_rows()>0);
    }

    /**
     * 确保数据条目存在，返回本条数据ID
     */
    public function ensure($data){
        $this->db->select('id')
            ->where($data)
            ->limit(1);
        $res = $this->db->get($this->tbname);
        $id = 0;
        if($res->num_rows()>0){
            $id = $res->row_object(0)->id;
        }else if($this->add($this->make_data($data))){
            $id = $this->db->insert_id();
        }
        return $id;
    }

    /**
     * 检查指定数据是否存在，返回本条数据ID(多个只返回第一个)
     */
    public function check($data){
        if(!is_array($data))return 0;
        $this->db->select('id')
            ->where($data)
            ->limit(1);
        $res = $this->db->get($this->tbname);
        if($res->num_rows()>0){
            return $res->row_object(0)->id;
        }
        return 0;
    }

    /**
     * 
     * 添加模型数据
     * @param $data 模型数据信息，模型结构须提供is_valid接口
     */
    public function add($data){
        if( !$data || !$data->is_valid() ){
            return false;
        }
        return $this->db->insert($this->tbname,$data);
    }

    /**
     * 子类继承，返回一个可添加的实例化对象
     */
    public function make_data($arraydata){
        return null;
    }

    /**
     * 
     * 移除模型数据
     * @param $id 模型数据标志
     */
    public function remove($id){
        if( !$this->exists($id) ){
            return false;
        }
        $this->db->where('id',$id)->limit(1);
        return $this->db->delete($this->tbname);
    }

    /**
     * 
     * 更新模型数据
     * @param $id 数据标志
     * @param $data 数据信息数组
     */
    public function update($id,$data){
        if( !$data || !$this->exists($id) ){
            return RC_GLOBAL_InvalidID;
        }
        $this->db->where('id',$id)->limit(1);
        return $this->db->update($this->tbname,$data);
    }
}


