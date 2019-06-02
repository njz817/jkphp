<?php
/**
 * PDO MySQL类
 * Class MySQLPDO
 */
namespace framework\db;

use \PDO;
use \PDOException;

class MySQLPDO
{
    //保存PDO实例
    protected static $db = null;

    public function __construct()
    {
        self::$db || self::connect();
    }

    /**
     * 连接MySQL
     */
    protected function connect()
    {
        list($host,$dbname,$charset,$user,$pwd) = [
            config('database.host'),
            config('database.dbname'),
            config('database.charset'),
            config('database.user'),
            config('database.password')
        ];

        // 配置MySQL 设置字符集
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
        //建立持久化连接
        self::$db = new \PDO($dsn,$user,$pwd,[\PDO::ATTR_PERSISTENT=>true]);
        // 设置错误处理为抛PDO异常
        self::$db->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
    }

    /**
     * 通过预处理方式执行SQL
     * @param string $sql 执行的SQL语句模板
     * @param array $data 数据部分 可能是多维
     * @return object PDOStatement
     */
    public function query($sql, array $data=[])
    {
        $stmt = self::$db->prepare($sql);
        #考虑批量操作情况
        is_array(current($data)) || $data = [$data];
        foreach ($data as $arr){
            if($stmt->execute($arr) === false ){
                throw new PDOException('数据库操作失败：'.implode('-',$stmt->errorInfo())."\nSQL语句：".$sql);
            }
        }
        return $stmt;
    }


    /**
     * 执行SQL-写操作（支持批量操作，返回受影响的行数）
     * @param $sql
     * @param array $data
     * @return mixed
     */
    public function exec($sql, $data=[])
    {
        return $this->query($sql, $data)->rowCount();
    }

    /**
     * 取得一行结果（关联数组）
     * @param $sql
     * @param array $data
     * @return mixed
     */
    public function fetchRow($sql, $data=[])
    {
        return $this->query($sql, $data)->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 取得所有结果（关联数组）
     * @param $sql
     * @param array $data
     * @return mixed
     */
    public function fetchAll($sql, $data=[])
    {
        return $this->query($sql, $data)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 取得一列结果
     * @param $sql
     * @param array $data
     * @return mixed
     */
    public function fetchColumn($sql, $data=[])
    {
        return $this->query($sql, $data)->fetchColumn();
    }

    /**
     * 最后插入的ID
     * @return mixed
     */
    public function lastInsertId()
    {
        return self::$db->lastInsertId();
    }

    /**
     * 事务处理-启动
     * @return mixed
     */
    public function startTrans()
    {
        return self::$db->beginTransaction();
    }

    /**
     * 事务处理-提交
     * @return mixed
     */
    public function commit()
    {
        return self::$db->commit();
    }


    /**
     * 事务处理-回滚
     * @return mixed
     */
    public function rollBack()
    {
        return self::$db->rollBack();
    }
}