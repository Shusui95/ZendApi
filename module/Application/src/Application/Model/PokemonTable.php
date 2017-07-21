<?php
/**
 * Created by PhpStorm.
 * User: jeremy.marchand
 * Date: 10/06/2017
 * Time: 18:00
 */
namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Update;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Application\Entity\Pokemon;
use Application\Entity\Type;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Text\Table\Table;

class PokemonTable extends AbstractTableGateway
{
    protected $table = 'pokemon';
    protected $tableGateway;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function fetchAll()
    {
        $select = new Select($this->table);
        $select->join(
            'evolution',
            'pokemon.id = evolution.poke_id',
            array('evol_id'),
            Select::JOIN_LEFT
        );
        $select->join(
            'typepoke',
            'pokemon.id = typepoke.poke_id',
            array(),
            Select::JOIN_INNER
        );
        $select->join(
            'type',
            'typepoke.type_id = type.id',
            array('typepoke'),
            Select::JOIN_INNER
        );
        //$select->group('id_national');
        $resultSet = $this->selectWith($select);
        $entities = array();
        $array = array();
        foreach ($resultSet as $row) {

            // Search if we already have pokemon in list, return his index list if true
            $index = array_search($row->id_national, $array);

            if (is_numeric($index)) {
                $entities[$index]->setEvolution($row->evol_id);
                $entities[$index]->setType($row->typepoke);
            } else {
                array_push($array, $row->id_national);

                $entity = new Pokemon();
                $entity->setId($row->id);
                $entity->setName($row->name);
                $entity->setPicture($row->picture);
                $entity->setDescription($row->description);
                $entity->setIdNational($row->id_national);
                $entity->setCreatedDate($row->created_date);
                $entity->setEvolution($row->evol_id);
                $entity->setType($row->typepoke);

                $entities[] = $entity;
            }
        }
        return $entities;
    }

    public function pokemonDetails($id)
    {
        $select = new Select($this->table);
        $select->where(['pokemon.id_national' => $id]);
        $select->join(
            'evolution',
            'pokemon.id = evolution.poke_id',
            array('evol_id'),
            Select::JOIN_LEFT
        );
        //$select->join('evolution',
        //	pokemon.id = evolution.evol_id',
        // array('poke_id'),
        // Select::JOIN_LEFT
        // if florizarre gonna dont work
        $select->join(
            'typepoke',
            'pokemon.id = typepoke.poke_id',
            array(),
            Select::JOIN_INNER
        );
        $select->join(
            'type',
            'typepoke.type_id = type.id',
            array('typepoke'),
            Select::JOIN_INNER
        );
        //$select->group('id_national');
        $resultSet = $this->selectWith($select);
        $entities = array();
        $array = array();
        foreach ($resultSet as $row) {
            // Search if we already have pokemon in list, return his index list if true
            $index = array_search($row->id_national, $array);

            if (is_numeric($index)) {
                $entities[$index]->setEvolution($row->evol_id);
                $entities[$index]->setType($row->typepoke);
            } else {
                array_push($array, $row->id_national);

                $entity = new Pokemon();
                $entity->setId($row->id);
                $entity->setName($row->name);
                $entity->setPicture($row->picture);
                $entity->setDescription($row->description);
                $entity->setIdNational($row->id_national);
                $entity->setCreatedDate($row->created_date);
                $entity->setEvolution($row->evol_id);
                $entity->setType($row->typepoke);

                array_push($entities, $entity);
            }
        }
        return !empty($entities[0]) ? $entities[0] : null;
    }

    public function pokemonDetailsById($id)
    {
        $select = new Select($this->table);
        $select->where(['pokemon.id' => $id]);
        $select->join(
            'evolution',
            'pokemon.id = evolution.poke_id',
            array('evol_id'),
            Select::JOIN_LEFT
        );
        //$select->join('evolution',
        //	pokemon.id = evolution.evol_id',
        // array('poke_id'),
        // Select::JOIN_LEFT
        // if florizarre gonna dont work
        $select->join(
            'typepoke',
            'pokemon.id = typepoke.poke_id',
            array(),
            Select::JOIN_INNER
        );
        $select->join(
            'type',
            'typepoke.type_id = type.id',
            array('typepoke'),
            Select::JOIN_INNER
        );
        //$select->group('id_national');
        $resultSet = $this->selectWith($select);
        $entities = array();
        $array = array();
        foreach ($resultSet as $row) {
            // Search if we already have pokemon in list, return his index list if true
            $index = array_search($row->id_national, $array);

            if (is_numeric($index)) {
                $entities[$index]->setEvolution($row->evol_id);
                $entities[$index]->setType($row->typepoke);
            } else {
                array_push($array, $row->id_national);

                $entity = new Pokemon();
                $entity->setId($row->id);
                $entity->setName($row->name);
                $entity->setPicture($row->picture);
                $entity->setDescription($row->description);
                $entity->setIdNational($row->id_national);
                $entity->setCreatedDate($row->created_date);
                $entity->setEvolution($row->evol_id);
                $entity->setType($row->typepoke);

                array_push($entities, $entity);
            }
        }
        return !empty($entities[0]) ? $entities[0] : null;
    }

    public function moreDetails($id)
    {
        $pokemon = new Pokemon();
        $pokemon = $this->pokemonDetails($id);
        $arrayEvolution = $pokemon->getEvolution();
            $pokemon->cleanEvolution();
        $pokemon = $this->detailsEvolution($arrayEvolution, $pokemon);
        return $pokemon;
    }
    public function moreDetailsBis($arrayEvolution, $pokemon)
    {

        $pokemon = $this->detailsEvolution($arrayEvolution, $pokemon);
        return $pokemon;
    }
    public function getPokemonInfos($id)
    {
        $pokemon = new Pokemon();
        $pokemon = $this->pokemonDetails($id);
        return $pokemon;
    }

    public function getEvolution(Pokemon $pokemon){
        $select = new Select('evolution');
        $select->where('evolution.poke_id ='.$pokemon->id.' OR evolution.evol_id='.$pokemon->id);
        $resultSet = $this->selectWith($select);
        foreach ($resultSet as $row) {
            if ($row->poke_id == $pokemon->id){
                $pokemon->setEvolution($row->evol_id);
            }else{
                $pokemon->setEvolution($row->poke_id);
            }
        }
        return $pokemon;
    }
    public function getEvolutionBis($pokemon){
        $arrayGiven = $pokemon->getEvolution();
        foreach ($arrayGiven as $idArray){

            $select = new Select('evolution');
            $select->where('(evolution.poke_id ='.$idArray.' OR evolution.evol_id='.$idArray.')');
            $resultSet = $this->selectWith($select);

            foreach ($resultSet as $row) {

                if ($row->poke_id != $pokemon->getId() && $row->evol_id != $pokemon->getId()){
                    if ($row->poke_id == $idArray){
                        if (!in_array($row->evol_id, $arrayGiven)){
                            $pokemon->setEvolution($row->evol_id);
                        }

                    }else{
                        if (!in_array($row->poke_id, $arrayGiven)){
                            $pokemon->setEvolution($row->poke_id);
                        }
                    }
                }

            }
        }
        return $pokemon;
    }

    public function detailsEvolution($array, Pokemon $pokemon)
    {
        if (empty($array)){
            return $pokemon;
        }

        foreach ($array as $row) {
            $pokemon->setEvolution($this->pokemonDetailsById($row));
        }
        return $pokemon;
    }

    public function insertPokemon(Pokemon $pokemon)
    {
        $sql = new Sql($this->adapter);
        $insert = $sql->insert($this->table);
        $newData = (
        $pokemon->extract()
        );
        $insert->values($newData);
        $selectString = $sql->getSqlStringForSqlObject($insert);
        $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
        return $results;
    }

    public function insertType($id, $type)
    {
        $sql = new Sql($this->adapter);
        $insert = $sql->insert('typepoke');
        $newData = (
            ['poke_id' => $id, 'type_id' => $type]
        );
        $insert->values($newData);
        $selectString = $sql->getSqlStringForSqlObject($insert);
        $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
        return $results;
    }

    public function getType($type){

            $select = new Select('type');
            $select->where(['typepoke '=> $type]);
            $resultSet = $this->selectWith($select);

            foreach ($resultSet as $row) {
                return $row->id;
            }
        return null;
    }

    public function insertEvolution($id, $evol)
    {
        $sql = new Sql($this->adapter);
        $insert = $sql->insert('evolution');
        $newData = (
            ['poke_id' => $id, 'evol_id' => $evol]
        );
        $insert->values($newData);
        $selectString = $sql->getSqlStringForSqlObject($insert);
        $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
        return $results;
    }

    public function getEvolutionId($evol){

        $select = new Select($this->table);
        $select->where('(pokemon.id_national ='.$evol.')');
        $resultSet = $this->selectWith($select);

        foreach ($resultSet as $row) {
            return $row->id;
        }
        return null;
    }

    public function updatePokemon(Pokemon $pokemon)
    {
        $action = new Update($this->table);
        $action->set($pokemon->extract());
        $action->where(['id_national = ?' => $pokemon->getIdNational()]);

        $sql = new Sql($this->adapter);
        $stmt = $sql->prepareStatementForSqlObject($action);
        $result = $stmt->execute();

        return $result;
    }
    public function updateEvolution($id, $evol)
    {
        $action = new Update('evolution');
        $action->set(['evol_id' => $evol]);
        $action->where(['poke_id = ?' => $id]);

        $sql = new Sql($this->adapter);
        $stmt = $sql->prepareStatementForSqlObject($action);
        $result = $stmt->execute();

        return $result;
    }
    public function deleteEvolution($id)
    {
        $action = new Delete('evolution');
        $action->where(['poke_id = ?' => $id]);

        $sql = new Sql($this->adapter);
        $stmt = $sql->prepareStatementForSqlObject($action);
        $result = $stmt->execute();

        return $result;
    }
    public function deleteEvolutionBis($id)
    {
        $action = new Delete('evolution');
        $action->where(['evol_id = ?' => $id]);

        $sql = new Sql($this->adapter);
        $stmt = $sql->prepareStatementForSqlObject($action);
        $result = $stmt->execute();

        return $result;
    }
    public function updateType($id, $type)
    {
        $action = new Update('typepoke');
        $action->set(['type_id' => $type]);
        $action->where(['poke_id = ?' => $id]);

        $sql = new Sql($this->adapter);
        $stmt = $sql->prepareStatementForSqlObject($action);
        $result = $stmt->execute();

        return $result;
    }
    public function deleteType($id)
    {
        $action = new Delete('typepoke');
        $action->where(['poke_id = ?' => $id]);

        $sql = new Sql($this->adapter);
        $stmt = $sql->prepareStatementForSqlObject($action);
        $result = $stmt->execute();

        return $result;
    }

    public function deletePokemon($id)
    {
        $action = new Delete($this->table);
        $action->where(['pokemon.id = ?' => $id]);

        $sql = new Sql($this->adapter);
        $stmt = $sql->prepareStatementForSqlObject($action);
        $result = $stmt->execute();

        return $result;
    }

    public function searchByIdNational($pokemonId)
    {
        $select = new Select($this->table);
        $select->where(['pokemon.id' => $pokemonId]);
        $resultSet = $this->selectWith($select);
        $entities = array();
        $array = array();
        foreach ($resultSet as $row) {
            // Search if we already have pokemon in list, return his index list if true
            $index = array_search($row->id_national, $array);

            if (is_numeric($index)) {
                $entities[$index]->setEvolution($row->evol_id);
                $entities[$index]->setType($row->name);
            } else {
                array_push($array, $row->id_national);

                $entity = new Pokemon();
                $entity->setId($row->id);
                $entity->setName($row->name);
                $entity->setPicture($row->picture);
                $entity->setDescription($row->description);
                $entity->setIdNational($row->id_national);
                $entity->setCreatedDate($row->created_date);
                $entity->setEvolution($row->evol_id);
                $entity->setType($row->name);

                $entities[] = $entity;
            }
        }
        return $entities[0];
    }

    public function searchByName($name)
    {
        $select = new Select($this->table);
        $where = new Where();
        $where->like('pokemon.name', '%' . $name . '%');
        $select->where($where);
        $resultSet = $this->selectWith($select);
        $entities = array();
        $array = array();
        foreach ($resultSet as $row) {
            // Search if we already have pokemon in list, return his index list if true
            $index = array_search($row->id_national, $array);

            if (is_numeric($index)) {
                $entities[$index]->setEvolution($row->evol_id);
                $entities[$index]->setType($row->name);
            } else {
                array_push($array, $row->id_national);

                $entity = new Pokemon();
                $entity->setId($row->id);
                $entity->setName($row->name);
                $entity->setPicture($row->picture);
                $entity->setDescription($row->description);
                $entity->setIdNational($row->id_national);



                $entities[] = $entity;
            }
        }
        return $entities;
    }

    public function getTypes(){
        $select = new Select('type');
        $resultSet = $this->selectWith($select);
        $entities = array();
        foreach ($resultSet as $row) {
            $entity = new Type();
            $entity->setId($row->id);
            $entity->setType($row->typepoke);
            $entities[] = $entity;
        }
        return $entities;
    }

    public function getNames(){
        $select = new Select($this->table);
        $resultSet = $this->selectWith($select);
        $entities = array();
        foreach ($resultSet as $row) {
            $entity = new Pokemon();
            $entity->setIdNational($row->id_national);
            $entity->setName($row->name);
            $entities[] = $entity;
        }
        return $entities;
    }
    public function getLastId(){
        $select = new Select($this->table);
        $select->order('id_national DESC');
        $select->limit(1);
        $resultSet = $this->selectWith($select);
        $entities = array();
        foreach ($resultSet as $row) {
            return $row->id_national;
        }
        return null;
    }
    public function getLastIdInserted(){
        $select = new Select($this->table);
        $select->order('id DESC');
        $select->limit(1);
        $resultSet = $this->selectWith($select);
        $entities = array();
        foreach ($resultSet as $row) {
            return $row->id;
        }
        return null;
    }

}
