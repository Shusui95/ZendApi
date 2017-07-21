<?php
/**
 * Created by PhpStorm.
 * User: jeremy.marchand
 * Date: 10/06/2017
 * Time: 18:00
 */
namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Application\Entity\User;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Update;
use Zend\Db\TableGateway\TableGateway;

class UserTable extends AbstractTableGateway {
    protected $table = 'user';
    protected $tableGateway;

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }

    public function getUserByLoginPassword($login, $password) {

        $select = new Select($this->table);
        $select->where(['user.login' => $login, 'user.password' => $password]);
        $resultSet = $this->selectWith($select);
        $entities = array();

        foreach ($resultSet as $row)
        {
            $entity = new User();
            $entity->setId($row->id);
            $entity->setLogin($row->login);
            //$entity->setPassword($row->password);
            $entities[] = $entity;
        }
        return !empty($entities[0]) ? $entities[0] : null;
    }

    public function insertGeolocalisation(User $user){
        $insert = $sql->insert($this->table);
        $newData = $user->extract();
        $insert->values($newData);
        $selectString = $sql->getSqlStringForSqlObject($insert);
        $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    }

    public function updateUser(User $user){
        $sql = new Update($this->table);
        $userUpdate = $sql->set($user->extract());
        return $userUpdate;
    }

}
