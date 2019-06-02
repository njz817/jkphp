<?php
namespace framework\db;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/20 0020
 * Time: 下午 3:24
 */
class Query
{
    // 数据库连接
    protected static $link = null;

    protected $conditionBox = [
        'field' => '*',
        'table' => '',
        'where' => '',
        'order' => '',
        'limit' => ''
    ];

    protected static $boxQueryTemplate = [
        'select' => 'select %s from %s%s%s%s',
        'insert' => 'insert into %s(%s) VALUES (%s)',
        'update' => 'update %s set %s%s',
        'delete' => 'delete from %s%s'
    ];

    /**
     * Query constructor.
     * @param array $dbinfo
     */
    public function __construct(array $dbinfo)
    {
        self::$link || self::connect($dbinfo);
    }

    /**
     * 数据库连接
     * @param $dbinfo
     */
    public static function connect($dbinfo)
    {
        self::$link = mysqli_connect($dbinfo['DB_HOST'],$dbinfo['DB_USER'],$dbinfo['DB_PASS'],$dbinfo['DB_NAME']);
        if( !self::$link ){
            self::errlog(mysqli_connect_error());
        }
        if( !mysqli_set_charset(self::$link,$dbinfo['DB_CHARSET']) ){
            self::errlog(mysqli_error(self::$link));
        }
    }

    /**
     * 设置表名
     * @param $tabname
     */
    public function table($tabname)
    {
        $this->conditionBox['table'] = $tabname;
        return $this;
    }

    /**
     * 设置查询字段
     * @param $fields  f1,f2  or  [f1,f2]
     * @return bool
     */
    public function field($fields)
    {
        if( is_array($fields) ){
            $fields = implode(',',$fields);
        }
        $this->conditionBox['field'] = $fields;
        return $this;
    }

    /**
     * @param $condition 查询条件
     */
    public function where($condition)
    {
        $this->conditionBox['where'] = " where $condition";
        return $this;
    }

    /**
     * 查询方法
     * @return array
     */
    public function select()
    {
        $sql = sprintf(self::$boxQueryTemplate['select'],
            $this->conditionBox['field'],
            $this->conditionBox['table'],
            $this->conditionBox['where'],
            $this->conditionBox['order'],
            $this->conditionBox['limit']
        );
        $rest = $this->query($sql);
        $data = [];
        if( $rest && mysqli_num_rows($rest) > 0 ){
            while ($rows = mysqli_fetch_assoc($rest)){
                $data[] = $rows;
            }
        }
        return $data;
    }

    /**
     * 插入数据库
     * @param array $data
     */
    public function insert(array $data)
    {
        $fields = $values = '';
        foreach ($data as $field=>$val){
            $fields .= $field . ',';
            $values .= "'$val'" . ',';
        }
        $fields = substr($fields,0,-1);
        $values = substr($values,0,-1);
        $sql = sprintf(self::$boxQueryTemplate['insert'], $this->conditionBox['table'], $fields, $values);
        $result = $this->query($sql);
        if( $result && mysqli_affected_rows(self::$link) > 0 ){
            return mysqli_insert_id(self::$link);
        }
        return false;
    }

    /**
     * 数据更新
     * @param array $data
     */
    public function update(array $data)
    {
        $arrSet = [];
        foreach ($data as $filed=>$value){
            $arrSet[] = "$filed='{$value}'";
        }
        $strSet = implode(',',$arrSet);
        $sql = sprintf(self::$boxQueryTemplate['update'],
            $this->conditionBox['table'],
            $strSet,
            $this->conditionBox['where']
        );
        if($this->query($sql)){
            return mysqli_affected_rows(self::$link);
        }
    }

    /**
     * 删除数据表记录
     */
    public function delete()
    {
        $sql = sprintf(self::$boxQueryTemplate['delete'],
            $this->conditionBox['table'],
            $this->conditionBox['where']
        );
        if($this->query($sql)){
            return mysqli_affected_rows(self::$link);
        }
    }
}
