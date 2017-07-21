<?php
/**
 * Created by PhpStorm.
 * User: jeremy.marchand
 * Date: 10/06/2017
 * Time: 18:00
 */
namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Application\Entity\Geolocalisation;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Zend\Form\Element\Date;
use Zend\XmlRpc\Value\DateTime;

class GeoTable extends AbstractTableGateway
{
    protected $table = 'geolocalisation';
    protected $tableGateway;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function getGeolocByPokemonId($id)
    {
        $select = new Select($this->table);
        $select->where(['geolocation.pokemon_id' => $id]);
        $select->join(
            'pokemon',
            'geolocalisation.pokemon_id = pokemon.id',
            array(),
            Select::JOIN_INNER
        );
        //$select->group('id_national');
        $resultSet = $this->selectWith($select);
        $entities = array();

        foreach ($resultSet as $row) {

            $entity = new Geolocalisation();
            $entity->setLatitude($row->latitude);
            $entity->setLongitude($row->longitude);
            $entity->setCreatedDate($row->created_date);
            $entities[] = $entity;
        }

        return $entities;
    }
    public function addGeoloc($id, $lat, $lng)
    {
        $sql = new Sql($this->adapter);
        $insert = $sql->insert('geolocalisation');
        $newData = (
        ['pokemon' => intval($id), 'latitude' => floatval($lat), 'longitude' => floatval($lng)]
        );
        $insert->values($newData);
        $selectString = $sql->getSqlStringForSqlObject($insert);
        $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
        return $results;
    }

    public function getUnder30minGeolocByPokemonId($id)
    {
        // Get un timestamp ou datetime
        $date = date('Y-m-d H:i:s');
        $dateSubstract = date('Y-m-d H:i:s', strtotime("-30 minutes"));

        $select = new Select($this->table);

        $where = new Where();
        $where->equalTo('geolocalisation.pokemon', intval($id));
        // ToDo :: 30 min window
        $where->AND->lessThanOrEqualTo('geolocalisation.created_date', $date);
        $where->AND->greaterThanOrEqualTo('geolocalisation.created_date', $dateSubstract);
        //$select->where(['geolocalisation.pokemon' => intval($id), 'geolocalisation.created_date' => $date]);
        $select->join(
            'pokemon',
            'geolocalisation.pokemon = pokemon.id',
            array(),
            Select::JOIN_INNER
        );
        $select->where($where);
        //$select->group('id_national');
        $resultSet = $this->selectWith($select);
        $entities = array();

        foreach ($resultSet as $row) {
            $entity = new Geolocalisation();
            $entity->setId($row->id);
            $entity->setLatitude($row->latitude);
            $entity->setLongitude($row->longitude);
            $entity->setCreatedDate($row->created_date);
            $entities[] = $entity;
        }
        return $entities;
    }

    public function insertGeolocalisation(Geolocalisation $geo)
    {
        $insert = $sql->insert($this->table);
        $newData = array(
            'latitude' => $geo->latitude,
            'longitude' => $geo->longitude,
            'pokemon_id' => $geo->pokemon
        );
        $insert->values($newData);
        $selectString = $sql->getSqlStringForSqlObject($insert);
        $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    }

}
