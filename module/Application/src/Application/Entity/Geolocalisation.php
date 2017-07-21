<?php
/**
 * Created by PhpStorm.
 * User: jeremy.marchand
 * Date: 09/06/2017
 * Time: 09:55
 */
namespace Application\Entity;
class Geolocalisation {
    public $id;
    public $latitude;
    public $longitude;
    public $pokemon;
    public $created_date;

    /**
     * Geolocalisation constructor.
     * @param $id
     * @param $latitude
     * @param $longitude
     * @param $pokemon
     * @param $created_date
     */
    public function __construct()
    {

    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return mixed
     */
    public function getPokemon()
    {
        return $this->pokemon;
    }

    /**
     * @param mixed $pokemon
     */
    public function setPokemon($pokemon)
    {
        $this->pokemon = $pokemon;
    }

    /**
     * @return mixed
     */
    public function getCreatedDate()
    {
        return $this->created_date;
    }

    /**
     * @param mixed $created_date
     */
    public function setCreatedDate($created_date)
    {
        $this->created_date = $created_date;
    }

    public function extract(){

        return [
            'id' => $this->id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'pokemon' => $this->pokemon,
            'created_date' => $this->created_date,
        ];
    }

    public function hydrate(array $data){

        $this->id = (isset($data['id']) ? intval($data['id']) : null);
        $this->latitude = (isset($data['latitude']) ? $data['latitude'] : null);
        $this->longitude = (isset($data['longitude']) ? $data['longitude'] : null);
        $this->pokemon = (isset($data['pokemon']) ? intval($data['pokemon']) : null);
        $this->created_date = (isset($data['created_date']) ? $data['created_date'] : null);
    }

}