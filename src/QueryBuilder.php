<?php

namespace Suvarivaza\AQB;

use Aura\SqlQuery\QueryFactory;
use PDO;
use PDOException;


class QueryBuilder
{

    private $pdo;
    private $queryFactory;
    protected $prefix = '';



    /*
     * Create connection in constructor
     * Gets the argument array with database connection configuration
     *
     * @param array $config
     * throw PDOException
     */
    function __construct(array $config)
    {

        $this->prefix = $config['prefix'];
        try {
            $db_server = $config['host'];
            $db_user = $config['db_user'];
            $db_password = $config['db_password'];
            $db_name = $config['db_name'];
            $charset = $config['charset'];
            $dsn = "mysql:host=$db_server;dbname=$db_name;charset=$charset";
            $options = $config['options'];
            $this->pdo = new PDO($dsn, $db_user, $db_password, $options);


        } catch (PDOException $exception) {
            $this->error = $exception->getMessage();
            die($exception->getMessage());
        }

        $this->queryFactory = new QueryFactory('mysql');
    }

    /*
     * @param string $table - table name
     * @param array $where
     */
    public function exists($table, array $where)
    {
        return count($this->select($table, '*', $where));
    }

    /*
     * getAll
     * @param string $table - table name
     * @param string $data_type can by: 'assoc', 'obj', 'both', 'num'
     * @return all results select()
     */
    public function getAll($table, $data_type = null)
    {
        return $this->select($table, '*', [], null, $data_type);
    }

    /*
     * getOne
     * @param string $table - table name
     * @param int $id
     * @param string $data_type can by: 'assoc', 'obj', 'both', 'num'
     * @return one result select()
     */
    public function getOne($table, $id, $data_type = null)
    {
        return $this->select($table, '*', ['id', '=', $id], 'one', $data_type);
    }

    /*
     * SELECT
     * @param string $table - table name
     * @param string $cols
     * @param array $where
     * @param string $fetch
     * @param string $data_type - can by: 'assoc', 'obj', 'both', 'num'
     * @return one result
     */
    public function select($table, $cols, $where = [], $fetch = null, $data_type = null)
    {

        $pdo_fetch_types = [
            'assoc' => PDO::FETCH_ASSOC,
            'obj' => PDO::FETCH_OBJ,
            'both' => PDO::FETCH_BOTH,
            'num' => PDO::FETCH_NUM,
        ];

        if ($data_type) {
            $pdo_fetch_type = $pdo_fetch_types[$data_type];
        } else {
            $pdo_fetch_type = PDO::FETCH_ASSOC;
        }

        $select = $this->queryFactory->newSelect();

        $select->cols([$cols])
            ->from("{$this->prefix}{$table}");

        if ($where) {
            $this->whereValidation($where);
            list($column, $operator, $value) = $where;
            $select
                ->where("{$column} {$operator} :{$column}")
                ->bindValue($column, $value);
        }

        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($select->getBindValues());

        if ($fetch === 'one') {
            return $sth->fetch($pdo_fetch_type);
        } else {
            return $sth->fetchAll($pdo_fetch_type);
        }


    }

    /*
     * INSERT
     * @param string $table - table name
     * @param array $data
     * @return bool $result
     */
    public function insert($table, $data)
    {
        $insert = $this->queryFactory->newInsert();

        $insert
            ->into("{$this->prefix}{$table}")// INTO this table
            ->cols($data);

        $sth = $this->pdo->prepare($insert->getStatement());
        return $result = $sth->execute($insert->getBindValues());
    }

    /*
     * UPDATE
     * @param string $table - table name
     * @param array $cols
     * @param array $where
     * @return bool $result
     */
    public function update($table, $cols, $where)
    {

        $update = $this->queryFactory->newUpdate();

        $this->whereValidation($where);
        list($column, $operator, $value) = $where;

        $update
            ->table("{$this->prefix}{$table}")// update this table
            ->cols($cols)
            ->where("{$column} {$operator} :{$column}")
            ->bindValue($column, $value);

        $sth = $this->pdo->prepare($update->getStatement());
        return $result = $sth->execute($update->getBindValues());

    }

    /*
     * DELETE
     * @param string $table - table name
     * @param array $where
     * @return bool $result
     */
    public function delete($table, $where)
    {

        $delete = $this->queryFactory->newDelete();

        $this->whereValidation($where);
        list($column, $operator, $value) = $where;

        $delete
            ->from("{$this->prefix}{$table}")// FROM this table
            ->where("{$column} {$operator} :{$column}")
            ->bindValue($column, $value);

        $sth = $this->pdo->prepare($delete->getStatement());
        return $result = $sth->execute($delete->getBindValues());

    }

    private function whereValidation($where)
    {
        if (count($where) !== 3) die('ERROR! where takes 3 parameters! ($column, $operator, $value)');
        $operator = $where[1];
        $operators = ['=', '<', '>', '<=', '>='];
        if (!in_array($operator, $operators)) die('Operator of this type is not supported!');
    }

    public function getPdo(){
        return $this->pdo;
    }


}
