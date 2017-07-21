<?php
/**
 * Created by PhpStorm.
 * User: jeremy.marchand
 * Date: 03/07/2017
 * Time: 09:02
 */
namespace Application\Entity;
class Type{
    public $id;
    public $type;

    /**
     * Type constructor.
     * @param $id
     * @param $type
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }




}