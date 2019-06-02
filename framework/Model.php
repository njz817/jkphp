<?php
/**
 * 数据表模型基类
 *  get_called_class  （实例的谁）  和 __CLASS__ （在哪个类里面） 区别
 *  static   self
 * User: Jack<376927050@qq.com>
 * Date: 2018/10/23
 * Time: 16:53
 */
namespace framework;

use framework\db\MySQLPDO;
use framework\db\Query;

class Model
{
    // 数据表名
    protected $tableName;
    // 数据表字段
    protected $primaryKey;
    // 条件
    protected $conditionBox = [
        'field' => '*',
        'where' => '',
        'order' => '',
        'limit' => ''
    ];
    protected $data = [];

    /**
     * 初始化工作
     * Model constructor.
     */
    public function __construct()
    {
        # 获取表名
        $arrTemp = explode('\\',get_called_class());
        $this->tableName = $arrTemp[count($arrTemp)-1];
        # 获取表的主键
        $this->primaryKey = $this->getTablePriamryKey();
    }

    /**
     * @param $condition 查询条件
     */
    public function where($field,$symbol,$value = '')
    {
        if( empty($value) ){
            $value = $symbol;
            $symbol = '=';
        }
        $where = [];
        switch ($symbol)
        {
            case '=':
                $where = ["`$field` = :$field",[":$field"=>$value]];
                break;
            case '>':
                $where = ["`$field` > :$field",[":$field"=>$value]];
                break;
            case '<':
                $where = ["`$field` < :$field",[":$field"=>$value]];
                break;
            case 'like':
                $where = ["`$field` like :$field",[":$field"=>$value]];
                break;
            case 'in':
                if( is_array($value) ){
                    $value = implode(",",$value);
                }
                $where = ["`$field` in ($value)",[]];
                break;
        }

        if( !empty($where[0]) ){
            $this->data = array_merge($this->data,$where[1]);
            $condition = $where[0];
            $this->conditionBox['where'] = " where $condition";
        }
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
     * 获取排序
     * @param $cond
     */
    public function order($cond)
    {
        $this->conditionBox['order'] = " order by $cond";
        return $this;
    }

    /**
     * 获取分隔
     * @param $cond
     */
    public function limit($condition)
    {
        $this->conditionBox['limit'] = " limit $condition";
        return $this;
    }

    /**
     * 通过主键获取记录
     * @param int $pkId
     * @return array | null
     */
    public function find(int $pkId)
    {
        $sql = "select * from {$this->tableName} where {$this->primaryKey}=:{$this->primaryKey}";
        return (new MySQLPDO())->fetchRow($sql,[":{$this->primaryKey}"=>$pkId]);
    }

    /**
     * 根据条件获取查询记录
     * @return array
     */
    public function select()
    {
        $sql = sprintf('select %s from %s%s%s%s',
            $this->conditionBox['field'],
            $this->tableName,
            $this->conditionBox['where'],
            $this->conditionBox['order'],
            $this->conditionBox['limit']
        );
        return (new MySQLPDO())->fetchAll($sql,$this->data);
    }

    /**
     * 添加数据
     * @param $data
     * @return int | bool
     */
    public function insert(array $data){
        //获取所有字段
        $fields = array_keys($data);
        //拼接SQL语句
        $sql = "INSERT INTO `{$this->tableName}` (`".implode('`,`', $fields).'`) VALUES (:'.implode(',:', $fields).')';
        //调用数据库操作类执行SQL，成功返回最后插入的ID，失败返回false
        $mysql_pdo = new MySQLPDO();
        return $mysql_pdo->query($sql, $data) ? $mysql_pdo->lastInsertId() : false;
    }

    /**
     * 更新数据
     * @param array $data
     * @return bool|mixed
     */
    public function update(array $data){
        //获取所有字段
        $fields = array_keys($data);
        $handelBindParams = array_map(function($v){
            return "`$v`=:$v";
        },$fields);
        $update_fields = implode(',', $handelBindParams);
        $format = "UPDATE %s SET %s %s";
        $sql = sprintf($format,$this->tableName,$update_fields,$this->conditionBox['where']);
        return (new MySQLPDO())->exec($sql, $data);
    }

    /**
     * 统计总记录数
     * @return mixed
     */
    public function count()
    {
        $format = "SELECT * FROM %s %s";
        $sql = sprintf($format,
            $this->tableName,
            $this->conditionBox['where']
        );
        return (new MySQLPDO())->exec($sql);
    }

    /**
     * 分页
     * @param int $limit
     * @return array
     */
    public function paginate(int $limit = 1)
    {
        $total = $this->count();
        $pager = new \framework\libs\Page($total,$limit);
        $condition = $pager->getDbFetchCondition();
        $list = $this->limit($condition)->select();
        return ['data'=>$list,'html'=>$pager->showPage()];
    }

    /**
     * 获取表的主键
     */
    protected function getTablePriamryKey()
    {
        $dbname = config('database.dbname');
        $sql = "SELECT * FROM information_schema.`COLUMNS` WHERE table_name='{$this->tableName}' AND table_schema = '{$dbname}'";
        $result = (new MySQLPDO())->fetchAll($sql);
        foreach ($result as $rows){
            if( $rows['COLUMN_KEY'] == 'PRI' ){
                return $rows['COLUMN_NAME'];
            }
        }
    }



}