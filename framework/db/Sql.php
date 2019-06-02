<?php
/**
 * SQL语句组合类
 * User: Jack<376927050@qq.com>
 * Date: 2018/10/28
 * Time: 9:25
 */

namespace framework\db;

use framework\libs\Page;

class Sql
{
    // 初始SQL组装集合
    private $_arrSql = [
        'field' => '*',
        'where' => '',
        'order' => '',
        'limit' => '',
        'table' => '',
    ];

    private $_where = [];
    private $_value = [];

    /**
     * 初始化
     */
    private function _initialize()
    {
        $this->_arrSql = [
            'field' => '*',
            'where' => '',
            'order' => '',
            'limit' => '',
            'table' => '', // 不用初始化表
        ];
    }

    /**
     * 设置查询字段
     * @param $field
     * @return $this
     */
    public function field($field)
    {
        if( !empty($field) ){
            $this->_arrSql['field'] = $field;
        }
        return $this;
    }

    /**
     * 设置操作表
     * @param $tabname
     * @return $this
     */
    public function table($tabname)
    {
        $this->_arrSql['table'] = $tabname;
        return $this;
    }

    /**
     * 设置where条件
     * @param $where ['id',1]
     * @return $this
     */
    public function where($field,$symbol,$value = '')
    {
        if( empty($value) ){
            $value = $symbol;
            $symbol = '=';
        }
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
        array_push($this->_where,$where[0]);
        $this->_value = array_merge($this->_value,$where[1]);
        if( !empty($this->_where) ){
            $wh = implode(' AND ',$this->_where);
            $this->_arrSql['where'] = "WHERE $wh";
        }
        return $this;
    }

    /**
     * 设置排序
     * @param $orderby
     * @return $this
     */
    public function order($orderby = 'id DESC')
    {
        if( !empty($orderby) ){
            $this->_arrSql['order'] = 'order by ' .$orderby;
        }
        return $this;
    }

    /**
     * 限制显示条件
     * @param $limit
     * @return $this
     */
    public function limit($limit = 30)
    {
        if( !empty($limit) ){
            $this->_arrSql['limit'] = 'limit ' .$limit;
        }
        return $this;
    }

    /**
     * 查询
     * @return $this
     */
    public function select()
    {
        $format = "SELECT %s FROM %s %s%s%s";
        $sql = sprintf($format,
            $this->_arrSql['field'],
            $this->_arrSql['table'],
            $this->_arrSql['where'],
            $this->_arrSql['order'],
            $this->_arrSql['limit']
        );
        $this->_initialize();
        //return [$sql,$this->_value];
        return (new MySQLPDO())->fetchAll($sql, $this->_value);
    }

    /**
     * 添加数据
     * @param $data
     * @return bool
     */
    public function insert(array $data){
        //获取所有字段
        $fields = array_keys($data);
        //拼接SQL语句
        $table = $this->_arrSql['table'];
        $sql = "INSERT INTO `$table` (`".implode('`,`', $fields).'`) VALUES (:'.implode(',:', $fields).')';
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
        $sql = sprintf($format,$this->_arrSql['table'],$update_fields,$this->_arrSql['where']);
        return (new MySQLPDO())->exec($sql, array_merge($data,$this->_value));
    }

    /**
     * 删除记录
     * @param $data
     * @return int
     */
    public function delete()
    {
        $format = "DELETE FROM %s %s";
        $sql = sprintf($format,
            $this->_arrSql['table'],
            $this->_arrSql['where']
        );
        $this->_initialize();
        //return [$sql,$this->_value];
        return (new MySQLPDO())->exec($sql, $this->_value);
    }

    public function count()
    {
        $format = "SELECT * FROM %s %s";
        $sql = sprintf($format,
            $this->_arrSql['table'],
            $this->_arrSql['where']
        );
        return (new MySQLPDO())->exec($sql, $this->_value);
    }

}