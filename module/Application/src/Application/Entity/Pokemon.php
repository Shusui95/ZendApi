<?php
/**
 * Created by PhpStorm.
 * User: jeremy.marchand
 * Date: 09/06/2017
 * Time: 09:50
 */
namespace Application\Entity;

class Pokemon{
    public $id;
    public $name;
    public $description;
    public $picture;
    public $id_national;
    public $evolution = [];
    public $anterieure_evol;
    public $precedente_evol;
    public $futur_evol;
    public $posterieure_evol;
    public $type = [];
    public $geolocalisation = [];
    public $created_date;

    /**
     * Pokemon constructor.
     * @param $id
     * @param $name
     * @param $description
     * @param $picture
     * @param $evolution
     * @param $id_national
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param mixed $picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    /**
     * @return mixed
     */
    public function getEvolution()
    {
        return $this->evolution;
    }

    /**
     * @param mixed $evolution
     */
    public function setEvolution($evolution)
    {
        if ($evolution != null){
            if (!in_array($evolution, $this->evolution)){
                array_push($this->evolution, $evolution);
            }
        }

    }

    /**
     * @param mixed $evolution
     */
    public function setEvolutionArray($evolution)
    {
        $this->evolution = $evolution;

    }
    
    public function cleanEvolution(){
    	$this->evolution = [];
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

    /**
     * @return mixed
     */
    public function getAnterieureEvol()
    {
        return $this->anterieure_evol;
    }

    /**
     * @param mixed $anterieure_evol
     */
    public function setAnterieureEvol($anterieure_evol)
    {
        $this->anterieure_evol = $anterieure_evol;
    }

    /**
     * @return mixed
     */
    public function getPrecedenteEvol()
    {
        return $this->precedente_evol;
    }

    /**
     * @param mixed $precedente_evol
     */
    public function setPrecedenteEvol($precedente_evol)
    {
        $this->precedente_evol = $precedente_evol;
    }

    /**
     * @return mixed
     */
    public function getFuturEvol()
    {
        return $this->futur_evol;
    }

    /**
     * @param mixed $futur_evol
     */
    public function setFuturEvol($futur_evol)
    {
        $this->futur_evol = $futur_evol;
    }

    /**
     * @return mixed
     */
    public function getPosterieureEvol()
    {
        return $this->posterieure_evol;
    }

    /**
     * @param mixed $posterieure_evol
     */
    public function setPosterieureEvol($posterieure_evol)
    {
        $this->posterieure_evol = $posterieure_evol;
    }

    /**
     * @return mixed
     */
    public function getIdNational()
    {
        return $this->id_national;
    }

    /**
     * @param mixed $id_national
     */
    public function setIdNational($id_national)
    {
        $this->id_national = $id_national;
    }

    /**
     * @return array
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param array $type
     */
    public function setType($type)
    {

        if (!in_array($type, $this->type)){
            array_push($this->type, $type);
        }

    }
    /**
     * @param array $type
     */
    public function setTypeArray($type)
    {

        $this->type = $type;

    }
    
    public function cleanType(){
    	$this->type = [];
    }

    /**
     * @return array
     */
    public function getGeolocalisation()
    {
        return $this->geolocalisation;
    }

    /**
     * @param array $geolocalisation
     */
    public function setGeolocalisation($geolocalisation)
    {
        if (!in_array($geolocalisation, $this->geolocalisation)){
            array_push($this->geolocalisation, $geolocalisation);
        }
    }

    public function affectGeolocalisation($geolocalisation){
        if (is_array($geolocalisation)){
            $this->geolocalisation = $geolocalisation;
        }
    }

    public function cleanGeolocalisation(){
        $this->geolocalisation = [];
    }

    public function extract(){

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'picture' => $this->picture,
            'id_national' => $this->id_national,
        ];
    }

    public function hydrate(array $data){

        $this->id = (isset($data['id']) ? intval($data['id']) : null);
        $this->name = (isset($data['name']) ? $data['name'] : null);
        $this->description = (isset($data['description']) ? $data['description'] : null);
        $this->picture = (isset($data['picture']) ? $data['picture'] : null);
        $this->evolution = (isset($data['evolution']) ? intval($data['evolution']) : null);
        $this->type = (isset($data['type']) ? intval($data['type']) : null);
        $this->id_national = (isset($data['id_national']) ? $data['id_national'] : null);
        $this->created_date = (isset($data['created_date']) ? $data['created_date'] : null);
    }

}
